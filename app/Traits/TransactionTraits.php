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

    /*  initiateCreate() method:
     *
     *  This method is used to create a new transaction.
     */
    public function initiateCreate( $transaction = null )
    {
        /*
         *  The $transaction variable represents the transaction 
         *  dataset provided through the request received.
         */
        $transaction = $transaction ?? request('transaction');

        /*
         *  The $template variable represents structure of the transaction.
         *  If no template is provided, we create one using the 
         *  request data.
         */
        $template = $template ?? [
            'type' => $transaction['type'] ?? null,
            'automatic' => $transaction['automatic'] ?? 0,
            'status' => $transaction['status'] ?? null,
            'payment_type' => $transaction['payment_type'] ?? null,
            'payment_amount' => $transaction['payment_amount'] ?? null
        ];

        try {

            /*
             *  Create a new transaction, then retrieve a fresh instance
             */
            $transaction = $this->create($template)->fresh();

            /*  If the transaction was created successfully  */
            if( $transaction ){

                /*  Return a fresh instance of the transaction  */
                return $transaction->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

}
