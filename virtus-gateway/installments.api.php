<?php
require_once __DIR__.'/settings.php';
require_once __DIR__.'/fetch.class.php';

add_action('rest_api_init', 'VirtusPayGatewayInstallmentsApiRegister');
function VirtusPayGatewayInstallmentsApiRegister() {
  register_rest_route(virtuspay_VIRTUSPAYMENTID.'/'.virtuspay_VERSION, '/installments', [
    'methods' => 'POST',
    'callback' => 'VirtusPayInstallmentsWpEndpoint',
  ]);
}

function VirtusPayInstallmentsWpEndpoint() {
  $virtusSettings = get_option(virtuspay_VIRTUSPAYMENTID.virtuspay_VIRTUSPAYMENTID.'_settings');

  if(is_array($virtusSettings)) {
    extract($virtusSettings);

    if($enabled !== 'yes') return;

    $isTestMode = 'yes' === $testmode;
    $authToken = $isTestMode ? $test_auth_token : $auth_token;
    $endpoint = $isTestMode ? virtuspay_TESTURL : virtuspay_PRODURL;

    if(empty($authToken)) {
      return new WP_Error(
        'virtus_no_auth_token_config',
        'As configurações do plugin VirtusPay devem ser verificadas.',
        ['status' => 400]
      );
    }

    if(empty($_POST)) {
      return new WP_Error(
        'virtus_installments_needs_amount_and_cpf',
        'É necessário passar os parâmetros com o valor total e CPF do cliente.',
        ['status' => 406]
      );
    }

    $request = new VirtusPayGateway\Fetch($authToken);
    $request->post($endpoint.'/v2/installments', $_POST);
    $response = $request->response();

    if(isset($response->error)) {
      return new WP_Error(
        'virtus_installments_endpoint_request',
        $response->error,
        ['status' => 400]
      );
    }

    $installments = array_map('VirtusPayGatewayInstallmentsNumberFormat', $response->installments);
    $response->installments = $installments;

    return $response;
  }
}

function VirtusPayGatewayInstallmentsNumberFormat($item) {
  $item->total = number_format($item->total, 2, ',', ' ');
  $item->entrada = number_format($item->entrada, 2, ',', ' ');
  $item->restante = number_format($item->restante, 2, ',', ' ');
  $item->parcela = number_format($item->parcela, 2, ',', ' ');

  return $item;
}
