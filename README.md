# API
We are using JSON RESTful(ish) API.

* [Authentication](doc/api/auth.md)
* [Accounts](doc/api/accounts.md)
* [Travels](doc/api/travels.md)

# Dev environment set up

####Install process for Ubuntu

####Server setup
* Install & setup Apache, PHP 5.5+ with postgres module:

 ```
 sudo apt-get install php5-pgsql
 ```
* Install postgres. For example install process for version 9.3, you can used latest.
* <b>pgadmin3</b> - free editor for postgres.

 ```
 sudo apt-get install postgresql-9.3 pgadmin3
 ```
 
####Setup postrgres

```
sudo -i -u postgres
```
* Then create new user for postgres:
```
createuser --interactive
```
* Enter name "vaca" , then n,n,n
* Connect to postrgres with admin rules for change user psw&privileges
```
psql postgres -U postgres
```
* In psql run sql query for change psw, create new db add rules for user vaca:
```sql
alter user vaca password 'vaca';
createdb vaca;
grant all on DATABASE vaca to vaca;
```
* Exit from psql ``` \q ``` and reconnect from user ``` vaca ``` for test connection:
```
psql vaca  -h 127.0.0.1 -d vaca
```
####Setup server app
* Clone git repo, open folder, install composers packages:
```
php composer.phar install
```
* Import schema to "vaca" db from path: "schema/main.install.sql"

* For run app with <a href="http://silex.sensiolabs.org/">Silex</a> enter in terminal:
```
APP_ENV=dev php -S localhost:8000 public/app.php
```
* Open in browser http://localhost:8000
* Debug/errors showing in terminal.

#### Branching <a href="http://nvie.com/posts/a-successful-git-branching-model/">model</a> 
