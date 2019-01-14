<?php

namespace App\Http\Controllers\Api;

use PDF;
use App\Jobcard;
use App\Quotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DownloadController extends Controller
{
    public function download(Request $request)
    {
        $user = auth('api')->user();

        $jobcard = Jobcard::with('priority', 'categories', 'costcenters', 'supplierslist', 'client')->where('id', request('id'))->first();
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
            "showSupplier": true,
            "SupplierOptions":
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

    public function downloadQuotation(Request $request, $quotation_id)
    {
        $user = auth('api')->user();
        $preview = request('preview');

        $quotation = Quotation::find($quotation_id);

        $pdf = PDF::loadView('pdf.quotation', array('quotation' => $quotation->details));

        if ($preview) {
            return $pdf->stream('quotation.pdf');
        } else {
            if (!empty($quotation->details['heading']) && !empty($quotation->details['refNumber']['value'])) {
                $pdfName = $quotation->details['heading'].' - '.
                           $quotation->details['refNumber']['value'].' - '.
                           \Carbon\Carbon::parse($quotation['createdDate']['value'])->format('M d Y').
                           '.pdf';
            } else {
                $pdfName = 'Quotation - '.$quotation->id.'.pdf';
            }

            return $pdf->download($pdfName);
        }
    }
}
