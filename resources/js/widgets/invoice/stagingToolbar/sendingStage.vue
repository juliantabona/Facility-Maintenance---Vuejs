<template>

    <div>

        <!-- Fade loader - Shows when sending invoice  -->
        <fadeLoader :loading="isSendingInvoice" msg="Sending, please wait..."></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="2" :showHeader="false" 
            :disabled="isSendingInvoice" :showVerticalLine="true">

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
            <template slot="rightContent">
                
                <!-- Skip Sending Button -->
                <Button v-if="!localInvoice.has_sent" class="float-right ml-2" type="default" size="large" @click="sendInvoice()">
                    <span>Skip</span>
                </Button>

                <!-- Focus Ripple  -->
                <focusRipple color="blue" :ripple="localInvoice.has_approved &&!localInvoice.has_sent" class="float-right">

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

    import fadeLoader from './fadeLoader.vue';
    import stagingCard from './stagingCard.vue';
    import sendInvoiceModal from './../modals/sendInvoiceModal.vue';

    /*  Ripples  */
    import focusRipple from './../ripples/focusRipple.vue';

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

                        //  NOTE that "data = updated invoice"
                        self.updateParent(updatedInvoice);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSendingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error sending invoice...');
                        console.log(response);
                    });
            },
            updateParent(updatedInvoice){
                //  Alert parent and pass updated invoice data
                this.$emit('sent', updatedInvoice);
            }

        }
    }
</script>
