<?php
	
	if(!function_exists('generate_years')){
		function generate_years()
		{
			if (date('m') > 3) 
	        {
	            $years = range('2000', date('Y')+1);
	        }
	        else 
	        {
	            $years = range('2000', date('Y'));
	        }
	        $range = array();
	        $size = sizeOf($years);
	        foreach ($years as $key => $value) 
	        {
	            if($key != $size - 1)   
	                $range[$key] = $value."-".$years[$key+1];
	        }
	        return array_reverse($range);
		}
	}

	if (!function_exists('assets'))
	{
		function assets($path = '')
		{
			return base_url()."public/assets/".$path;
		}
	}

	if (!function_exists('uploads'))
	{
		function uploads($path = '')
		{
			return base_url()."public/uploads/".$path;
		}
	}

	if (!function_exists('sessionExist'))
	{
		function sessionExist()
		{
			$CI =& get_instance();
			return ($CI->session->userdata('user_id')) ? true : false;
    	}
	}
	if (!function_exists('binSessionExist'))
	{
		function binSessionExist()
		{
			$CI =& get_instance();
			return ($CI->session->userdata('bins_id')) ? true : false;
    	}
	}

	if (!function_exists('encrypt_decrypt')) 
	{
		function encrypt_decrypt($action, $data, $secret_key) 
		{
		    $output         = false;
		    $encrypt_method = "AES-256-CBC";
		    $secret_iv      = $secret_key;
		    $key            = hash('sha256', $secret_key);
		    $iv             = substr(hash('sha256', $secret_iv), 0, 16);

		    if ($action == 'encrypt') 
		    {
		        $output = openssl_encrypt($data, $encrypt_method, $key, 0, $iv);
		        $output = base64_encode($output);
		    } 
		    else if ($action == 'decrypt') 
		    {
		        $output = openssl_decrypt(base64_decode($data), $encrypt_method, $key, 0, $iv);
		    }

		    return $output;
		}
	}

	if (!function_exists('number_to_word'))
	{
		function number_to_word( $number = '' )
		{
			//$number = 190908100.25;
		   $no = round($number);
		   $point = round($number - $no, 2) * 100;
		   $hundred = null;
		   $digits_1 = strlen($no);
		   $i = 0;
		   $str = array();
		   $words = array('0' => '', '1' => 'One', '2' => 'Two',
			'3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
			'7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
			'10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
			'13' => 'Thirteen', '14' => 'Fourteen',
			'15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
			'18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
			'30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
			'60' => 'Sixty', '70' => 'Seventy',
			'80' => 'Eighty', '90' => 'Ninety');
		   $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
		   while ($i < $digits_1) {
			 $divider = ($i == 2) ? 10 : 100;
			 $number = floor($no % $divider);
			 $no = floor($no / $divider);
			 $i += ($divider == 10) ? 1 : 2;
			 if ($number) {
				$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
				$hundred = ($counter == 1 && $str[0]) ? 'And ' : null;
				$str [] = ($number < 21) ? $words[$number] .
					" " . $digits[$counter] . $plural . " " . $hundred
					:
					$words[floor($number / 10) * 10]
					. " " . $words[$number % 10] . " "
					. $digits[$counter] . $plural . " " . $hundred;
			 } else $str[] = null;
		  }
		  $str = array_reverse($str);
		  $result = implode('', $str);
		  $result = rtrim($result);
		  $points = ($point) ?
			"." . $words[$point / 10] . " " .
				  $words[$point = $point % 10] : '';
		  return $result . " Rupees" . $points . " "."Only";
		}
	}

?>