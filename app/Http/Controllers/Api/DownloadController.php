<?php

namespace App\Http\Controllers\Api;

use PDF;
use App\Jobcard;
use App\Invoice;
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
        $preview = request('preview', 0);
        $print = request('print', 0);

        $quotation = Quotation::find($quotation_id);

        //return view('pdf.quotation', ['quotation' => $quotation, 'print' => $print]);
        $pdf = PDF::loadView('pdf.quotation', array('quotation' => $quotation, 'print' => $print));

        if ($preview || $print) {
            return $pdf->stream('quotation.pdf');
        } else {
            if (!empty($quotation->details['heading']) && !empty($quotation['reference_no_value'])) {
                $pdfName = $quotation->details['heading'].' - '.
                           $quotation->details['reference_no_value'].' - '.
                           \Carbon\Carbon::parse($quotation['created_date'])->format('M d Y').
                           '.pdf';
            } else {
                $pdfName = 'Quotation - '.$quotation->id.'.pdf';
            }

            return $pdf->download($pdfName);
        }
    }

    public function downloadInvoice(Request $request, $invoice_id)
    {
        $user = auth('api')->user();
        $preview = request('preview', 0);
        $print = request('print', 0);

        $invoice = Invoice::find($invoice_id);

        //return view('pdf.invoice', ['invoice' => $invoice, 'print' => $print]);
        $pdf = PDF::loadView('pdf.invoice', array('invoice' => $invoice, 'print' => $print));

        if ($preview || $print) {
            return $pdf->stream('invoice.pdf');
        } else {
            if (!empty($invoice->details['heading']) && !empty($invoice['reference_no_value'])) {
                $pdfName = $invoice->details['heading'].' - '.
                           $invoice->details['reference_no_value'].' - '.
                           \Carbon\Carbon::parse($invoice['created_date'])->format('M d Y').
                           '.pdf';
            } else {
                $pdfName = 'Invoice - '.$invoice->id.'.pdf';
            }

            return $pdf->download($pdfName);
        }
    }
}
