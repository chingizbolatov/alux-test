<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Requests\CurrencyRequestsClass;
use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencyRequestsClass = new CurrencyRequestsClass();
        $data = $currencyRequestsClass->getCurrencyList();

        $names = [];
        foreach ($data as $currency_name) {
            $names[] = substr($currency_name, 0, 3);
            $names[] = substr($currency_name, 3, 3);
        }
        $names = array_unique($names);

        $this->saveRecords($names);
    }

    private function saveRecords($names)
    {
        foreach ($names as $name) {
            $currencyModel = new Currency();
            $currencyModel->name = $name;
            $currencyModel->save();
        }
    }
}
