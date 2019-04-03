<style scoped>

    .jobcard-calendar >>> .fc-event-container a{
        font-size: 12px;
        margin-bottom: 3px;
        padding: 2px 5px;
    }

</style>

<template>

    <Row>
        <Col span="20" offset="2">
            <!-- Loader -->
            <Loader v-if="isLoadingJobcards" :loading="isLoadingJobcards" type="text" class="text-left" theme="white">Loading calendar...</Loader>
        </Col>

        <Col span="20" offset="2">
            <full-calendar ref="calendar" v-if="!isLoadingJobcards" class="jobcard-calendar" 
                :events="jobcardEvents" :config="config"></full-calendar>
        </Col>
    </Row>

</template>
<script type="text/javascript">

    /*  Loaders   */
    import Loader from './../../components/_common/loaders/Loader.vue'; 

    import Poptip from 'iview/src/components/poptip/poptip.vue'

    import { FullCalendar } from 'vue-full-calendar';
    import 'fullcalendar/dist/fullcalendar.css';



    export default {
        components: { Loader, FullCalendar },
        data() {
            return {
                jobcards: [],
                isLoadingJobcards: false,
                config:{
                    defaultView: 'month',
                    eventRender: function (event, element, view) {
                        /*
                        var instance = new Event.extend(Poptip);
                        instance.$mount() // pass nothing
                        this.$refs.calendar.appendChild(instance.$el);
                        */
                        element.find('.fc-title').append('<div class="hr-line-solid-no-margin"></div><span style="font-size: 10px">'+event.description+'</span></div>');
                    },
                }
            }
        },
        computed: {
            jobcardEvents(){
                var events = this.jobcards.map( (jobcard, index) => {
                                return {
                                        title  : jobcard.title,
                                        description: jobcard.description,
                                        start  : jobcard.start_date,
                                        end  : jobcard.end_date,
                                        backgroundColor:'#2d8cf0',
                                        textColor:'#fff',
                                        classNames:['event-tag']
                                    }
                            }); 

                return events ? events : [];
            }
        },
        methods: {
            fetchJobcards() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoadingJobcards = true;

                console.log('Start getting jobcards details...');

                //  Additional data to eager load along with the jobcard found
                var connections = '?connections=lifecycle,priority,categories,costcenters,assignedStaff';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/jobcards?'+connections)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingJobcards = false;

                        //  Store the jobcard data
                        self.jobcards = data.data;

                        //  Re-render the component
                        self.renderComponent();

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingJobcards = false;

                        //  Error Location
                        console.log('dashboard/jobcard/show/main.vue - Error getting jobcard details...');

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){
            //  Fetch the company
            this.fetchJobcards();
        }
    }
</script>
