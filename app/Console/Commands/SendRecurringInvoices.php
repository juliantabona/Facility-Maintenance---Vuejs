<?php

namespace App\Console\Commands;

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

        //  Foreach invoice
        foreach ($invoices as $invoice) {
            $recurringSettings = $invoice['recurringSettings'];

            //  Get the total number of recurring child invoices
            $recurringInvoiceCount = $invoice->childInvoices->count();

            //  If we have set to stop sending based on the count e.g) "Count" 3 invoices sent
            if ($recurringSettings['stop']['chosen'] == 'Count') {
                //  Get the number of invoices until we need to stop sending
                $stopOnCount = $recurringSettings['stop']['count'];

                //  If we have reached or exceeded the limit we will stop here
                if ($stopOnCount >= $recurringInvoiceCount) {
                    //  Stop making this a recurring invoice
                    $invoice->update(['isRecurring', 0]);

                    //  Record stop recurring activity

                    //  Notify the users

                    return true;
                }
            } elseif ($recurringSettings['stop']['chosen'] == 'Date') {
                //  Get the date until we need to stop sending
                $stopDate = $recurringSettings['stop']['date'];
                $stopDateTimestamp = (new Carbon($stopDate) )->getTimestamp();   //   make sure its relative to timezone

                //  If we have reached or exceeded the limit we will stop here
                if ($stopDateTimestamp < $nowTimestamp) {
                    //  Stop making this a recurring invoice
                    $invoice->update(['isRecurring', 0]);

                    //  Record stop recurring activity

                    //  Notify the users

                    return true;
                }
            }

            //  Get the start sending date
            $startSendDate = $recurringSettings['startDate'];

            //  Get the next sending date
            $nextSendDate = $recurringSettings['nextDate'];

            //  Get the stop sending date
            $stopSendDate = $recurringSettings['stop']['date'];

            //  Check if we are sending this for the first time
            if (!empty($nextSendDate)) {
                //  Not the first time
                $sendDate = $nextSendDate;
            } else {
                //  This is the first time
                $sendDate = $startSendDate;
            }

            $sendDateTimestamp = (new Carbon($sendDate) )->getTimestamp();   //   make sure its relative to timezone

            //  If the time to send is less than or equal to the now time then we can send
            if ($sendDateTimestamp <= $nowTimestamp) {
                /*********************************************************************
                 *   SET THE NEXT SCHEDULE TIME ACCORDING TO RECURRING SCHEDULE      *
                 *********************************************************************/
                $chosenSchedule = $recurringSettings['chosen'];           //  Daily, Weekly, Monthly, Yearly, Custom
                $weekly = $recurringSettings['weekly'];                   //  Monday
                $monthly = $recurringSettings['monthly'];                 //  4th
                $yearlyMonth = $recurringSettings['yearly']['month'];     //  June
                $yearlyDay = $recurringSettings['yearly']['day'];         //  8th

                //  If chose custom from the $chosenSchedule
                $customCount = $recurringSettings['custom']['count'];             //  2
                $chosenCustom = $recurringSettings['custom']['chosen'];           //  Days, Weeks, Months, Years
                $weeks = $recurringSettings['custom']['weeks'];                  //  Tuesday
                $months = $recurringSettings['custom']['months'];                //  8th
                $yearsMonth = $recurringSettings['custom']['years']['month'];    //  July
                $yearsDay = $recurringSettings['custom']['years']['day'];        //  10th

                //  Get the current send date
                $currentDate = Carbon::createFromFormat('Y-m-d H:i:s', $sendDate);

                //  Format the next schedule according to user specifications
                if ($chosenSchedule == 'Daily') {
                    //  Add one day
                    $newDate = $currentDate->addDay;
                } elseif ($chosenSchedule == 'Weekly') {
                    //  Add one week
                    $newDate = $currentDate->addWeek;
                } elseif ($chosenSchedule == 'Monthly') {
                    //  Add one month
                    $newDate = $currentDate->addMonth;
                } elseif ($chosenSchedule == 'Yearly') {
                    //  Add one year
                    $newDate = $currentDate->addYear;
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
                $schedule = $invoice->recurringSettings;
                $schedule['nextDate'] = (new Carbon($newDate))->format('Y-m-d H:i:s');

                $invoice->update(['recurringSettings' => $schedule]);

                //  Record next estimated recurring activity

                //  Notify the users

                /*******************************************************
                 *   MAKE A CHILD INVOICE AND SEND TO THE CLIENT       *
                 ******************************************************/

                // We will deep copy the parent and edit to make the child copy
                $childInvoice = $invoice->replicate();
                //  Set the recurring status to be off
                $childInvoice->isRecurring = 0;
                $childInvoice->recurringSettings = null;
                //  Link this child invoice to the parent
                $childInvoice->invoice_parent_id = $invoice->id;
                //  Update the invoice created date
                $childInvoice->created_date_value = (new Carbon($currentDate))->format('Y-m-d');
                //  Update the invoice expiry date according to the parent days apart between created and expiry dates
                $daysApart = (new Carbon($invoice->created_date_value) )->diffInDays((new Carbon($invoice->expiry_date_value) ));
                $childInvoice->expiry_date_value = (new Carbon($currentDate) )->addDays($daysApart);
                //  Save the child invoice
                $childInvoice->save();

                //  Email details

                $email = $invoice->recurringSettings['mail']['email'];
                $subject = $invoice->recurringSettings['mail']['subject'];
                $message = $invoice->recurringSettings['mail']['message'];

                $status = 'automated sending';

                //  Send invoice via mail
                $childInvoice->sendInvoiceAsMail($email, $subject, $message, $childInvoice, $status);

                $this->info('New child invoice created! - days apart: '.$daysApart);
            }
        }

        $this->info('Action executed successfully!');
    }
}
