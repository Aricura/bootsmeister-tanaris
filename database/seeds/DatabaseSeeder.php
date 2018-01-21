<?php

use Illuminate\Database\Seeder;

/**
 * @author Stefan Herndler
 * @since x.x.x
 * @class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsSeeder::class);
		$this->call(ResourcesSeeder::class);
		$this->call(GuildsSeeder::class);
    }
}
