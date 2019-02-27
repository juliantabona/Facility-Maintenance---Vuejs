<template>

    <Row>   
        
        <Col :span="24">
        
            <!-- Get the stage for setting the recurring schedule plan -->
            <invoiceRecurringSchedulePlanStage :invoice="localInvoice" :style="{ position:'relative', zIndex: 3 }" 
                @saved="$emit('saved', $event)">
            </invoiceRecurringSchedulePlanStage>

            <!-- Get the stage for setting the recurring payment plan -->
            <invoiceRecurringPaymentPlanStage :invoice="localInvoice" :style="{ position:'relative', zIndex: 2 }" 
                @saved="$emit('saved', $event)">
            </invoiceRecurringPaymentPlanStage>

            <!-- Get the stage for setting the recurring delivery plan -->
            <invoiceRecurringDeliveryPlanStage :invoice="localInvoice" :style="{ position:'relative', zIndex: 1 }" 
                @saved="$emit('saved', $event)">
            </invoiceRecurringDeliveryPlanStage>

        </Col>

    </Row>

</template>
<script type="text/javascript">

    import invoiceRecurringSchedulePlanStage from './../../../components/_common/steps/invoiceRecurringSchedulePlanStage.vue'; 
    import invoiceRecurringPaymentPlanStage from './../../../components/_common/steps/invoiceRecurringPaymentPlanStage.vue'; 
    import invoiceRecurringDeliveryPlanStage from './../../../components/_common/steps/invoiceRecurringDeliveryPlanStage.vue'; 

    export default {
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        components: { invoiceRecurringSchedulePlanStage, invoiceRecurringPaymentPlanStage, invoiceRecurringDeliveryPlanStage },
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
            getRecurringSettingsTemplate(){
                
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
                                'Email',
                                'Sms'
                            ],
                            mail: {
                                email: '',
                                subject: '',
                                message: ''
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
            },
        },
        created(){
            
            if(!this.invoice.recurringSettings){
                this.localInvoice.recurringSettings = this.getRecurringSettingsTemplate();
            }
        }
    }
</script>
