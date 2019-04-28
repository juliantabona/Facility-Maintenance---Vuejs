<?php

namespace App\Http\Controllers\Api;

use App\Appointment;
use App\Http\Controllers\Controller;

class AppointmentController extends Controller
{
    /*  Index()
     *  Returns all the appointments from the database
     *  Can return Authenticated user company/branch specific appointments
     *  Can return all appointments in the system if accesssed by Super-Admin
     */
    public function index()
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointments were found successfully
        if ($success) {
            //  If this is a success then we have the paginated list of appointments
            $appointments = $response;

            //  Action was executed successfully
            return oq_api_notify($appointments, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store()
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment was created successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateUpdate($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment was updated successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    /*  show()
     *  Returns only one appointment from the database.
     *  This appointment must be specified using the appointment_id
     */
    public function show($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateShow($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment was found successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    /*  approve()
     *  Approves a draft appointment so that the user can take further actions
     */
    public function approve($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateApprove($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment was approved successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function send($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateSendAppointment($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment was sent successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function skipSend($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateSkipSend($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment sending was skipped successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function confirm($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateConfirmation($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment was confirmed successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function cancelConfirmation($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateCancelConfirmation($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment payment was cancelled successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function updateReminders($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateUpdateReminders($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment reminders were updated successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    /*  updateRecurringSettingsSchedulePlan()
     *  Updates the schedule plan (date, time and frequency) of how the appointments
     *  will be sent over a time period
     */
    public function updateRecurringSettingsSchedulePlan($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateUpdateRecurringSettingsSchedulePlan($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment schedule  plan was updated successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function updateRecurringSettingsDeliveryPlan($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateUpdateRecurringSettingsDeliveryPlan($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment schedule sending plan was updated successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function approveRecurringSettings($appointment_id)
    {
        //  Appointment Instance
        $data = ( new Appointment() )->initiateApproveRecurringSettings($appointment_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the appointment was approved successfully
        if ($success) {
            //  If this is a success then we have the appointment
            $appointment = $response;

            //  Action was executed successfully
            return oq_api_notify($appointment, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }
}
