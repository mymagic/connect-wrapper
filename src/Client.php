<?php 

namespace MyMagic\Connect;
use \GuzzleHttp\Client as BaseClient;
use \GuzzleHttp\Exception\ClientException;

class Client {
  
  private $client, $cipher, $compare_str, $email, $id;
	
  public function connect($request,$client_id,$client_secret,$uri){
        $http = new BaseClient;
        try {
            $response = $http->post('http://account.mymagic.my/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $uri,
                    'code' => $request
                ]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            if ($response && $response->getStatusCode() === 401) {
                return \Redirect::to('login')->with('alert-fail', 'These credentials do not match our records.');
            }
        }
        try {
            $grab = json_decode((string)$response->getBody(), true)['access_token'];
        } catch (\Exception $e) {
            return \Redirect::to('/error')->with('alert-fail', 'Permission Denied.');
        }
        $apiresponse = $http->get('http://account.mymagic.my/api/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $grab,
                'Content-Type' => 'application/json',
            ],
        ]);
        return json_decode((string)$apiresponse->getBody(), true);
    }


  public function __construct() {
    $this->client = new BaseClient(array(
      'base_uri'  =>  'http://connect.mymagic.my/api/',
      'timeout'   =>  60,
      'auth'      =>  array('magic', 'ilovemymagic'),
      'debug'     =>  false
    ));

    $this->analyzeMagicCookie();
  }
    
  public function analyzeMagicCookie() {
	if($this->hasMagicCookie())
	{
		$decoded_data = base64_decode($_COOKIE['magic_cookie']);
		$data = explode("|||", $decoded_data);

		$this->id = $data[0];
		$this->email = $data[1];
		$this->cipher = $data[2];
	}
  }
  
  public function getUserData() {
    // http://connect.mymagic.my/api/users/4
    $json_string = $this->client->get('users/'.$this->id)->getBody()->getContents();
    $data = json_decode($json_string)->data;
    return $data;
  }
  
  public function isLoggedIn() {
    return $this->compareIvString();
  }

  private function compareIvString() {
    $salt = "thisisaveryawesomemagicsalt12345";
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $iv_string = md5(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $this->email, MCRYPT_MODE_ECB, $iv));

    return $this->cipher === $iv_string;
  }
	
  
	public function getLoginUrl($redirectUrl='')
	{
		return 'http://connect.mymagic.my/login?redirect_uri='.$redirectUrl;
	}

	public function getLogoutUrl($redirectUrl='')
	{
		return 'http://connect.mymagic.my/logout?redirect_uri='.$redirectUrl;
	}

	public function getProfileUrl()
	{
		return 'http://connect.mymagic.my/profile';
	}

	public function hasMagicCookie() 
	{
		if(!empty($_COOKIE['magic_cookie'])) return true;
		return false;
	}
  
	public function isUserExists($email) 
	{
		$email = urlencode($email);
		// http://connect.mymagic.my/api/get-user-by-email?email=exiang83%40yahoo.com
		$data = null;
		
		try 
		{
			$response = $this->client->get('get-user-by-email?email='.$email);
			
			if($response->getStatusCode() == 200)
			{
				$json_string = $response->getBody()->getContents();
				$data = json_decode($json_string)->data;
				if(!empty($data) && !empty($data->id)) return true;
			}
			else
			{
				return false;
			}
		} 
		catch (\Exception $e) 
		{
			return false;
		}
		
		return false;
	}

	public function getEmail()
	{
		return $this->email;
	}
  
	public function createUser($email, $firstName='', $lastName='')
	{
		// some how email should not be urlencode else it failed
		//$email = urlencode($email);
		
		try
		{
			// http://connect.mymagic.my/api/signup
			$r = $this->client->post('signup', array(
				'form_params' => array(
					'email' => $email, 
					'first_name' => str_replace('@', ' ', $firstName),
					'last_name' => str_replace('@', ' ', $lastName),
				)
			));
			
			if($r->getStatusCode() == 200)
			{
				return true;
			}
		}
		catch (\Exception $e) 
		{
			/*echo "<pre>";
			echo "Status code: " . $e->getResponse()->getStatusCode();
			echo "<br>";
			print_r( $e->getResponse()->getBody()->getContents());*/
			return false;
		}

	}
	
	private function generateRandomPassword($max='8', $min='8', $lowerCase=false)
	{
		$limit = rand($min, $max);
		$buffer = '';
		for($i=0; $i<$limit; $i++)
		{
			$switch = rand(1, 4);
			if($switch%2 == 0)
			{
				$buffer .= chr(rand(97, 122));
			}
			else
			{
				$buffer .= chr(rand(48,57));
			}
		}

		if($lowerCase) $buffer = strtolower($buffer);
		
		return $buffer;
	}
}
