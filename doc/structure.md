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

## Database schema and migrations
## Database access layer (mappers)
## Model
## Controllers
