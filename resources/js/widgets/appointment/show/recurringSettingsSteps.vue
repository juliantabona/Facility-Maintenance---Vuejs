<template>

    <Row>   
        
        <Col :span="24">
        
            <!-- Get the stage for setting the recurring schedule plan -->
            <Poptip confirm title="Reset all the settings back to factory so that you can start over." word-wrap width="300" 
                    ok-text="Yes" cancel-text="No" @on-ok="resetAllSettings()"
                    class="float-right mb-1" trigger="hover" placement="left">
                <Button type="default" size="large">
                    <span>Reset All</span>
                </Button>
            </Poptip>
            <div class="clearfix"></div>

            <!-- Get the stage for setting the recurring schedule plan -->
            <recurringSchedulePlanStage 
                resourceName="appointment"
                resourceNamePlural="appointments"
                :recurringSettings="localAppointment.recurringSettings" 
                :showHeader="!localAppointment.has_approved_recurring_schedule"
                :showCheckMark="localAppointment.has_set_recurring_schedule_plan && !(((localAppointment.recurringSettings || {}).editing || {}).schedulePlan)"
                :showNextStepBtn="localAppointment.has_set_recurring_schedule_plan"
                :isEditing="(((localAppointment.recurringSettings || {}).editing || {}).schedulePlan)"
                :rippleEffect="!localAppointment.has_set_recurring_schedule_plan"
                :url="'/api/appointments/'+localAppointment.id+'/recurring/update-schedule-plan'"
                :style="{ position:'relative', zIndex: 4 }" 
                @saved="$emit('saved', $event)">
            </recurringSchedulePlanStage>

            <!-- Get the stage for setting the recurring payment plan -->
            <recurringDeliveryPlanStage 
                resourceName="appointment"
                resourceNamePlural="appointments"
                :client="localAppointment.customized_client_details"
                smsTemplate="appointment-sms"
                :smsTemplateData="localAppointment"
                :testSmsUrl="'/api/invoices/'+localAppointment.id+'/send?test=1'"
                :testEmailUrl="'/api/invoices/'+localAppointment.id+'/send?test=1'"
                :recurringSettings="localAppointment.recurringSettings" 
                :disabled="!localAppointment.has_set_recurring_payment_plan"
                :showCheckMark="localAppointment.has_set_recurring_delivery_plan && !(((localAppointment.recurringSettings || {}).editing || {}).deliveryPlan)"
                :showToggleSwitch="localAppointment.has_set_recurring_payment_plan && (((localAppointment.recurringSettings || {}).editing || {}).deliveryPlan)"
                :showSettings="localAppointment.has_set_recurring_payment_plan && (((localAppointment.recurringSettings || {}).editing || {}).deliveryPlan)"
                :showMessage="!localAppointment.has_set_recurring_payment_plan && !(((localAppointment.recurringSettings || {}).editing || {}).deliveryPlan)"
                :showActionBtns="localAppointment.has_set_recurring_payment_plan"
                :showDoneText="localAppointment.has_set_recurring_delivery_plan"
                :isEditing="(((localAppointment.recurringSettings || {}).editing || {}).schedulePlan)"
                :url="'/api/invoices/'+localAppointment.id+'/recurring/update-delivery-plan'"
                :style="{ position:'relative', zIndex: 4 }" 
                @saved="$emit('saved', $event)">
            </recurringDeliveryPlanStage>
            <!-- Get the stage for setting the recurring delivery plan -->
            <recurringDeliveryPlanStage :invoice="localAppointment" :style="{ position:'relative', zIndex: 1 }" 
                @saved="$emit('saved', $event)">
            </recurringDeliveryPlanStage>

            <!-- Get the stage for setting the recurring delivery plan -->
            <recurringApprovalStage :invoice="localAppointment" :style="{ position:'relative', zIndex: 2 }" 
                @saved="$emit('saved', $event)">
            </recurringApprovalStage>

        </Col>

    </Row>

