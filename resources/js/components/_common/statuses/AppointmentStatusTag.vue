<template>

    <span v-if="localAppointment">
        <Poptip word-wrap width="320" trigger="hover" :content="status.description">
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
            appointment: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localAppointment: this.appointment,
                status: {
                    text: '',
                    description: '',
                    color: ''
                }
            }
        },
        watch: {
            appointment: {
                handler: function (val, oldVal) {
                    this.localAppointment = val;
                    this.determineStatus();
                },
                deep: true
            }
        },
        methods: {
            determineStatus() {

                //  If approved
                if( this.hasApproved() ){

                    // Appointment approved status details
                    this.status.description = 'This appointment has been approved for processing';
                    this.status.text = 'Approved';
                    this.status.color = '#2d8cf0';
                //  If confirmed
                }else if( this.hasConfirmed() ){
                    // Appointment confirmed status details
                    this.status.description = 'This appointment has been confirmed';
                    this.status.text = 'Confirmed';
                    this.status.color = '#19be6b';
                //  If declined
                }else if( this.hasDeclined() ){
                    // Appointment declined status details
                    this.status.description = 'This appointment has been declined';
                    this.status.text = 'Declined';
                    this.status.color = '#ed4014';
                //  If rechedule
                }else if( this.hasRescheduled() ){
                    
                    // Appointment paid status details
                    this.status.description = 'Reschedule requested';
                    this.status.text = 'Reschedule';
                    this.status.color = '#f90';

                //  If cancelled
                }else if( this.hasCancelled() ){
                    
                    // Appointment cancelled status details
                    this.status.description = 'This appointment has been cancelled';
                    this.status.text = 'Cancelled';
                    this.status.color = '#ed4014';

                //  If expired
                }else if( this.hasExpired() ){

                    // Appointment expired status details
                    this.status.description = 'This appointment has exceeded its appointed date';
                    this.status.text = 'Expired';
                    this.status.color = '#ed4014';

                //  If sent
                }else if( this.hasSent() ){

                    // Appointment sent status details
                    this.status.description = 'This appointment has been sent to the customer';
                    this.status.text = 'Sent';
                    this.status.color = '#ff9900';

                //  If draft
                }else if( this.IsDraft() ){

                    // Appointment approved status details
                    this.status.description = 'This appointment has not been approved';
                    this.status.text = 'Draft';
                    this.status.color = '#808695';
                } else{

                    // Appointment approved status details
                    this.status.description = 'The current status of the appointment is unknown';
                    this.status.text = '...';
                    this.status.color = '#808695';
                } 
            },
            hasApproved(){
                return this.localAppointment.current_activity_status == 'Approved' ? true: false;
            },
            hasConfirmed(){
                return this.localAppointment.current_activity_status == 'Confirmed' ? true: false;
            },
            hasDeclined(){
                return this.localAppointment.current_activity_status == 'Declined' ? true: false;
            },
            hasRescheduled(){
                return this.localAppointment.current_activity_status == 'Reschedule' ? true: false;
            },
            hasCancelled(){
                return this.localAppointment.current_activity_status == 'Cancelled' ? true: false;
            },
            hasExpired(){
                return this.localAppointment.current_activity_status == 'Expired' ? true: false;
            },
            hasSent(){
                return this.localAppointment.current_activity_status == 'Sent' ? true: false;
            },
            IsDraft(){
                return this.localAppointment.current_activity_status == 'Draft' ? true: false;
            }
        },
        created() {
            this.determineStatus();
        }
    }
</script>
