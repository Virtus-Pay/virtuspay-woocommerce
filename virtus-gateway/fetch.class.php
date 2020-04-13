<?php
if(!function_exists('curl_init')) die('A biblioteca CURL é necessária para o funcionamento do plugin.');
class Fetch {
  private $curl;
  private $sentHeaders = [];
  private $responseHeaders = '';
  private $response;

  public function __construct(string $token = '') {
    $this->sentHeaders = [
      'Content-Type: application/json'
    ];

    if(strlen($token)) array_push($this->sentHeaders, 'Authorization: Token '.$token);

    $this->curl = curl_init();
  }

  public function get(string $url): void {
    curl_setopt($this->curl, CURLOPT_URL, $url);
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->sentHeaders);
    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);

    $this->response = curl_exec($this->curl);
  }

  public function post(string $url, array $data): void {
    $data = json_encode($data);
    array_push($this->sentHeaders, 'Content-Length: '.strlen($data));

    curl_setopt($this->curl, CURLOPT_URL, $url);
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->sentHeaders);
    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);

    $this->response = curl_exec($this->curl);
  }

  public function response() {
    return json_decode(trim($this->response));
  }
}
