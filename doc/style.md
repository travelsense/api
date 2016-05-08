#Code style
## PHP
PHP code relies heavily on [PSR standards](http://www.php-fig.org/psr/), 
particularly on [PSR-2](http://www.php-fig.org/psr/psr-2/) 
and proposed [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md),
with the following clarifications:
* Valiable names must be in `lowerCamelCase`
* Array keys (including configuration) must be in `lower_snake_case`
* Silex DI Container keys must follow this format: `section_name.subsection_name.long_key_name` eg `controller.user`

## Database schema
* table and database names must be in `lower_snake_case`
* table names must be plural, e.g. users, sessions
