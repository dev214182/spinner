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
        $user->roles()->sync([1,1]);

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
        $user->roles()->sync([2,2]);    

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
        $user->roles()->sync([3,3]);    
        // $faker = Faker\Factory::create();
    	// foreach (range(1,50) as $index) {
	    //     DB::table('users')->insert([
        //         'name'          => $faker->name,
        //         'email'         => $faker->email,
        //         'password'      => Hash::make('123456789'),
        //         'phone'         => '05012345678',
        //         'role'          => 4,
        //         'status'        => true,
        //         'company_id'    => 1,
	    //     ]);
	    // }
    }
}
