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

    public function get_equity($accountNumber)
    {
        $resource = sprintf('/widgets/equity/%d', $accountNumber);
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->items)) {
            return $data->items;
        }

        return array();
    }

    public function get_portfolio_equity($portfolio_id)
    {
        $resource = sprintf('/widgets/portfolio/equity/%d', $portfolio_id);
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->items)) {
            return $data->items;
        }

        return array();
    }

    /**
     * @return array
     */
    public function get_instruments($accountNumber)
    {
        $resource = sprintf('/widgets/instruments/%d', $accountNumber);
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->items)) {
            return $data->items;
        }

        return array();
    }

    public function get_portfolio_instruments($portfolio_id)
    {
        $resource = sprintf('/widgets/portfolio/instruments/%d', $portfolio_id);
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->items)) {
            return $data->items;
        }

        return array();
    }

    /**
     * @return array
     */
    public function get_advstat($accountNumber)
    {
        $resource = sprintf('/widgets/advstat/%d', $accountNumber);
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->item)) {
            return $data->item;
        }

        return array();
    }

    public function get_portfolio_advstat($portfolio_id)
    {
        $resource = sprintf('/widgets/portfolio/advstat/%d', $portfolio_id);
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->item)) {
            return $data->item;
        }

        return array();
    }

    /**
     * @return array
     */
    public function get_history($accountNumber)
    {
        $resource = sprintf('/widgets/history/%d', $accountNumber);
        $data = $this->client->get($resource);

        if (is_object($data) && isset($data->items)) {
            return $data->items;
        }

        return array();
    }

    public function get_portfolio_history($portfolio_id)
    {
        $resource = sprintf('/widgets/portfolio/history/%d', $portfolio_id);
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

    public function list_portfolios()
    {
        $resource = sprintf('/widgets/portfolios');
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
