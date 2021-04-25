<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * HMAPI the API endpoint for InvoicePlane
 */
class Hmapi extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('hmapi/Mdl_hmapi_query');
    }

    public function index(){
        $this->success_output([
            'message'=>'HELLO'
        ]);
    }

    public function user_get($user_id)
    {
        if(!isset($user_id))
        {
            $this->bad_output([
                "code"=>400,
                "message" => "400 BAD REQUEST"
            ]);
            exit;
        }

        $user_data = $this->Mdl_hmapi_query->clients_select_by_id($user_id);
        $this->success_output([
            $user_data
        ]);
    }

    //TODO: fix, user creation not working
    public function user_put()
    {
        $post_data = $this->input->post();
        
        //validate request
        $target = [
            "client_name",
            "client_surname",
            "client_address_1",
            "client_city",
            "client_phone",
            "email"
        ];

        if(count(array_intersect($post_data, $target)) != count($target)){
            $this->bad_output([
                'code'=> 400,
                'message' => '400 BAD REQUEST'
            ]);
            exit;
        }


        $data = [
            "client_active"=> 1,
            "client_name"=> $post_data['client_name'],
            "client_surname"=> $post_data['client_surname'],
            "client_language"=> "system",
            "client_address_1"=> $post_data['client_address_1'],
            "client_address_2"=> null,
            "client_city"=> $post_data['client_city'],
            "client_state"=> null,
            "client_country"=> "CH",
            "client_phone"=> $post_data['client_phone'],
            "client_fax"=> null,
            "client_mobile"=>null,
            "client_email"=> $post_data['email'],
            "client_web"=> null,
            "client_gender"=>  0,
            "client_birthdate"=> null,
            "client_vat_id"=> null,
            "client_tax_code"=>null,
            "client_date_created"=>date('c')
        ];

        $create_query = $this->Mdl_hmapi_query->clients_create($data);
        $this->success_output($create_query);
    }

    public function invoice_get($client_id)
    {
        $this->load->database();
        $this->load->model('invoices/mdl_invoices');
        $invoices = $this->mdl_invoices->by_client($client_id)->limit(20)->get()->result();
        
        $this->success_output($invoices);
    }

    /**
     * Create an invoice for the specified user
     *
     * @param int $client_id
     * @return void
     */
    public function invoice_create($client_id)
    {
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_POST['client_id'] = $client_id;
        $_POST['invoice_date_created'] = date('j.n.Y');
        $_POST['invoice_group_id'] = 3;
        $_POST['invoice_time_created'] = date('G:i:s');
        $_POST['invoice_password'] = null;
        $_POST['user_id'] = 2;
        $_POST['payment_method'] = 3;


        $this->load->library('session');
        $this->load->helper('trans_helper');
        $this->load->helper('date_helper');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('settings/mdl_settings');

        

        if ($this->mdl_invoices->run_validation()) {
            $invoice_id = $this->mdl_invoices->create();

            $response = [
                'success' => 1,
                'invoice_id' => $invoice_id,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }


    private function success_output(array $data)
    {
        return $this->output
        ->set_content_type('application/json')
        ->set_status_header('200')
        ->set_output(json_encode($data));
    }

    private function bad_output(array $data)
    {
        return $this->output
        ->set_content_type('application/json')
        ->set_status_header('400')
        ->set_output(json_encode($data));
    }

    private function autherror_output()
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header('403')
            ->set_output(json_encode([
                'code'=> 403,
                'message' => '403 ERROR'
            ]));
    }
}
