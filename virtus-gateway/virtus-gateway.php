<?php
/**
  * Plugin Name: VirtusPay Boleto Parcelado
  * Plugin URI: https://documenter.getpostman.com/view/215460/SVSPnmLs?version=latest
  * Description: Pagamentos para o WooCommerce de boletos parcelados através da VirtusPay.
  * Version: 1.2.6
  * Author: VirtusPay Dev Team
  * Author URI: https://usevirtus.com.br
  * Privacy Policy: https://www.usevirtus.com.br/privacidade-virtuspay
  *
  * @package VirtusPay
  */
require_once __DIR__.'/settings.php';
require_once __DIR__.'/helpers.class.php';
require_once __DIR__.'/fetch.class.php';
require_once __DIR__.'/installments.api.php';

add_action('plugins_loaded', 'VirtusPayGatewayInit', 0);
function VirtusPayGatewayInit() {
  add_filter('woocommerce_payment_gateways', 'VirtusPayGatewayAddPaymentMethod');
  function VirtusPayGatewayAddPaymentMethod(array $paymentMethods): array {
    array_push($paymentMethods, 'VirtusPayGateway');
    return $paymentMethods;
  }

  add_filter('plugin_action_links_virtus-gateway/virtus-gateway.php', 'VirtusPayGatewayAddConfigInPluginList');
  function VirtusPayGatewayAddConfigInPluginList(array $links): array {
    $url = esc_url( add_query_arg(
      'page',
      'wc-settings&tab=checkout&section=virtuspay',
      get_admin_url().'admin.php'
    ));

    $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';

    array_push(
      $links,
      $settings_link
    );

    return $links;
  }

  add_filter('woocommerce_payment_gateways', 'VirtusPayGatewayAddGatewayNameForWooCommerce');
	function VirtusPayGatewayAddGatewayNameForWooCommerce(array $methods): array {
		array_push($methods, virtuspay_TITLE);
		return $methods;
	}

  if(!class_exists('WC_Payment_Gateway'))
    return new WP_Error(
      'virtus_payment_gateway_undefined',
      'É necessária a instalação do Woocommerce',
      ['status' => 400]
    );

  // actions para alteração de status de pedidos
  include_once __DIR__.'/orders-status-actions.php';

  class VirtusPayGateway extends WC_Payment_Gateway {
    public $id = virtuspay_VIRTUSPAYMENTID;
    public $plugin_id = virtuspay_VIRTUSPAYMENTID;
    public $icon = virtuspay_ICON;
    public $has_fields = true;
    public $supports = [];

    // config woocommerce/settings[/...]
    public $method_title = virtuspay_TITLE;
    public $method_description = virtuspay_DESCRIPTION;
    public $title = virtuspay_TITLE;
    public $description = virtuspay_DESCRIPTION;

    // defaults
    public $enabled = 'no';
    private $return_url = '';
    private $testmode = 'no';
    private $isTestMode = false;
    private $authTestToken;
    private $authProdToken;
    private $authToken;
    private $remoteApiUrl;
    private $currentAmount;

    public function __construct() {
      global $woocommerce;
      $this->wc = $woocommerce;

      $this->supports = [
        'products'
      ];

      $this->init_form_fields();
      $this->init_settings();

      $helpers = new VirtusPayGateway\Helpers;
      $this->enabled = $helpers->option('enabled');

      $returnURL = $helpers->option('return_url');
      $this->return_url = !empty($returnURL) ? $returnURL : wc_get_checkout_url();

      $this->testmode = $helpers->option('testmode');
      $this->isTestMode = $helpers->isTestmode();
      $this->remoteApiUrl = $helpers->virtusEndpoint();

      $this->authTestToken = $helpers->option('test_auth_token');
      $this->authProdToken = $helpers->option('auth_token');
      $this->authToken = $helpers->getToken();

      // Begin APIs Endpoints
      add_action(
        "woocommerce_api_{$this->id}",
        [$this, 'virtusCallback']
      );
      // End APIs Endpoints

      add_filter(
        'woocommerce_billing_fields',
        [$this, 'custom_woocommerce_billing_fields'],
        15
      );

      add_action(
        'woocommerce_update_options_payment_gateways_' . $this->id,
        [$this, 'process_admin_options']
      );

      // Begin CSS Custom
      wp_enqueue_style(
        'psiCustomStyles',
        virtuspay_PLUGINURL.'/assets/css/virtus.css?virtusPayVersion='.virtuspay_VERSION
      );
      // End CSS Custom

      // Begin JS Scripts
      wp_enqueue_script(
        'virtus-jquery-mask',
        virtuspay_PLUGINURL.'/assets/js/jquery.mask.min.js',
        ['jquery']
      );

      wp_enqueue_script(
        'virtus-library',
        virtuspay_PLUGINURL.'/assets/js/virtus.js?virtusPayVersion='.virtuspay_VERSION,
        ['virtus-jquery-mask']
      );
      // End JS Scripts

      if(!is_null($this->wc) and !is_null($this->wc->cart)) {
        $currentCartString = $this->wc->cart->get_cart_total();

        preg_match_all('/[0-9]+/', $currentCartString, $cartNumbersOnly);
        $currentCartCents = array_slice($cartNumbersOnly[0], -2);
        $this->currentAmount = implode('.', $currentCartCents);
      }
    }

    public function init_form_fields(): void {
      $this->form_fields = [
        'enabled' => [
          'title' => 'Ativação',
          'label' => 'Ativar '.virtuspay_TITLE.'?',
          'type'  => 'checkbox',
          'description' => 'A ativação ou desativação de pagamentos influenciará na tomada de decisão do seu comprador.',
          'default' => $this->enabled,
          'name' => 'enabled',
          'id' => 'enabled',
          'desc_tip' => true
        ],
        'testmode' => [
          'title' => 'Modo de testes',
          'label' => 'Ativar '.virtuspay_TITLE.' em modo de testes?',
          'type' => 'checkbox',
          'description' => 'Ativar o modo de testes permite que você possa homologar os seus pagamentos fora do seu ambiente de produção.',
          'default' => $this->testmode,
          'name' => 'testmode',
          'id' => 'testmode',
          'desc_tip' => true
        ],
        'return_url' => [
          'title' => 'URL de retorno',
          'type' => 'text',
          'description' => 'URL para qual devemos redirecionar o usuário após a validação do seu pagamento.',
          'required' =>  true,
          'default' => $this->return_url,
          'name' => 'return_url',
          'id' => 'return_url',
          'desc_tip' => true
        ],
        'test_auth_token' => [
          'title' => 'Credencial / Homologação',
          'description' => 'Token de acesso a API do ambiente de testes/homologação.',
          'type' => 'text',
          'required' =>  true,
          'default' => $this->authTestToken,
          'name' => 'test_auth_token',
          'id' => 'test_auth_token',
          'desc_tip' => true
        ],
        'auth_token' => [
          'title' => 'Credencial / Produção',
          'description' => 'Token de acesso a API do ambiente de produção/publicação.',
          'type' => 'text',
          'default' => $this->authProdToken,
          'name' => 'auth_token',
          'id' => 'auth_token',
          'desc_tip' => true
        ]
      ];
    }

    public function payment_fields(): void {
      if($this->description) {
        if ($this->isTestMode) {
          $this->description = '
            <b>!!! '.$this->title.' EM MODO DE TESTES !!!</b>
          ';
        }

        echo wpautop($this->description);
      }

      $response = '<div class="form-group">
          <select
            class="form-control"
            name="billing_installment"
            id="billing_installment"
            data-amount="'.esc_attr($this->currentAmount).'">
            <option selected disabled>Carregando...</option>
          </select><br />
          <small id="interestAndCet"></small>
        </div>';

      echo $response;
    }

    private function orderEntropyConcat(string $orderID): string {
      return $orderID.'.'.time();
    }

    private function orderEntropyReverse(string $entropy): string {
      return strstr($entropy, '.', true);
    }

    public function virtusCallback() {
      $virtus = json_decode(file_get_contents('php://input'), true);

      if(!isset($virtus['transaction'])) {
        return new WP_Error(
          'virtus_unidentified_transaction',
          'Não foi recebido o identificador da transação.',
          ['status' => 400]
        );
      }

      $virtusProposal = new VirtusPayGateway\Fetch($this->authToken);
      $virtusProposal->get($this->remoteApiUrl."/v1/order/{$virtus['transaction']}");
      $proposalResponse = $virtusProposal->response();
      $proposal = array_shift($proposalResponse);

      if(isset($proposal->detail)) {
        return new WP_Error(
          'virtus_proposal_detail_error',
          $proposal->detail,
          ['status' => 400]
        );
      }

      $orderId = $this->orderEntropyReverse($proposal->order_ref);
      $order = wc_get_order($orderId);

      if($proposal->status && !empty($proposal->status)) {
        // E = aprova o pedido
        if($proposal->status === 'E') {
          $order->update_status('completed', 'Parcela de entrada paga.');
        }
  
        // R e C = cancela o pedido
        if(in_array($proposal->status, ['R', 'C'])) {
          $order->update_status('cancelled', 'Proposta cancelada.');
        }
  
        // P, N, A = status processando
        if(in_array($proposal->status, ['P', 'N', 'A'])) {
          $order->update_status('processing', 'Proposta em processamento.');
        }
      }

      return $order;
    }

    private function getProductCategoriesByIDs(array $data): string {
      $categories = [];

      foreach($data as $id) {
        $term = get_term_by('id', $id, 'product_cat');
        if($term) array_push($categories, $term->name);
      }

      return implode(', ', $categories);
    }

    public function process_payment($order_id) {
      $billing_cpf = sanitize_text_field($_POST['billing_cpf']);
      $billing_income = sanitize_text_field($_POST['billing_income']);
      $billing_address_1 = sanitize_text_field($_POST['billing_address_1']);
      $billing_number = sanitize_text_field($_POST['billing_number']);
      $billing_address_2 = sanitize_text_field($_POST['billing_address_2']);
      $billing_neighborhood = sanitize_text_field($_POST['billing_neighborhood']);
      $billing_cellphone = sanitize_text_field($_POST['billing_cellphone']);
      $billing_birthdate = sanitize_text_field($_POST['billing_birthdate']);
      $billing_installment = sanitize_text_field($_POST['billing_installment']);
      $billing_wooccm8 = sanitize_text_field($_POST['billing_wooccm8']);
      $billing_wooccm11 = sanitize_text_field($_POST['billing_wooccm11']);
      $billing_wooccm10 = sanitize_text_field($_POST['billing_wooccm10']);
      $billing_wooccm12 = sanitize_text_field($_POST['billing_wooccm12']);
      $billing_phone = sanitize_text_field($_POST['billing_phone']);

      $cartItems = WC()->cart->get_cart();
      $description = [];
      $items = [];

      foreach($cartItems as $item) {
        array_push($description, $item['quantity']."x ".$item['data']->get_title());
        array_push($items, [
          "product" => $item['data']->get_name(),
          "price" => $item['data']->get_price(),
          "detail" => $item['data']->get_sku(),
          "quantity" => $item['quantity'],
          "category" => $this->getProductCategoriesByIDs($item['data']->get_category_ids())
        ]);
      }

      $order = wc_get_order($order_id);
      $amount = $order->get_total();
      $costumerId = $order->get_user_id();
      $orderId = $order->get_order_number();
      $cpf = !empty($billing_cpf) ? $billing_cpf : $billing_wooccm8;
      $income = !empty($billing_income) ? $billing_income : "1500,00";
      $mainAddress = !empty($billing_address_1) ? $billing_address_1 : $billing_wooccm11;

      $billing_address = [
        'street' => $mainAddress,
        'number' => !empty($billing_number) ? $billing_number : $maybeIsANumberFromAddress,
        'complement' => !empty($billing_address_2) ? $billing_address_2 : $billing_wooccm10,
        'neighborhood' => !empty($billing_neighborhood) ? $billing_neighborhood : $billing_wooccm12,
        'city' => $order->get_billing_city(),
        'state' => $order->get_billing_state(),
        'cep' => $order->get_billing_postcode()
      ];

      $shipping_address = $billing_address;
      $callback = home_url("/wc-api/{$this->id}");
      $costumerName = $order->get_billing_first_name()." ".$order->get_billing_last_name();
      $costumerEmail = $order->get_billing_email();
      $costumerPhone = !empty($billing_cellphone) ? $billing_cellphone : $billing_phone;
      $birthdate = !empty($billing_birthdate) ? $billing_birthdate : '01-01-1900';

      $customer = [
        "full_name" => $costumerName,
        "cpf" => $cpf,
        "income"=> number_format(str_replace(',', '.', str_replace('.', '', $income)), 2, ".", ""),
        "cellphone" => $costumerPhone,
        "email" => $costumerEmail,
        "birthdate" => date('Y-m-d', strtotime(str_replace('/', '-', $birthdate))),
        "customer_address" => $billing_address
      ];

      //Montando array com os dados da requisição
      $data = [
        "order_ref" => $this->orderEntropyConcat((string)$orderId),
        "customer" => $customer,
        "delivery_address" => $shipping_address,
        "total_amount" => $amount,
        "installment" => !empty($billing_installment) ? $billing_installment : 3,
        "description" => implode('; ', $description),
        "callback" => $callback,
        "return_url" => $this->return_url,
        "channel" => "woocommerce",
        "items" => $items
      ];

      $virtusProposal = new VirtusPayGateway\Fetch($this->authToken);
      $virtusProposal->post($this->remoteApiUrl.'/v1/order', $data);
      $proposal = $virtusProposal->response();

      if(isset($proposal->detail)) wc_add_notice($proposal->detail, 'error');
      else if(!isset($proposal->transaction)) {
        $grandpa = (array)$proposal;
        foreach($grandpa as $grandpaHead => $grandpaBody) {
          $notice = '';

          if(is_object($grandpaBody)) $grandpaBody = (array)$grandpaBody;
          if(!is_array($grandpaBody)) $notice = "({$grandpaHead}) {$grandpaBody}";
          else {
            foreach($grandpaBody as $fatherHead => $fatherBody) {
              if(is_object($fatherBody)) $fatherBody = (array)$fatherBody;
              if(!is_array($fatherBody)) $notice = "({$grandpaHead})[{$fatherHead}] {$fatherBody}";
              else {
                foreach($fatherBody as $children) $notice = "({$grandpaHead})[{$fatherHead}] {$children}";
              }
            }
          }

          if(!empty($notice)) {
            wc_add_notice($notice, 'error');
            $notice = '';
          }
        }
      }
      else {
        update_post_meta($order->get_id(), 'virtusPayOrderTransaction', $proposal->transaction);
        $order->add_order_note('Pedido enviado para checkout VirtusPay.');

        $txLink = $this->remoteApiUrl.'/salesman/order/'.$proposal->transaction;
        $order->add_order_note('Proposta disponível para consulta em: <a target="_blank" href='.$txLink.'>'.$txLink.'</a>');

        $this->wc->cart->empty_cart();
        $order->reduce_order_stock();

        //Redirect para nosso checkout
        return [
          'result' => 'success',
          'redirect' => str_replace('/api', '', $this->remoteApiUrl)."/taker/order/{$proposal->transaction}/accept"
        ];
      }
    }

    function custom_woocommerce_billing_fields(array $fields): array {
			$customer = WC()->session->get('customer');
      $data = WC()->session->get('custom_data');

      $fields['billing_cpf']['class'] = [
        'form-row-wide',
        'person-type-field',
        'cpf'
      ];

      $fields['billing_neighborhood']['required']	= true;
      $fields['billing_cellphone']['required']	= true;

      return $fields;
		}
  }
}
