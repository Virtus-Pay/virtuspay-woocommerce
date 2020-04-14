<?php
class Helpers {
  public static function debug(mixed $data, bool $die = false): void {
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
        $d += $cpf{$c} * (($t + 1) - $c);
      }

      $d = ((10 * $d) % 11) % 10;
      if ($cpf{$c} != $d) return '';
    }

    return $cpf;
  }
}
