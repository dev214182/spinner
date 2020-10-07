<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\User([
            'name'          => 'Romel Indemne',
            'email'         => 'admin@admin.com',
            'password'      => Hash::make('gag@112211'),
            'phone'         => '05012345678',
            'role'          => 1,
            'status'        => true,
            'company_id'    => 1,
        ]);
        $user->save();
        $user->roles()->sync([1]); // adding role_id 1 on table users_roles
        $user->permissions()->sync([1]);

        $user = new \App\User([
            'name'          => 'Steve Ayala',
            'email'         => 'jacob@gagroup.net',
            'password'      => Hash::make('gag@112211'),
            'phone'         => '05012345678',
            'role'          => 1,
            'status'        => true,
            'company_id'    => 1,
        ]);
        $user->save();
        $user->roles()->sync([2]); // adding role_id 2 on table users_roles   
        $user->permissions()->sync([2,3,4,5]); // adding permision id 2 - 5 on table roles_permissions

        $user = new \App\User([
            'name'          => 'Editor Account',
            'email'         => 'editor@editor.com',
            'password'      => Hash::make('gag@112211'),
            'phone'         => '05012345678',
            'role'          => 4,
            'status'        => true,
            'company_id'    => 1,
        ]);
        $user->save();
        $user->roles()->sync([3]); // adding role_id 3 on table users_roles    
        $user->permissions()->sync([2,3,4,5]); // adding permision id 2 - 5 on table roles_permissions
         
    }
}
