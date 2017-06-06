# Getting Started

```
composer install
```

# 1.0 Usage

```
// Init MaGIC Connect
$this->magicConnect = new MyMagic\Connect\Client; 

// optional, default to http://account.mymagic.my. use it in dev environment
$this->magicConnect->setConnectUrl('http:accountd.mymagic.my');

// Returns  {JSON} once the user authorize their account. Refer 1.1
$this->magicConnect->connect("CODE","CLIENTID","CLIENTSECRET","CALLBACKURI"); 

// Logout user from connect and return to logout user on client side. Refer 1.2
$this->magicConnect->getLogoutUrl($redirectUrl = ''); 

// User connect profile URL
$this->magicConnect->getProfileUrl(); 

// Check whether user exist or not on connect. Refer 1.3
$this->magicConnect->isUserExists($email) 

// Create user on connect. Refer 1.4
$this->magicConnect->createUser($email, $firstName = '', $lastName = '', $password, $gender = '', $t_and_c = '1', $confirmation = '1', $country = ''); 

```


# 1.1 JSON Usage

```
//Execute
    $data = $this->magicConnect->connect('$_GET['code']','1','longclientsecret','http://clienturi.tld/callback');

//Get the client ID and secret at http://account.mymagic.my/api

//Output of $data

    {
    firstname: first_name // Foo
    lastname: last_name // Bar
    email: user_email // name@domain.tld
    gender: user_gender // M or F (Male/Female)
    country: user_country // Ex. MY (Malaysia)
    avatar: user_avatar // http://domain.tld/image.format
    }

```

# 1.2 Logout URL

```

//Execute 
    $this->magicConnect->getLogoutUrl("http://clientsideurl.tld/logout");

//Will logout both connect and client

```

# 1.3 Is User Exist

```
//Execute 
    $this->magicConnect->isUserExists("EMAIL")

//If exist will return true else false.

```

# 1.4 Create user on connect

```
//Variables
    $email = 'email@domain.tld'
    $firstname = 'Foo'
    $lastname = 'Bar'
    $password = 'worldsmostsecurepassword'

//Execute
    this->magicConnect->createUser($email,$firstname,$lastname,$password);

//If create success wil return true else false

```
