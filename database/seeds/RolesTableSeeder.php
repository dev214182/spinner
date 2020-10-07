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
        $user = new \App\Role([
            'name'          => 'Administrator',
            'slug'         => 'Administrator' 
        ]);
        $user->save();
        $user = new \App\Role([
            'name'          => 'Editor',
            'slug'         => 'editor' 
        ]);
        $user->save();
        $user = new \App\Role([
            'name'          => 'Free',
            'slug'         => 'free-user' 
        ]);
        $user->save();
        $user = new \App\Role([
            'name'          => 'Basic',
            'slug'         => 'basic-user' 
        ]);
        $user->save();
        $user = new \App\Role([
            'name'          => 'Advanced',
            'slug'         => 'advanced-user' 
        ]);
        $user->save();
        $user = new \App\Role([
            'name'          => 'Premium',
            'slug'         => 'premium-user' 
        ]);
        $user->save();
 
    }
}
