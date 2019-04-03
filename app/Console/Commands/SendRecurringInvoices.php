<?php

namespace App\Console\Commands;

use App\User;
use App\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRecurringInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:recurringInvoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send recurring invoices that are due';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $totalSent = 0;

        $nowTimestamp = Carbon::now()->getTimestamp();                   //   make sure its relative to timezone

        // Get all invoices that are set to recurring
        $invoices = Invoice::with(['childInvoices'])->where('isRecurring', 1)->get();

        //  Filter to only those that have been approved
        $invoices = collect($invoices)->filter(function ($invoice) { return $invoice->has_approved; });

        //  Foreach invoice
        foreach ($invoices as $invoice) {
            //  Get the recurring settings
            $recurringSettings = $invoice['recurring_settings'];
            $schedulePlan = $recurringSettings['schedulePlan'];

            //  Get the total number of recurring child invoices
            $recurringInvoiceCount = $invoice->childInvoices->count();

            //  If we have set to stop sending based on the count e.g) "Count" 3 invoices sent
            if ($schedulePlan['stop']['chosen'] == 'Count') {
                //  Get the number of invoices until we need to stop sending
                $stopOnCount = $schedulePlan['stop']['count'];

                //  If this is the last invoice to send before we reach the limit
                if (($stopOnCount + 1) == $recurringInvoiceCount) {
                    //  Stop making this a recurring invoice
                    $invoice->update(['isRecurring', 0]);

                    //  Record stop recurring activity

                    //  Notify the users that we are sending the last invoice

                    return true;
                }
            } elseif ($schedulePlan['stop']['chosen'] == 'Date') {
                //  Get the date until we need to stop sending
                $stopDate = $schedulePlan['stop']['date'];
                $stopDateTimestamp = (new Carbon($stopDate) )->getTimestamp();   //   make sure its relative to timezone

                //  If we have reached or exceeded the limit we will stop here
                if ($stopDateTimestamp < $nowTimestamp) {
                    //  Stop making this a recurring invoice
                    $invoice->update(['isRecurring', 0]);

                    //  Record stop recurring activity

                    //  Notify the users that we are sending the last invoice

                    return true;
                }
            }

            //  Get the start sending date
            $startSendDate = $schedulePlan['startDate'];

            //  Get the next sending date
            $nextSendDate = $schedulePlan['nextDate'];

            //  Get the stop sending date
            $stopSendDate = $schedulePlan['stop']['date'];

            //  Check if we are sending this for the first time
            if (!empty($nextSendDate)) {
                //  Not the first time. Lets use the next sending date
                $sendDate = $nextSendDate;
            } elseif (!empty($startSendDate)) {
                //  This is the first time - Lets use the start sending date
                $sendDate = $startSendDate;
            } else {
                //  Otherwise get the now moment as the sending dae
                $sendDate = Carbon::now();
            }

            $sendDateTimestamp = (new Carbon($sendDate) )->getTimestamp();   //   make sure its relative to timezone

            //  If the time to send is less than or equal to the now time then we can send
            if ($sendDateTimestamp <= $nowTimestamp) {
                /*********************************************************************
                 *   SET THE NEXT SCHEDULE TIME ACCORDING TO RECURRING SCHEDULE      *
                 *********************************************************************/
                $chosenSchedule = $schedulePlan['chosen'];           //  Daily, Weekly, Monthly, Yearly, Custom
                $weekly = $schedulePlan['weekly'];                   //  Monday
                $monthly = $schedulePlan['monthly'];                 //  4th
                $yearlyMonth = $schedulePlan['yearly']['month'];     //  June
                $yearlyDay = $schedulePlan['yearly']['day'];         //  8th

                //  If chose custom from the $chosenSchedule
                $customCount = $schedulePlan['custom']['count'];            //  2
                $chosenCustom = $schedulePlan['custom']['chosen'];          //  Days, Weeks, Months, Years
                $weeks = $schedulePlan['custom']['weeks'];                  //  Tuesday
                $months = $schedulePlan['custom']['months'];                //  8th
                $yearsMonth = $schedulePlan['custom']['years']['month'];    //  July
                $yearsDay = $schedulePlan['custom']['years']['day'];        //  10th

                //  Get the current send date
                $currentDate = Carbon::createFromFormat('Y-m-d H:i:s', $sendDate);

                //  Format the next schedule according to user specifications
                if ($chosenSchedule == 'Daily') {
                    //  Add one day
                    $newDate = $currentDate->addDay();
                } elseif ($chosenSchedule == 'Weekly') {
                    //  Add one week
                    $newDate = $currentDate->addWeek();
                } elseif ($chosenSchedule == 'Monthly') {
                    //  Add one month
                    $newDate = $currentDate->addMonth();
                } elseif ($chosenSchedule == 'Yearly') {
                    //  Add one year
                    $newDate = $currentDate->addYear();
                } elseif ($chosenSchedule == 'Custom') {
                    if ($chosenCustom == 'Days') {
                        //  Add specified number of days
                        $newDate = $currentDate->addDays($customCount);
                    } elseif ($chosenCustom == 'Weeks') {
                        //  Add specified number of weeks
                        $newDate = $currentDate->addWeeks($customCount);
                    } elseif ($chosenCustom == 'Months') {
                        //  Add specified number of months
                        $newDate = $currentDate->addMonths($customCount);
                    } elseif ($chosenCustom == 'Years') {
                        //  Add specified number of years
                        $newDate = $currentDate->addYears($customCount);
                    }
                }

                //  Now update scheduled time for next sending
                $recurringSettings['schedulePlan']['nextDate'] = (new Carbon($newDate))->format('Y-m-d H:i:s');
                $invoice->update(['recurring_settings' => $recurringSettings]);

                //  Record next estimated recurring activity

                //  Notify the users

                /*******************************************************
                 *   MAKE A CHILD INVOICE AND SEND TO THE CLIENT       *
                 ******************************************************/

                // We will deep copy the parent and edit to make the child copy
                $childInvoice = $invoice->replicate();
                //  Set the recurring status to be off
                $childInvoice->isRecurring = 0;
                //  Remove the recurring settings
                $childInvoice->recurringSettings = null;
                //  Link this child invoice to the parent
                $childInvoice->invoice_parent_id = $invoice->id;
                //  Update the invoice created date
                $childInvoice->created_date_value = (Carbon::now())->format('Y-m-d');
                //  Update the invoice expiry date according to the parent days apart between created and expiry dates
                $daysApart = (new Carbon($invoice->created_date_value) )->diffInDays((new Carbon($invoice->expiry_date_value) ));
                $childInvoice->expiry_date_value = (new Carbon($currentDate) )->addDays($daysApart - 1);
                //  Save the child invoice
                $childInvoice->save();

                //  Update the reference number
                $childInvoice->update(['reference_no_value' => $childInvoice->id]);

                /***********************************
                 *   SEND INVOICE VIA EMAIL/SMS    *
                 ***********************************/

                //  Get the user responsible for this invoice
                $user = $invoice->createdActivities()->createdBy;

                $deliveryPlan = $recurringSettings['deliveryPlan'];

                //  Accepted Delivery Methods
                $isDeliveryAutomated = $deliveryPlan['automatic'];
                $deliveryMethods = $deliveryPlan['methods'];

                //  If this is an automated delivery
                if ($isDeliveryAutomated) {
                    //  If specified to send invoice via sms
                    if (in_array('Sms', $deliveryMethods)) {
                        //  Sms details
                        $phones = $deliveryPlan['sms']['phones'];
                        $smsMessage = $deliveryPlan['sms']['message'];

                        //  Send via sms
                        ( new Invoice() )->sendInvoiceAsSMS($childInvoice, $phones, $smsMessage, $user);
                    }

                    //  If specified to send invoice via mail
                    if (in_array('Email', $deliveryMethods)) {
                        //  Email details
                        $primaryEmails = [$deliveryPlan['mail']['email']];
                        $ccEmails = [];
                        $bccEmails = [];
                        $subject = $deliveryPlan['mail']['subject'];
                        $message = $deliveryPlan['mail']['message'];

                        //  send via email
                        ( new Invoice() )->sendInvoiceAsMail($childInvoice, $primaryEmails, $ccEmails, $bccEmails, $subject, $message, $user);
                    }

                    //  If this is a manual delivery
                } else {
                    //  Notify the user to send the invoice
                }
            }
        }

        $this->info('Action executed successfully!');
    }
}
