<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
*   Authorization_Token
* -------------------------------------------------------------------
* API Token Check and Generate
*
* @author: Jeevan Lal
* @version: 0.0.5
*/

require_once APPPATH . 'third_party/php-jwt/JWT.php';
require_once APPPATH . 'third_party/php-jwt/BeforeValidException.php';
require_once APPPATH . 'third_party/php-jwt/ExpiredException.php';
require_once APPPATH . 'third_party/php-jwt/SignatureInvalidException.php';

use \Firebase\JWT\JWT;

class Authorization_Token 
{
    /**
     * Token Key
     */
    protected $token_key;

    /**
     * Token algorithm
     */
    protected $token_algorithm;

    /**
     * Request Header Name
     */
    protected $token_header     = ['authorization','Authorization', 'X-Auth-Token'];
    protected $access_key_header= ['XX-Auth-Token', 'Xx-Auth-Token'];

    /**
     * Token Expire Time
     * ----------------------
     * ( 1 Day ) : 60 * 60 * 24 = 86400
     * ( 1 Hour ) : 60 * 60     = 3600
     */
    protected $token_expire_time = 86400; 


    public function __construct()
	{
        $this->CI =& get_instance();

        /** 
         * jwt config file load
         */
        $this->CI->load->config('jwt');

        /**
         * Load Config Items Values 
         */
        $this->token_key        = $this->CI->config->item('jwt_key');
        $this->token_algorithm  = $this->CI->config->item('jwt_algorithm');
    }

    /**
     * Generate Token
     * @param: user data
     */
    public function generateToken($data)
    {
        try {
            return JWT::encode($data, $this->token_key, $this->token_algorithm);
        }
        catch(Exception $e) {
            return 'Message: ' .$e->getMessage();
        }
    }

    /**
     * Validate Token with Header
     * @return : user informations
     */
    public function validateToken()
    {
        /**
         * Request All Headers
         */
        // echo "111"; exit();
        $headers = $this->CI->input->request_headers();
        // echo "<pre>"; print_r($headers); exit();
        /**
         * Authorization Header Exists
         */
        $token_data = $this->tokenIsExist($headers);
        if($token_data['status'] === TRUE)
        {
            try
            {
                /**
                 * Token Decode
                 */
                try 
                {
                    $token_decode = JWT::decode($headers[$token_data['key']], $this->token_key, array($this->token_algorithm));
                }
                catch(Exception $e) 
                {
                    return ['status' => FALSE, 'errors' => [['field' => 'Token', 'message' => $e->getMessage()]]];
                }

                if(!empty($token_decode) && is_object($token_decode))
                {
                    // Check User ID (exists and numeric)
                    if(empty($token_decode->id) || !is_numeric($token_decode->id)) 
                    {
                        return ['status' => FALSE, 'errors' => [['field' => 'Token', 'message' => 'User ID Not Define!']]];

                    // Check Token Time
                    }else if(empty($token_decode->time) || !is_numeric($token_decode->time)) {
                        
                        return ['status' => FALSE, 'errors' => [['field' => 'Token', 'message' => 'Token Time Not Define!']]];
                    }
                    else
                    {
                        /**
                         * Check Token Time Valid 
                         */
                        $time_difference = strtotime('now') - $token_decode->time;
                        if( $time_difference >= $this->token_expire_time )
                        {
                            return ['status' => FALSE, 'errors' => [['field' => 'Token', 'message' => 'Token Time Expire.']]];       
                        }
                        else
                        {
                            /**
                             * All Validation False Return Data
                             */
                            return ['status' => TRUE, 'data' => ['decode_token' => $token_decode, 'token' => $headers[$token_data['key']]]];
                        }
                    }
                    
                }
                else
                {
                    return ['status' => FALSE, 'errors' => [['field' => 'Token', 'message' => 'Forbidden']]];
                }
            }
            catch(Exception $e) 
            {
                return ['status' => FALSE, 'errors' => [['field' => 'Token', 'message' => $e->getMessage()]]];
            }
        }else
        {
            // Authorization Header Not Found!
            return $token_data;
        }
    }

