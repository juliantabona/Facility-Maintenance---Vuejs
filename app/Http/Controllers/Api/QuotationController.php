<?php

namespace App\Http\Controllers\Api;

use App\Quotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuotationController extends Controller
{
    public function update(Request $request, $quotation_id)
    {
        $updatedQuotation = request('quotation');

        if (!empty($updatedQuotation)) {
            try {
                //  Run query
                $quotation = Quotation::where('id', $quotation_id)->update([
                    'details' => json_encode($updatedQuotation['details'], JSON_FORCE_OBJECT),
                ]);

                //  If the lifecycle was updated successfully
                if ($quotation) {
                    //  refetch the updated quotation
                    $quotation = Quotation::find($quotation_id);

                    //  Action was executed successfully
                    return oq_api_notify($quotation, 200);
                }
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }
        } else {
            //  No resource found
            oq_api_notify_no_resource();
        }
    }
}
