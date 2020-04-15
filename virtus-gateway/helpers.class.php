<?php
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

  public static function custom_woocommerce_billing_fields(array $fields = []): array {
    if(isset($fields['billing_company'])) unset($fields['billing_company']);

    $customer = WC()->session->get('customer');

    $fields['billing_cpf'] = [
      'label' => 'CPF',
      'placeholder' => '000.000.000-00',
      'required' => true,
      'type' => 'text',
      'priority' => 20,
      'class' => [
        'form-row-first',
      ]
    ];

    $fields['billing_birthdate'] = [
      'label' => 'Data de Nascimento',
      'placeholder' => '00/00/0000',
      'clear' => false,
      'required' => true,
      'type' => 'text',
      'priority' => 20,
      'class' => [
        'form-row-last'
      ]
    ];

    $fields['billing_email']['priority'] = 30;
    $fields['billing_email']['type'] = 'email';
    $fields['billing_email']['class'] = [
      'form-row-first'
    ];

    $fields['billing_phone']['placeholder'] = '(00) 0-0000-0000';
    $fields['billing_phone']['priority'] = 30;
    $fields['billing_phone']['class'] = [
      'form-row-last'
    ];

    $fields['billing_postcode']['priority'] = 40;
    $fields['billing_postcode']['class'] = [
      'form-row-last'
    ];

    $fields['billing_income'] = [
      'label' => 'Renda',
      'required' => true,
      'type' => 'number',
      'clear' => true,
      'priority' => 40,
      'class' => [
        'form-row-first',
        'income-field'
      ]
    ];

    $fields['billing_number'] = [
      'label' => 'Número',
      'required' => true,
      'clear' => true,
      'priority' => 50,
      'class' => [
        'form-row-first',
        'address-field'
      ]
    ];

    $fields['billing_address_2']['label'] = 'Complemento';
    $fields['billing_address_2']['priority'] = 50;
    $fields['billing_address_2']['class'] = [
      'form-row-last'
    ];

    $fields['billing_neighborhood'] = [
      'label' => 'Bairro',
      'required' => true,
      'readonly' => true,
      'priority' => 60,
      'class' => [
        'form-row-first'
      ]
    ];

    $fields['billing_city']['priority'] = 60;
    $fields['billing_city']['class'] = [
      'form-row-last'
    ];

    $fields['billing_state']['priority'] = 70;
    $fields['billing_state']['type'] = 'text';
    $fields['billing_state']['readonly'] = true;
    $fields['billing_state']['class'] = [
      'form-row-first'
    ];

    $fields['billing_country']['priority'] = 70;
    $fields['billing_country']['type'] = 'text';
    $fields['billing_country']['readonly'] = true;
    $fields['billing_country']['class'] = [
      'form-row-last'
    ];

    if(isset($customer['phone']) && !empty($customer['phone']))
      $fields['billing_phone']['default'] = $customer['phone'];

    if(isset($customer['email']) && !empty($customer['email']))
      $fields['billing_email']['default'] = $customer['email'];

    if(isset($customer['first_name']) && !empty($customer['first_name']))
       $fields['billing_first_name']['default'] = $customer['first_name'];

    if(isset($customer['last_name']) && !empty($customer['last_name']))
       $fields['billing_last_name']['default'] = $customer['last_name'];

    if(isset($customer['income']) && !empty($customer['income']))
      $fields['billing_income']['default'] = $customer['income'];

    if(isset($customer['postcode']) && !empty($customer['postcode']))
      $fields['billing_postcode']['default'] = $customer['postcode'];

    if(isset($customer['address']) && !empty($customer['address']))
       $fields['billing_address_1']['default'] = $customer['address'];

    if(isset($customer['neighborhood']) && !empty($customer['neighborhood']))
      $fields['billing_neighborhood']['default'] = $customer['neighborhood'];

    if(isset($customer['city']) && !empty($customer['city']))
      $fields['billing_city']['default'] = $customer['city'];

    if(isset($customer['state']) && !empty($customer['state']))
       $fields['billing_state']['default'] = $customer['state'];

    if(isset($data['billing_cpf']) && !empty($data['billing_cpf']))
       $fields['billing_cpf']['default'] = $data['billing_cpf'];

    if(isset($data['billing_birthdate']) && !empty($data['billing_birthdate']))
       $fields['billing_birthdate']['default'] = $data['billing_birthdate'];

    if(isset($data['billing_number']) && !empty($data['billing_number']))
       $fields['billing_number']['default'] = $data['billing_number'];

    return $fields;
  }
}
