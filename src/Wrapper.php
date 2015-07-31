<?php 

namespace MyMagic\Connect;
use \GuzzleHttp\Client;
 
class Wrapper extends Client {
 
  private static $email, $cipher, $compare_str, $client, $id;
  public static $client_url;

  static function init($client_url = "http://mymagic.my") {
    if (empty(static::$client) && isset($_COOKIE['magic_cookie']))
    {
      static::$client = new Client(array(
        'base_uri'  =>  'http://connect.mymagic.my/api/',
        'timeout'   =>  60,
        'auth'      =>  array('magic', 'ilovemymagic'),
        'debug'     =>  false
      ));
      static::$client_url = $client_url;
      $cookie = base64_decode($_COOKIE['magic_cookie']);
      $data = explode("|||", $cookie);    
      static::$id = $data[0];
      static::$email = $data[1];
      static::$cipher = $data[2];
      $salt = "thisisaveryawesomemagicsalt12345";
      $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
      $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
      static::$compare_str = md5(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, static::$email, MCRYPT_MODE_ECB, $iv));
    } else {
      throw new \Exception("You are not logged into MaGIC Connect");
    }
  }

  public static function getUserData() {
    return static::$client->get('users/'.static::$id)->getBody();
  }

  public static function isUserLoggedIn() {
    return static::$cipher === static::$compare_str;
  }
 
}
