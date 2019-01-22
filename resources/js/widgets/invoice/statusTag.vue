<template>

    <span v-if="localInvoice.expiry_date_value">
        <Poptip word-wrap width="200" trigger="hover" :content="status.description">
            <Tag :style="{ 
                maxWidth: '70px',
                background: status.color + '10 !important',
                border: '1px solid '+status.color + ' !important'}">
                <span :style="{ color: status.color }">{{ status.text }}</span>
            </Tag>
        </Poptip>
    </span>

</template>
<script type="text/javascript">

    import moment from 'moment';

    export default {
        props:{
            invoice: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localInvoice: this.invoice,
                status: {
                    text: '',
                    description: '',
                    color: ''
                }
            }
        },
        watch: {
            invoice: {
                handler: function (val, oldVal) {
                    this.localInvoice = val;
                    this.determineStatus();
                },
                deep: true
            }
        },
        methods: {
            determineStatus() {

                //  If paid
                if( this.hasPaid() ){
                    
                    // Invoice paid status details
                    this.status.description = 'This invoice has been paid';
                    this.status.text = 'Paid';
                    this.status.color = '#19be6b';

                //  If expired
                }else if( this.hasExpired() ){

                    // Invoice expired status details
                    this.status.description = 'This invoice has exceeded its period of validity';
                    this.status.text = 'Expired';
                    this.status.color = '#ed4014';

                //  If sent
                }else if( this.hasSent() ){

                    // Invoice sent status details
                    this.status.description = 'This invoice has been sent to the customer';
                    this.status.text = 'Sent';
                    this.status.color = '#ff9900';

                //  If approved
                }else if( this.hasApproved() ){

                    // Invoice approved status details
                    this.status.description = 'This invoice has been approved for processing';
                    this.status.text = 'Approved';
                    this.status.color = '#2d8cf0';

                //  If draft
                }else{

                    // Invoice approved status details
                    this.status.description = 'This invoice has not been approved';
                    this.status.text = 'Draft';
                    this.status.color = '#808695';

                }
                    
            },
            hasPaid(){

                //  Have we ever cancelled before
                if( this.localInvoice.last_payment_cancelled_activity ){
                    //  Then that means we have recorded payment before
                    if( this.localInvoice.last_paid_activity ){
                        //  Then we can compare the two dates and see which was more recent

                        var cancelledDate = this.localInvoice.last_payment_cancelled_activity.created_at.split(' ').join('/');
                        var recordedPaymentDate = this.localInvoice.last_paid_activity.created_at.split(' ').join('/');

                        cancelledDate = new Date(cancelledDate).getTime();
                        recordedPaymentDate = new Date(recordedPaymentDate).getTime();

                        if (recordedPaymentDate > cancelledDate) {
                            // The user has been confirmed as paid
                            return true;
                        } else {
                            //  Payment record was cancelled
                            return false
                        }

                    }
                }else if( this.localInvoice.last_paid_activity ){
                    return true;
                }

            },
            hasExpired(){
                
                var expiryDate = moment(this.localInvoice.expiry_date_value);
                var now = moment();

                return (now > expiryDate) ? true: false;
            },
            hasSent(){
                return this.localInvoice.last_sent_activity ? true: false;
            },
            hasApproved(){
                return this.localInvoice.last_approved_activity ? true: false;
            }
        },
        created() {
            this.determineStatus();
        }
    }
</script>
