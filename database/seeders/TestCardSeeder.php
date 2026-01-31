<?php

namespace Database\Seeders;

use App\Models\TestCard;
use Illuminate\Database\Seeder;

class TestCardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            // --- SUCCESSFUL CARDS ---
            ['card_number' => '5890040000000016', 'bank_name' => 'Akbank', 'scheme' => 'Master Card', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '5526080000000006', 'bank_name' => 'Akbank', 'scheme' => 'Master Card', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '9792072000017956', 'bank_name' => 'Akbank', 'scheme' => 'Troy', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '4766620000000001', 'bank_name' => 'Denizbank', 'scheme' => 'Visa', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '4603450000000000', 'bank_name' => 'Denizbank', 'scheme' => 'Visa', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '9792023757123604', 'bank_name' => 'QNB', 'scheme' => 'Troy', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '4987490000000002', 'bank_name' => 'QNB', 'scheme' => 'Visa', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '5311570000000005', 'bank_name' => 'QNB', 'scheme' => 'Master Card', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '5170410000000004', 'bank_name' => 'Garanti', 'scheme' => 'Master Card', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '5400360000000003', 'bank_name' => 'Garanti', 'scheme' => 'Master Card', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '374427000000003',  'bank_name' => 'Garanti', 'scheme' => 'Amex', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '4475050000000003', 'bank_name' => 'Halkbank', 'scheme' => 'Visa', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '5528790000000008', 'bank_name' => 'Halkbank', 'scheme' => 'Master Card', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '5892830000000000', 'bank_name' => 'Is Bankasi', 'scheme' => 'Master Card', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '4543590000000006', 'bank_name' => 'Is Bankasi', 'scheme' => 'Visa', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '6500528865390837', 'bank_name' => 'Vakifbank', 'scheme' => 'Troy', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '6501700194147183', 'bank_name' => 'Vakifbank', 'scheme' => 'Troy', 'type' => 'Credit', 'should_succeed' => true],
            ['card_number' => '5168880000000002', 'bank_name' => 'Yapi Kredi', 'scheme' => 'Master Card', 'type' => 'Debit', 'should_succeed' => true],
            ['card_number' => '5451030000000000', 'bank_name' => 'Yapi Kredi', 'scheme' => 'Master Card', 'type' => 'Credit', 'should_succeed' => true],

            // --- ERROR/FAILURE CARDS ---
            [
                'card_number' => '4111111111111129',
                'should_succeed' => false,
                'error_code' => '50064',
                'error_message' => 'Insufficient Balance'
            ],
            [
                'card_number' => '4129111111111111',
                'should_succeed' => false,
                'error_code' => '50005',
                'error_message' => 'Transaction Not Approved (Do not honor)'
            ],
            [
                'card_number' => '4126111111111114',
                'should_succeed' => false,
                'error_code' => '50018',
                'error_message' => 'Stolen Card'
            ],
            [
                'card_number' => '4125111111111115',
                'should_succeed' => false,
                'error_code' => '50073',
                'error_message' => 'Expired Card'
            ],
            [
                'card_number' => '4121111111111119',
                'should_succeed' => false,
                'error_code' => '50099',
                'error_message' => 'Fraud Suspect'
            ],
        ];

        foreach ($cards as $card) {
            TestCard::create($card);
        }
    }
}
