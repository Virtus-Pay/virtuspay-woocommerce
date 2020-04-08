<?php
class cpf {
  private $document;

  public function __construct(string $document) {
    $this->document = $document;

    $documentNumber = $this->numbers();
    if(!is_null($documentNumber)) {
      if($this->validate()) return $documentNumber;
    }

    return null;
  }

  private function numbers(): string {
    $onlyNumbers = preg_replace('/[^0-9]/is', '', $this->document);
    if(strlen($onlyNumbers) !== 11) return null;

    return $onlyNumbers;
  }

  private function validate(): bool {
    for ($t = 9; $t < 11; $t++) {
      for ($d = 0, $c = 0; $c < $t; $c++) {
        $d += $this->document{$c} * (($t + 1) - $c);
      }

      $d = ((10 * $d) % 11) % 10;
      if ($this->document{$c} != $d) return false;
    }

    return true;
  }
}
