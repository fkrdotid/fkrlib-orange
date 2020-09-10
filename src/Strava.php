<?php

namespace FkrCode\Strava;

class Strava
{
    private $strava_uri = 'https://www.strava.com/api/v3';
    private $client;
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function __construct($CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI, $GUZZLE_CLIENT)
    {
        $this->client = $GUZZLE_CLIENT; # Guzzle Client
        $this->client_id = $CLIENT_ID; # Strava Client ID
        $this->client_secret = $CLIENT_SECRET; # Strava Secrect
        $this->redirect_uri = $REDIRECT_URI; # Strava Redirect URi
    }


    public function authenticate($scope='read_all,profile:read_all,activity:read_all')
    {
        return redirect('https://www.strava.com/oauth/authorize?client_id='. $this->client_id .'&response_type=code&redirect_uri='. $this->redirect_uri . '&scope=' . $scope . '&state=strava');
    }

    public function unauthenticate($token)
    {
        $url = 'https://www.strava.com/oauth/deauthorize';
        $config = [
            'form_params' => [
                'access_token' => $token
            ]
        ];
        $res = $this->post($url, $config);
        return $res;
    }

    public function token($code)
    {
        $url = 'https://www.strava.com/oauth/token';
        $config = [
            'form_params' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code,
                'grant_type' => 'authorization_code'
            ]
        ];
        $res = $this->post($url, $config);
        return $res;
    }


    public function refreshToken($refreshToken)
    {
        $url = 'https://www.strava.com/oauth/token';
        $config = [
            'form_params' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token'
            ]
        ];
        $res = $this->post($url, $config);
        return $res;
    }


    public function athlete($token)
    {
        $url = $this->strava_uri . '/athlete';
        $config = $this->bearer($token);
        $res = $this->get($url, $config);
        return $res;
    }

    public function activities($token, $page = 1, $perPage = 10, $before = null, $after = null)
    {
        $url = $this->strava_uri . '/athlete/activities?page=' . $page . '&per_page=' . $perPage;

        if ($after !== null) {
            $url .= '&after=' . $after;
        }

        if ($before !== null) {
            $url .= '&before=' . $before;
        }

        $config = $this->bearer($token);
        $res = $this->get($url, $config);
        return $res;
    }

    public function activityDetail($token, $activityID)
    {
        $url = $this->strava_uri . '/activities/'. $activityID .'?include_all_efforts=true';
        $config = $this->bearer($token);
        $res = $this->get($url, $config);
        return $res;
    }



    public function activityStream($token, $activityID, $keys = '', $keyByType = true)
    {
        if ($keys != '')
            $keys = join(",", $keys);

        $url = $this->strava_uri . '/activities/'. $activityID .'/streams?keys='. $keys .'&key_by_type'. $keyByType;
        $config = $this->bearer($token);
        $res = $this->get($url, $config);
        return $res;
    }


    public function activityLaps($token, $activityID)
    {
        $url = $this->strava_uri . '/activities/'. $activityID .'/laps';
        $config = $this->bearer($token);
        $res = $this->get($url, $config);
        return $res;
    }



    public function activityZones($token, $activityID)
    {
        $url = $this->strava_uri . '/activities/'. $activityID .'/zones';
        $config = $this->bearer($token);
        $res = $this->get($url, $config);
        return $res;
    }

    public function get($url, $config)
    {
        $res = $this->client->get( $url, $config );
        $result = json_decode($res->getBody()->getContents());
        return $result;
    }

    public function post($url, $config)
    {
        $res = $this->client->post( $url, $config );
        $result = json_decode($res->getBody()->getContents());
        return $result;
    }

    private function bearer($token)
    {
        $config = [
            'headers' => [
                'Authorization' => 'Bearer '.$token.''
            ],
        ];
        return $config;
    }


}