<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
// require APPPATH . 'libraries/Format.php';
class Testctrl extends \Restserver\Libraries\REST_Controller
{
    public function __construct() 
    {
        parent::__construct();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Access-Control-Allow-Headers, X-Auth-Token');
    }

    
    /*just for testing*/
    public function somefunction_get()
    {
        $this->response(['status' => true, 'message' => 'working ....'], REST_Controller::HTTP_OK);
        
    }

/*CI controller end*/    
}