#!/bin/bash
### Packages and repos
echo deb http://packages.dotdeb.org jessie all > /etc/apt/sources.list.d/dotdeb.list
echo deb http://apt.postgresql.org/pub/repos/apt/ $(lsb_release -cs)-pgdg main > /etc/apt/sources.list.d/postgresql.list 
wget -qO - https://www.dotdeb.org/dotdeb.gpg | apt-key add -
wget -qO - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
apt-get update
apt-get upgrade -y

### PHP
apt-get purge php5-common -y
apt-get install php7.0-common php7.0-dev php7.0-cli php7.0-fpm curl php7.0-curl php7.0-pgsql php7.0-xdebug -y
for SAPI in cli fpm; do
    cp "/vagrant/provision/config/php/$SAPI/php.ini" "/etc/php/7.0/$SAPI/php.ini"
done

### PostgreSQL
apt-get install postgresql-9.5 postgresql-contrib-9.5 postgresql-9.5-postgis-2.2 -y
cp /vagrant/provision/config/postgres/* /etc/postgresql/9.5/main/
service postgresql restart
sudo -u postgres psql -f /vagrant/provision/db_setup.sql
sudo -u postgres psql -f /vagrant/db/ext/postgis.sql api_dev

### NGiNX
apt-get install nginx -y
rm -f /etc/nginx/sites-available/*
cp /vagrant/provision/config/nginx/api_dev /etc/nginx/sites-available/api_dev
ln -sf /etc/nginx/sites-available/api_dev /etc/nginx/sites-enabled/api_dev
service nginx restart

### Cleanup
apt-get autoremove -y

### Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
cd /vagrant
composer install

### Application set up
APP_ENV=dev php bin/db.php up
