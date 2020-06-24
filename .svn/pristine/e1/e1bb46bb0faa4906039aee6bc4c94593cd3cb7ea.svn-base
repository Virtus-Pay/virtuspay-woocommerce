<?php
// add_action('woocommerce_order_status_pending', 'virtusPaymentOrderPending');
// add_action('woocommerce_order_status_failed', 'virtusPaymentOrderFailed');
// add_action('woocommerce_order_status_on-hold', 'virtusPaymentOrderHold');
// add_action('woocommerce_order_status_processing', 'virtusPaymentOrderProcessing');
// add_action('woocommerce_order_status_completed', 'virtusPaymentOrderCompleted');
// add_action('woocommerce_order_status_refunded', 'virtusPaymentOrderRefunded');
add_action('woocommerce_order_status_cancelled', 'virtusPaymentOrderCancelled');

// function virtusPaymentOrderPending($order_id) {
//   $order = wc_get_order($order_id);
//   if(!$order) return error_log("Não foi encontrado pedido para o ID {$order_id}.");
//
//   $options = virtusPaymentsAdminOptionsForStatuses();
//   return [$options, $order];
// }

// function virtusPaymentOrderFailed($order_id) {
//   $order = wc_get_order($order_id);
//   if(!$order) return error_log("Não foi encontrado pedido para o ID {$order_id}.");
//
//   $options = virtusPaymentsAdminOptionsForStatuses();
//   return [$options, $order];
// }

// function virtusPaymentOrderHold($order_id) {
//   $order = wc_get_order($order_id);
//   if(!$order) return error_log("Não foi encontrado pedido para o ID {$order_id}.");
//
//   $options = virtusPaymentsAdminOptionsForStatuses();
//   return [$options, $order];
// }

// function virtusPaymentOrderProcessing($order_id) {
//   $order = wc_get_order($order_id);
//   if(!$order) return error_log("Não foi encontrado pedido para o ID {$order_id}.");
//
//   $options = virtusPaymentsAdminOptionsForStatuses();
//   return [$options, $order];
// }

// function virtusPaymentOrderCompleted($order_id) {
//   $order = wc_get_order($order_id);
//   if(!$order) return error_log("Não foi encontrado pedido para o ID {$order_id}.");
//
//   $options = virtusPaymentsAdminOptionsForStatuses();
//   return [$options, $order];
// }

// function virtusPaymentOrderRefunded($order_id) {
//   $order = wc_get_order($order_id);
//   if(!$order) return error_log("Não foi encontrado pedido para o ID {$order_id}.");
//
//   $options = virtusPaymentsAdminOptionsForStatuses();
//   return [$options, $order];
// }

function virtusPaymentOrderCancelled($order_id) {
  $order = wc_get_order($order_id);
  if(!$order) return error_log("Não foi encontrado pedido para o ID {$order_id}.");

  $helpers = new VirtusPayGateway\Helpers;
  $transaction = get_post_meta($order->get_id(), 'virtusPayOrderTransaction', true);

  $request = new VirtusPayGateway\Fetch($helpers->getToken());
  $request->post(
    $helpers->virtusEndpoint("/v1/order/{$transaction}/void"),
    [
      'refund_by' => 'ORPAG',
      'reason_cancellation' => 'WooCommerce: Motivo não definido.',
    ]
  );

  return $request->response();
}
