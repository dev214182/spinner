<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\Permission([
            'name'          => 'All Permission',
            'slug'         => 'all-permission' 
        ]);
        $user->save();
        $user = new \App\Permission([
            'name'          => 'Can Create',
            'slug'         => 'c-only' 
        ]);
        $user->save();
        $user = new \App\Permission([
            'name'          => 'Can Update',
            'slug'         => 'u-only' 
        ]);
        $user->save();

        $user = new \App\Permission([
            'name'          => 'Viewing Only',
            'slug'         => 'v-only' 
        ]);
        $user->save(); 
      
        $user = new \App\Permission([
            'name'          => 'Can Delete',
            'slug'         => 'd-only' 
        ]);
        $user->save(); 
        
    }
}
