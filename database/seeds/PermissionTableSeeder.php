<?php

use App\User;
use App\Common;

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
            'camp-approve',

            // general user management
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

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
            'organization-list',
            'organization-create',
            'organization-edit',
            'organization-delete',

            // question management
            'question-list',
            'question-create',
            'question-edit',
            'question-delete',

            // answer management
            'answer-list',
            'answer-create',
            'answer-edit',
            'answer-grade',
            'answer-delete',

            // badge management
            'badge-list',
            'badge-create',
            'badge-edit',
            'badge-delete',

            // certificate management
            'certificate-list',
            'certificate-create',
            'certificate-edit',
            'certificate-delete',

            // certificate template management
            'certificate-template-list',
            'certificate-template-create',
            'certificate-template-edit',
            'certificate-template-delete',

            // payment slip management
            'payment-list',
            'payment-create',
            'payment-edit',
            'payment-delete',
            'payment-status',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $this->setAdmin();
        $this->setCamper();
        $this->setCampMaker();
    }

    public function setAdmin()
    {
        $role = Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());
        $admin = Common::admin();
        $admin->assignRole('admin');
        $admin->save();
    }

    public function setCamper()
    {
        $role = Role::create(['name' => 'camper']);
        $role->givePermissionTo([
            // campers can view all camps
            'camp-list',
            // campers can edit their profile
            'camper-edit',
            'camper-delete',
            // campers can see questions
            'question-list',
            // campers can manage their answers
            'answer-list',
            'answer-create',
            'answer-edit',
            'answer-delete',
            // campers can view their badges
            'badge-list',
            // campers can view their certificates
            'certificate-list',
            // campers can manage their payment slips
            'payment-create',
            'payment-edit',
            'payment-delete',
        ]);
        foreach (User::campers()->cursor() as $camper) {
            $camper->assignRole('camper');
            $camper->save();
        }
    }

    public function setCampMaker()
    {
        $role = Role::create(['name' => 'campmaker']);
        $role->givePermissionTo([
            // camp makers can manage camps
            'camp-list',
            'camp-create',
            'camp-edit',
            // camp makers can see the list of campers
            'camper-list',
            // camp makers can manage their account
            'campmaker-list',
            'campmaker-create',
            'campmaker-edit',
            'campmaker-delete',
            // camp makers can manage their candidate list
            'candidate-list',
            'candidate-edit',
            'candidate-delete',
            // camp makers can manage their questions
            'question-list',
            'question-create',
            'question-edit',
            'question-delete',
            // camp makers can view answers from campers
            'answer-list',
            'answer-grade',
            // camp makers can see badges of campers
            'badge-list',
            // camp makers can see certificates of campers
            'certificate-list',
            // camp makers can view and set status of payment slips
            'payment-list',
            'payment-status',
        ]);
        foreach (User::campMakers()->cursor() as $campmaker) {
            $campmaker->assignRole('campmaker');
            $campmaker->save();
        }
    }
}
