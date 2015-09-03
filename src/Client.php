<?php 

namespace MyMagic\Connect;
use \GuzzleHttp\Client as BaseClient;

class Client {
  
  private $client, $cipher, $compare_str, $email, $id;

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
  
  public function hasMagicCookie() {
	if(!empty($_COOKIE['magic_cookie'])) return true;
	return false;
  }
  
  public function getUserData() {
    $json_string = $this->client->get('users/'.$this->id)->getBody()->getContents();
    $data = json_decode($json_string)->data;
    return $data;
  }

  public function isLoggedIn() {
    return $this->compareIvString();
  }
  
  public function getEmail(){
	return $this->email;
  }
  
  public function getLoginUrl($redirectUrl=''){
	return 'http://connect.mymagic.my/login?redirect_uri='.$redirectUrl;
  }
  
  public function getLogoutUrl($redirectUrl=''){
	return 'http://connect.mymagic.my/logout?redirect_uri='.$redirectUrl;
  }
  
  public function getProfileUrl(){
	return 'http://connect.mymagic.my/profile';
  }

  private function compareIvString() {
    $salt = "thisisaveryawesomemagicsalt12345";
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $iv_string = md5(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $this->email, MCRYPT_MODE_ECB, $iv));

    return $this->cipher === $iv_string;
  } 
}
