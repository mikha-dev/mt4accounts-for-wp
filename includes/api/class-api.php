<?php

/**
 * Class MT4WP_API_v3
 */
class MT4WP_API
{

    /**
     * @var MT4WP_API_v3_Client
     */
    protected $client;


    /**
     * Constructor
     *
     * @param string $api_key
     */
    public function __construct($api_key, $api_url)
    {
        $this->client = new MT4WP_API_Client($api_key, $api_url);
    }

    /**
     * Gets the API client to perform raw API calls.
     *
     * @return MT4WP_API_v3_Client
     */
    public function get_client()
    {
        return $this->client;
    }

    public function is_connected()
    {
        $data = $this->client->get('/ping', array( 'fields' => 'success' ));
        $connected = is_object($data) && isset($data->success);
        return $connected;
    }

    public function get_emailsubscription($id, array $args = array())
    {
        $resource = sprintf('/emailsubscriptions/%s', $id);
        $data = $this->client->get($resource, $args);
        return $data;
    }

    public function get_emailsubscription_group($id, array $args = array())
    {
        $resource = sprintf('/emailsubscriptions/groups/%s', $id);
        $data = $this->client->get($resource, $args);

        if (is_object($data) && isset($data->item)) {
            return $data->item;
        }

        return null;
    }

    public function list_emailsubscription_groups($args = array())
    {
        $resource = '/emailsubscriptions/groups';
        $data = $this->client->get($resource, $args);

        if (is_object($data) && isset($data->list)) {
            return $data->list;
        }

        return array();
    }

    public function list_emailsubscriptions($args = array())
    {
        $resource = '/emailsubscriptions';
        $data = $this->client->get($resource, $args);

        if (is_object($data) && isset($data->list)) {
            return $data->list;
        }

        return array();
    }

    public function create_emailsubscription($name, $email, $subscription_ids)
    {
        $args = array('name' => $name, 'email' => $email, 'ids' => implode(',', $subscription_ids));
        $resource = sprintf('/emailsubscriptions/create');
        return $this->client->post($resource, $args);
    }

    public function create_emailsubscription_for_group($group_id, $name, $email, $max_subscriptions)
    {
        $args = array(
            'name' => $name, 'email' => $email, 'id' => $group_id,
            'max_subscriptions'=> $max_subscriptions
        );
        $resource = '/emailsubscriptions/create4group';
        return $this->client->post($resource, $args);
    }

    public function delete_emailsubscription($email, $subscription_ids)
    {
        if($subscription_ids != 'all')
            $subscription_ids = implode(',', $subscription_ids);

        $args = array('email' => $email, 'ids' => $subscription_ids);
        $resource = sprintf('/emailsubscriptions/delete');
        return $this->client->post($resource, $args);
    }

    public function get_copiersubscription($id, array $args = array())
    {
        $resource = sprintf('/copiersubscriptions/%s', $id);
        $data = $this->client->get($resource, $args);
        return $data;
    }

    public function delete_emailsubscription_for_group($group_id, $email)
    {
        $args = array('email' => $email, 'id' => $group_id);
        $resource = '/emailsubscriptions/delete4group';
        return $this->client->post($resource, $args);
    }

    public function get_copiersubscription_group($id, array $args = array())
    {
        $resource = sprintf('/copiersubscriptions/groups/%s', $id);
        $data = $this->client->get($resource, $args);

        if (is_object($data) && isset($data->item)) {
            return $data->item;
        }

        return null;
    }

    public function list_copiersubscriptions($args = array())
    {
        $resource = '/copiersubscriptions';
        $data = $this->client->get($resource, $args);

        if (is_object($data) && isset($data->list)) {
            return $data->list;
        }

        return array();
    }

    public function list_copiersubscription_groups($args = array())
    {
        $resource = '/copiersubscriptions/groups';
        $data = $this->client->get($resource, $args);

        if (is_object($data) && isset($data->list)) {
            return $data->list;
        }

        return array();
    }

    public function create_copiersubscription($name, $email, $subscription_ids)
    {
        $args = array('name' => $name, 'email' => $email, 'ids' => implode(',', $subscription_ids));
        $resource = sprintf('/copiersubscriptions/create');
        return $this->client->post($resource, $args);
    }

    public function delete_copiersubscription($email, $subscription_ids)
    {
        if($subscription_ids != 'all')
            $subscription_ids = implode(',', $subscription_ids);

        $args = array('email' => $email, 'ids' => $subscription_ids);
        $resource = sprintf('/copiersubscriptions/delete');
        return $this->client->post($resource, $args);
    }

    public function create_copiersubscription_for_group($group_id, $name, $email, 
        $max_subscriptions, $max_accounts)
    {
        $args = array(
            'name' => $name, 'email' => $email, 'id' => $group_id,
            'max_subscriptions'=> $max_subscriptions,
            'max_accounts'=> $max_accounts
        );
        $resource = '/copiersubscriptions/create4group';
        return $this->client->post($resource, $args);
    }

    public function delete_copiersubscription_for_group($group_id, $email)
    {
        $args = array('email' => $email, 'id' => $group_id);
        $resource = '/copiersubscriptions/groups/delete';
        return $this->client->post($resource, $args);
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
