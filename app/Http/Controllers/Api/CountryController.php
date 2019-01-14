<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use PragmaRX\Countries\Package\Countries;

class CountryController extends Controller
{
    public function currencies()
    {
        $countries = new Countries();

        $currencies = collect($countries->all()->hydrate('currencies'))->map(function ($country, $key) {
            $currency = collect($country['currencies'])->map(function ($currency, $key) use ($country) {
                return isset($currency['units']) ?
                            [
                                'country' => $country['name']['common'],
                                'currency' => array_merge(['iso' => $currency['iso']], $currency['units']['major']),
                            ] : null;
            })->collapse();

            return $currency;
        })->reject(function ($value, $key) {
            return  $value == null || !count($value);
        })->values();

        //  Action was executed successfully
        return oq_api_notify($currencies, 200);
    }
}
