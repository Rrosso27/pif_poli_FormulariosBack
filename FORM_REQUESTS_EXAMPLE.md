# Versión alternativa del AuthController usando Form Requests

Si prefieres usar Form Requests para una mejor organización, puedes actualizar tu AuthController:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        // La validación ya se hizo automáticamente por RegisterRequest
        // donde 'name' ES requerido
        
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login user and create token
     */
    public function login(LoginRequest $request)
    {
        // La validación ya se hizo automáticamente por LoginRequest
        // donde 'name' NO es requerido, solo email y password
        
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->input('email'))->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // ... resto de métodos igual
}
```

## Ventajas de usar Form Requests:

1. **Separación de responsabilidades**: La validación está separada del controlador
2. **Reutilización**: Puedes reutilizar las validaciones en otros lugares
3. **Mensajes personalizados**: Fácil configuración de mensajes de error
4. **Autorización**: Puedes agregar lógica de autorización específica
5. **Limpieza**: El controlador queda más limpio y enfocado en la lógica de negocio
