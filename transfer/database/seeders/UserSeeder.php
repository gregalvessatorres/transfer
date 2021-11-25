<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(
            [
                'name'      => 'admin',
                'email'     => 'admin@admin',
                'cpf_cnpj'  => '00000000000',
                'user_type' => 1,
                'password'  => Hash::make('admin')
            ]);
        User::create([
                         'name'      => 'greg',
                         'email'     => 'gregsatorres@gmail.com',
                         'cpf_cnpj'  => '88888888888',
                         'user_type' => 1,
                         'password'  => Hash::make('admin')
                     ]);
    }
}
