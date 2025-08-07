<?php

namespace App\Libraries;


class SendGridEmail
{

	public function send_email($to, $to_name, $from, $from_name, $subject, $body) 
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



	public function send_emails($tos, $from, $from_name, $subject, $body) 
	{
		if ($from === '' || $from === null) {
			$from = 'noreply@geedesk.com';
		}
		$email_s = new \SendGrid\Mail\Mail(); 
		$email_s->setFrom($from, $from_name);
		$email_s->setSubject($subject);
		$email_s->addTos($tos);
		$email_s->addContent(
			"text/html", $body
		);

		$api_key = env('SENDGRID_API_KEY');

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