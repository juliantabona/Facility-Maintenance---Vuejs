<?php

namespace App\Traits;

trait InvoiceTraits
{
    public function summarize()
    {
        return collect($this->select(
            'status', 'heading', 'reference_no_title', 'reference_no_value', 'created_date_title', 'created_date_value',
            'expiry_date_title', 'expiry_date_value', 'sub_total_title', 'sub_total_value', 'grand_total_title', 'grand_total_value',
            'currency_type', 'calculated_taxes', 'invoice_to_title', 'customized_company_details', 'customized_client_details', 'client_id',
            'table_columns', 'items', 'notes', 'colors', 'footer', 'quotation_id'
        )->first())
        //  Remove all custom attributes since the are all based on recent activities
        ->forget(['last_approved_activity', 'last_sent_activity', 'last_paid_activity', 'last_payment_cancelled_activity',
                  'has_paid', 'has_expired', 'has_cancelled', 'has_sent', 'has_approved', 'current_activity_status', 'recent_activities', ]);
    }
}
