<?php

namespace Database\Seeders;

use App\Models\User;
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
        $users = [
            [
                "id" => 1,
                "name" => "Arisha Barron"
            ],
            [
                "id" => 2,
                "name" => "Branden Gibson"
            ],
            [
                "id" => 3,
                "name" => "Rhonda Church"
            ],
            [
                "id" => 4,
                "name" => "Georgina Hazel"
            ]
        ];

        User::query()->insert($users);
    }
}
