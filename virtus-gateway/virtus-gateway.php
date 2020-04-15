<?php
/*
Plugin Name: WooCommerce VirtusPay
Plugin URI: https://usevirtus.com.br
Description: Pagamentos para o WooCommerce através da VirtusPay.
Version: 1.1
Author: VirtusPay Dev Team
Author URI: https://documenter.getpostman.com/view/215460/SVSPnmLs?version=latest
*/
require_once __DIR__.'/settings.php';
require_once __DIR__.'/helpers.class.php';
require_once __DIR__.'/fetch.class.php';

add_action('plugins_loaded', 'virtusPaymentGateInit', 0);
function virtusPaymentGateInit(): void {
  add_filter('woocommerce_payment_gateways', 'addVirtusPaymentMethod');
  function addVirtusPaymentMethod(array $paymentMethods): array {
    array_push($paymentMethods, 'WooCommerceVirtusPayment');
    return $paymentMethods;
  }

  add_filter('plugin_action_links_virtus-gateway/virtus-gateway.php', 'addConfigLinkInPluginList');
  function addConfigLinkInPluginList(array $links): array {
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

  add_filter('woocommerce_payment_gateways', 'addGatewayNameForWooCommerce');
	function addGatewayNameForWooCommerce(array $methods): array {
		array_push($methods, TITLE);
		return $methods;
	}

  if(!class_exists('WC_Payment_Gateway'))
    throw new \Exception('É necessária a instalação do Woocommerce', 0);

  class WooCommerceVirtusPayment extends WC_Payment_Gateway {
    public $id = VIRTUSPAYMENTID;
    public $plugin_id = VIRTUSPAYMENTID;
    public $icon = ICON;
    public $has_fields = true;
    public $supports = [];

    // config woocommerce/settings[/...]
    public $method_title = TITLE;
    public $method_description = DESCRIPTION;
    public $title = TITLE;
    public $description = DESCRIPTION;

    // defaults
    public $enabled = 'no';
    private $return_url = '';
    private $testmode = 'no';
    private $isTestMode = false;
    private $authTestToken;
    private $authProdToken;
    private $authToken;
    private $remoteApiUrl;

    public function __construct() {
      global $woocommerce;
      $this->wc = $woocommerce;

      $this->supports = [
        'products'
      ];

      $this->init_form_fields();
      $this->init_settings();

      $this->enabled = $this->get_option('enabled');
      $this->return_url = (strlen($this->get_option('return_url')) > 0)? $this->get_option('return_url') : wc_get_checkout_url();
      $this->testmode = $this->get_option('testmode');
      $this->isTestMode = 'yes' === $this->testmode;
      $this->remoteApiUrl = $this->isTestMode ? TESTURL : PRODURL;

      $this->authTestToken = $this->get_option('test_auth_token');
      $this->authProdToken = $this->get_option('auth_token');
      $this->authToken = $this->isTestMode ? $this->authTestToken : $this->authProdToken;

      // Begin APIs Endpoints
      add_action(
        "woocommerce_api_{$this->id}",
        [$this, 'virtusCallback']
      );

      add_action(
        "woocommerce_api_{$this->id}_installments",
        [$this, 'virtusGetInstallments']
      );
      // End APIs Endpoints

      register_activation_hook(__FILE__, [$this, 'child_plugin_has_parent_plugin']);
    }

    public function child_plugin_has_parent_plugin() {
      $plugin_slug = 'woocommerce-extra-checkout-fields-for-brazil';
			if(
        is_admin() &&
        current_user_can('activate_plugins') &&
        !is_plugin_active($plugin_slug.'/'.$plugin_slug.'.php')
      ) {
        if(current_user_can('install_plugins')) {
        	$url = wp_nonce_url(
            self_admin_url(
              'update.php?action=install-plugin&plugin='.$plugin_slug
            ),
            'install-plugin_'.$plugin_slug
          );
        }
        else $url = 'http://wordpress.org/plugins/'.$plugin_slug;

        echo '
          <div class="error">
          	<p>
              <strong>'.$this->title.' foi desabilitado</strong>: <br />
              Esta extensão para pagamentos depende de um plugin para gerenciamento de campos para pagamentos que pode ser encontrado <a href="'.$url.'">aqui</a>.
            </p>
            <p>
              Após a instalação desta dependência, tente habilitar o plugin '.$this->title.' novamente.
            </p>
          </div>
        ';

				deactivate_plugins(plugin_basename( __FILE__ ));

        if(isset($_GET['activate'])) unset($_GET['activate']);
			}
		}

    public function virtusGetInstallments(): string {
      return '';
    }

    public function init_form_fields(): void {
      $this->form_fields = [
        'enabled' => [
          'title' => 'Ativação',
          'label' => 'Ativar '.TITLE.'?',
          'type'  => 'checkbox',
          'description' => 'A ativação ou desativação de pagamentos influenciará na tomada de decisão do seu comprador.',
          'default' => $this->enabled,
          'name' => 'enabled',
          'id' => 'enabled',
          'desc_tip' => true
        ],
        'testmode' => [
          'title' => 'Modo de testes',
          'label' => 'Ativar '.TITLE.' em modo de testes?',
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
          'title' => 'TOKEN para ambientes de testes / homologação',
          'description' => 'Autenticação de acesso para a API de dados em ambiente de testes / homologação.',
          'type' => 'text',
          'required' =>  true,
          'default' => $this->authTestToken,
          'name' => 'test_auth_token',
          'id' => 'test_auth_token',
          'desc_tip' => true
        ],
        'auth_token' => [
          'title' => 'TOKEN para ambientes de produção / publicação',
          'description' => 'Autenticação de acesso para a API de dados em ambiente de produção / publicação.',
          'type' => 'text',
          'default' => $this->authProdToken,
          'name' => 'auth_token',
          'id' => 'auth_token',
          'desc_tip' => true
        ]
      ];
    }

    private function orderEntropyConcat(string $orderID): string {
      return $orderID.'.'.time();
    }

    private function orderEntropyReverse(string $entropy): string {
      return strstr($entropy, '.', true);
    }

    public function virtusCallback() {
        // debug($_POST, true);
        extract($_POST);

        $virtusProposal = new Fetch($this->authToken);
        $virtusProposal->get($this->remoteApiUrl."/v1/order/{$transaction}");
        $proposal = $virtusProposal->response();

        if(isset($proposal->detail)) throw new \Exception($proposal->detail, true);
        if(is_array($proposal) && isset($proposal[0]['status']) && $proposal[0]['status'] !== "E") {
          throw new \Exception($proposal, true);
        }

        $orderId = $this->orderEntropyReverse($proposal[0]['order_ref']);

        $order = wc_get_order($orderId);
        $order->add_order_note('Parcela de entrada paga.', true);
        $order->payment_complete();

        update_option('webhook_debug', $_GET);
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
      $cartItems = WC()->cart->get_cart();
      $description = [];
      $items = [];

      foreach($cartItems as $item) {
        array_push($description, $item['quantity']."x".$item['data']->get_title());
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
      $cpf = isset($_POST['billing_cpf']) ? $_POST['billing_cpf'] : $_POST['billing_wooccm8'];

      $mainAddress = isset($_POST['billing_address_1'])? $_POST['billing_address_1'] : $_POST['billing_wooccm11'];

      preg_match_all('/([^,])([0-9a-zA-Z\-\ \/]+)$/', $mainAddress, $addressEmulateMatch);
      $tryReadNumber = trim(array_shift(end($addressEmulateMatch)));
      $maybeIsANumberFromAddress = !empty($tryReadNumber) ? $tryReadNumber : 'S/N';

      $billing_address = [
        'street' => str_replace($maybeIsANumberFromAddress, '', str_replace(', ', '', $mainAddress)),
        'number' => isset($_POST['billing_number']) ? $_POST['billing_number'] : $maybeIsANumberFromAddress,
        'complement' => isset($_POST['billing_address_2']) ? $_POST['billing_address_2'] : $_POST['billing_wooccm10'],
        'neighborhood' => isset($_POST['billing_neighborhood']) ? $_POST['billing_neighborhood'] : $_POST['billing_wooccm12'],
        'city' => $order->get_billing_city(),
        'state' => $order->get_billing_state(),
        'cep' => $order->get_billing_postcode()
      ];

      $billingCep = new Cep();
      $billingCep->query($billing_address['cep']);
      $billingCepData = $billingCep->response();

      $billing_address['neighborhood'] = $billingCepData->bairro;
      $billing_address['city'] = $billingCepData->localidade;
      $billing_address['state'] = $billingCepData->uf;

      $shipping_address = $billing_address;

      //Define a URL de callback com base na url sendo acessada atualmente
      //ToDo:: Checar necessidade de mudança
      $callback = home_url("/wc-api/{$this->id}");

      $costumerName = $order->get_billing_first_name()." ".$order->get_billing_last_name();
      $costumerEmail = $order->get_billing_email();
      $costumerPhone = isset($_POST['billing_phone']) ? $_POST['billing_phone'] : $_POST['billing_wooccm13'];

      $customer = [
        "full_name" => $costumerName,
        "cpf" => $cpf,
        "income"=> $amount,
        "cellphone" => $costumerPhone,
        "email" => $costumerEmail,
        "birthdate" => isset($_POST['birthdate']) ? $_POST['birthdate'] : '1900-01-01',
        "customer_address" => $billing_address
      ];

      //Montando array com os dados da requisição
      $data = [
        "order_ref" => $this->orderEntropyConcat((string)$orderId),
        "customer" => $customer,
        "delivery_address" => $shipping_address,
        "total_amount" => $amount,
        "installment" => 3,
        "description" => implode('; ', $description),
        "callback" => $callback,
        "return_url" => $this->return_url,
        "channel" => "woocommerce",
        "items" => $items
      ];

      $virtusProposal = new Fetch($this->authToken);
      $virtusProposal->post($this->remoteApiUrl.'/v1/order', $data);
      $proposal = $virtusProposal->response();

      if(isset($proposal->detail)) {
        wc_add_notice($proposal->detail, 'error');
      }
      else {
        //Adicionando notas para exibição no painel da order
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
  }
}
