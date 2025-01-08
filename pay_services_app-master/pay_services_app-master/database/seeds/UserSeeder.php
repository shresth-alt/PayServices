<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        DB::table('images')->insert([
            'public_id'=>Str::random('20'),
            'meta'=>Str::random(20),
            'format'=>Str::random(20),
            ]);
        }
    }
    