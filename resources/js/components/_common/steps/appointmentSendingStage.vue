<template>

    <div>

        <!-- Fade loader - Shows when sending/skipping to send the appointment  -->
        <fadeLoader :loading="isSendingAppointment" msg="Sending, please wait..."></fadeLoader>
        <fadeLoader :loading="isSkippingSendingAppointment" msg="Skipping, please wait..."></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="2" :showHeader="false" 
            :disabled="!localAppointment.has_approved || isSendingAppointment || isSkippingSendingAppointment" :showVerticalLine="true">

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 class="text-secondary">{{ localAppointment.has_sent ? 'Appointment Sent' : 'Send Appointment' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="(localAppointment.last_sent_activity || {}).created_at | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span v-if="!localAppointment.has_sent" class="font-weight-bold">Last Sent: Never</span>
                        <span v-if="localAppointment.has_sent" class="font-weight-bold">Last Sent:</span> {{ (localAppointment.last_sent_activity || {}).created_at | moment("from", "now") }}
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template v-if="localAppointment.has_approved" slot="rightContent">
                
                <!-- Skip Sending Button -->
                <Button v-if="!localAppointment.has_sent && !localAppointment.has_skipped_sending" class="float-right ml-2" type="default" size="large" @click="skipSendAppointment()">
                    <span>Skip</span>
                </Button>

                <!-- Focus Ripple  -->
                <focusRipple color="blue" :ripple="(localAppointment.has_approved && !localAppointment.has_sent) && !localAppointment.has_skipped_sending" class="float-right">

                    <!-- Send/Resend Button -->
                    <Button :type="localAppointment.has_sent ? 'default' : 'primary'" 
                            size="large" @click="isOpenSendAppointmentModal = true">
                        <span>{{ localAppointment.has_sent ? 'Resend Appointment': 'Send Appointment' }}</span>
                    </Button>

                </focusRipple>
                
            </template>
            
        </stagingCard>

        <!-- 
            MODAL TO SEND APPOINTMENT - VIA EMAIL
        -->
        <sendAppointmentModal 
            v-if="isOpenSendAppointmentModal" 
            :appointment="localAppointment"
            smsTemplate="appointment-sms"
            :smsTemplateData="localAppointment" 
            @visibility="isOpenSendAppointmentModal = $event"
            @sent="$emit('sent', $event)">
        </sendAppointmentModal>

    </div>

</template>
<script type="text/javascript">

    import stagingCard from './main.vue';
    import fadeLoader from './../loaders/fadeLoader.vue';
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    /*  Modals  */
    import sendAppointmentModal from './../modals/sendAppointmentModal.vue';

    export default {
        components: { fadeLoader, stagingCard, focusRipple, sendAppointmentModal },
        props: {
            appointment: {
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
                isOpenSendAppointmentModal: false,
                isSendingAppointment: false,
                isSkippingSendingAppointment: false,
                localAppointment: this.appointment
            }
        },
        watch: {

            //  Watch for changes on the appointment
            appointment: {
                handler: function (val, oldVal) {
                    
                    //  Update the local appointment value
                    this.localAppointment = val;

                },
                deep: true
            }
        },
        methods: {
            skipSendAppointment(){

                var self = this;

                //  Start loader
                self.isSkippingSendingAppointment = true;

                console.log('Attempt to skip sending of appointment...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments/'+self.localAppointment.id+'/skip-send')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSkippingSendingAppointment = false;
                        
                        //  Alert skip success
                        self.$Message.success('Step skipped sucessfully!');

                        //  Notify parent on updates
                        //  NOTE that "data = updated appointment"
                        self.$emit('skipped', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSkippingSendingAppointment = false;

                        console.log('appointmentSummaryWidget.vue - Error skipping of appointment...');
                        console.log(response);
                    });
            }

        }
    }
</script>
