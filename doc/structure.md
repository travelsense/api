# Project structure
## Front controller
This front controller is [public/app.php](../public/app.php). All HTTP requests are served by this file. It creates an instance of [Api\Application](../src/Application.php) and calls `run()`. 

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
* stage - staging environment
* prod - production, the default environment

## Database schema and migrations
Database migration scripts are in [db](../db). The naming convention for migration is `<db_name>/migration.php`

To manage the DB, use `bin/db.php`:

Database is managed by a slightly tailored [Doctrine Migraitons](http://www.doctrine-project.org/projects/migrations.html).

## Mappers and Models
* [Mappers](../src/Mapper)
* [Model](../src/Model)

Mappers is the layer responsible for CRUD operations on models. The storage-specific logic (e.g. SQL statements) must reside in mappers. The database mappers is a thin layer on top of PDO. Mappers may operate entire model object as well as their ids. To keep it simple, there can be mappers without models and models without corresponding mappers.

## Controllers and routing
Refer to [Silex routing documentation](http://silex.sensiolabs.org/doc/master/usage.html) to get the general idea. The routing is configured in [routing.php](../app/config/service/routing.php). The route parameters and HTTP GET parameters are passed to controller methods as arguments.

## Access Rights Management

See [Access](../src/Security/Access).
Actors (ActorInterface) can perform Actions (Action) on Subjects (Subject). 
The Access Manager (Manager) decides if a certain action is permitted.

# Servers
* API production: https://api.hoptrip.us
* API stage: http://api.stage.hoptrip.us:2280/
* Web production: https://hoptrip.us
