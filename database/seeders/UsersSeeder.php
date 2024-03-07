<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$data=[
			'name' => "Javier Oñate Mendía",
			'email' => "jom@dedalo.com.mx",
			'password' => bcrypt('15Pericos')
		];
		DB::table('users')->insert($data);
		$data=[
			'name' => "Sergio Martinez Garcia",
			'email' => "serchmaster@icloud.com",
			'password' => bcrypt('serchmaster')
		];
		DB::table('users')->insert($data);
	}
}