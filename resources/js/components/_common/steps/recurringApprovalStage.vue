<template>

    <div>

        <!-- Fade loader - Shows when approving the recurring invoice  -->
        <fadeLoader :loading="isSavingApproval" msg="Approving recurring invoice, please wait..."></fadeLoader>

        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="4" :showCheckMark="localInvoice.has_approved_recurring_settings" :showHeader="false" 
            :disabled="!localInvoice.has_set_recurring_delivery_plan" :showVerticalLine="false"
            :isSaving="isSavingApproval" :leftWidth="16" :rightWidth="8">

            <template slot="leftContent">

                <h4 :class="'text-secondary' + (localInvoice.has_approved_recurring_settings) ? ' mt-2': ''">{{ (localInvoice.has_approved_recurring_settings) ? 'Approved': 'Approve:' }}</h4>
                <span v-if="!localInvoice.has_approved_recurring_settings" class="d-inline-block mt-2 mb-2">Approve to allow for recurring invoices to start.</span>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <!-- Focus Ripple  -->
                <focusRipple v-if="!localInvoice.has_approved_recurring_settings" :ripple="true" color="blue" class="float-right mt-3">
                    
                    <!-- Approve Button  -->
                    <Poptip confirm title="Are you sure you want to approve this recurring invoice?"  width="300"
                            ok-text="Yes" cancel-text="No" @on-ok="approveRecurringSettings()" placement="left">
                        
                        <Button type="primary" size="large">
                            <span>Approve</span>
                        </Button>

                    </Poptip>

                </focusRipple>

            </template>
            
        </stagingCard>

    </div>

</template>
<script type="text/javascript">

    /*  Steps  */
    import stagingCard from './main.vue';

    /*  Loaders  */
    import fadeLoader from './../loaders/fadeLoader.vue';

    /*  Animated Icons  */
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';

    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    export default {
        components: { fadeLoader, stagingCard, animatedCheckmark, focusRipple },
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        data(){
            return {

                /*
                    We are using cloneDeep to create a coplete copy of the javascript object without
                    having reactivity to the main invoice. This is so that whatever changes we make to 
                    the localInvoice, they must not affect the parent "invoice". We will only update
                    the parent when we save the changes to the database.
                */
                localInvoice: _.cloneDeep(this.invoice),

                isSavingApproval: false
    
            } 
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {
                    
                    //  Update the local invoice value
                    this.localInvoice = _.cloneDeep(val);

                },
                deep: true
            }
        },
        methods: {
            approveRecurringSettings(){

                var self = this;

                //  Start loader
                self.isSavingApproval = true;

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                console.log('Attempt to approve recurring invoice...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/recurring/approve', invoiceData)
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSavingApproval = false;
                        
                        //  Alert creation success
                        self.$Message.success('Invoice approved sucessfully!');

                        self.$emit('saved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingApproval = false;

                        console.log('recurringSettingsStage.vue - Error in approving recurring invoice...');
                        console.log(response);
                    });
            }
        }
    }
</script>
