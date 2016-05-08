# Development environment setup
## With Vagrant
* install [Vagrant](https://www.vagrantup.com/) and [VitrualBox](https://www.virtualbox.org)
* clone the repo and cd into ```git clone https://github.com/travelsense/api && cd api```
* run ```vagrant up```
* open (http://172.16.0.101/healthCheck) in a browser, the page should show a JSON response
### Troubleshooting
* On some OS Vagrant does not always set the network up automatically. You may need to do something similar to `sudo ip link set vboxnet0 up` and `sudo ip addr add 172.16.0.1/24 dev vboxnet0` to enable networking.


## Like a boss 
* refer to [setup.sh](provision/setup.sh) to see the packages and configs
* ``` cd public && APP_ENV=dev php -S localhost:8000 app.php```
