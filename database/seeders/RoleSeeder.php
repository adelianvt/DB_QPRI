<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['MAKER', 'ADMIN', 'APPROVER'] as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role], // key
                ['name' => $role]  // values (boleh sama)
            );
        }
    }
}
