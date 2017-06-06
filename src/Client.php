<?php

namespace MyMagic\Connect;

use \GuzzleHttp\Client as BaseClient;
use \GuzzleHttp\Exception\ClientException as Exception;

class Client
{

    private $url = "http://account.mymagic.my",
    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImMzNjdmMzZiYzNhOWQxYTY5NmViNGEwZWY4NTUyNzg2MTcwMzUxNDE1NjkzMmRmZmExY2Q4NWNlZTY5OGZkZjQzYzk4NTU5MjQ5OTFhNDRmIn0.eyJhdWQiOiIxMCIsImp0aSI6ImMzNjdmMzZiYzNhOWQxYTY5NmViNGEwZWY4NTUyNzg2MTcwMzUxNDE1NjkzMmRmZmExY2Q4NWNlZTY5OGZkZjQzYzk4NTU5MjQ5OTFhNDRmIiwiaWF0IjoxNDg1OTIyNDQ0LCJuYmYiOjE0ODU5MjI0NDQsImV4cCI6MTgwMTQ1NTI0NCwic3ViIjoiMjMyMCIsInNjb3BlcyI6W119.Ee2YhXKPxJMXcqpElOovpG80ATep4VQKBFvFJ4tGBdr0N6EYdlCR56GCDtbwofkqNr2SE9hjngqkYBxu4VTYn9agfG-8zlg2KH9Uw6TRbilJbXacGGuve5mynyT-bjQpzsAshIR5VNW_uTcf330TwA3bv3rz_0KGjWL9WTWJk5atLzS84AjQc90QbNxvuh4rOH__VrDQeM6I1gqxj1fpMlpLg07MwT8BcNZ1aPIn1VPFswq-aPQfEm_JH8ND7Oj4l5VlMnKx8mZADqu-TsqIe7-0cSjYUXK-BkpyXfeMJLhWOroadh878lSD67xLfBdmeAOWU4w3ncXBur8anzmsQqCLtnTX71uQnmh4ApRDcpeOcyuok1S4-JQ-e72PGXbwLwkU2PHQedqayjwCshoKpAis-Gbef9LWWB7RcKavRf6PeMcuNaqNxJmqduYaVW5cmTZz_jH6MldUJG9p4s8mGycnxioo30UNOBMOrBmGCmOCvGJEnQb9yIZBFxJXVUTYYeT13GOyNJMKRfTFntM-z3-fdu1GST0lQtpJqngPcWHC7kvckjXptObA0bIFPc7pTuEX6PCz83ay3BNn5XwHpNApfCCHDQbvfaI9-dOgjLKFtrW55yHDTV04OE4Dzbwd5i2nMNlN4N6yVl9T_ZL1nOGWG6ZqPXpVRzOh7qSQY9k";

    public function setConnectUrl($connectUrl)
    {
        $this->url = $connectUrl;
    }

    public function redirect($url, $permanent = false)
    {
        if ($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header('Location: ' . $url);
        exit();
    }

    public function connect($request, $client_id, $client_secret, $uri)
    {
        $http = new BaseClient;
        try {
            $response = $http->post($this->url . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $uri,
                    'code' => $request
                ]
            ]);
        } catch (Exception $e) {
            $response = $e->getResponse();
            if ($response && $response->getStatusCode() === 401) {
                return $this->redirect('/');
            }
        }
        try {
            $grab = json_decode((string)$response->getBody(), true)['access_token'];
        } catch (\Exception $e) {
            return $this->redirect('/');
        }

        $apiResponse = $http->get($this->url . '/api/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $grab,
                'Content-Type' => 'application/json',
            ],
        ]);
        return json_decode((string)$apiResponse->getBody(), true);
    }

    public function getLogoutUrl($redirectUrl = '')
    {
        return $this->url . 'logout?redirect_uri=' . $redirectUrl;
    }

    public function getProfileUrl()
    {
        return $this->url . '/profile';
    }


    public function isUserExists($email)
    {
        $http = new BaseClient;
        $response = $http->get($this->url . '/api/get-user-details/' . $email, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ],
        ]);

        $data = json_decode((string)$response->getBody(), true);
        if ($response && $response->getStatusCode() === 200) {
            if ($data->email === $email) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function createUser($email, $firstName = '', $lastName = '', $password, $gender = '', $t_and_c = '1', $confirmation = '1', $country = '')
    {
        try {
            $http = new BaseClient;
            $r = $http->post($this->url . 'register', array(
                'form_params' => array(
                    'email' => $email,
                    'firstname' => str_replace('@', ' ', $firstName),
                    'lastname' => str_replace('@', ' ', $lastName),
                    'password' => $password,
                    'password_confirmation' => $password,
                    't_and_c' => $t_and_c,
                    'confirmation' => $confirmation,
                    'gender' => $gender,
                    'country' => $country
                )
            ));

            if ($r->getStatusCode() == 200) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

}
