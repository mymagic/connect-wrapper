# Getting Started

```
composer install
```

# Usage

```
Wrapper::init("http://theclienturl/); // Initialize the wrapper, throws exception if user is not logged in
Wrapper::getUserData(); // {JSON} returns logged in user data
Wrapper::isUserLoggedIn(); // {Boolean} returns true if user is logged in, false if otherwise
Wrapper::$client_url; // {String} gets the client url
```
