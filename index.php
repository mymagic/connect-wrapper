<?php 
if (isset($_COOKIE['magic_cookie'])){
    $cc = base64_decode($_COOKIE['magic_cookie']);
    $data = explode("|||", $cc);    

    $user_id = $data[0];
    $user_email = $data[1];
    $cipher = $data[2];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://connect.mymagic.my/api/users/".$user_id);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'magic:ilovemymagic');
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $output = curl_exec($ch);
    curl_close($ch);
    
    echo '<pre>';
    $return_data = json_decode($output);
    
    print_r($return_data);
    
    $salt = "thisisaveryawesomemagicsalt12345";//must be 32 char long
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $compare_str = md5(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $user_email, MCRYPT_MODE_ECB, $iv));
    if($cipher == $compare_str){
        echo '[[Logged in]] email: '.$user_email.', user_id: '.$user_id.', cipher_str: '.$cipher;
        echo '<br/><a href="http://connect.mymagic.my/logout?redirect_uri=http://apply.mymagic.my">Click to logout</a>';
    }
}else{
    echo 'Not logged in: magic_cookie not found';
    echo '<br/><a href="http://connect.mymagic.my/login?redirect_uri=http://apply.mymagic.my/">Click to login</a>';
}



