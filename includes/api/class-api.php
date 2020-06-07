<?php

/**
 * Class MT4_Accounts_Api
 */
class MT4_Accounts_Api
{

    protected $client;

    public function __construct($api_url)
    {
        $this->client = new MT4_Accounts_Api_Client($api_url);
    }

    /**
     * @return MT4_Accounts_Api_Client
     */
    public function get_client()
    {
        return $this->client;
    }

    /**
     * @return bool
     */
    public function check_trusted()
    {
        $data = $this->client->get('/widgets/check_trusted');
        $connected = is_object($data) && isset($data->success);

        return $connected;
    }

    /**
     * @return bool
     */
    public function add_trusted($apiToken)
    {
        $resource = sprintf('/widgets/add_trusted/api_token=%s', $apiToken);

        $data = $this->client->get($resource);
        $success = is_object($data) && isset($data->success);

        return $success;
    }

    /**
     * @return array
     */
    public function get_equity($accountNumber)
    {
        $resource = sprintf('/widgets/equity/%d', $accountNumber);
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->items)) {
            return $data->items;
        }

        return array();
    }

    /**
     * @return array
     */
    public function list_accounts()
    {
        $resource = sprintf('/widgets/list_accounts');
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->items)) {
            return $data->items;
        }

        return array();
    }

    /**
     * @return string
     */
    public function get_last_response_body()
    {
        return $this->client->get_last_response_body();
    }

    /**
     * @return array
     */
    public function get_last_response_headers()
    {
        return $this->client->get_last_response_headers();
    }
}
