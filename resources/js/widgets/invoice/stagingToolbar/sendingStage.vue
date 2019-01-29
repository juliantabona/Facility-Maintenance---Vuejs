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

                <Button v-if="!localInvoice.has_sent" class="float-right ml-2" type="default" size="large" @click="sendInvoice()">
                    <span>Skip</span>
                </Button>

                <Button class="float-right" :type="localInvoice.has_sent ? 'default' : 'primary'" 
                        size="large" @click="isOpenSendInvoiceModal = true">
                    <span>{{ localInvoice.has_sent ? 'Resend Invoice': 'Send Invoice' }}</span>
                </Button>
                
            </template>
            
        </stagingCard>

        <!-- 
            MODAL TO SEND INVOICE
        -->
        <sendInvoiceModal
            v-show="isOpenSendInvoiceModal" 
            :show="isOpenSendInvoiceModal"
            :invoice="localInvoice"
            v-on:closed="closeModal">
        </sendInvoiceModal>

    </div>

</template>
<script type="text/javascript">

    import fadeLoader from './fadeLoader.vue';
    import stagingCard from './stagingCard.vue';
    import sendInvoiceModal from './sendInvoiceModal.vue';

    export default {
        components: { fadeLoader, stagingCard, sendInvoiceModal },
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
                    if(this.localInvoice != val){

                        //  Update the local invoice value
                        this.localInvoice = val;
                    }
                }
            }
        },
        methods: {
            closeModal(){
                this.isOpenSendInvoiceModal = !this.isOpenSendInvoiceModal;
            },
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

                        //  Alert parent and pass updated invoice data
                        self.$emit('sent', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSendingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error sending invoice...');
                        console.log(response);
                    });
            },

        }
    }
</script>