    /**
     * Validate Access Key with Header
     * @return : user access key
     */
    public function validateAccessKey()
    {
        /**
         * Request All Headers
         */
        // echo "111"; exit();
        $headers = $this->CI->input->request_headers();
        // echo "<pre>"; print_r($headers); exit();
        /**
         * Authorization Header Exists
         */
        $access_key_data = $this->accessKeyIsExist($headers);
        if($access_key_data['status'] === TRUE)
        {  
            // echo "<pre>";print_r($headers[$access_key_data['key']]); exit;          
            if($headers[$access_key_data['key']] === API_ACCESS_KEY)
            {
                return ['status' => TRUE, 'data' => ['access_key' => $headers[$access_key_data['key']]]];
            }
            else
            {
                return [
                            'status'    => FALSE,
                            'errors'    => [
                                                ['field' => 'Access Key', 'message' => $headers[$access_key_data['key']]],
                                                ['field' => 'Access Key', 'message' => 'Signature verification failed.']
                                            ]
                        ];
            }
            
        }
        else
        {
            // Header Not Found!
            return $access_key_data;
        }
    }

    /**
     * Validate Token with POST Request
     */
    public function validateTokenPost()
    {
        if(isset($_POST['token']))
        {
            $token = $this->CI->input->post('token', TRUE);
            if(!empty($token) && is_string($token) && !is_array($token))
            {
                try
                {
                    /**
                     * Token Decode
                     */
                    try {
                        $token_decode = JWT::decode($token, $this->token_key, array($this->token_algorithm));
                    }
                    catch(Exception $e) {
                        return ['status' => FALSE, 'message' => $e->getMessage()];
                    }
    
                    if(!empty($token_decode) && is_object($token_decode))
                    {
                        // Check User ID (exists and numeric)
                        if(empty($token_decode->id) || !is_numeric($token_decode->id)) 
                        {
                            return ['status' => FALSE, 'message' => 'User ID Not Define!'];
    
                        // Check Token Time
                        }else if(empty($token_decode->time) || !is_numeric($token_decode->time)) {
                            
                            return ['status' => FALSE, 'message' => 'Token Time Not Define!'];
                        }
                        else
                        {
                            /**
                             * Check Token Time Valid 
                             */
                            $time_difference = strtotime('now') - $token_decode->time;
                            if( $time_difference >= $this->token_expire_time )
                            {
                                return ['status' => FALSE, 'message' => 'Token Time Expire.'];
    
                            }else
                            {
                                /**
                                 * All Validation False Return Data
                                 */
                                return ['status' => TRUE, 'data' => $token_decode];
                            }
                        }
                        
                    }else{
                        return ['status' => FALSE, 'message' => 'Forbidden'];
                    }
                }
                catch(Exception $e) {
                    return ['status' => FALSE, 'message' => $e->getMessage()];
                }
            }
            else
            {
                return ['status' => FALSE, 'message' => 'Token is not defined.' ];
            }
        } 
        else 
        {
            return ['status' => FALSE, 'message' => 'Token is not defined.'];
        }
    }

    /**
     * Token Header Check
     * @param: request headers
     */
    public function tokenIsExist($headers)
    {
        if(!empty($headers) && is_array($headers)) 
        {
            foreach ($this->token_header as $key) 
            {
                if (array_key_exists($key, $headers) && !empty($key))
                    return ['status' => TRUE, 'key' => $key];
            }
        }
        return ['status' => FALSE, 'errors' => [['field' => 'Token', 'message' => 'Token is not defined.']]];
    }

    /**
     * Access Key Header Check
     * @param: request headers
     */
    public function accessKeyIsExist($headers)
    {
        if(!empty($headers) && is_array($headers)) {
            foreach ($this->access_key_header as $key) {
                if (array_key_exists($key, $headers) && !empty($key))
                    return ['status' => TRUE, 'key' => $key];
            }
        }
        return ['status' => FALSE, 'errors' => [['field' => 'Access Key', 'message' => 'Access key is not defined.']]];
    }

    /**
     * Fetch User Data
     * -----------------
     * @param: token
     * @return: user_data
     */
    public function userData()
    {
        /**
         * Request All Headers
         */
        $headers = $this->CI->input->request_headers();

        /**
         * Authorization Header Exists
         */
        $token_data = $this->tokenIsExist($headers);
        if($token_data['status'] === TRUE)
        {
            try
            {
                /**
                 * Token Decode
                 */
                try {
                    $token_decode = JWT::decode($headers[$token_data['key']], $this->token_key, array($this->token_algorithm));
                }
                catch(Exception $e) {
                    return ['status' => FALSE, 'message' => $e->getMessage()];
                }

                if(!empty($token_decode) && is_object($token_decode))
                {
                    return $token_decode;
                }else{
                    return ['status' => FALSE, 'message' => 'Forbidden'];
                }
            }
            catch(Exception $e) {
                return ['status' => FALSE, 'message' => $e->getMessage()];
            }
        }else
        {
            // Authorization Header Not Found!
            return ['status' => FALSE, 'message' => $token_data['message'] ];
        }
    }
}