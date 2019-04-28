<template>

    <div>

        <!-- Fade loader - Shows when recording appointment confirmation  -->
        <fadeLoader :loading="isRecordingConfirmation" msg="Confirming, please wait..."></fadeLoader>

        <!-- Fade loader - Shows when cancelling appointment confirmation  -->
        <fadeLoader :loading="isCancelingConfirmation" msg="Cancelling confirmation, please wait..."></fadeLoader>

        <!-- Fade loader - Shows when updating appointment reminders  -->
        <fadeLoader :loading="isUpdatingReminders" msg="Updating reminders, please wait..."></fadeLoader>

        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="3" :showHeader="false" 
            :disabled="isRecordingConfirmation || isCancelingConfirmation || isUpdatingReminders || (!localAppointment.has_sent && !localAppointment.has_skipped_sending)" :showVerticalLine="true"
            :leftWidth="14" :rightWidth="10">

            <!-- Left Content  -->
            <template slot="leftContent">
                <h4 class="text-secondary">{{ (localAppointment.current_activity_status == 'Confirmed') ? 'Appointment Confirmed' : 'Client Confirmation' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="(localAppointment.last_sent_activity || {}).created_at | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p v-if="(localAppointment.current_activity_status == 'Confirmed')" class="mt-2 mb-2">
                        <span class="font-weight-bold">Last Confirmed:</span> {{ (localAppointment.last_confirmed_activity || {}).created_at | moment("from", "now") }}<span v-if="!(localAppointment.current_activity_status == 'Confirmed')" class="font-weight-bold">Last Confirmed: Never</span>
                    </p>
                    <p v-else class="mt-2 mb-2">
                        <span class="font-weight-bold">Last Confirmed: Never</span>
                    </p>
                </Poptip>
            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <!-- Animated checkmark  -->
                <animatedCheckmark v-if="(localAppointment.current_activity_status == 'Confirmed')"></animatedCheckmark>

                <!-- Focus Ripple  -->
                <focusRipple v-if="!(localAppointment.current_activity_status == 'Confirmed') && (localAppointment.has_sent || localAppointment.has_skipped_sending)" 
                            :ripple="localAppointment.has_sent || localAppointment.has_skipped_sending" color="blue" class="float-right">
                    
                    <!-- Confirmation Button  -->
                    <Poptip confirm title="Are you sure this appointment has been accepted by the client?"  width="300"
                            ok-text="Yes" cancel-text="No" @on-ok="recordConfirmation()" placement="top">
                        
                        <Button v-if="localAppointment.has_sent || localAppointment.has_skipped_sending" type="primary" size="large">
                            <span>Confirm Appointment</span>
                        </Button>

                    </Poptip>

                </focusRipple>

                <!-- Cancel Confirmation Button  -->
                <Button v-else class="float-right mt-2 mr-2" type="default" size="large" @click="cancelConfirmation()">
                    <span>Cancel Confirmation</span>
                </Button>
                
            </template>

            <!-- Extra Content  -->
            <template slot="extraContent">
                
                <Row :gutter="20" v-if="(localAppointment.has_sent || localAppointment.has_skipped_sending) && !(localAppointment.current_activity_status == 'Confirmed')">

                    <!-- Schedule Client Reminders -->
                    <Row>
                        <Col span="24">
                            <div style="background:#eee;padding: 20px">
                                <Card :bordered="false">
                                    <Row>
                                        <Col span="24" class="mb-3">
                                            <h6>
                                                <Icon type="ios-information-circle-outline" :size="24" :style="{ marginTop:'-3px' }" />
                                                <span class="font-weight-bold">Get appointments confirmed on time by scheduling reminders for your customer:</span>
                                                <Alert class="mt-2 mb-1" :style="{ zIndex:'1' }">
                                                    <div class="float-left mr-2">
                                                        <span class="font-weight-bold">NOTE:</span> 
                                                    </div>
                                                    <div class="float-left">
                                                        {{ moment(localAppointment.created_at).subtract(1, 'days') }}
                                                        <span>Sms reminders will be sent to - "{{ ((localAppointment.client || {}).phones || [])[0] }}" - <span class="btn btn-link m-0 p-0">Edit</span></span><br>
                                                        <span>Email reminders will be sent to - "{{ (localAppointment.client || {}).email }}" - <span class="btn btn-link m-0 p-0">Edit</span></span>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </Alert>
                                            </h6>
                                        </Col>

                                        <!-- Reminders - Before Due Date  -->
                                        <Col span="24">
                                            <h6 class="text-secondary mb-3">Remind customer:</h6>
                                            <CheckboxGroup v-model="confirmationReminderTime">
                                                <Checkbox class="font-weight-bold" label="1-hr-b">1 hour before</Checkbox>
                                                <Checkbox class="font-weight-bold" label="2-hr-b">2 hours before</Checkbox>
                                                <Checkbox class="font-weight-bold" label="3-hr-b">6 hours before</Checkbox>
                                                <Checkbox class="font-weight-bold" label="12-hr-b">12 hours before</Checkbox>
                                                <Checkbox class="font-weight-bold" label="1-d-b">1 day before</Checkbox>
                                                <Checkbox class="font-weight-bold" label="2-d-b">2 days before</Checkbox>
                                            </CheckboxGroup>
                                        </Col>
                                        
                                        <Col span="24 mt-2 pt-3 border-top">
                                            <span class="font-weight-bold mr-1">Reminder Method: </span>

                                            <Row>
                                                <Col span="12">
                                                    <!-- Reminder method Selector -->
                                                    <Select v-model="confirmationReminderMethod" :style="{ width:'250px' }" placeholder="Select reminder method" multiple filterable>
                                                        <Option value="email" key="email">Email</Option>
                                                        <Option value="sms" key="sms" :disabled="true">SMS</Option>
                                                    </Select>
                                                </Col>
                                            
                                                <Col span="12">
                                                    <!-- Save Button -->
                                                    <Button class="float-right" type="primary" size="large" @click="updateConfirmationReminders()">
                                                        <span>Save Reminders</span>
                                                    </Button>
                                                </Col>
                                            </Row>

                                        </Col>
                                    </Row>
                                </Card>
                            </div>
                        </Col>
                    </Row>

                </Row>
                
            </template>
            
        </stagingCard>

    </div>

</template>
<script type="text/javascript">

    import stagingCard from './main.vue';
    import fadeLoader from './../loaders/fadeLoader.vue';
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    import moment from 'moment';

    export default {
        components: { fadeLoader, stagingCard, animatedCheckmark, focusRipple },
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
                moment: moment,
                isRecordingConfirmation: false,
                isCancelingConfirmation: false,
                isUpdatingReminders: false,
                localAppointment: this.appointment,

                confirmationReminderTime: this.getConfirmationReminderTime(),
                confirmationReminderMethod: this.getConfirmationReminderMethod(),
            }
        },
        watch: {

            //  Watch for changes on the appointment
            appointment: {
                handler: function (val, oldVal) {

                    //  Update the local appointment value
                    this.localAppointment = val;

                    //  Update the confirmation reminder times
                    this.confirmationReminderTime = this.getConfirmationReminderTime();

                    //  Update the confirmation reminder methods
                    this.confirmationReminderMethod = this.getConfirmationReminderMethod();

                },
                deep: true
            }
        },
        methods: {

            getConfirmationReminderTime: function(){
                if( ((this.appointment || {}).reminders || {}).length ){
                    return this.appointment.reminders.map(reminder => reminder.days_after);
                }else{
                    return [];
                }

            },
            getConfirmationReminderMethod: function(){
                if( ((this.appointment || {}).reminders || {}).length ){
                    return this.appointment.reminders.map(reminder => {
                        var can = [];

                        //  If this reminder can be emailed return 'email' value
                        if(reminder.can_email){
                            can.push('email');

                        //  If this reminder can be sms'd return 'sms' value
                        }else if(reminder.can_sms){
                            can.push('sms');
                        }

                        return can;

                    //  if result is [['email', 'email'], ['sms', 'sms']] then flatten to ['email', 'email', 'sms', 'sms']
                    }).flat()

                    //  if result is ['email', 'email', 'sms', 'sms'] then filter to only unique values ['email', 'sms']
                    .filter( (value, index, self) => { 
                        return self.indexOf(value) === index;
                    });
                }else{
                    return ['email'];
                }

            },
            recordConfirmation(){

                var self = this;

                //  Start loader
                self.isRecordingConfirmation = true;

                console.log('Attempt to record appointment confirmation...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments/'+self.localAppointment.id+'/confirm')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isRecordingConfirmation = false;
                        
                        //  Alert creation success
                        self.$Message.success('Appointment confirmed sucessfully!');

                        //  Alert parent and pass updated appointment data
                        self.$emit('confirmed', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isRecordingConfirmation = false;

                        console.log('appointmentConfirmationStage.vue - Error recording appointment confirmation...');
                        console.log(response);
                    });
            },
            cancelConfirmation(){

                var self = this;

                //  Start loader
                self.isCancelingConfirmation = true;

                console.log('Attempt to cancel appointment confirmation...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments/'+self.localAppointment.id+'/cancel-confirmation')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isCancelingConfirmation = false;
                        
                        //  Alert creation success
                        self.$Message.success('Appointment confirmation cancelled sucessfully!');

                        //  Alert parent and pass updated appointment data
                        self.$emit('cancelled', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCancelingConfirmation = false;

                        console.log('appointmentConfirmationStage.vue - Error canceling appointment confirmation...');
                        console.log(response);
                    });
            },
            updateConfirmationReminders(){

                var self = this;

                //  Start loader
                self.isUpdatingReminders = true;

                var remindersData = { 
                    reminders: {
                        days: this.confirmationReminderTime,
                        method: this.confirmationReminderMethod
                    } 
                };

                console.log('Attempt to update appointment confirmation reminders...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments/'+self.localAppointment.id+'/reminders', remindersData)
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isUpdatingReminders = false;
                        
                        //  Alert creation success
                        self.$Message.success('Reminder added sucessfully!');

                        //  Alert parent and pass updated appointment data
                        self.$emit('reminderSet', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isUpdatingReminders = false;

                        console.log('appointmentConfirmationStage.vue - Error updating appointment confirmation reminders...');
                        console.log(response);
                    });
            }
        }
    }
</script>
