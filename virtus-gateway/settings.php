<?php
define('PLUGINURL', site_url(str_replace(ABSPATH, null, __DIR__)), true);
define('ICON', PLUGINURL.'/virtus.png', true);
define('TITLE', 'VirtusPay Pagamentos', true);
define('DESCRIPTION', 'Pagamento parcelado no boleto', true);
define('VIRTUSPAYMENTID', 'virtuspay', true);


$virtusEnvironment = function() {
  extract($_SERVER);
  if(
      $HTTP_HOST === 'localhost ' || 
      filter_var($HTTP_HOST, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
      filter_var($HTTP_HOST, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
    ) return "https://hml.usevirtus.com.br/api";
  
  return "https://usevirtus.com.br/api";
};
define('VIRTUSENV', $virtusEnvironment(), true);