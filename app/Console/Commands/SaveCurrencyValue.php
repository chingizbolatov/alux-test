<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\CurrencyPairs;
use App\Requests\CurrencyRequestsClass;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SaveCurrencyValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save currencies rates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currencyRequestsClass = new CurrencyRequestsClass();
        $rates = $currencyRequestsClass->getCurrencyRatesList('EURUSD,BTCUSD,RUBCHF,USDRUB');
        $rates['USDCHF'] = $rates['RUBCHF'] * $rates['USDRUB'];

        foreach ($rates as $currency_pair => $rate) {
            $c1_id = Currency::where('name', '=', substr($currency_pair, 0, 3))->first()->id;
            $c2_id = Currency::where('name', '=', substr($currency_pair, 3, 3))->first()->id;

            $currencyPairRecord = CurrencyPairs::where('c1_id', $c1_id)->where('c2_id', $c2_id)->first();
            if (empty($currencyPairRecord)) {
                $currencyPairModel = new CurrencyPairs();
            } else {
                $currencyPairModel = $currencyPairRecord;
                if (empty($currencyPairModel->history)) {
                    $history_data[0] = [
                        'price' => Arr::get($currencyPairModel, 'price', null),
                        'updated_at' => Arr::get($currencyPairModel, 'updated_at', null)
                    ];
                    $currencyPairModel->history = $history_data;
                } else {
                    $history_data = json_decode($currencyPairModel->history, true);
                    $history_data[count($history_data)] = [
                        'price' => Arr::get($currencyPairModel, 'price', null),
                        'updated_at' => Arr::get($currencyPairModel, 'updated_at', null)
                    ];
                    $currencyPairModel->history = $history_data;
                }
            }
            $currencyPairModel->c1_id = $c1_id;
            $currencyPairModel->c2_id = $c2_id;
            $currencyPairModel->price = $rate;
            $currencyPairModel->save();
        }
    }
}
