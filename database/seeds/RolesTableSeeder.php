<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\Role([
            'name'          => 'Super Admin',
            'slug'         => 'super-admin' 
        ]);
        $user->save();
        $user->permissions()->sync([1]); // adding permision id 1 on table roles_permissions

        $user = new \App\Role([
            'name'          => 'Administrator',
            'slug'         => 'Administrator' 
        ]);
        $user->save(); 
        $user->permissions()->sync([2,3,4,5]); // adding permision id 2 - 5 on table roles_permissions
        
        $editor = new \App\Role([
            'name'          => 'Editor',
            'slug'         => 'editor' 
        ]);
        $editor->save();
        $editor->permissions()->sync([2,3,4,5]); // adding permision id 2 - 5 on table roles_permissions
 
        $user = new \App\Role([
            'name'          => 'Free',
            'slug'         => 'free-user' 
        ]);
        $user->save();
        $user->permissions()->sync([2,3,4,5]); // adding permision id 2 - 5 on table roles_permissions
        $user = new \App\Role([
            'name'          => 'Basic',
            'slug'         => 'basic-user' 
        ]);
        $user->save();
        $user->permissions()->sync([2,3,4,5]); // adding permision id 2 - 5 on table roles_permissions
        $user = new \App\Role([
            'name'          => 'Advanced',
            'slug'         => 'advanced-user' 
        ]);
        $user->save(); 
        $user->permissions()->sync([2,3,4,5]); // adding permision id 2 - 5 on table roles_permissions
        $user = new \App\Role([
            'name'          => 'Premium',
            'slug'         => 'premium-user' 
        ]);
        $user->save();
        $user->permissions()->sync([2,3,4,5]); // adding permision id 2 - 5 on table roles_permissions
    }
}
