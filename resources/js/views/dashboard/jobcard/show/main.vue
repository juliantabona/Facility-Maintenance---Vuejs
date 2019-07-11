<template>

    <Row :gutter="20">
        
        <Col v-if="isLoading" span="8" offset="8">
            <!-- Loader -->
            <Loader :loading="true" type="text" class="text-left" theme="white">Loading jobcard...</Loader>
        </Col>

        <Col v-else span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar 
                :showBackBtn="true"
                :fallbackRoute="{ name: 'jobcards', params: { id: jobcard.id } }">

            </pageToolbar>

            <!-- Get the jobcard details -->
            <jobcardSummaryWidget v-if="jobcard" :jobcard="jobcard" :key="renderKey"></jobcardSummaryWidget>
            
        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Toolbars   */
    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';

    /*  Widgets   */
    import jobcardSummaryWidget from './../../../../widgets/jobcard/show/main.vue';


    export default {
        components: { 
          Loader, pageToolbar, jobcardSummaryWidget
        },
        data(){
            return {
                renderKey: 1,
                jobcard: null,
                isLoading: false,
            }
        },
        watch: {
            //  Watch for changes on the jobcard id
            '$route.params.id': function (id) {
                
                // react to route changes by fetching the associated jobcard...
                this.fetchJobcard();

            }
        },
        methods: {
            fetchJobcard() {

                //  If we have the route id set
                if( this.$route.params.id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    console.log('Start getting jobcard details...');

                    //  Additional data to eager load along with the jobcard found
                    var connections = '?connections=lifecycle,priority,categories,costcenters,assignedStaff';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/jobcards/'+this.$route.params.id+connections)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            //  Store the jobcard data
                            self.jobcard = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoading = false;

                            //  Error Location
                            console.log('dashboard/jobcard/show/main.vue - Error getting jobcard details...');

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
            //  Fetch the jobcard
            this.fetchJobcard();
        }
    };
</script>