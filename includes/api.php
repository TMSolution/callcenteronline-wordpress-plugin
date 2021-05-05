<?php

class cco_api
{
    protected $options;
    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getToken()
    {

        $uri = sprintf('%s/oauth/v2/token',
            $this->options['endpoint']
        );

        $body = array(
            'grant_type' => 'password',
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['client_secret'],
            'username' => $this->options['login'],
            'password' => $this->options['password'],
        );

        $args = array(
            'body' => $body,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
        );

        $response = wp_remote_post($uri, $args);
        $tokenObject = json_decode($response['body']);

        return $tokenObject->access_token;

    }

    public function getCampaigns()
    {
        $token = $this->getToken();

        $uri = sprintf('%s/api/campaigns/list?access_token=%s', $this->options['endpoint'], $token);

        $body = array(
            'grant_type' => 'password',
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['client_secret'],
            'username' => $this->options['login'],
            'password' => $this->options['password'],
        );

        $args = array(
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Content-Type' => ' application/json',
            ),
            'cookies' => array(),
        );

        $response = wp_remote_get($uri, $args);
        $decodedResponse = json_decode(json_decode($response['body']));
  
        if (is_array($decodedResponse->data)) {

            $camps = [];
            foreach ($decodedResponse->data as $campaign) {
                if ($campaign->active) {
                    $camp = new \stdClass();
                    $camp->id = $campaign->id;
                    $camp->name = $campaign->name;
                    $camps[] = $camp;
                }
            }
            
            return $camps;
        }
        return [];

    }
}
