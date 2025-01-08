<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
  * Seed the application's database.
  *
  * @return void
  */
  public function run() {
    /** Admin user seeds */
    $this->adminUserSeeds();
    /** Services seeds */
  }

  private function adminUserSeeds() {
    DB::table('users')->insert([
      'name' => 'Ram Sharma',
      'mobile' => '9785094709',
      'type' => 'admin',
      'email' => 'ram@gmail.com',
      'password' => Hash::make('admin@123')
    ]);
  }

  private function servicesSeeds() {
    DB::table('images')->insert(array(
      'public_id' => 'paint-brush-1575182-1331516_eirmqr',
      'format' => '.png',
      'meta' => Str::random(10),
    ));
  }
}
