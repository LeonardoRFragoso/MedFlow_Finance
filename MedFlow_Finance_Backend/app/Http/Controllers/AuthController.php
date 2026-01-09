<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Usuário inativo.'],
            ]);
        }

        // Atualizar last_login_at
        $user->update(['last_login_at' => now()]);

        // Gerar token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respondSuccess([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'clinic_id' => $user->clinic_id,
                'roles' => $user->roles()->pluck('name'),
                'permissions' => $user->permissions()->pluck('name'),
            ],
            'clinic' => [
                'id' => $user->clinic->id,
                'name' => $user->clinic->name,
                'cnpj' => $user->clinic->cnpj,
            ],
            'token' => $token,
        ], 'Login realizado com sucesso', 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->respondSuccess(null, 'Logout realizado com sucesso', 200);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();

        // Revogar token antigo
        $user->currentAccessToken()->delete();

        // Gerar novo token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respondSuccess([
            'token' => $token,
        ], 'Token renovado com sucesso', 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return $this->respondSuccess([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar_url' => $user->avatar_url,
                'clinic_id' => $user->clinic_id,
                'status' => $user->status,
                'last_login_at' => $user->last_login_at,
                'roles' => $user->roles()->pluck('name'),
                'permissions' => $user->permissions()->pluck('name'),
            ],
            'clinic' => [
                'id' => $user->clinic->id,
                'name' => $user->clinic->name,
                'cnpj' => $user->clinic->cnpj,
                'status' => $user->clinic->status,
                'plan_type' => $user->clinic->plan_type,
            ],
        ], 'Dados do usuário', 200);
    }
}
