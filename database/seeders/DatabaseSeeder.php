<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@brif.my'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('brif12345'),
                'is_admin' => true,
            ]
        );

        // 确保 admin 有 referral_code
        if (empty($admin->referral_code)) {
            $admin->referral_code = User::generateReferralCode();
            $admin->save();
        }

        // Normal customer
        $user = User::updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name'     => 'User',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        // 确保 user 有 referral_code
        if (empty($user->referral_code)) {
            $user->referral_code = User::generateReferralCode();
            $user->save();
        }

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            PaymentMethodSeeder::class,
            ShippingRateSeeder::class,
        ]);
    }
}
