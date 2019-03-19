<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Company;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function getEstimatedStats()
    {
        //  Start getting the company stats
        $companyData = ( new Company() )->getStatistics();
        $companySuccess = $companyData['success'];
        $companyResponse = $companyData['response'];

        if (!$companySuccess) {
            //  If the data was not a success then return the response
            return $companyResponse;
        }

        //  Start getting the individuals stats
        $individualsData = ( new User() )->getStatistics();
        $individualsSuccess = $individualsData['success'];
        $individualsResponse = $individualsData['response'];

        return oq_api_notify($individualsResponse, 200);

        if (!$individualsSuccess) {
            //  If the data was not a success then return the response
            return $individualsResponse;
        }

        if ($companySuccess && $individualsSuccess) {
            //  Action was executed successfully
            $stats = [
                'companies' => $companyData,
                'individuals' => $individualsData,
            ];

            return oq_api_notify($stats, 200);
        }
    }
}
