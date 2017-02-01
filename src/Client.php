<?php

namespace MyMagic\Connect;

use \GuzzleHttp\Client as BaseClient;
use \GuzzleHttp\Exception\ClientException as Exception;

class Client
{

    private $url = "http://account.mymagic.my",
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijg2ZmRmY2Y1NjhmZjMxMzhhMjA1NTA5MjFlZWRiZjdmMjJjN2VmZGZlYjUzNDA2YWMzODMyN2M5ZjUyMGI3NjlkN2QzMzFmZTcwYzA2ODk4In0.eyJhdWQiOiI4IiwianRpIjoiODZmZGZjZjU2OGZmMzEzOGEyMDU1MDkyMWVlZGJmN2YyMmM3ZWZkZmViNTM0MDZhYzM4MzI3YzlmNTIwYjc2OWQ3ZDMzMWZlNzBjMDY4OTgiLCJpYXQiOjE0ODU5MTk2MTAsIm5iZiI6MTQ4NTkxOTYxMCwiZXhwIjoxODAxNDUyNDEwLCJzdWIiOiIyMzIwIiwic2NvcGVzIjpbXX0.sNekvOu2iYKo4Sy3lJVq7DhSqidMm-e_s4HvyDdsrYvH4jodOnOkapz9hWd5tM2uMtK2EWZxprLKd5V9UexuNFX7qX_qHzQgNRZksBJLVC60uYxn-khLkwPSGyar_TPNUKBh3vujevnci0-rbZ0VIQq8BCUQALNdZCP6ltx6OQctAF1LxQAmT0T0hlpC6pTFr9qG0I0VJLnoyWZHIXEhXwKaWUKhPzLCiIn4GEqOVmnuYzPX4nl1lyenJvaRZHdr91XMIDGVKRKdb1HkwUpFtSn7lypExn4Uknans3V_3hj1O-GoKk4s7EWmokCJJjAzGGC29qZFkMAaJmH_IfWfFTT1fB7FJWt4Z873VCBIIU9Cioj9wPyVgtboWryDxs_vQfZFDI_I3ZwMbnomzTo44pOi8TVAnf25W7906pLMfUVhXBB_udDTaGwt5tC6oVLoiugyv6wEQh3RFw18Z1H01HaVsHmxdKSXaVRKyueBcMcHE9IY-s9OPY6K5LaTduQZsIXm2k36scm6WxQ2-pEygORX5wsibdezVk8BY67xk9ti7Z_Bnh5kSEh-2dxGTOfmFwBOvrHw4yPHe-IV5NgRbNQHmhopPZtZB5zqzMqk8I3pj3bnBT3C36TqcKSO_7z_3kHP8aU29yopzDwc0HyCvjQFetsUNBthinTgSYBtwhE";

    public function redirect($url, $permanent = false) {
        if($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header('Location: '.$url);
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

        $apiresponse = $http->get($this->url . '/api/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $grab,
                'Content-Type' => 'application/json',
            ],
        ]);
        return json_decode((string)$apiresponse->getBody(), true);
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

        if ($response && $response->getStatusCode() === 200) {
            return true;
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
