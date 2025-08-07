<?php

namespace App\Libraries;

use Mailgun\Mailgun;

/**
 * Library to send emails
 */

class MailgunEmail 
{
    /**
     * 
     */
    public function send_email($to_name, $to_email, $from_name, $from_email, $subject, $email_body)
    {
        $mg = Mailgun::create(env('MAILGUN_API_KEY'), env('MAILGUN_EU_URL'));
        $domain = env('MAILGUN_DOMAIN');
        # Make the call to the client.
        $mg->messages()->send('email.daysupport.co.uk', [
            'from'    => 'noreply@email.daysupport.co.uk',
            'to'      => 'krishnan.ubuntu@gmail.com',
            'subject' => 'The PHP SDK is awesome MC ONCALL!',
            'text'    => 'It is so simple to send a message MC ONCALL.'
          ]);
    }

}
