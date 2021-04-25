<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Mdl_Clients
 */
class Mdl_hmapi_query extends Response_Model
{
    public function __constructor()
    {
        parent::__constructor();
        
    }
    public function clients_select_by_id($id)
    {
        $this->load->database();
        return $this->db->get_where('ip_clients',[
            'client_id'=>$id
        ])->result_array();
    }

    public function clients_create($data)
    {
        $this->load->database();
        $insert_query = $this->db->insert('ip_clients',$data);

        if($insert_query)
            return ["billing_id"=>$this->db->insert_id()];
        else
            return ["billing_id"=>"error"];
    }
}