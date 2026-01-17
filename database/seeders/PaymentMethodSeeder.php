<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * 1️⃣ Online / Bank Transfer
         */
        PaymentMethod::updateOrCreate(
            ['code' => 'online_transfer'],
            [
                'name'                => 'Direct Payment',
                'short_description'   => 'Transfer to our company bank account & upload receipt',
                'is_active'           => true,
                'is_default'          => true,

                'bank_name'           => 'Maybank',
                'bank_account_name'   => 'BR INNOVATE FUTURE SDN. BHD.',
                'bank_account_number' => '514280900032',

                'duitnow_qr_path'     => null,
                'instructions'        => 'Please transfer the total amount and upload your payment receipt.',
            ]
        );

        /**
         * 2️⃣ Revenue Monster (Online Payment / E-Wallet)
         */
        PaymentMethod::updateOrCreate(
            ['code' => 'revenue_monster'],
            [
                'name'              => 'Online Payment',
                'short_description' => 'Pay securely via Touch ’n Go, GrabPay, ShopeePay, FPX & more',
                'is_active'         => true,
                'is_default'        => false,

                // ❌ 不需要银行信息
                'bank_name'           => null,
                'bank_account_name'   => null,
                'bank_account_number' => null,
                'duitnow_qr_path'     => null,

                'instructions' => 'You will be redirected to a secure payment page to complete your payment.',
            ]
        );
    }
}
