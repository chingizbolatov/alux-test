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

        $c1_name = empty(Currency::find($request->c1_id)) ? null : Currency::find($request->c1_id)->name;
        $c2_name = empty(Currency::find($request->c2_id)) ? null : Currency::find($request->c2_id)->name;

        if (is_null($c1_name) || is_null($c2_name)) {
            return $this->response(412, 'Could not find currency');
        }

        $pair_name = $c1_name . $c2_name;
        $currencyRequestsClass = new CurrencyRequestsClass();
        $rates = $currencyRequestsClass->getCurrencyRatesList($pair_name);

        return $this->response(200, 'Success', $rates);
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

        $c1_name = empty(Currency::find($request->c1_id)) ? null : Currency::find($request->c1_id)->name;
        $c2_name = empty(Currency::find($request->c2_id)) ? null : Currency::find($request->c2_id)->name;

        if (is_null($c1_name) || is_null($c2_name)) {
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
