<?php

namespace App\Libraries;
use Illuminate\Support\Facades\Http;

/**
 * Library to send emails
 */

class GeedeskEmailAPI
{
    
    public function send_email($to, $to_name, $from, $from_name, $subject, $body, $reply_to = false) 
    {
        $url = env('GEEDESK_EMAIL_API_ENDPOINT');

        if (!$reply_to) {
            $reply_to = $from;
        }

        $parameters = [
            'from' => $from,
            'from_name' => $from_name,
            'to' => $to,
            'to_name' => $to_name,
            'subject' => $subject,
            'body' => $body,
            'reply_to' => $reply_to
        ];

        // Bearer token
        $api_key = env('GEEDESK_EMAIL_API');

        // Send the POST request with the bearer token and parameters
        $response = Http::withToken($api_key)
                        ->post($url, $parameters);

        // Check the status of the response
        if ($response->successful()) {
            // If the request was successful, handle the response data
            $data = $response->json();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            // Handle errors, if any
            return response()->json([
                'success' => false,
                'message' => 'Request failed',
                'error' => $response->body()
            ], $response->status());
        }
    }


    public function send_attachment_email($to, $to_name, $from, $from_name, $subject, $body, $attachment_path, $reply_to = false) 
    {
        $url = env('GEEDESK_EMAIL_API_ENDPOINT');

        if (!$reply_to) {
            $reply_to = $from;
        }

        $parameters = [
            'from' => $from,
            'from_name' => $from_name,
            'to' => $to,
            'to_name' => $to_name,
            'subject' => $subject,
            'body' => $body,
            'reply_to' => $reply_to,
            'attachment_path' => $attachment_path
        ];

        // Bearer token
        $api_key = env('GEEDESK_EMAIL_API');

        // Send the POST request with the bearer token and parameters
        $response = Http::withToken($api_key)
                        ->post($url, $parameters);

        // Check the status of the response
        if ($response->successful()) {
            // If the request was successful, handle the response data
            $data = $response->json();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            // Handle errors, if any
            return response()->json([
                'success' => false,
                'message' => 'Request failed',
                'error' => $response->body()
            ], $response->status());
        }
    }

}
