<?php
define('PLUGINURL', site_url(str_replace(ABSPATH, null, __DIR__)), true);
define('ICON', PLUGINURL.'/virtus.png', true);
define('TITLE', 'VirtusPay Boleto Parcelado', true);
define('DESCRIPTION', 'Pagamento parcelado no boleto com a VirtusPay', true);
define('VIRTUSPAYMENTID', 'virtuspay', true);
define('TESTURL', 'https://hml.usevirtus.com.br/api', true);
define('PRODURL', 'https://www.usevirtus.com.br/api', true);
