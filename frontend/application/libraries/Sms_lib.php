<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Sms_lib 
{
	public function __construct() 
    { 
        $CI =& get_instance();
        $CI->load->helper('array');
        $CI->load->helper('url');
        $CI->load->database();
        $CI->load->config('product_config', TRUE);
    }



    public function send_sms_via_msg91($sms_sender_code, $user_mobile, $sms_body)
    {
    	$CI =& get_instance();
    	$user_mob_arr           = explode("+91", $user_mobile);
  		$user_mob               = $user_mob_arr[0];

  		$apiKey = urlencode('255004AyBQlEMsQ5c2f317e');

  		$sender = urlencode($sms_sender_code);
  		$message = rawurlencode($sms_body);

  		$data = array('authkey' => $apiKey, 'mobiles' => $user_mob, "sender" => $sender, "message" => $message, 'country' => '91', 'route' => 4);

  		$ch = curl_init('http://api.msg91.com/api/v2/sendsms?country=91');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

      $res = json_decode($response);

      if ($res->type == 'success') {
        return true;
      }
      else {
        return false;
      }
    }


} //class ends
