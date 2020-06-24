<?php
namespace VirtusPayGateway;
class Helpers {
  public static function debug(array $data, bool $die = false): void {
    $template = '<pre>'.print_r($data, true).'</pre>';
    if($die) die($template);
    echo $template.PHP_EOL;
  }

  public static function cpf(string $cpf): string {
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);

    if(strlen($cpf) != 11) return '';
    if(preg_match('/(\d)\1{10}/', $cpf)) return '';

    for ($t = 9; $t < 11; $t++) {
      for ($d = 0, $c = 0; $c < $t; $c++) {
        $d += $cpf[$c] * (($t + 1) - $c);
      }

      $d = ((10 * $d) % 11) % 10;
      if ($cpf[$c] != $d) return '';
    }

    return $cpf;
  }

  private function wpOptions(): array {
    $options = get_option('virtuspayvirtuspay_settings', true);
    return (array)$options;
  }

  public function option(string $key): string {
    $options = $this->wpOptions();
    if(empty($options)) return '';

    if(!in_array($key, array_keys($options))) return '';
    return !is_null($options[$key]) ? $options[$key] : '';
  }

  public function isTestMode(): bool {
    return 'yes' === $this->option('testmode');
  }

  public function virtusEndpoint(string $path = ''): string {
    $base = $this->isTestMode() ? virtuspay_TESTURL : virtuspay_PRODURL;
    return $base.(!empty($path) ? '/'.ltrim($path, '/') : false);
  }

  public function getToken(): string {
    $prefix = $this->isTestMode() ? 'test_' : '';
    return $this->option("${prefix}auth_token");
  }
}
