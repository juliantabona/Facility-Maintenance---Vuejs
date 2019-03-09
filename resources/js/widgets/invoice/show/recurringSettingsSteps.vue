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
            <invoiceRecurringSchedulePlanStage :invoice="localInvoice" :style="{ position:'relative', zIndex: 4 }" 
                @saved="$emit('saved', $event)">
            </invoiceRecurringSchedulePlanStage>

            <!-- Get the stage for setting the recurring payment plan -->
            <invoiceRecurringPaymentPlanStage :invoice="localInvoice" :style="{ position:'relative', zIndex: 3 }" 
                @saved="$emit('saved', $event)">
            </invoiceRecurringPaymentPlanStage>

            <!-- Get the stage for setting the recurring delivery plan -->
            <invoiceRecurringDeliveryPlanStage :invoice="localInvoice" :style="{ position:'relative', zIndex: 1 }" 
                @saved="$emit('saved', $event)">
            </invoiceRecurringDeliveryPlanStage>

            <!-- Get the stage for setting the recurring delivery plan -->
            <invoiceRecurringApprovalStage :invoice="localInvoice" :style="{ position:'relative', zIndex: 2 }" 
                @saved="$emit('saved', $event)">
            </invoiceRecurringApprovalStage>

        </Col>

    </Row>

</template>
<script type="text/javascript">

    import invoiceRecurringSchedulePlanStage from './../../../components/_common/steps/invoiceRecurringSchedulePlanStage.vue'; 
    import invoiceRecurringPaymentPlanStage from './../../../components/_common/steps/invoiceRecurringPaymentPlanStage.vue'; 
    import invoiceRecurringDeliveryPlanStage from './../../../components/_common/steps/invoiceRecurringDeliveryPlanStage.vue'; 
    import invoiceRecurringApprovalStage from './../../../components/_common/steps/invoiceRecurringApprovalStage.vue'; 

    export default {
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        components: { invoiceRecurringSchedulePlanStage, invoiceRecurringPaymentPlanStage, invoiceRecurringDeliveryPlanStage, invoiceRecurringApprovalStage },
        data(){
            var vm = this;
            return {
                /*
                    We are using cloneDeep to create a coplete copy of the javascript object without
                    having reactivity to the main invoice. This is so that whatever changes we make to 
                    the localInvoice, they must not affect the parent "invoice". We will only update
                    the parent when we save the changes to the database in either the the 
                    1) Schedule plan step
                    2) Payment plan step
                    3) Delivery plan step
                */
                localInvoice: _.cloneDeep(this.invoice)
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {
                    
                    //  Update the local invoice value
                    this.localInvoice = _.cloneDeep(val);

                    if(!this.localInvoice.recurringSettings){
                        this.localInvoice.recurringSettings = this.getRecurringSettingsTemplate();
                    }
                },
                deep: true
            }
        },
        methods: {
            resetAllSettings(){
                this.localInvoice.recurringSettings = this.getRecurringSettingsTemplate();
            
                //  Alert creation success
                this.$Message.success('Reset successfully!');

            },
            getRecurringSettingsTemplate(){
                
            //  Delivery settings
            var deliveryMailSubject = 'Invoice [invoice_reference_no]';
            var deliveryMailAddress = this.localInvoice.customized_client_details.email;
            var deliveryMailMessage = 
                '<p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    Good day,  \
                </p> \
                <br> \
                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    Please find attached <strong>Invoice [invoice_reference_no]</strong> \
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
            
            if(!this.invoice.recurringSettings){
                this.localInvoice.recurringSettings = this.getRecurringSettingsTemplate();
            }
        }
    }
</script>
