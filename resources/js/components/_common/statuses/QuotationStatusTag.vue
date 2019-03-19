<template>

    <span v-if="localQuotation">
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
            quotation: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localQuotation: this.quotation,
                status: {
                    text: '',
                    description: '',
                    color: ''
                }
            }
        },
        watch: {
            quotation: {
                handler: function (val, oldVal) {
                    this.localQuotation = val;
                    this.determineStatus();
                },
                deep: true
            }
        },
        methods: {
            determineStatus() {

                //  If paid
                if( this.hasConverted() ){
                    
                    // Quotation converted status details
                    this.status.description = 'This quotation has been converted to an invoice';
                    this.status.text = 'Converted';
                    this.status.color = '#19be6b';

                //  If expired
                }else if( this.hasExpired() ){

                    // Quotation expired status details
                    this.status.description = 'This quotation has exceeded its period of validity';
                    this.status.text = 'Expired';
                    this.status.color = '#ed4014';

                //  If sent
                }else if( this.hasSent() ){

                    // Quotation sent status details
                    this.status.description = 'This quotation has been sent to the customer';
                    this.status.text = 'Sent';
                    this.status.color = '#ff9900';

                //  If approved
                }else if( this.hasApproved() ){

                    // Quotation approved status details
                    this.status.description = 'This quotation has been approved for processing';
                    this.status.text = 'Approved';
                    this.status.color = '#2d8cf0';

                //  If draft
                }else if( this.IsDraft() ){

                    // Quotation approved status details
                    this.status.description = 'This quotation has not been approved';
                    this.status.text = 'Draft';
                    this.status.color = '#808695';
                } else{

                    // Quotation approved status details
                    this.status.description = 'The current status of the quotation is unknown';
                    this.status.text = '...';
                    this.status.color = '#808695';
                } 
            },
            hasConverted(){
                return this.localQuotation.current_activity_status == 'Converted' ? true: false;
            },
            hasExpired(){
                return this.localQuotation.current_activity_status == 'Expired' ? true: false;
            },
            hasSent(){
                return this.localQuotation.current_activity_status == 'Sent' ? true: false;
            },
            hasApproved(){
                return this.localQuotation.current_activity_status == 'Approved' ? true: false;
            },
            IsDraft(){
                return this.localQuotation.current_activity_status == 'Draft' ? true: false;
            }
        },
        created() {
            this.determineStatus();
        }
    }
</script>
