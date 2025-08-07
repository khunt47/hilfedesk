<?php

namespace App\Libraries;

/**
 * Library to send curl requests
 */

class Curlrequests 
{

    public function post_request($api_url, $api_key, $data)
    {
        header('Content-Type: application/json'); // Specify the type of data
        $ch = curl_init($api_url); // Initialise cURL
        $params = json_encode($data); // Encode the data array into a JSON string
        $authorization = "Authorization: Bearer ".$api_key; // Prepare the authorisation token
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params); // Set the posted fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection
        return json_decode($result); // Return the received data
    }

    public function get_request($url, $token, $data)
    {
        echo 'get request';
    }

}
