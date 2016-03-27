#!/bin/bash
### Packages and repos
echo deb http://packages.dotdeb.org jessie all >> /etc/apt/sources.list
echo deb-src http://packages.dotdeb.org jessie all >> /etc/apt/sources.list 
wget https://www.dotdeb.org/dotdeb.gpg
sudo apt-key add dotdeb.gpg
apt-get update

### PHP
apt-get install php7.0-common php7.0-dev php7.0-cli php7.0-fpm curl php7.0-curl php7.0-pgsql php7.0-xdebug -y
for SAPI in cli fpm;
 do
    cp "/vagrant/provision/config/php/$SAPI/php.ini" "/etc/php/7.0/$SAPI/php.ini"
done


### Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
cd /vagrant
composer install

### PostgreSQL
wget -quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add –
echo deb http://apt.postgresql.org/pub/repos/apt/ $(lsb_release -cs)-pgdg main >> /etc/apt/sources.list
apt-get update && apt-get upgrade
apt-get install postgresql-9.5 postgresql-contrib-9.5 --yes --force-yes
cp /vagrant/provision/config/postgres/* /etc/postgresql/9.5/main/
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
