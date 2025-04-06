REST API for posts with JWT authentication.

Instalation:
------
```bash
$git clone https://github.com/Grazvydas-M/crud_api_with_auth.git
$cd crud_api_with_auth
```
Run the project:

```bash
$ docker compose up -d
```
Enter php container:
```bash
docker exec -it symfony_php bash
composer install
```
Create database and run migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Generate JWT keys:
```bash
php bin/console lexik:jwt:generate-keypair
```


Login:
To get JWT token make POST request to **/api/login_check**.
Request body:
```bash
{
  "username": "admin",
  "password": "123456"
}
```
To test endpoints use authorization **Bearer Token** and your generated jwt token.

Examples:
------
Create:
```bash
 Request: POST /api/posts
  Body: 
    {
    "title": "first title",
    "content": "first content"
    }

  Response:
  {
    "id": 3,
    "title": "first title",
    "content": "first content",
    "updatedAt": "2025-04-06T10:44:24+00:00",
    "createdAt": "2025-04-06T10:44:24+00:00"
  }
```
Get list of posts:
```bash
  Request: GET /api/posts?page=1&limit=3.
  Params: page = 1, limit per page 3.
  Returns 3 most recent posts.

  Response:
[
    {
        "id": 15,
        "title": "newest title",
        "content": "newest content",
        "updatedAt": "2025-04-06T10:57:19+00:00",
        "createdAt": "2025-04-06T10:57:19+00:00"
    },
    {
        "id": 14,
        "title": "random title",
        "content": "random content",
        "updatedAt": "2025-04-06T10:57:09+00:00",
        "createdAt": "2025-04-06T10:57:09+00:00"
    },
    {
        "id": 13,
        "title": "first title",
        "content": "first content",
        "updatedAt": "2025-04-06T10:50:53+00:00",
        "createdAt": "2025-04-06T10:50:53+00:00"
    }
]
```
