<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use PragmaRX\Countries\Package\Countries;

class CountryController extends Controller
{
    public function countries()
    {
        $countries = new Countries();
        $countries = collect($countries->all()->hydrate('flag'))->map(function ($country, $key) {
            $name = $country['name']['common'];
            $flag_icon = $country['flag']['flag-icon'];

            return ['name' => $name, 'flag' => $flag_icon];
        })->values();

        return oq_api_notify($countries, 200);
    }

    public function callingCodes()
    {
        $countries = new Countries();
        $countries = collect($countries->all()->hydrate('flag'))->map(function ($country, $key) {
            if (isset($country['dialling'])) {
                if (isset($country['dialling']['calling_code'])) {
                    $calling_code = $country['dialling']['calling_code'];

                    $name = $country['name']['common'];
                    $calling_code = isset($calling_code[0]) ? $calling_code[0] : $calling_code;
                    $flag_icon = $country['flag']['flag-icon'];

                    return ['country' => $name, 'calling_code' => $calling_code, 'flag' => $flag_icon];
                }
            }
        })
        ->sortBy('country')
        ->reject(function ($value, $key) {
            return  $value == null || !count($value) || !count($value['calling_code']);
        })->values();

        return oq_api_notify($countries, 200);
    }

    public function cities()
    {
        $country_name = request('country', null);

        $countries = new Countries();

        $countries = collect($countries->all()->hydrate('cities'))->map(function ($country, $key) {
            $cities = collect($country['cities'])->map(function ($city, $key) use ($country) {
                return isset($city['name']) ? $city['name'] : null;
            })->values();

            return collect(['country' => $country['name']['common'], 'cities' => $cities]);
        })->values();

        if ($country_name != null) {
            $countries = collect($countries)->where('country', $country_name)->values()->first();
        }

        return oq_api_notify($countries, 200);
    }

    public function states()
    {
        $country_name = request('country', null);

        $countries = new Countries();

        $countries = collect($countries->all()->hydrate('states'))->map(function ($country, $key) {
            $states = collect($country['states'])->map(function ($state, $key) use ($country) {
                $excludeForBotswana = ['Francistown', 'Gaborone', 'Jwaneng', 'Lobatse', 'Selebi-Phikwe', 'Sowa'];

                if (isset($state['name'])) {
                    if (!in_array($state['name'], $excludeForBotswana)) {
                        return $state['name'];
                    }
                }

                return null;
            })->reject(function ($value, $key) {
                return  $value == null || !count($value);
            })->values();

            return collect(['country' => $country['name']['common'], 'states' => $states]);
        })->values();

        if ($country_name != null) {
            $countries = collect($countries)->where('country', $country_name)->values()->first();
        }

        return oq_api_notify($countries, 200);
    }

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
