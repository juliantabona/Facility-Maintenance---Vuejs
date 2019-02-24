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
                }else if( this.hasCancelled() ){

                    // Invoice expired status details
                    this.status.description = 'This invoice payment was cancelled';
                    this.status.text = 'Cancelled';
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
                }else if( this.IsDraft() ){

                    // Invoice approved status details
                    this.status.description = 'This invoice has not been approved';
                    this.status.text = 'Draft';
                    this.status.color = '#808695';
                } else{

                    // Invoice approved status details
                    this.status.description = 'The current status of the invoice is unknown';
                    this.status.text = '...';
                    this.status.color = '#808695';
                } 
            },
            hasPaid(){
                return this.localInvoice.current_activity_status == 'Paid' ? true: false;
            },
            hasExpired(){
                return this.localInvoice.current_activity_status == 'Expired' ? true: false;
            },
            hasCancelled(){
                return this.localInvoice.current_activity_status == 'Cancelled' ? true: false;
            },
            hasSent(){
                return this.localInvoice.current_activity_status == 'Sent' ? true: false;
            },
            hasApproved(){
                return this.localInvoice.current_activity_status == 'Approved' ? true: false;
            },
            IsDraft(){
                return this.localInvoice.current_activity_status == 'Draft' ? true: false;
            }
        },
        created() {
            this.determineStatus();
        }
    }
</script>
