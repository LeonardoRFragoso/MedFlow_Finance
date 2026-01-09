<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Upload
            ['name' => 'upload.create', 'description' => 'Criar upload', 'resource' => 'uploads', 'action' => 'create'],
            ['name' => 'upload.read', 'description' => 'Visualizar uploads', 'resource' => 'uploads', 'action' => 'read'],
            ['name' => 'upload.delete', 'description' => 'Deletar uploads', 'resource' => 'uploads', 'action' => 'delete'],

            // Records
            ['name' => 'record.read', 'description' => 'Visualizar registros', 'resource' => 'records', 'action' => 'read'],
            ['name' => 'record.update', 'description' => 'Atualizar registros', 'resource' => 'records', 'action' => 'update'],

            // Reports
            ['name' => 'report.create', 'description' => 'Criar relatórios', 'resource' => 'reports', 'action' => 'create'],
            ['name' => 'report.read', 'description' => 'Visualizar relatórios', 'resource' => 'reports', 'action' => 'read'],
            ['name' => 'report.export', 'description' => 'Exportar relatórios', 'resource' => 'reports', 'action' => 'export'],

            // Users
            ['name' => 'user.create', 'description' => 'Criar usuários', 'resource' => 'users', 'action' => 'create'],
            ['name' => 'user.read', 'description' => 'Visualizar usuários', 'resource' => 'users', 'action' => 'read'],
            ['name' => 'user.update', 'description' => 'Atualizar usuários', 'resource' => 'users', 'action' => 'update'],
            ['name' => 'user.delete', 'description' => 'Deletar usuários', 'resource' => 'users', 'action' => 'delete'],

            // Settings
            ['name' => 'settings.read', 'description' => 'Visualizar configurações', 'resource' => 'settings', 'action' => 'read'],
            ['name' => 'settings.update', 'description' => 'Atualizar configurações', 'resource' => 'settings', 'action' => 'update'],

            // Audit
            ['name' => 'audit.read', 'description' => 'Visualizar logs de auditoria', 'resource' => 'audit', 'action' => 'read'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Atribuir permissões aos roles
        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles(): void
    {
        $admin = Role::where('name', 'admin')->first();
        $financialManager = Role::where('name', 'financial_manager')->first();
        $administrative = Role::where('name', 'administrative')->first();
        $viewer = Role::where('name', 'viewer')->first();

        // Admin: todas as permissões
        if ($admin) {
            $allPermissions = Permission::pluck('id')->toArray();
            $admin->permissions()->sync($allPermissions);
        }

        // Financial Manager: Upload, Records, Reports
        if ($financialManager) {
            $permissions = Permission::whereIn('resource', ['uploads', 'records', 'reports'])
                ->whereIn('action', ['create', 'read', 'update', 'export'])
                ->pluck('id')
                ->toArray();
            $financialManager->permissions()->sync($permissions);
        }

        // Administrative: Upload, Records (create, read)
        if ($administrative) {
            $permissions = Permission::whereIn('resource', ['uploads', 'records'])
                ->whereIn('action', ['create', 'read'])
                ->pluck('id')
                ->toArray();
            $administrative->permissions()->sync($permissions);
        }

        // Viewer: read-only
        if ($viewer) {
            $permissions = Permission::where('action', 'read')
                ->pluck('id')
                ->toArray();
            $viewer->permissions()->sync($permissions);
        }
    }
}
