<template>

    <Row :gutter="20">
        
        <Col v-if="!appointment" span="20" offset="2">
            <!-- Loader -->
            <Loader v-if="true" :loading="true" type="text" class="text-left" theme="white">Loading appointment...</Loader>
        </Col>

        <Col v-else span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar 
                :showBackBtn="true"
                :fallbackRoute="{ name: 'appointments', params: { id: appointment.id } }">

            </pageToolbar>

            <!-- Get the appointment details -->
            <appointmentSummaryWidget v-if="appointment" :appointment="appointment" :key="renderKey"></appointmentSummaryWidget>
            
        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Toolbars   */
    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';

    /*  Widgets   */
    import appointmentSummaryWidget from './../../../../widgets/appointment/show/main.vue';


    export default {
        components: { 
          Loader, pageToolbar, appointmentSummaryWidget
        },
        data(){
            return {
                renderKey: 1,
                appointment: null,
                isLoading: false,
            }
        },
        watch: {
            //  Watch for changes on the appointment id
            '$route.params.id': function (id) {
                
                // react to route changes by fetching the associated appointment...
                this.fetchAppointment();

            }
        },
        methods: {
            fetchAppointment() {

                //  If we have the route id set
                if( this.$route.params.id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    console.log('Start getting appointment details...');

                    //  Additional data to eager load along with the appointment found
                    var connections = '?connections=client,categories,assignedStaff';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/appointments/'+this.$route.params.id+connections)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            //  Store the appointment data
                            self.appointment = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoading = false;

                            //  Error Location
                            console.log('dashboard/appointment/show/main.vue - Error getting appointment details...');

                            //  Log the responce
                            console.log(response);    
                        });

                }
            },
            renderComponent: function(){
                //  Re-render the component
                this.renderKey++;
            }
        },
        created(){
            //  Fetch the appointment
            this.fetchAppointment();
        }
    };
</script>