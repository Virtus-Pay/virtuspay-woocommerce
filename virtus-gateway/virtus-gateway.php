<?php
/*
Plugin Name: WooCommerce VirtusPay
Plugin URI: https://usevirtus.com.br
Description: Pagamentos para o WooCommerce através da VirtusPay.
Version: 1.0
Author: VirtusPay Dev Team
Author URI: https://documenter.getpostman.com/view/215460/SVSPnmLs?version=latest
*/
require_once __DIR__.'/settings.php';
require_once __DIR__.'/cpf.class.php';

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

  if(!class_exists('WC_Payment_Gateway')) return;

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
    private $isTestMode = false;
    private $authTestToken;
    private $authProdToken;
    private $authToken;

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
      $this->isTestMode = 'yes' === $this->get_option('testmode');

      $this->authTestToken = $this->get_option('test_auth_token');
      $this->authProdToken = $this->get_option('auth_token');
      $this->auth_token = $this->isTestMode ? $this->authTestToken : $this->authProdToken;

      print_r('<pre>'.print_r([
        'enabled' => $this->enabled,
        'return_url' => $this->return_url,
        'isTestMode' => $this->isTestMode,
        'authTestToken' => $this->authTestToken,
        'authProdToken' => $this->authProdToken,
        'enabled_option' => $this->get_option('enabled'),
        'return_url_option' => $this->get_option('return_url'),
        'isTestMode_option' => $this->get_option('testmode'),
        'authTestToken_option' => $this->get_option('test_auth_token'),
        'authProdToken_option' => $this->get_option('auth_token'),
      ], true).'</pre>');

      add_action(
        'woocommerce_update_options_payment_gateways_'.$this->id,
        [$this, 'process_admin_options']
      );

      add_action(
        'wp_enqueue_scripts',
        [$this, 'payment_scripts']
      );

      add_action(
        "woocommerce_api_{$this->id}",
        [$this, 'virtusCallback']
      );
    }

    public function process_admin_options(): bool {
      // if(isset($_POST) && !empty($_POST)) {
        // die(print_r($_POST, true));
        // extract($_POST);

        // if(empty($xpto)) {
        //   WC_Admin_Settings::add_error( 'Error: Please fill required fields' );
        //   return false;
        // }
      // }

      return true;
    }

    public function init_form_fields(): void {
      $this->form_fields = [
        'enabled' => [
          'title' => 'Ativação',
          'label' => 'Ativar '.TITLE.'?',
          'type'  => 'checkbox',
          'description' => 'A ativação ou desativação de pagamentos influenciará na tomada de decisão do seu comprador.',
          'default' => 'no',
          'desc_tip' => true
        ],
        'testmode' => [
          'title' => 'Modo de testes',
          'label' => 'Ativar '.TITLE.' em modo de testes?',
          'type' => 'checkbox',
          'description' => 'Ativar o modo de testes permite que você possa homologar os seus pagamentos fora do seu ambiente de produção.',
          'default' => $this->isTestMode ? $this->isTestMode : 'yes',
          'desc_tip' => true
        ],
        'return_url' => [
          'title' => 'URL de retorno',
          'type' => 'text',
          'description' => 'URL para qual devemos redirecionar o usuário após a validação do seu pagamento.',
          'required' =>  true,
          'default' => $this->return_url,
          'desc_tip' => true
        ],
        'test_auth_token' => [
          'title' => 'TOKEN para ambientes de testes / homologação',
          'description' => 'Autenticação de acesso para a API de dados em ambiente de testes / homologação.',
          'type' => 'text',
          'required' =>  true,
          'default' => $this->authTestToken,
          'desc_tip' => true
        ],
        'auth_token' => [
          'title' => 'TOKEN para ambientes de produção / publicação',
          'description' => 'Autenticação de acesso para a API de dados em ambiente de produção / publicação.',
          'type' => 'text',
          'default' => $this->authProdToken,
          'desc_tip' => true
        ]
      ];
    }

    public function payment_fields(): void {
      if($this->description) {
        if ($this->isTestMode) {
          $this->description = <<<DESCRIPTION
            <b>!!! {$this->title} EM MODO DE TESTES !!!</b>
          DESCRIPTION;
        }

        echo wpautop(wp_kses_post($this->description));
      }

      $response = <<<FORM
        <div class="woocommerce-billing-fields">
          <div class="form-row form-row-wide">
            <label class="" for="cpf">
              CPF <span class="required">*</span>
            </label>
            <input id="virtusCPF" name="cpf" type="text" autocomplete="off" class="input-text cpf">
          </div>
        </div>
      FORM;

      echo $response;
    }

    public function payment_scripts() {
      wp_enqueue_style($this->id, PLUGINURL.'/virtus.css');
      wp_enqueue_script('virtusMasked', PLUGINURL.'/jquery-mask-plugin/dist/jquery.mask.min.js');

      wp_register_script(
        'virtusGateway',
        plugins_url(
          'virtus-gateway.js',
          __FILE__
        ),
        ['jquery', 'virtusMasked']
      );
      wp_enqueue_script('virtusGateway');
    }

    /*
      * Fields validation, more in Step 5
      */
    public function validate_fields() {
      return;
    }

    public function virtusCallback() {
        //Pegando a transação por parâmetro GET
        $transaction = $_GET['transaction'];

        //Configurando cURL
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
          'Content-Type: application/json',
          'Authorization: Token '.$this->auth_token
        ]);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL,"$env/v1/order/$transaction");
        $response = curl_exec($ch);
        curl_close($ch);
        //Pegando resposta da requisição
        $orderData = json_decode($response, true);
        //Separando status da resposta
        $status = $orderData[0]['status'];

        //Se o status da transação for 'E', atualizo o status do pagamento do pedido para completo e adiciono uma nota visível para o usuário
        if($status !== "E") return print_r($response);

        $orderId = $orderData[0]['order_ref'];
        $order = wc_get_order($orderId);
        $order->add_order_note('Parcela de entrada paga.', true);
        $order->payment_complete();
        update_option('webhook_debug', $_GET);
    }


    public function process_payment($order_id) {
      //Quebra os produtos do pedido em 'nome' e 'quantidade', criando a string de descrição
      foreach ( WC()->cart->get_cart() as $cart_item ) {
        $description = $cart_item['quantity']."x ".$cart_item['data']->get_title().';';
      }

      $order = wc_get_order($order_id);
die('<pre>'.print_r($order, true).'</pre>');
      //Separando os dados em variáveis para montar o JSON a ser enviado na requisição
      $amount = $order->get_total();
      $costumerId = $order->get_user_id();
      $orderId = $order->get_order_number();
      $cpf = !isset($_POST['billing_cpf']) ? $_POST['billing_wooccm8'] : $_POST['billing_cpf'] ;

      $billing_address = [
        'city' => $order->get_billing_city(),
        'neighborhood' => isset($_POST['billing_neighborhood']) ? $_POST['billing_neighborhood'] : $_POST['billing_wooccm12'],
        'street' => isset($_POST['billing_address_1'])? $_POST['billing_address_1'] : $_POST['billing_wooccm11'],
        'number' => isset($_POST['billing_number']) ? $_POST['billing_number'] : $_POST['billing_wooccm9'],
        'complement' => isset($_POST['billing_address_2']) ? $_POST['billing_address_2'] : $_POST['billing_wooccm10'],
        'state' => $order->get_billing_state(),
        'cep' => $order->get_billing_postcode()
      ];

      $shipping_address = [
        'city' => $order->get_shipping_city(),
        'neighborhood' => isset($_POST['shipping_neighborhood']) ? $_POST['shipping_neighborhood'] : $_POST['shipping_wooccm10'],
        'street' => isset($_POST['shipping_address_1']) ? $_POST['shipping_address_1'] : $_POST['billing_wooccm11'],
        'number' => isset($_POST['shipping_number']) ? $_POST['shipping_number'] : $_POST['shipping_wooccm9'],
        'complement' => isset($_POST['shipping_address_2']) ? $_POST['shipping_address_2'] : $_POST['shipping_wooccm8'],
        'state' => $order->get_shipping_state(),
        'cep' => $order->get_shipping_postcode()
      ];

      //Define a URL de callback com base na url sendo acessada atualmente
      //ToDo:: Checar necessidade de mudança
      $callback = "?wc-api={$this->id}";

      $costumerName = $order->get_billing_first_name()." ".$order->get_billing_last_name();
      $costumerEmail = $order->get_billing_email();
      $costumerPhone = isset($_POST['billing_phone']) ? $_POST['billing_phone'] : $_POST['billing_wooccm13'];

      $ch = curl_init();

      //Montando array com os dados da requisição
      $data = [
        "order_ref" => "$orderId",
        "customer" => [
          "full_name" => $costumerName,
          "cpf" => $cpf,
          "income"=>"1000.00",
          "cellphone" => $costumerPhone,
          "email" => $costumerEmail,
          "birthdate" => '1900-01-01',
          "customer_address" => [
            "state" => $billing_address['state'],
            "city" => $billing_address['city'],
            "neighborhood" => $billing_address['neighborhood'],
            "street" => $billing_address['street'],
            "number" => $billing_address['number'],
            "cep" => $billing_address['cep']
          ]
        ],
        "delivery_address" => [
          "state" => $shipping_address['state'],
          "city" => $shipping_address['city'],
          "neighborhood" => $shipping_address['neighborhood'],
          "street" => $shipping_address['street'],
          "number" => $shipping_address['number'],
          "cep" => $shipping_address['cep'],
          "complement" => $shipping_address['complement']
        ],
        "total_amount" => $amount,
        "installment" => 3,
        "description" => $description,
        "callback" => $callback,
        "return_url" => $this->return_url,
        "channel" => "woocommerce"
      ];

      //Convertendo array para json
      $json = json_encode($data);

      //Configurando as options do cURL para a requisição
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Token {$this->auth_token}"
      ]);
      curl_setopt($ch, CURLOPT_URL, VIRTUSENV."/v1/order");
      curl_setopt($ch, CURLOPT_POST, count($data));
      curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

      //Executando requisição e fechando o cURL
      $response = curl_exec($ch);
      curl_close($ch);

      //Pegando a resposta e separando a transação
      $transaction = json_decode($response,true);
      $transaction = $transaction['transaction'];

      //Adicionando notas para exibição no painel da order
      $order->add_order_note('Pedido enviado para checkout VirtusPay.', true);

      $txLink = VIRTUSENV.'/salesman/order/'.$transaction;
      $order->add_order_note(
        'Proposta disponível para consulta em: <a target="_blank" href='.$txLink.'>'.$txLink.'</a>',
        false
      );

      $this->wc->cart->empty_cart();
      $order->reduce_order_stock();

      //Redirect para nosso checkout
      return [
        'result' => 'success',
        'redirect' => "$env/taker/order/$transaction/accept"
      ];
    }
  }
}
