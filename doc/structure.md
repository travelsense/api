# Project structire
## Application and configuration
The main application class extends Silex\Application. All the project configuration is in [app/config](../app/config).
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

## Front controller
## Database schema and migrations
## Database access layer (mappers)
## Model
## Controllers
