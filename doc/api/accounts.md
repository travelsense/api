# Account creation
## By email and password
### Request: `POST /user`
#### Body: JSON object with the following fields:

* string email
* string password
* string firstName
* string lastName
* string picture (optional)

This will send an email with confirmation code to the user. The email contains a [link](#email-confirmation) with a confirmation token.
### Response: empty

## By facebook token
The account is created (or updated) automatically when the user [logs in](auth.md) using a FB token.

# Current user info
### Request: `GET /user`
### Response: JSON object
* string email
* string firstName
* string lastName
* string picture

