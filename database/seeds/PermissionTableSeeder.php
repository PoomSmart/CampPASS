<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // role management
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // camp management
            'camp-list',
            'camp-create',
            'camp-edit',
            'camp-delete',

            // camper management
            'camper-list',
            'camper-create',
            'camper-edit',
            'camper-delete',

            // camp maker management
            'campmaker-list',
            'campmaker-create',
            'campmaker-edit',
            'campmaker-delete',

            // candidate management
            'candidate-list',
            'candidate-edit',
            'candidate-delete',

            // school management
            'school-list',
            'school-create',
            'school-edit',
            'school-delete',

            // organization management
            'org-list',
            'org-create',
            'org-edit',
            'org-delete',

            // question management
            'question-list',
            'question-create',
            'question-edit',
            'question-delete',

            // answer management
            'ans-list',
            'ans-create',
            'ans-edit',
            'ans-delete',

            // badge management
            'badge-list',
            'badge-create',
            'badge-edit',
            'badge-delete',

            // certificate management
            'cert-list',
            'cert-create',
            'cert-edit',
            'cert-delete',

            // certificate template management
            'certtemp-list',
            'certtemp-create',
            'certtemp-edit',
            'certtemp-delete',

            // payment slip management
            'pay-list',
            'pay-edit',
            'pay-delete',
        ];
 
 
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
        
        $admin = User::where('type', config('const.account.admin'))->limit(1)->first();
        $admin->assignRole('admin');
        $admin->save();
    }
}
