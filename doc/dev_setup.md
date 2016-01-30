# Development environment setup
## With Vagrant
* install [Vagrant](https://www.vagrantup.com/) and [VitrualBox](https://www.virtualbox.org)
* clone the repo and cd into ```git clone https://github.com/remizorrr/vacarious_web && cd vacarious_web```
* run ```vagrant up```
* make sure you can connect to 172.16.0.101. If not, check VirtualBox network adapter settings
* add to /etc/hosts
```
172.16.0.101	vacarious-dev.com
172.16.0.101	api.vacarious-dev.com
```
* open http://vacarious-dev.com

## With your own hands (for real men only)
* get php (5.6 or higher), postgres (9.0 or higher)
* git clone, composer install, you know
* do the other stuff from [setup.sh](provision/setup.sh)
* ``` cd public && APP_ENV=dev php -S localhost:8000 app.php```
