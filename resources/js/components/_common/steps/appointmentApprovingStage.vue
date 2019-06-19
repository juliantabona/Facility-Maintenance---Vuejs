<template>

    <div>
        
        <!-- Fade loader - Shows when approving appointment  -->
        <fadeLoader :loading="isApprovingAppointment" msg="Approving, please wait..." class="mt-1 mb-3"></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="1" :showHeader="!localAppointment.has_approved" 
            :disabled="isApprovingAppointment" :showVerticalLine="true"
            :leftWidth="16" :rightWidth="8">

            <!-- Header  -->
            <template slot="header">

                <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                <span>This is a DRAFT appointment. You can take further actions once you approve it. <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
            
            </template>

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 v-if="" class="text-secondary">{{ localAppointment.has_approved ? 'Appointment Approved' : 'Approve Appointment' }}</h4>
                <Poptip word-wrap width="200" trigger="hover" :content="localAppointment.created_at | moment('DD MMM YYYY, H:mmA') || '___'">
                    <p class="mt-2 mb-2">
                        <span class="font-weight-bold">Created:</span> {{ localAppointment.created_at | moment("from", "now") | capitalize }}</a>
                    </p>
                </Poptip>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <Button class="float-right ml-2" type="default" size="large" @click.native="$emit('toggleEditMode', true)">
                    <span>{{ localAppointment.has_approved ? 'Edit Appointment' : 'Edit Draft' }}</span>
                </Button>

                <!-- Focus Ripple  -->
                <focusRipple v-if="!localAppointment.has_approved" color="blue" :ripple="true" class="float-right">

                    <!-- Create Appointment Button  -->
                    <Button type="primary" size="large" @click="approveAppointment()">
                        <span>Approve Draft</span>
                    </Button>

                </focusRipple>

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

    export default {
        components: { fadeLoader, stagingCard, focusRipple },
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
                isApprovingAppointment: false,
                localAppointment: this.appointment,
                localEditMode: this.editMode
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
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    //  Update the edit mode value
                    this.localEditMode = val;
                }
            }
        },
        methods: {
            
            approveAppointment(){

                var self = this;

                //  Start loader
                self.isApprovingAppointment = true;

                console.log('Attempt to approve appointment...');
                console.log( self.localAppointment );

                //  Additional data to eager load along with the appointment found
                var connections = '?connections=client,categories,assignedStaff';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments/'+self.localAppointment.id+'/approve'+connections)
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isApprovingAppointment = false;

                        //  Disable edit mode
                        self.editMode = false;
                        
                        //  Alert creation success
                        self.$Message.success('Appointment approved sucessfully!');

                        self.$emit('approved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isApprovingAppointment = false;

                        console.log('appointmentApprovingStage.vue - Error approving appointment...');
                        console.log(response);
                    });
            }
        }
    }
</script>
