<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CooperativeMember;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        
        CooperativeMember::create([
            'username' => 'cuhoangdk',
            'slug' => 'cuhoangdk',
            'email' => 'cuhoangdk@gmail.com',
            'password' => Hash::make('1234'),
            'full_name' => 'Nguyễn Trần Việt Hoàng',
            'phone_number' => '0901234567',
            'address' => '123 Đường ABC, Quận 1, TP.HCM',
            'farm_location' => 'Long An',
            'farm_size' => 10.5,
            'bank_account_number' => '1234567890',
            'bank_name' => 'Vietcombank',
            'avatar_url' => 'https://example.com/avatar.jpg',
            'bio' => 'Nông dân chuyên trồng rau hữu cơ',
            'is_active' => true,
            'verified_at' => now(),
            'joined_at' => now(),
        ]);
    }
}
