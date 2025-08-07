<?php

namespace App\Libraries;
use App\Libraries\Curlrequests;

/**
 * Library for LTCRM integration
 */

class Ltcrm 
{

    protected $api_url;
    protected $api_key;


    public function __construct() 
    {
        $this->api_url = env('LTCRM_BASE_API_URL').env('LTCRM_GET_CUSTOMERS_API');
        $this->api_key = env('LTCRM_API_KEY');
    }


    public function fetch_new_customers($created_date)
    {
        $data = array(
            'created_date' => $created_date
        );
        $curl = new Curlrequests();
        $new_customers = $curl->post_request($this->api_url, $this->api_key, $data);
        return $new_customers;
    }


    public function fetch_customer($customer_id)
    {
        $data = array(
            'customer_id' => $customer_id
        );
        $curl = new Curlrequests();
        $customer = $curl->post_request($this->api_url, $this->api_key, $data);
        return $customer;
    }

}
