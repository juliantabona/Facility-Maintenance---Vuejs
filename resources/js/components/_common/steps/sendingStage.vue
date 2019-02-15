<template>

    <div>

        <!-- Fade loader - Shows when sending/skipping to send the invoice  -->
        <fadeLoader :loading="isSendingInvoice" msg="Sending, please wait..."></fadeLoader>
        <fadeLoader :loading="isSkippingSendingInvoice" msg="Skipping, please wait..."></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="2" :showHeader="false" 
            :disabled="!localInvoice.has_approved || isSendingInvoice || isSkippingSendingInvoice" :showVerticalLine="true">

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 class="text-secondary">{{ localInvoice.has_sent ? 'Invoice Sent' : 'Send Invoice' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="(localInvoice.last_sent_activity || {}).created_at | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span v-if="!localInvoice.has_sent" class="font-weight-bold">Last Sent: Never</span>
                        <span v-if="localInvoice.has_sent" class="font-weight-bold">Last Sent:</span> {{ (localInvoice.last_sent_activity || {}).created_at | moment("from", "now") }}
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template v-if="localInvoice.has_approved" slot="rightContent">
                
                <!-- Skip Sending Button -->
                <Button v-if="!localInvoice.has_sent && !localInvoice.has_skipped_sending" class="float-right ml-2" type="default" size="large" @click="skipSendInvoice()">
                    <span>Skip</span>
                </Button>

                <!-- Focus Ripple  -->
                <focusRipple color="blue" :ripple="(localInvoice.has_approved && !localInvoice.has_sent) && !localInvoice.has_skipped_sending" class="float-right">

                    <!-- Send/Resend Button -->
                    <Button :type="localInvoice.has_sent ? 'default' : 'primary'" 
                            size="large" @click="isOpenSendInvoiceModal = true">
                        <span>{{ localInvoice.has_sent ? 'Resend Invoice': 'Send Invoice' }}</span>
                    </Button>

                </focusRipple>
                
            </template>
            
        </stagingCard>

        <!-- 
            MODAL TO SEND INVOICE - VIA EMAIL
        -->
        <sendInvoiceModal 
            v-if="isOpenSendInvoiceModal" 
            :invoice="localInvoice" 
            @visibility="isOpenSendInvoiceModal = $event"
            @sent="updateParent($event)">
        </sendInvoiceModal>

    </div>

</template>
<script type="text/javascript">

    import stagingCard from './main.vue';
    import fadeLoader from './../loaders/fadeLoader.vue';
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    /*  Modals  */
    import sendInvoiceModal from './../modals/sendInvoiceModal.vue';

    export default {
        components: { fadeLoader, stagingCard, focusRipple, sendInvoiceModal },
        props: {
            invoice: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return {
                isOpenSendInvoiceModal: false,
                isSendingInvoice: false,
                isSkippingSendingInvoice: false,
                localInvoice: this.invoice
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {
                    
                    //  Update the local invoice value
                    this.localInvoice = val;

                },
                deep: true
            }
        },
        methods: {
            sendInvoice(){

                var self = this;

                //  Start loader
                self.isSendingInvoice = true;

                console.log('Attempt to send invoice...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/send')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSendingInvoice = false;
                        
                        //  Alert creation success
                        self.$Message.success('Invoice sent sucessfully!');

                        //  Notify parent on updates
                        //  NOTE that "data = updated invoice"
                        self.$emit('skipped', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSendingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error sending invoice...');
                        console.log(response);
                    });
            },
            skipSendInvoice(){

                var self = this;

                //  Start loader
                self.isSkippingSendingInvoice = true;

                console.log('Attempt to skip sending of invoice...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/skip-send')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSkippingSendingInvoice = false;
                        
                        //  Alert skip success
                        self.$Message.success('Step skipped sucessfully!');

                        //  Notify parent on updates
                        //  NOTE that "data = updated invoice"
                        self.$emit('skipped', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSkippingSendingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error skipping of invoice...');
                        console.log(response);
                    });
            }

        }
    }
</script>
