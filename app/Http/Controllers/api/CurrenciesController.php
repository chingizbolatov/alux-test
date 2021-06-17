<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\CurrencyPairs;
use App\Requests\CurrencyRequestsClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrenciesController extends Controller
{
    public function index()
    {
        $currency_list = Currency::all(['id', 'name']);

        return $this->response(200, 'Success', $currency_list);
    }

    public function getCurrencyPairRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'c1_id' => 'required|integer',
            'c2_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->response(422, 'Validation error', $validator->errors()->getMessages());
        }

        $currencyModel = new Currency();
        $c1_name = $currencyModel->getName($request->c1_id);
        $c2_name = $currencyModel->getName($request->c2_id);

        if (empty($c1_name) || empty($c2_name)) {
            return $this->response(412, 'Could not find currency');
        }

        $pair_name = $c1_name . $c2_name;
        $currencyRequestsClass = new CurrencyRequestsClass();
        $rates = $currencyRequestsClass->getCurrencyRatesList($pair_name);

        return $this->response(200, 'Actual rate for your request', $rates);
    }

    public function getCurrencyPairRateHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'c1_id' => 'required|integer',
            'c2_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->response(422, 'Validation error', $validator->errors()->getMessages());
        }

        $currencyModel = new Currency();
        $c1_name = $currencyModel->getName($request->c1_id);
        $c2_name = $currencyModel->getName($request->c2_id);

        if (empty($c1_name) || empty($c2_name)) {
            return $this->response(412, 'Could not find currency');
        }

        $currencyPairsRecord = CurrencyPairs::where('c1_id', $request->c1_id)->where('c2_id', $request->c2_id)->first();

        if (empty($currencyPairsRecord)) {
            return $this->response(412, 'Currency pair rate history not found');
        }

        $data = [
            'pair' => $c1_name . $c2_name,
            'rate' => $currencyPairsRecord->price,
            'history' => json_decode($currencyPairsRecord->history)
        ];

        return $this->response(200, 'Success', $data);
    }
}