</template>
<script type="text/javascript">

    import recurringSchedulePlanStage from './../../../components/_common/steps/recurringSchedulePlanStage.vue'; 
    import recurringPaymentPlanStage from './../../../components/_common/steps/recurringPaymentPlanStage.vue'; 
    import recurringDeliveryPlanStage from './../../../components/_common/steps/recurringDeliveryPlanStage.vue'; 
    import recurringApprovalStage from './../../../components/_common/steps/recurringApprovalStage.vue'; 

    export default {
        props: {
            appointment: {
                type: Object,
                default: null
            }
        },
        components: { recurringSchedulePlanStage, recurringPaymentPlanStage, recurringDeliveryPlanStage, recurringApprovalStage },
        data(){
            var vm = this;
            return {
                /*
                    We are using cloneDeep to create a coplete copy of the javascript object without
                    having reactivity to the main appointment. This is so that whatever changes we make to 
                    the localAppointment, they must not affect the parent "appointment". We will only update
                    the parent when we save the changes to the database in either the the 
                    1) Schedule plan step
                    2) Payment plan step
                    3) Delivery plan step
                */
                localAppointment: _.cloneDeep(this.appointment)
            }
        },
        watch: {

            //  Watch for changes on the appointment
            appointment: {
                handler: function (val, oldVal) {
                    
                    //  Update the local appointment value
                    this.localAppointment = _.cloneDeep(val);

                    if(!this.localAppointment.recurringSettings){
                        this.localAppointment.recurringSettings = this.getRecurringSettingsTemplate();
                    }
                },
                deep: true
            }
        },
        methods: {
            resetAllSettings(){
                this.localAppointment.recurringSettings = this.getRecurringSettingsTemplate();
            
                //  Alert creation success
                this.$Message.success('Reset successfully!');

            },
            getRecurringSettingsTemplate(){
                
            //  Delivery settings
            var deliveryMailSubject = 'Appointment [appointment_reference_no]';
            var deliveryMailAddress = this.localAppointment.client.email;
            var deliveryMailMessage = 
                '<p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    Good day,  \
                </p> \
                <br> \
                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    Please find attached <strong>Appointment [appointment_reference_no]</strong> \
                    created on your account for services rendered. Payment regarding the&nbsp;balance of  \
                    <strong>[grand_total] </strong> \
                    must be settled by the  \
                    <strong>[expiry_date]</strong>  \
                    or earlier. \
                </p> \
                <br> \
                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    We look forward to conducting future business with you. \
                </p> \
                <br> \
                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    Regards, \
                    <br> \
                    [my_company_name] \
                </p>';

                var template = {
                        schedulePlan: {
                            chosen: 'Daily',        //  Daily, Weekly, Monthly, Yearly, Custom
                            weekly: '1',            //  Sunday=0, Monday=1... Saturday=6
                            monthly: '25',          //  1-31
                            yearly: {
                                month: '0',         //  January=0... December=11
                                day: '25'           //  1-31
                            },
                            custom: {
                                count: '3',         //  1,2,3...99
                                chosen: 'Months',   //  Days, Weeks, Months, Years
                                weeks: '1',         //  Sunday=0, Monday=1... Saturday=6
                                months: '25',       //  1-31
                                years: {        
                                    month: '0',     //  January=0... December=11
                                    day: '25'       //  1-31
                                }
                            },
                            startDate: '',
                            nextDate: null,
                            stop: {
                                chosen: 'Never',    //  Count, Date, Never
                                count: '3',         //  1,2,3...99
                                date: ''            //  2019-03-25
                            },
                            timezone: null
                        },
                        paymentPlan: {
                            automatic: true,
                            methods: [
                                'OrangeMoney',
                                'MyZaka'
                            ],
                            'automatic': true
                        },
                        deliveryPlan: {
                            automatic: true,
                            methods: [
                                /*'Email',*/
                                'Sms'
                            ],
                            mail: {
                                email: deliveryMailAddress,
                                subject: deliveryMailSubject,
                                message: deliveryMailMessage
                            },
                            sms: {
                                phones: [],
                                message: null
                            }
                        },
                        editing: {
                            schedulePlan: true,
                            paymentPlan: false,
                            deliveryPlan: false
                        },
                        has_approved: false
                    }

                return template;
            }
        },
        created(){
            
            if(!this.appointment.recurringSettings){
                this.localAppointment.recurringSettings = this.getRecurringSettingsTemplate();
            }
        }
    }
</script>
