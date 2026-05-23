# Ejemplos de uso de la API de Autenticación con Laravel Sanctum

## 1. Registro de Usuario (name ES requerido)

```bash
POST /api/register
Content-Type: application/json

{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Respuesta exitosa:**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "Juan Pérez",
        "email": "juan@example.com",
        "created_at": "2025-07-20T10:00:00.000000Z",
        "updated_at": "2025-07-20T10:00:00.000000Z"
    },
    "access_token": "1|laravel_sanctum_token_here",
    "token_type": "Bearer"
}
```

## 2. Login de Usuario (name NO es requerido)

```bash
POST /api/login
Content-Type: application/json

{
    "email": "juan@example.com",
    "password": "password123"
}
```

**Respuesta exitosa:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Juan Pérez",
        "email": "juan@example.com",
        "created_at": "2025-07-20T10:00:00.000000Z",
        "updated_at": "2025-07-20T10:00:00.000000Z"
    },
    "access_token": "2|another_sanctum_token_here",
    "token_type": "Bearer"
}
```

## 3. Obtener información del usuario autenticado

```bash
GET /api/user
Authorization: Bearer your_token_here
```

## 4. Logout (cerrar sesión actual)

```bash
POST /api/logout
Authorization: Bearer your_token_here
```

## 5. Logout de todos los dispositivos

```bash
POST /api/logout-all
Authorization: Bearer your_token_here
```

## Errores de Validación

### Error al registrar sin name:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

### Error al hacer login sin email:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

## Cómo usar el token

Después de registrarte o hacer login, incluye el token en el header Authorization:

```
Authorization: Bearer 1|laravel_sanctum_token_here
```
