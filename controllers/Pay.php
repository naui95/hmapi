<?php
(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * HMAPI/Auth
 * 
 * Handels the authentication for the HMAPI module.
 * All calls towards the HMAPI endpoint must be authenticated
 * to authenticate call the following endpoint
 * Endpoint: /hmapi/auth/start/<user>/<pass>
 */
class Pay extends Base_Controller
{
    private string $endpoint_secret = "YOUR WEBHOOK SECRET";
    private string $stripe_api_key = "YOUR STRIPE API KEY";
    
    function stripe($invoice_id)
    {
        require_once(APPPATH.'modules/hmapi/library/Stripe/init.php');
        \Stripe\Stripe::setApiKey($this->stripe_api_key);
        //header('Content-Type: application/json');

        $invoice_information = $this->_payment_information($invoice_id);

        if($invoice_information['disable_form'])
        {
            redirect('guest/view/invoice/' . $invoice_id);
        }

        $YOUR_DOMAIN = getenv('IP_URL');
        $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
            'currency' => 'chf',
            'unit_amount' => $invoice_information['invoice']->invoice_balance*100,
            'product_data' => [
                'name' => 'Invoice '.$invoice_information['invoice']->invoice_number,
            ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'client_reference_id'=>$invoice_id,
        'customer_email' => $invoice_information['invoice']->client_email,
        'locale'=>'auto',
        'success_url' => $YOUR_DOMAIN . '/hmapi/pay/success/'.$invoice_id,
        'cancel_url' => $YOUR_DOMAIN . '/hmapi/pay/cancel/'.$invoice_id,
        ]);
        $checkout_session->id;
        // print_r($invoice_information);
        // exit;
        $this->load->view('hmapi/stripe_checkout',['stripe_session_id'=>$checkout_session->id]);
        //echo json_encode(['id' => $checkout_session->id]);
    }

    function _payment_information($invoice_url_key)
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('payment_methods/mdl_payment_methods');
        $disable_form = false;

        // Check if the invoice exists and is billable
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)
            ->get()->row();

        if (!$invoice) {
            show_404();
        }

        // Check if the invoice is payable
        if ($invoice->invoice_balance == 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            $disable_form = true;
        }

        $view_data = array(
            'disable_form' => $disable_form,
            'invoice' => $invoice,
        );
        return $view_data;
    }

    function cancel($invoice_id)
    {
        //record the cancelation
        $this->_record_transaction($invoice_id,'Transaction canceled by the user','',false,true);

        $this->session->set_flashdata('alert_info', trans('online_payment_payment_cancelled'));
        redirect('guest/view/invoice/' . $invoice_id);
    }

    function success($invoice_id)
    {
        $invoice = $this->_payment_information($invoice_id)['invoice'];
        $payment_success_msg = sprintf(trans('online_payment_payment_successful'), $invoice->invoice_number);

        redirect('guest/view/invoice/' . $invoice_id);
    }

    function stripe_validate_payment()
    {
        require_once(APPPATH.'modules/hmapi/library/Stripe/init.php');

        \Stripe\Stripe::setApiKey($this->stripe_api_key);

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $this->endpoint_secret
        );
        } catch(\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        http_response_code(400);
        exit();
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
        $session = $event->data->object;
        //register payment in InvoicePlane
        $this->_set_paid_invoice($session->client_reference_id,$session->payment_intent);
        }

        http_response_code(200);
    }

    /**
     * Sets the current invoice as payed
     *
     * @param string $invoice_id the id of the invoice
     * @return string the invoice number
     */
    function _set_paid_invoice($invoice_id,$payment_note)
    {
        // Set invoice to paid
        $this->load->database();
        $this->load->model('payments/mdl_payments');

        $invoice = $this->_payment_information($invoice_id)['invoice'];

        $db_array = [
            'invoice_id' => $invoice->invoice_id,
            'payment_date' => date('Y-m-d'),
            'payment_amount' => $invoice->invoice_balance,
            'payment_method_id' => $invoice->payment_method,
            'payment_note' => $payment_note,
        ];

        $this->mdl_payments->save(null, $db_array);
        $this->_record_transaction($invoice_id,'Payment successful!','Stripe',true,false);
    }

    function _record_transaction($invoice_id,$message,$merchant_reference,$payment_success=false,$canceled=false)
    {
        $this->load->database();
        $invoice = $this->_payment_information($invoice_id)['invoice'];

        $db_array = [
            'invoice_id' => $invoice->invoice_id,
            'merchant_response_successful' => $payment_success,
            'merchant_response_date' => date('Y-m-d'),
            'merchant_response_driver' => 'Stripe',
            'merchant_response' => $message,
            'merchant_response_reference' => $canceled ? '' : $merchant_reference,
        ];

        $this->db->insert('ip_merchant_responses', $db_array);
    }
}