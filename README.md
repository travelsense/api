# API
We are using JSON ReSTful(ish) API.

## Account creation

### By email and password
#### Request `POST` a new user object to `/user`:
* string email
* string password
* string firstName
* string lastName
* string picture (optional)

This will send an email with confirnation code to the user

#### Response is empty

