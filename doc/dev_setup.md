# Development environment setup
## The workflow
First of all, [fork](https://help.github.com/articles/fork-a-repo/) the repo and [clone](https://help.github.com/articles/cloning-a-repository/) it to your local machine. 

The development cycle is similar to [Contributing to Open Source]( https://guides.github.com/activities/contributing-to-open-source/):
* Get an assignment to work on
* Create a dedicated branch in your fork
* Commit your work and push the changes
* Open a pull request to the main project 

## Getting Vagrant running
* install [Vagrant](https://www.vagrantup.com/). Do **not** run `vagrant init`.
* install [VitrualBox](https://www.virtualbox.org). Do **not** install any OS into VirtualBox.
* open a console and `cd` into the project root folder
* run `vagrant plugin install vagrant-vbguest` to install Guest Additions plugin
* run `vagrant up` and wait till it finishes
* open http://172.16.0.101/healthCheck in a browser, the page should show a JSON response

## Troubleshooting
* On some Linux boxes Vagrant does not always set the network up automatically. You may need to do something similar to `sudo ip link set vboxnet0 up && sudo ip addr add 172.16.0.1/24 dev vboxnet0` to enable networking.

## Getting Database access
PostgreSQL server is listening on all interfaces at port 5432. There are 2 databases available:

* `api_dev` (user `api_dev`, password `api_dev`) - regular development database
* `api_test` (user `api_test`, password `api_test`) - used by unit/functional tests

PostgreSQL superuser account name is `postgres`, password `postgres`.  
To connect the database from the VM, use [psql](http://www.postgresql.org/docs/current/static/app-psql.html) 
e.g. `psql -U api_dev -h localhost api_dev`. To connect from your local box, use your [favorite client](https://wiki.postgresql.org/wiki/Community_Guide_to_PostgreSQL_GUI_Tools) and connect to 172.16.0.101:5432.

## Making API calls
Use [ApiClient](../src/Test/ApiClient.php) to make calls to the API. Consider the following example.
Create a file named `example.php` in the project root:

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
$api = new \Api\Test\ApiClient('http://172.16.0.101');

try {
    $api->registerUser([
        'firstName' => 'John',
        'lastName' => 'Smith',
        'email' => 'john@example.com',
        'password' => '123',
    ]);
} catch (\Api\Test\ApiClientException $e) {
    if ($e->getCode() === \Api\Exception\ApiException::USER_EXISTS) {
        echo "User already registered\n";
    } else {
        throw $e;
    }
}

$token = $api->getTokenByEmail('john@example.com', '123');

echo "Auth token: $token\n";

$api->setAuthToken($token);

var_dump($api->getCurrentUser());
```

Run it using console:

```
$ php example.php 
Auth token: a8ffb63291f06796f0098e6b5903e950c2bdc42611

class stdClass#5 (6) {
  public $id =>
  int(2)
  public $email =>
  string(16) "john@example.com"
  public $picture =>
  string(0) ""
  public $firstName =>
  string(4) "John"
  public $lastName =>
  string(5) "Smith"
  public $created =>
  string(25) "2016-05-21T01:11:38+00:00"
}
```

## SSH'ing into VM
To ssh into the Virtual Machine you can 
* connect to `172.16.0.101:22` using an [ssh client](https://en.wikipedia.org/wiki/Comparison_of_SSH_clients)
* or just run `vagrant ssh` from the project root folder

## Debugging
The following VM logs can be used to debug:
* `/var/log/nginx/access.log` - access log
* `/var/log/nginx/error.log` - error log, contains fatal php errors
* `/tmp/api_dev.log` - application log

## Testing

* Unit tests:`vendor/bin/phpunit --testsuite unit`
* Integration tests:`vendor/bin/phpunit --testsuite integration` (require VM or DB)
* Functional tests:`vendor/bin/phpunit --testsuite functional` (require VM or DB)
* All tests:`vendor/bin/phpunit` (require VM or DB)
