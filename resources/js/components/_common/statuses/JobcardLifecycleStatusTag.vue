<style scoped>

    .status-tag >>> .ivu-poptip-body-content-inner{
        text-align:center;
    }

</style>

<template>

    <span v-if="localJobcard" class="status-tag">
        <Poptip word-wrap width="300" trigger="hover" :content="status.description">
            <Tag :style="{ 
                maxWidth: '100px',
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
            jobcard: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localJobcard: this.jobcard,
                status: {
                    text: '',
                    description: '',
                    color: ''
                }
            }
        },
        watch: {
            jobcard: {
                handler: function (val, oldVal) {
                    
                    this.localJobcard = val;
                    this.determineStatus();
                    
                },
                deep: true
            }
        },
        methods: {
            determineStatus() {
                
                //  If payment
                if( this.paymentStage() ){

                    // Lifecycle status info
                    this.status.description = 'This jobcard has received payment';
                    this.status.text = (this.localJobcard.current_lifecycle_status || {}).name;
                    this.status.color = '#2d8cf0';
                
                //  If closed
                }else if( this.jobstartedStage() ){

                    // Lifecycle status info
                    if( (this.localJobcard.current_lifecycle_status || {}).name == 'Job Cancelled' ){
                        
                        this.status.description = 'This jobcard has been cancelled';
                        this.status.text = (this.localJobcard.current_lifecycle_status || {}).name;
                        this.status.color = '#ed4014';

                    }else if( (this.localJobcard.current_lifecycle_status || {}).name == 'Job Pending' ){

                        this.status.description = 'This jobcard is currently pending';
                        this.status.text = (this.localJobcard.current_lifecycle_status || {}).name;
                        this.status.color = '#f90';
                    }else{

                        this.status.description = 'The job is currently running';
                        this.status.text = (this.localJobcard.current_lifecycle_status || {}).name;
                        this.status.color = '#2d8cf0';
                    }

                //  If closed
                }else if( this.closedStage() ){

                    // Lifecycle status info
                    this.status.description = 'This jobcard has been completed successfully';
                    this.status.text = (this.localJobcard.current_lifecycle_status || {}).name;
                    this.status.color = '#19be6b';

                //  If open
                }else if( this.openStage() ){

                    // Lifecycle status info
                    this.status.description = 'This jobcard is open';
                    this.status.text = (this.localJobcard.current_lifecycle_status || {}).name;
                    this.status.color = '#2d8cf0';
                }else{
                    // Lifecycle status info
                    this.status.description = 'The current status of the jobcard is unknown';
                    this.status.text = '...';
                    this.status.color = '#808695';
                } 
            },
            openStage(){
                return (this.localJobcard.current_lifecycle_status || {}).type == 'open' ? true: false;
            },
            paymentStage(){
                return (this.localJobcard.current_lifecycle_status || {}).type == 'payment' ? true: false;
            },
            jobstartedStage(){
                return (this.localJobcard.current_lifecycle_status || {}).type == 'job_started' ? true: false;
            },
            closedStage(){
                return (this.localJobcard.current_lifecycle_status || {}).type == 'closed' ? true: false;
            }
        },
        created() {
            this.determineStatus();
        }
    }
</script>
