# Authentication token
The client must use an authentication token to get access to protected part of the API. The HTTP `Authorization` header is used to bear the token. Example:

```
Authorization: Token f4091876df6a5d39e6690b7395a95399
```

## Get by email and password
### Request: `POST /token/by-email/{email}`
#### Body: JSON-encoded password
### Response: JSON-encoded token

## Get by facebook oath token
### Request: `POST /token/by-facebook/{fbOathToken}`
### Response: JSON-encoded token
