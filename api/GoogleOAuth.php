<?php
namespace App;

use Google\Client;
use Google\Service\Oauth2;

class GoogleOAuth
{
    private $client;
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/google.php';

        $this->client = new Client();
        $this->client->setClientId($this->config['client_id']);
        $this->client->setClientSecret($this->config['client_secret']);
        $this->client->setRedirectUri($this->config['redirect_uri']);
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function fetchAccessTokenWithAuthCode($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    public function getUserProfile()
    {
        if (!$this->client->getAccessToken()) {
            throw new \Exception("Access token not set for Google API Client.");
        }
        $oauth2 = new Oauth2($this->client);
        return $oauth2->userinfo->get();
    }

    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
    }
}