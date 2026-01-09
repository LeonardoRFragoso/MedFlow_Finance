<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('read', User::class);

        $query = User::where('clinic_id', auth()->user()->clinic_id);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->respondPaginated($users, 'Usuários listados com sucesso');
    }

    public function show($id)
    {
        $this->authorize('read', User::class);

        $user = User::where('clinic_id', auth()->user()->clinic_id)
            ->with('roles', 'permissions')
            ->findOrFail($id);

        return $this->respondSuccess([
            'user' => $user,
            'roles' => $user->roles()->pluck('name'),
            'permissions' => $user->permissions()->pluck('name'),
        ], 'Usuário recuperado com sucesso');
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|exists:roles,name',
        ]);

        $clinic = auth()->user()->clinic;

        // Validar limite de usuários
        $userCount = User::where('clinic_id', $clinic->id)->count();
        if ($userCount >= $clinic->max_users) {
            return $this->respondError(
                'Limite de usuários atingido para este plano',
                422
            );
        }

        // Criar usuário
        $user = User::create([
            'clinic_id' => $clinic->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Atribuir role
        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $user->roles()->attach($role->id, ['clinic_id' => $clinic->id]);
        }

        return $this->respondSuccess([
            'user' => $user,
            'roles' => $user->roles()->pluck('name'),
        ], 'Usuário criado com sucesso', 201);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', User::class);

        $user = User::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'sometimes|in:active,inactive,suspended',
            'role' => 'sometimes|string|exists:roles,name',
        ]);

        $user->update($validated);

        // Atualizar role se fornecido
        if ($request->has('role')) {
            $role = Role::where('name', $validated['role'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }

        return $this->respondSuccess([
            'user' => $user,
            'roles' => $user->roles()->pluck('name'),
        ], 'Usuário atualizado com sucesso');
    }

    public function destroy($id)
    {
        $this->authorize('delete', User::class);

        $user = User::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        // Não permitir deletar o último admin
        if ($user->hasRole('admin')) {
            $adminCount = User::where('clinic_id', auth()->user()->clinic_id)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                })
                ->count();

            if ($adminCount <= 1) {
                return $this->respondError(
                    'Não é possível deletar o último administrador',
                    422
                );
            }
        }

        $user->delete();

        return $this->respondSuccess(null, 'Usuário deletado com sucesso');
    }

    public function assignRole(Request $request, $id)
    {
        $this->authorize('update', User::class);

        $user = User::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $role = Role::where('name', $validated['role'])->first();
        $clinic = auth()->user()->clinic;

        $user->roles()->syncWithoutDetaching([$role->id]);

        return $this->respondSuccess([
            'user' => $user,
            'roles' => $user->roles()->pluck('name'),
        ], 'Role atribuído com sucesso');
    }

    public function removeRole(Request $request, $id)
    {
        $this->authorize('update', User::class);

        $user = User::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $role = Role::where('name', $validated['role'])->first();

        // Não permitir remover o último admin
        if ($validated['role'] === 'admin') {
            $adminCount = User::where('clinic_id', auth()->user()->clinic_id)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                })
                ->count();

            if ($adminCount <= 1) {
                return $this->respondError(
                    'Não é possível remover o último administrador',
                    422
                );
            }
        }

        $user->roles()->detach($role->id);

        return $this->respondSuccess([
            'user' => $user,
            'roles' => $user->roles()->pluck('name'),
        ], 'Role removido com sucesso');
    }
}
