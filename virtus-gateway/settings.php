<?php
define('virtuspay_VERSION', 'v1.0.3');
define('virtuspay_PLUGINURL', site_url(str_replace(ABSPATH, null, __DIR__)));
define('virtuspay_ICON', virtuspay_PLUGINURL.'/virtus.png');
define('virtuspay_TITLE', 'VirtusPay Boleto Parcelado');
define('virtuspay_DESCRIPTION', 'Pagamento parcelado no boleto com a VirtusPay');
define('virtuspay_VIRTUSPAYMENTID', 'virtuspay');
define('virtuspay_TESTURL', 'https://hml.usevirtus.com.br/api');
define('virtuspay_PRODURL', 'https://www.usevirtus.com.br/api');
