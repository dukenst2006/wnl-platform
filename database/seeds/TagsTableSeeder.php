<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('tags')->insert([
			['name' => 'O kursie'],
			['name' => 'O założeniach'],
			['name' => 'Reumatologia'],
		]);
	}
}
