<?php

namespace App\Traits;

use App\Http\Resources\Transaction as TransactionResource;
use App\Http\Resources\Transactions as TransactionsResource;

trait TransactionTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($transactions = null)
    {

        try {

            if( $transactions ){

                //  Transform the transactions
                return new TransactionsResource($transactions);

            }else{

                //  Transform the transaction
                return new TransactionResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

}
