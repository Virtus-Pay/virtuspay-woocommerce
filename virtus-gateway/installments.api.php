<?php
require_once __DIR__.'/settings.php';
require_once __DIR__.'/fetch.class.php';

add_action('rest_api_init', 'virtusInstallmentsApiRegister');
function virtusInstallmentsApiRegister() {
  register_rest_route(VIRTUSPAYMENTID.'/'.VERSION, '/installments', [
    'methods' => 'POST',
    'callback' => 'virtusInstallmentsEndpoint',
  ]);
}

function virtusInstallmentsEndpoint() {
  $virtusSettings = get_option(VIRTUSPAYMENTID.VIRTUSPAYMENTID.'_settings');

  if(is_array($virtusSettings)) {
    extract($virtusSettings);

    if($enabled !== 'yes') return;

    $isTestMode = 'yes' === $testmode;
    $authToken = $isTestMode ? $test_auth_token : $auth_token;
    $endpoint = $isTestMode ? TESTURL : PRODURL;

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

    $request = new Fetch($authToken);
    $request->post($endpoint.'/v2/installments', $_POST);
    $response = $request->response();

    $installments = array_map('virtusInstallmentsNumberFormat', $response->installments);
    $response->installments = $installments;

    return $response;
  }
}

function virtusInstallmentsNumberFormat($item) {
  $item->total = number_format($item->total, 2, '.', ' ');
  $item->entrada = number_format($item->entrada, 2, '.', ' ');
  $item->restante = number_format($item->restante, 2, '.', ' ');
  $item->parcela = number_format($item->parcela, 2, '.', ' ');

  return $item;
}
