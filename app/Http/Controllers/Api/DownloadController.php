<?php

namespace App\Http\Controllers\Api;

use PDF;
use App\Jobcard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DownloadController extends Controller
{
    public function download(Request $request)
    {
        $user = auth('api')->user();

        $jobcard = Jobcard::with('categories', 'priorities', 'costcenters', 'contractorslist', 'client')->where('id', request('id'))->first();
        $options = json_decode(urldecode(request('options')));

        $optionsFormatted = collect($options)->map(function ($option) {
            $subOptions = collect($option->children)->map(function ($subOption) {
                return array(
                    implode('_', explode(' ', str_replace(array('/'), ' ', $subOption->title))) => $subOption->checked,
                );
            })->collapse();

            //  Lets assume that the main parent is hidden by default
            $mainOptionChecked = false;

            //  If any of the children is checked, then lets also check the parent
            foreach ($subOptions as $subOption) {
                if ($subOption) {
                    $mainOptionChecked = true;
                }
            }

            return array(
                    'show'.implode('_', explode(' ', str_replace(array('/'), ' ', $option->title))) => $mainOptionChecked,
                    implode('_', explode(' ', str_replace(array('/'), ' ', $option->title))).'Options' => $subOptions,
            );
        })->collapse();

        /*  The $optionsFormatted result =
        {
            "showJobcard": true,
            "JobcardOptions":
            {
                "Title": true,
                "Description": true,
                "Deadline": true,
                "Start_Date": true,
                "End_Date": true,
                "Priority": true,
                "Category": true,
                "Cost_Centers": true
            },
            "showClient": true,
            "ClientOptions":
            {
                "Logo": true,
                "Name": true,
                "Address": true,
                "State_Region": true,
                "Industry": true,
                "Type": true,
                "Email": true,
                "Phone": true,
                "Fax": true
            },
            "showContractor": true,
            "ContractorOptions":
            {
                "Logo": true,
                "Name": true,
                "Address": true,
                "State_Region": true,
                "Industry": true,
                "Type": true,
                "Email": true,
                "Phone": true,
                "Fax": true
            }
        }
        */

        $pdf = PDF::loadView('pdf.jobcard',
                    array(
                        'jobcard' => $jobcard,
                        'options' => $optionsFormatted,
                    ));

        return $pdf->stream('jobcard.pdf');

        /*
        $jobcard = $jobcard;
        $options = $optionsFormatted;

        return view('pdf.jobcard', compact('jobcard', 'options'));
        */
    }
}
