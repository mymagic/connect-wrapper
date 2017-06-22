<?php

namespace MyMagic\Connect;

use \GuzzleHttp\Client as BaseClient;
use \GuzzleHttp\Exception\ClientException as Exception;

class Client
{

    private $url = "http://account.mymagic.my",
    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImUxNGY3ZTYxZjRiNTYwZTU5ZmNmM2JlM2RkNzVlYjA5OTlmYjExNTY1MWJkYzE2MDdhYzEwZmFhODk2MGM0ZWE2ZTU2MzE3Yjk0Y2VlNjc4In0.eyJhdWQiOiIyNiIsImp0aSI6ImUxNGY3ZTYxZjRiNTYwZTU5ZmNmM2JlM2RkNzVlYjA5OTlmYjExNTY1MWJkYzE2MDdhYzEwZmFhODk2MGM0ZWE2ZTU2MzE3Yjk0Y2VlNjc4IiwiaWF0IjoxNDk4MTI2MjAwLCJuYmYiOjE0OTgxMjYyMDAsImV4cCI6MTgxMzY1OTAwMCwic3ViIjoiMjMyMCIsInNjb3BlcyI6W119.BaRAoYV6RvBK3yEhl1GkEQkT9BPxAWMnd2ir3bC4ZrrudF9jsRwen64EHzpgDpv2Iqsn7a-k5hrQ_gN4C255iC4iKDlbN_2mRVrgzJon-I2h_8fLHiXuW7ROWrgz9SJBngteSvakz71qgDRDDW0y1OgJmU2AgvYTz6UwcR7PDZhyrK1-UKLM3u-woKfapvbTPdmlDn49J7KcAbCgjbakLsS3S_tFcSec7zLVyuqPlUNSYvvGR-_ROgUmWvK1XFuv65vLmBMaip_gYIPBVNsRgD7nL9jWN5GvFh9UZQFDLUUW3_zn3-19ecxBCZlqeoDerXkJe-qwdFt96cajnYXJ3INZSQe3au-dnEFS_vwq-7hy5tLcTdsySaf853puXfjZDzzyXV08xzEBhz_emFz8NDdBs4suBv2n_8Rd6HmtXSilWn40dishB9Rp4Y1RsWnHMlA0HkgErJykk0usHNWNb8y1iezqxxD-RD0Ea3EFfZip_zK_5SMR-Wu-oX4guF-N6f5vNH0-9WBGeInxz8IeFQI0_wVILqdqQ9BKIPMV-VEzpxonh3GzQ0uL7bo4oAdqYfRktFtlcRzk1WIfESh1VZROpk4B6eW5oVW77Tv3Bvb2q4AObjRq3usuvZVQQsi84Hzy1hRbj_7T4xmTUw6wn2VJLcO8gwBbEapgLIYqgC8";

    public function setConnectUrl($connectUrl)
    {
        $this->url = $connectUrl;
    }

    public function getConnectUrl()
    {
        return $this->url;
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
        return $this->url . '/logout?redirect_uri=' . $redirectUrl;
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
            $r = $http->post($this->url . '/register', array(
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
