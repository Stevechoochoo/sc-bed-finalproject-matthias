# Kahuna Setup

## Requirements

- Docker Desktop
- Postman or any other API client
- Any browser

## Running the Project

1. Open Docker Desktop.
2. Open a terminal in the project folder.
3. Start the project:

```bash
.\run.cmd
```

4. Open the client in your browser:

```text
http://localhost:8000
```

The API is available at:

```text
http://localhost:8000/kahuna/api
```

## Database

The project uses MariaDB.

```text
Host: mariadb
Database: kahuna
User: root
Password: root
```

The database tables are defined in:

```text
db/db.sql
```

## API Usage

Use `form-data` in Postman for `POST` requests.

### Register User

```text
POST http://localhost:8000/kahuna/api/user
```

Body:

```text
name
surname
email
password
```

### Login

```text
POST http://localhost:8000/kahuna/api/login
```

Body:

```text
email
password
```

### Logout

```text
POST http://localhost:8000/kahuna/api/logout
```

Headers:

```text
X-Api-User
X-Api-Key
```

### Register Product

```text
POST http://localhost:8000/kahuna/api/product
```

Headers:

```text
X-Api-User
X-Api-Key
```

Body:

```text
serial_number
purchase_date
```

Example serial number:

```text
KHWM8199911
```

### View Registered Products

```text
GET http://localhost:8000/kahuna/api/registered-products
```

Headers:

```text
X-Api-User
X-Api-Key
```

### View One Registered Product

```text
GET http://localhost:8000/kahuna/api/registered-product
```

Headers:

```text
X-Api-User
X-Api-Key
```

Params:

```text
serial_number
```

### Admin Add Product

This endpoint only works for users with `role = 'admin'`.

```text
POST http://localhost:8000/kahuna/api/admin-product
```

Headers:

```text
X-Api-User
X-Api-Key
```

Body:

```text
serial_number
product_name
warranty
```

To change a user to admin:

```sql
UPDATE Users
SET role = 'admin'
WHERE email = 'admin@email.com';
```

Then log in again.

## Client Pages

```text
http://localhost:8000/register
http://localhost:8000/login
http://localhost:8000/product
http://localhost:8000/products
http://localhost:8000/product-details
```