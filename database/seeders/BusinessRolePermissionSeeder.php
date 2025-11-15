<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BusinessRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('business_roles')->insert([
            [
                'name' => 'owner',
                'description' => 'Primary owner with full access',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'partner',
                'description' => 'Partner/co-owner with many privileges',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'staff',
                'description' => 'Internal staff with limited privileges',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        DB::table('business_permissions')->insert([
            [
                'name' => 'admin',
                'description' => 'System administrator for business',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'data_operator',
                'description' => 'Can create and edit transactional data',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'viewer',
                'description' => 'Read-only access to business data',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // Assign permissions to roles
        $rolePermissions = [
            'owner' => ['admin'],
            'partner' => ['data_operator'],
            'staff' => ['data_operator'],
        ];
        foreach ($rolePermissions as $roleName => $permissions) {
            $roleId = DB::table('business_roles')->where('name', $roleName)->value('id');
            foreach ($permissions as $permissionName) {
                $permissionId = DB::table('business_permissions')->where('name', $permissionName)->value('id');
                DB::table('business_role_permission')->insert([
                    'business_role_id' => $roleId,
                    'business_permission_id' => $permissionId,
                ]);
            }
        }
    }
}
