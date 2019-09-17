<?php

namespace App;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\AdvancedFilter\Dataviewer;
use App\Traits\AppointmentTraits;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
]);

class Appointment extends Model
{
    use Dataviewer;
    use AppointmentTraits;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'recurring_settings' => 'array',
    ];

    protected $with = ['client', 'company', 'companyBranch', 'reminders'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'agenda', 'start_date', 'end_date', 'duration', 'location', 'client_id', 'client_type',
        'company_branch_id', 'company_id', 'isRecurring', 'recurring_settings',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //  Hide the recentActivities from the returned results
        //  since they can be way too many
        'recentActivities',
    ];

    protected $allowedFilters = [
        'id', 'subject', 'agenda', 'start_date', 'end_date', 'client_id', 'client_type', 'company_branch_id', 'company_id', 'created_at',
    ];

    protected $allowedOrderableColumns = [
        'id', 'subject', 'agenda', 'start_date', 'end_date', 'client_id', 'client_type', 'company_branch_id', 'company_id', 'created_at',
    ];

    /**
     *   Get the client the appointment was created for. This client could be a company
     *   or an individual.
     */
    public function client()
    {
        return $this->morphTo();
    }

    /**
     *   Get the company the appointment belongs to. This is the company that the appointment
     *   was created by. In this case it was created by a user who belongs to that company.
     */
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    /**
     *   Get the branch the appointment belongs to. This is the branch that the appointment
     *   was created by. In this case it was created by a user who belongs to that branch.
     */
    public function companyBranch()
    {
        return $this->belongsTo('App\CompanyBranch', 'company_branch_id');
    }

    public function childAppointments()
    {
        return $this->hasMany('App\Appointment', 'Appointment_parent_id');
    }

    /**
     * Get all of the categories for the appointment.
     */
    public function categories()
    {
        return $this->morphToMany('App\Category', 'trackable', 'category_allocations');
    }

    /**
     * Get the staff members for the appointment.
     */
    public function assignedStaff()
    {
        return $this->morphToMany('App\User', 'trackable', 'staff_allocations');
    }

    /**
     * Get all of the appointment reminders.
     */
    public function reminders()
    {
        return $this->morphMany('App\Reminder', 'trackable');
    }

    /***********************************************************************
     ***********************************************************************

     *  TYPES OF ACTIVITY DETAILS                                          *

     * *********************************************************************
     **********************************************************************/

    /*  ALL RECENT ACTIVITIES LINKED TO THIS APPOINTMENT
    */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'appointment')
                    ->orderBy('created_at', 'desc');
    }

    /*  CREATED BY ACTIVITIES
     *  Activity that shows the user details of the person who created the appointment
     */
    public function getCreatedByAttribute()
    {
        $publishingUser = $this->recentActivities()->select('type', 'created_by', 'created_at')
                               ->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'created')->first();

        if ($publishingUser) {
            return $publishingUser->createdBy;
        }
    }

    /*  CREATED ACTIVITIES
     *  Activity that shows when this appointment was created including the user details
     */
    public function createdActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'created');
    }

    /*  APPROVED ACTIVITIES
     *  Activity that shows when this appointment was apporved including the user details
     */
    public function approvedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'approved');
    }

    /*  SENT ACTIVITIES
     *  Activity that shows when this appointment was sent either via email/sms including the user details
     */
    public function sentActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where(function ($q) {
            $q->where('type', 'sent email')->orWhere('type', 'sent sms');
        });
    }

    /*  SKIPPED SENDING ACTIVITIES
     *  Activity that shows when this appointment was skipped for sending including the user details
     */
    public function skippedSendingActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'skipped sending');
    }

    /*  ACCEPTED ACTIVITIES
     *  Activity that shows when this appointment was confirmed by the receipient/receiver
     */
    public function confirmedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'confirmed')->limit(1);
    }

    /*  DECLINED ACTIVITIES
     *  Activity that shows when this appointment was declined by the receipient/receiver
     */
    public function declinedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'declined')->limit(1);
    }

    /*  RESCHEDULE ACTIVITIES
     *  Activity that shows when this appointment was rescheduled by the receipient/receiver
     */
    public function rescheduleActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'reschedule')->limit(1);
    }

    /*  CANCELLED ACTIVITIES
     *  Activity that shows when this appointment was cancelled including the user details
     */
    public function cancelledActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'cancelled confirmation')->limit(1);
    }

    /***********************************************************************
     ***********************************************************************

     *  TYPES OF LAST ACTIVITY DETAILS                                     *

     * *********************************************************************
     **********************************************************************/

    /*  LAST APPROVED ACTIVITY
     *  The last activity that shows when this appointment was apporved including the user details
     */
    public function getLastApprovedActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'approved')->first();
    }

    /*  LAST SENT ACTIVITY
     *  The last activity that shows when this appointment was sent either via email/sms including the user details
     */
    public function getLastSentActivityAttribute()
    {
        return $this->recentActivities()->select('id', 'type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where(function ($q) {
            $q->where('type', 'sent email')->orWhere('type', 'sent sms');
        })->first();
    }

    /*  LAST SKIPPED SENDING ACTIVITY
     *  The last activity that shows when this appointment was skipped for sending including the user details
     */
    public function getLastSkippedSendingActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'skipped sending')->first();
    }

    /*  LAST ACCEPTED ACTIVITY
     *  The last activity that shows when this appointment was confirmed by the receipient/receiver
     */
    public function getLastConfirmedActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'confirmed')->first();
    }

    /*  LAST DECLINED ACTIVITY
     *  The last activity that shows when this appointment was declined by the receipient/receiver
     */
    public function getLastDeclinedActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'declined')->first();
    }

    /*  LAST RESCHEDULE ACTIVITY
     *  The last activity that shows when this appointment was rescheduled by the receipient/receiver
     */
    public function getLastRescheduleActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'reschedule')->first();
    }

    /*  LAST CANCELLED ACTIVITY
     *  The last activity that shows when this appointment was cancelled including the user details
     */
    public function getLastCancelledActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'cancelled confirmation')->first();
    }

    /*  LAST RECURRING SCHEDULE PLAN ACTIVITY
     *  The last activity that shows when this appointment schedule plan (sending time, date and frequency)
     *  was last updated including the user details
     */
    public function getLastRecurringSchedulePlanActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'updated recurring schedule')->first();
    }

    /*  LAST RECURRING DELIVERY PLAN ACTIVITY
     *  The last activity that shows when this appointment delivery plan (sending method and destination)
     *  was last updated including the user details
     */
    public function getLastRecurringDeliveryPlanActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'updated recurring delivery')->first();
    }

    /*  LAST RECURRING APPOINTMENT APPROVAL ACTIVITY
     *  The last activity that shows when this appointment recurring settings
     *  was last approved including the user details
     */
    public function getlastApprovedRecurringSettingsActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'appointment')->where('type', 'approved recurring settings')->first();
    }

    /***********************************************************************
     ***********************************************************************

     *  TYPES OF ACTIVITY CONFIRMATIONS                                    *

     * *********************************************************************
     **********************************************************************/

    public function gethasApprovedAttribute()
    {
        return $this->last_approved_activity ? true : false;
    }

    public function gethasSentAttribute()
    {
        return $this->last_sent_activity ? true : false;
    }

    public function gethasSkippedSendingAttribute()
    {
        return $this->last_skipped_sending_activity ? true : false;
    }

    public function gethasConfirmedAttribute()
    {
        return $this->last_confirmed_activity ? true : false;
    }

    public function gethasDeclinedAttribute()
    {
        return $this->last_declined_activity ? true : false;
    }

    public function gethasRescheduledAttribute()
    {
        return $this->last_reschedule_activity ? true : false;
    }

    public function gethasExpiredAttribute()
    {
        $expiryDate = (new Carbon($this->end_date) )->getTimestamp();
        $now = Carbon::now()->getTimestamp();

        return ($now > $expiryDate) ? true : false;
    }

    public function gethasCancelledAttribute()
    {
        return $this->last_cancelled_activity ? true : false;
    }

    public function gethasSetRecurringSchedulePlanAttribute()
    {
        return $this->last_recurring_schedule_plan_activity ? true : false;
    }

    public function gethasSetRecurringDeliveryPlanAttribute()
    {
        return $this->last_recurring_delivery_plan_activity ? true : false;
    }

    public function gethasApprovedRecurringSettingsAttribute()
    {
        return $this->last_approved_recurring_settings_activity ? true : false;
    }

    /***********************************************************************
     ***********************************************************************

     *  TYPES OF ACTIVITY STATUS                                           *

     * *********************************************************************
     **********************************************************************/

    public function getCurrentActivityStatusAttribute()
    {
        //  If confirmed/declined/rescheduled/cancelled
        if ($this->has_confirmed || $this->has_declined || $this->has_rescheduled || $this->has_cancelled) {
            $confirmedDate = $this->last_confirmed_activity ? (new Carbon($this->last_confirmed_activity->created_at) )->getTimestamp() : 0;
            $declinedDate = $this->last_declined_activity ? (new Carbon($this->last_declined_activity->created_at) )->getTimestamp() : 0;
            $rescheduleDate = $this->last_reschedule_activity ? (new Carbon($this->last_reschedule_activity->created_at) )->getTimestamp() : 0;
            $cancelledDate = $this->last_cancelled_activity ? (new Carbon($this->last_cancelled_activity->created_at) )->getTimestamp() : 0;

            //  If we confirmed last
            if ($confirmedDate > $declinedDate && $confirmedDate > $rescheduleDate && $confirmedDate > $cancelledDate) {
                return 'Confirmed';

            //  If we declined last
            } elseif ($declinedDate > $confirmedDate && $declinedDate > $rescheduleDate && $declinedDate > $cancelledDate) {
                return 'Declined';

            //  If we rescheduled last
            } elseif ($rescheduleDate > $confirmedDate && $rescheduleDate > $declinedDate && $rescheduleDate > $cancelledDate) {
                return 'Reschedule';

            //  If cancelled last
            } elseif ($cancelledDate > $confirmedDate && $cancelledDate > $declinedDate && $cancelledDate > $rescheduleDate) {
                return 'Cancelled';
            }

            //  If expired
        } elseif ($this->has_expired) {
            return 'Expired';

        //  If sent
        } elseif ($this->has_sent) {
            return 'Sent';

        //  If approved
        } elseif ($this->has_approved) {
            return 'Approved';

        //  If draft
        } else {
            return 'Draft';
        }
    }

    /***********************************************************************
     ***********************************************************************

     *  TYPES OF ACTIVITY TOTALS                                           *

     * *********************************************************************
     **********************************************************************/

    public function getActivityCountAttribute()
    {
        $count = $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'appointment')
                                          ->select(DB::raw('count(*) as total'))
                                          ->groupBy('trackable_type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    protected $appends = [
                          'createdBy',
                          /*    Last Activity Record     */
                          'last_approved_activity', 'last_sent_activity', 'last_skipped_sending_activity',
                          'last_confirmed_activity', 'last_declined_activity', 'last_reschedule_activity',
                          'last_cancelled_activity',
                          'last_recurring_schedule_plan_activity', 'last_recurring_delivery_plan_activity',
                          'last_approved_recurring_settings_activity',
                          /*    Activity Confirmation Record     */
                          'has_approved', 'has_sent', 'has_skipped_sending', 'has_confirmed', 'has_declined',
                          'has_rescheduled', 'has_expired', 'has_cancelled',
                          'has_set_recurring_schedule_plan', 'has_set_recurring_delivery_plan',
                          'has_approved_recurring_settings',
                          'current_activity_status', 'activity_count',
                        ];
}
