# Project structire
## Front controller
This front controller is [public/app.php](../public/app.php). All requests are served by this file. It creates an instance of [Api\Application](../src/Applcation.php) and calls `run()`. 

## Application and configuration
[Api\Application](../src/Application.php), the main project application, is basically a [Silex](http://silex.sensiolabs.org/) app. It is configured by one of environment-specific [configs](../app/config).
### Dependency Injection
DI configuration is in [app/config/service](../app/config/service). 
* app.php - application-wide settings
* controllers.php - controllers
* email.php - email service
* mappers.php - database mappers
* routing.php - routing configuration
* security.php - user authentication config
* storages.php - databases, key-value storages, etc

### Environments
The application environment is determined by the OS env variable `APP_ENV`. The default environment is `prod`. There are four different environments currently defined.
* dev - local development environment. The VM runs in dev environment.
* test - to run functional tests
* stage - staging envirnoment
* prod - production, the default environment

## Database schema and migrations
Database migration scripts are in [db](../db). The naming convention for migration is `<db_name>.<version>.<direction>.<memo>.sql`
* db_name - the database name, e.g. `main`
* varsion - the migration version, 001 to 999
* direction - the migration direction: *up* (upgrade) or *dn* (downgrade)
* memo - a short description (optional)

To manage the DB, use `bin/db.php`:
* ` APP_ENV=dev bin/db.php st main` - show status of db `main` in `dev` env
* ` APP_ENV=stage bin/db.php up main 123` - update db `main` to version 123 in `stage` env

## Database access layer (mappers)
## Model
## Controllers
