#!/bin/bash
### Packages and repos
add-apt-repository ppa:ondrej/php5-5.6 -y
apt-get update
apt-get upgrade -y

### PHP
apt-get install php5-common php5-dev php5-cli php5-fpm curl php5-curl php5-pgsql php5-xdebug -y
for SAPI in cli fpm;
 do
    cp "/vagrant/provision/config/php/$SAPI/php.ini" "/etc/php5/$SAPI/php.ini"
done


### Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
cd /vagrant
composer install

### PostgreSQL
apt-get install postgresql-9.3 postgresql-contrib-9.3 -y
cp /vagrant/provision/config/postgres/* /etc/postgresql/9.3/main/
service postgresql restart
sudo -u postgres psql -f /vagrant/provision/db_setup.sql
apt-get install phppgadmin

### NGiNX
apt-get install nginx -y
rm -f /etc/nginx/sites-available/vaca_dev
cp /vagrant/provision/config/nginx/vaca_dev /etc/nginx/sites-available/vaca_dev
ln -sf /etc/nginx/sites-available/vaca_dev /etc/nginx/sites-enabled/vaca_dev
service nginx restart

### Application set up
APP_ENV=dev php bin/db.php up
