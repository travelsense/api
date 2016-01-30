#!/bin/bash
### Packages and repos
rm -rf /var/lib/apt/lists/* # Hash sum mismatch fix
add-apt-repository ppa:ondrej/php5-5.6 -y
apt-get update
apt-get upgrade -y

### PHP
apt-get install php5-common php5-dev php5-cli php5-fpm curl php5-curl php5-pgsql -y
ln -sf /etc/php5/mods-available/pdo_pgsql.ini /etc/php5/cli/conf.d/pdo_pgsql.ini
ln -sf /etc/php5/mods-available/pdo_pgsql.ini /etc/php5/fpm/conf.d/pdo_pgsql.ini
for SAPI in cli fpm;
 do
    cp "/vagrant/provision/config/php/$SAPI/php.ini" "/etc/php5/$SAPI/php.ini"
done

### Composer
php -r "readfile('https://getcomposer.org/installer');" | php
sudo mv composer.phar /usr/local/bin/composer
composer install

### PostgreSQL
apt-get install postgresql postgresql-contrib -y
HBA=`sudo -u postgres psql -t -P format=unaligned -c 'show hba_file';`
cp /vagrant/provision/config/postgres/pg_hba.conf $HBA
service postgresql restart
sudo -u postgres psql -f /vagrant/provision/db_setup.sql

### NGiNX
apt-get install nginx -y
rm -f /etc/nginx/sites-available/vaca_dev
cp /vagrant/provision/config/nginx/vaca_dev /etc/nginx/sites-available/vaca_dev
ln -sf /etc/nginx/sites-available/vaca_dev /etc/nginx/sites-enabled/vaca_dev
service nginx restart
