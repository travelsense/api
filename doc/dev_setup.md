# Development environment setup
## With Vagrant
* install [Vagrant](https://www.vagrantup.com/) and [VitrualBox](https://www.virtualbox.org)
* clone the repo and cd into ```git clone https://github.com/remizorrr/vacarious_web && cd vacarious_web```
* run ```vagrant up```
* make sure you can connect to 172.16.0.101. If not, check VirtualBox network adapter settings
* add the following to /etc/hosts
```
172.16.0.101	vacarious-dev.com
172.16.0.101	api.vacarious-dev.com
```
* open http://vacarious-dev.com

## Like real men do
* get php (5.6 or higher), postgres (9.0 or higher). No Web server is required to run the API
* git clone, composer install, you know
* do some other stuff from [setup.sh](provision/setup.sh)
* ``` cd public && APP_ENV=dev php -S localhost:8000 app.php```
