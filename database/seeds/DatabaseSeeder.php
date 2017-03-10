<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(UsersTableSeeder::class);
		$this->call(ProductsTableSeeder::class);

		$this->call(CoursesTableSeeder::class);
		$this->call(GroupsTableSeeder::class);
		$this->call(LessonsTableSeeder::class);
		$this->call(SectionsTableSeeder::class);

		$this->call(CategoriesTableSeeder::class);

		$this->call(SlidesTableSeeder::class);
		$this->call(SnippetsTableSeeder::class);
		$this->call(ScreensTableSeeder::class);
		$this->call(PresentablesTableSeeder::class);

		$this->call(EditionsTableSeeder::class);
	}
}
