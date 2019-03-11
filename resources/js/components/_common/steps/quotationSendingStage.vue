<template>

    <div>

        <!-- Fade loader - Shows when sending/skipping to send the quotation  -->
        <fadeLoader :loading="isSendingQuotation" msg="Sending, please wait..."></fadeLoader>
        <fadeLoader :loading="isSkippingSendingQuotation" msg="Skipping, please wait..."></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="2" :showHeader="false" 
            :disabled="!localQuotation.has_approved || isSendingQuotation || isSkippingSendingQuotation" :showVerticalLine="true">

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 class="text-secondary">{{ localQuotation.has_sent ? 'Quotation Sent' : 'Send Quotation' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="(localQuotation.last_sent_activity || {}).created_at | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span v-if="!localQuotation.has_sent" class="font-weight-bold">Last Sent: Never</span>
                        <span v-if="localQuotation.has_sent" class="font-weight-bold">Last Sent:</span> {{ (localQuotation.last_sent_activity || {}).created_at | moment("from", "now") }}
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template v-if="localQuotation.has_approved" slot="rightContent">
                
                <!-- Skip Sending Button -->
                <Button v-if="!localQuotation.has_sent && !localQuotation.has_skipped_sending" class="float-right ml-2" type="default" size="large" @click="skipSendQuotation()">
                    <span>Skip</span>
                </Button>

                <!-- Focus Ripple  -->
                <focusRipple color="blue" :ripple="(localQuotation.has_approved && !localQuotation.has_sent) && !localQuotation.has_skipped_sending" class="float-right">

                    <!-- Send/Resend Button -->
                    <Button :type="localQuotation.has_sent ? 'default' : 'primary'" 
                            size="large" @click="isOpenSendQuotationModal = true">
                        <span>{{ localQuotation.has_sent ? 'Resend Quotation': 'Send Quotation' }}</span>
                    </Button>

                </focusRipple>
                
            </template>
            
        </stagingCard>

        <!-- 
            MODAL TO SEND QUOTATION - VIA EMAIL
        -->
        <sendQuotationModal 
            v-if="isOpenSendQuotationModal" 
            :quotation="localQuotation" 
            @visibility="isOpenSendQuotationModal = $event"
            @sent="$emit('sent', $event)">
        </sendQuotationModal>

    </div>

</template>
<script type="text/javascript">

    import stagingCard from './main.vue';
    import fadeLoader from './../loaders/fadeLoader.vue';
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    /*  Modals  */
    import sendQuotationModal from './../modals/sendQuotationModal.vue';

    export default {
        components: { fadeLoader, stagingCard, focusRipple, sendQuotationModal },
        props: {
            quotation: {
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
                isOpenSendQuotationModal: false,
                isSendingQuotation: false,
                isSkippingSendingQuotation: false,
                localQuotation: this.quotation
            }
        },
        watch: {

            //  Watch for changes on the quotation
            quotation: {
                handler: function (val, oldVal) {
                    
                    //  Update the local quotation value
                    this.localQuotation = val;

                },
                deep: true
            }
        },
        methods: {
            skipSendQuotation(){

                var self = this;

                //  Start loader
                self.isSkippingSendingQuotation = true;

                console.log('Attempt to skip sending of quotation...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations/'+self.localQuotation.id+'/skip-send')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSkippingSendingQuotation = false;
                        
                        //  Alert skip success
                        self.$Message.success('Step skipped sucessfully!');

                        //  Notify parent on updates
                        //  NOTE that "data = updated quotation"
                        self.$emit('skipped', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSkippingSendingQuotation = false;

                        console.log('quotationSummaryWidget.vue - Error skipping of quotation...');
                        console.log(response);
                    });
            }

        }
    }
</script>
