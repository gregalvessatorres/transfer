<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Faker\Generator;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $users->each(function ($user) {
            Wallet::create([
                               'user'     => $user->id,
                               'ballance' => rand(1, 99)
                           ]);
        });
    }
}
