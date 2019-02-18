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
            $recurringSchedule = $invoice['recurringSchedule'];

            //  Get the total number of recurring child invoices
            $recurringInvoiceCount = $invoice->childInvoices->count();

            //  If we have set to stop sending based on the count e.g) "Count" 3 invoices sent
            if ($recurringSchedule['stop']['chosen'] == 'Count') {
                //  Get the number of invoices until we need to stop sending
                $stopOnCount = $recurringSchedule['stop']['count'];

                //  If we have reached or exceeded the limit we will stop here
                if ($stopOnCount >= $recurringInvoiceCount) {
                    //  Stop making this a recurring invoice
                    $invoice->update(['isRecurring', 0]);

                    //  Record stop recurring activity

                    //  Notify the users

                    return true;
                }
            } elseif ($recurringSchedule['stop']['chosen'] == 'Date') {
                //  Get the date until we need to stop sending
                $stopDate = $recurringSchedule['stop']['date'];
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
            $startSendDate = $recurringSchedule['startDate'];

            //  Get the next sending date
            $nextSendDate = $recurringSchedule['nextDate'];

            //  Get the stop sending date
            $stopSendDate = $recurringSchedule['stop']['date'];

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
                //  Lets set the next schedule time
                $chosenSchedule = $recurringSchedule['chosen'];           //  Daily, Weekly, Monthly, Yearly, Custom
                $weekly = $recurringSchedule['weekly'];                   //  Monday
                $monthly = $recurringSchedule['monthly'];                 //  4th
                $yearlyMonth = $recurringSchedule['yearly']['month'];     //  June
                $yearlyDay = $recurringSchedule['yearly']['day'];         //  8th

                //  If chose custom from the $chosenSchedule
                $customCount = $recurringSchedule['custom']['count'];             //  2
                $chosenCustom = $recurringSchedule['custom']['chosen'];           //  Days, Weeks, Months, Years
                $weekly = $recurringSchedule['custom']['weeks'];                  //  Tuesday
                $monthly = $recurringSchedule['custom']['months'];                //  8th
                $yearlyMonth = $recurringSchedule['custom']['years']['month'];    //  July
                $yearlyDay = $recurringSchedule['custom']['years']['day'];        //  10th

                //  Get the current  send date
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
                $schedule = $invoice->recurringSchedule;
                $schedule['nextDate'] = (new Carbon($newDate))->format('Y-m-d H:i:s');

                $invoice->update(['recurringSchedule' => $schedule]);

                //  Record next estimated recurring activity

                //  Notify the users
            }
        }

        $this->info('Action executed successfully!');
    }
}
