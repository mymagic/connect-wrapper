# Getting Started

```
composer install
```

# 1.0 Usage

```

$this->magicConnect = new MyMagic\Connect\Client; // Init MaGIC Connect
$this->magicConnect->connect("CODE","CLIENTID","CLIENTSECRET","CALLBACKURI"); // Returns  {JSON} once the user authorize their account. Refer 1.1
$this->magicConnect->getLogoutUrl("REDIRECTURI"); // Logout user from connect and return to logout user on client side
$this->magicConnect->getProfileUrl(); // User connect profile URL
$this->magicConnect->isUserExists("EMAIL") // Check whether user exist or not on connect
$this->magicConnect->createUser("EMAIL","FIRSTNAME","LASTNAME","PASSWORD");

```


# 1.1 JSON Usage

```

{
firstname: first_name // Foo
lastname: last_name // Bar
email: user_email // name@domain.tld
gender: user_gender // M or F (Male/Female)
country: user_country // Ex. MY (Malaysia)
avatar: user_avatar // http://domain.tld/image.format
}

```
