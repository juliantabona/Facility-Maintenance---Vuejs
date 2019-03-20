<template>

    <span v-if="localJobcard">
        <Poptip word-wrap width="250" trigger="hover" :content="status.description">
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

                //  If approved
                if( this.hasApproved() ){

                    // Company approved status details
                    this.status.description = 'This jobcard has been approved';
                    this.status.text = 'Approved';
                    this.status.color = '#2d8cf0';

                //  If draft
                }else if( this.IsDraft() ){

                    // Company approved status details
                    this.status.description = 'This jobcard has not been approved';
                    this.status.text = 'Draft';
                    this.status.color = '#808695';
                } else{

                    // Company approved status details
                    this.status.description = 'The current status of the jobcard is unknown';
                    this.status.text = '...';
                    this.status.color = '#808695';
                } 
            },
            hasApproved(){
                return this.localJobcard.current_activity_status == 'Approved' ? true: false;
            },
            IsDraft(){
                return this.localJobcard.current_activity_status == 'Draft' ? true: false;
            }
        },
        created() {
            this.determineStatus();
        }
    }
</script>
