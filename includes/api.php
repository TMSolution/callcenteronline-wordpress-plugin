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

        $curlHandler = curl_init();
        $uri = sprintf('%s/oauth/v2/token?',
            $this->options['endpoint']
        );

        $params = sprintf('grant_type=password&client_id=%s&client_secret=%s&username=%s&password=%s',
            $this->options['client_id'],
            $this->options['client_secret'],
            $this->options['login'],
            $this->options['password']
        );

        curl_setopt($curlHandler, CURLOPT_URL, $uri);
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandler, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $params);

        $tokenObject = json_decode(curl_exec($curlHandler));
        curl_close($curlHandler);

        return $tokenObject->access_token;

    }

    public function getCampaignStructure()
    {
        $token = $this->getToken();

        $uri = sprintf('%s/api/campaign/structure?access_token=%s&campaignId=%s', $this->options['endpoint'], $token, $this->options['campaignid']);

        $curlHandler = curl_init();

        curl_setopt($curlHandler, CURLOPT_URL, $uri);
        $data_string = json_encode($data);
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
        $info = curl_getinfo($curlHandler);
        $result = json_decode(curl_exec($curlHandler));
        $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        curl_close($curlHandler);

    }

    public function getCampaigns()
    {
        $token = $this->getToken();

        $uri = sprintf('%s/api/campaigns/list?access_token=%s', $this->options['endpoint'], $token);

        $curlHandler = curl_init();

        curl_setopt($curlHandler, CURLOPT_URL, $uri);
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        $info = curl_getinfo($curlHandler);
        $result = json_decode(json_decode(curl_exec($curlHandler)));
        $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        curl_close($curlHandler);
        if (is_array($result->data)) {

            $camps = [];
            foreach ($result->data as $campaign) {
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
