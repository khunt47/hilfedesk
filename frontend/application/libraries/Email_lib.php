<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH. 'third_party/sendgrid-php/vendor/autoload.php';


class Email_lib 
{
	public function __construct() 
    { 
        $CI =& get_instance();
        $CI->load->helper('array');
        $CI->load->helper('url');
        $CI->load->database();
        $CI->load->config('product_config', TRUE);
    }



    public function send_email($to, $to_name, $from, $from_name, $subject, $body)
    {
    	// $from_name = '';
    	// $to_name = '';

    	if (empty($from)) {
    		$from = 'noreply@geedesk.com';
    	}

    	if (is_array($to)) {

    		for ($i=0; $i < count($to) ; $i++) {
    			$email_s = new \SendGrid\Mail\Mail(); 
				$email_s->setFrom($from, $from_name);
				$email_s->setSubject($subject);
				$email_s->addTo($to[$i], $to_name);
				$email_s->addContent(
				    "text/html", $body
				);

				$api_key = '';

				$sendgrid = new \SendGrid($api_key);

				try {
				    $response = $sendgrid->send($email_s);
				    // print $response->statusCode() . "\n";
				    // print_r($response->headers());
				    // print $response->body() . "\n";
				} catch (Exception $e) {
				    echo 'Caught exception: '. $e->getMessage() ."\n";
				}
    		}
    	}
    	else 
    	{
	    	$email_s = new \SendGrid\Mail\Mail(); 
			$email_s->setFrom($from, $from_name);
			$email_s->setSubject($subject);
			$email_s->addTo($to, $to_name);
			$email_s->addContent(
			    "text/html", $body
			);

			$api_key = '';

			$sendgrid = new \SendGrid($api_key);
			try {
			    $response = $sendgrid->send($email_s);
			    // print $response->statusCode() . "\n";
			    // print_r($response->headers());
			    // print $response->body() . "\n";
			} catch (Exception $e) {
			    echo 'Caught exception: '. $e->getMessage() ."\n";
			}
		}
    }




    public function send_email_with_attachments($from, $from_name, $to, $to_name, $subject, $body, $attached_files) 
    {
    	if (is_array($to)) 
    	{
    		for ($i=0; $i < count($to) ; $i++) 
    		{
    			$email_s = new \SendGrid\Mail\Mail(); 
				$email_s->setFrom($from, $from_name);
				$email_s->setSubject($subject);
				$email_s->addTo($to[$i], $to_name);
				$email_s->addContent(
				    "text/html", $body
				);

				foreach ($attached_files as $value) 
				{
					$file_url = $value['ticket_attachment'];
					$file_type = $value['file_type'];
					$file_name = $value['file_name'];

					$file_encoded = base64_encode(file_get_contents($file_url));

					$email_s->addAttachment(
							    $file_encoded,
							    $file_type,
							    $file_name,
							    "attachment"
							);
				}				

				$api_key = '';

				$sendgrid = new \SendGrid($api_key);
				try {
				    $response = $sendgrid->send($email_s);
				    // print $response->statusCode() . "\n";
				    // print_r($response->headers());
				    // print $response->body() . "\n";
				} catch (Exception $e) {
				    echo 'Caught exception: '. $e->getMessage() ."\n";
				}
    		}
    	}
    	else 
    	{
	    	$email_s = new \SendGrid\Mail\Mail(); 
			$email_s->setFrom($from, $from_name);
			$email_s->setSubject($subject);
			$email_s->addTo($to, $to_name);
			$email_s->addContent(
			    "text/html", $body
			);

			foreach ($attached_files as $value) 
			{
				$file_url = $value['ticket_attachment'];
				$file_type = $value['file_type'];
				$file_name = $value['file_name'];

				$file_encoded = base64_encode(file_get_contents($file_url));

				$email_s->addAttachment(
						    $file_encoded,
						    $file_type,
						    $file_name,
						    "attachment"
						);
			}

			$api_key = '';

			$sendgrid = new \SendGrid($api_key);
			try {
			    $response = $sendgrid->send($email_s);
			} catch (Exception $e) {
			    echo 'Caught exception: '. $e->getMessage() ."\n";
			}
		}
    }


} //class ends
