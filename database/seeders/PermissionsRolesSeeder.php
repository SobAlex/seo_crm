<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Очищаем кэш прав
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========== ПРАВА (Permissions) ==========
        $permissions = [
            // Клиенты
            'view clients', 'create clients', 'edit clients', 'delete clients',
            // Проекты
            'view projects', 'create projects', 'edit projects', 'delete projects',
            // Сайты
            'view websites', 'create websites', 'edit websites', 'delete websites',
            // Треки
            'view tracks', 'create tracks', 'edit tracks', 'delete tracks',
            // Задачи
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            // Планирование
            'view plannings', 'create plannings', 'edit plannings', 'delete plannings',
            // Отчёты
            'view reports', 'create reports', 'delete reports',
            // Комментарии
            'view comments', 'create comments', 'delete comments',
            // Управление
            'manage users', 'manage contractors', 'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // ========== РОЛИ (Roles) ==========
        // Супер-админ (все права)
        $superAdmin = Role::findOrCreate('super_admin');
        $superAdmin->givePermissionTo(Permission::all());

        // Владелец компании (почти всё, кроме управления ролями)
        $owner = Role::findOrCreate('owner');
        $owner->givePermissionTo([
            'view clients', 'create clients', 'edit clients', 'delete clients',
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view websites', 'create websites', 'edit websites', 'delete websites',
            'view tracks', 'create tracks', 'edit tracks', 'delete tracks',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'view plannings', 'create plannings', 'edit plannings', 'delete plannings',
            'view reports', 'create reports', 'delete reports',
            'view comments', 'create comments', 'delete comments',
            'manage users', 'manage contractors',
        ]);

        // Администратор (все права, кроме удаления компании и управления ролями)
        $admin = Role::findOrCreate('admin');
        $admin->givePermissionTo([
            'view clients', 'create clients', 'edit clients', 'delete clients',
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view websites', 'create websites', 'edit websites', 'delete websites',
            'view tracks', 'create tracks', 'edit tracks', 'delete tracks',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'view plannings', 'create plannings', 'edit plannings', 'delete plannings',
            'view reports', 'create reports', 'delete reports',
            'view comments', 'create comments', 'delete comments',
            'manage users', 'manage contractors',
        ]);

        // Сотрудник (базовые права)
        $employee = Role::findOrCreate('employee');
        $employee->givePermissionTo([
            'view clients', 'view projects', 'view websites',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'view comments', 'create comments',
            'view reports',
        ]);

        // Подрядчик (минимальные права)
        $contractor = Role::findOrCreate('contractor');
        $contractor->givePermissionTo([
            'view tasks', 'view comments', 'create comments',
        ]);
    }
}
