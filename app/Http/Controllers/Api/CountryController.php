<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\CountryTraits;

class CountryController extends Controller
{

    use CountryTraits;

    public function countries()
    {
        return oq_api_notify($this->getCountries(), 200);
    }

    public function callingCodes()
    {
        return oq_api_notify($this->getCallingCodes(), 200);
    }

    public function cities()
    {
        return oq_api_notify($this->getCities(), 200);
    }

    public function states()
    {
        return oq_api_notify($this->getStates(), 200);
    }

    public function currencies()
    {
        return oq_api_notify($this->getCurrencies(), 200);
    }

    public function exchangeRates()
    {
        return oq_api_notify($this->getExchangeRates(), 200);
    }
}
