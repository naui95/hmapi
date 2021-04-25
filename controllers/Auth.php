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
class Auth extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The authentication method
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function start($username, $password)
    {
        $this->load->module('sessions');
        $authentication = $this->sessions->authenticate($username,$password);

        if($authentication)
        {
            return $this->output
            ->set_content_type('application/json')
            ->set_status_header('200')
            ->set_output(json_encode([
                'code'=>200,
                'message'=>'Authentication OK'
            ]));
        }
        else
        {
            return $this->output
            ->set_content_type('application/json')
            ->set_status_header('401')
            ->set_output(json_encode([
                'code'=>200,
                'message'=>'Authentication FAIL'
            ]));
        }
    }    
}
