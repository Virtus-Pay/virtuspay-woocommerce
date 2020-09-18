<?php
namespace VirtusPayGateway;
class Fetch {
  private $sentHeaders = [];
  private $response;

  public function __construct(string $token = '') {
    $this->sentHeaders['Content-Type'] = 'application/json';
    if(strlen($token)) $this->sentHeaders['Authorization'] = 'Token '.$token;
  }

  public function get(string $url): void {
    $request = wp_remote_get($url, [
      'headers' => $this->sentHeaders
    ]);

    if(is_wp_error($request)) {
      $this->response = ['error' => $request->get_error_message()];
    }
    else $this->response = $request['body'];
  }

  public function post(string $url, array $data): void {
    $payload = json_encode($data);
    $this->sentHeaders['Content-Length'] = strlen((string)$payload);
    $request = wp_remote_post($url, [
      'method' => 'POST',
      'data_format' => 'body',
      'headers' => $this->sentHeaders,
      'body' => $payload
    ]);

    if(is_wp_error($request)) {
      $this->response = ['error' => $request->get_error_message()];
    }
    else $this->response = $request['body'];
  }

  public function response() {
    return json_decode($this->response);
  }
}
