<?php
class Cep extends Fetch {
  private $urlPattern = 'https://viacep.com.br/ws/%d/json';

  public function __construct() {
    parent::__construct();
  }

  public function query(string $cep): void {
    $viaCepUrl = sprintf(
      $this->urlPattern,
      preg_replace('/[^0-9]/', '', $cep)
    );

    $this->get($viaCepUrl);
  }
}
