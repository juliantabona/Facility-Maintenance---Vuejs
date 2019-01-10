<template>
    <div>
        
        <!-- Jobcard Loader -->
        <Loader v-if="isLoading" :loading="isLoading"></Loader>

        <!-- Jobcard Not Found Alert -->
        <notFoundAlert v-if="!JobcardExists && !isLoading" :message="'No Jobcard Found'"></notFoundAlert>

        <!-- Jobcard Lifecycle - Only show if jobcard exists and is authourized -->
        <statusLifecycle v-if="JobcardExists && jobcard.authourizedBy" v-bind="$props" class="mb-1"></statusLifecycle>

        <!-- Jobcard Not Authourized Alert -->
        <notAuthourizedAlert v-if="JobcardExists && !jobcard.authourizedBy" v-bind="$props"></notAuthourizedAlert>

        <Card v-if="JobcardExists && !isLoading" :style="{ width: '100%' }">

            <!-- Jobcard Header -->

                <!-- Jobcard Title -->
                <jobcardHeaderTitle slot="title"></jobcardHeaderTitle>

                <!-- Jobcard Menu -->
                <jobcardHeaderMenu slot="extra" v-bind="$props"></jobcardHeaderMenu>

            <!-- Jobcard Body -->
            
            <jobcardSummaryBody v-bind="$props"></jobcardSummaryBody>

            <!-- Jobcard Footer -->
            
            <jobcardSummaryFooter v-bind="$props"></jobcardSummaryFooter>
            
        </Card>
    </div>

</template>

<script>

    import Loader from './../../components/_common/loader/Loader.vue';
    import notFoundAlert from './../../components/_common/notFound/notFoundAlert.vue';

    import statusLifecycle from './../../components/jobcard/lifecycle/progressFlow.vue';
    import notAuthourizedAlert from './../../components/jobcard/lifecycle/notAuthourizedAlert.vue';
    import jobcardHeaderTitle from './../../components/jobcard/header/title.vue';
    import jobcardHeaderMenu from './../../components/jobcard/header/menu.vue';
    import jobcardSummaryBody from './../../components/jobcard/body/main.vue';
    import jobcardSummaryFooter from './../../components/jobcard/footer/main.vue';

    export default {
        components: { Loader, notFoundAlert, statusLifecycle, notAuthourizedAlert, jobcardHeaderTitle, jobcardHeaderMenu, jobcardSummaryBody, jobcardSummaryFooter },

        /*  We create a property "jobcard" linked to the parent v-model. We still 
        *  need to register the "jobcard" property in the props array as seen below.
        *  After this we listen to the "updated" event usually called after
        *  we get a new jobcard object when making an Api call. This will 
        *  update the v-model jobcard value. As such our widget will be
        *  updated with the new jobcard information. 
        * 
        *  Read Docs: https://vuejs.org/v2/guide/components-custom-events.html
        */
        model: {
            prop: 'jobcard',
            event: 'updated'
        },
        props: {
            /*  Note that you can either provide a Jobcard Object or Jobcard Id
             *  to build the widget. If we don't have a Company Object, then we use 
             *  the jobcardId to make an Api and retrieve the jobcard information.
             */
            jobcard: {              //  Linked to the v-model property of the parent element
                type: Object,
                default: null
            },
            jobcardId: {
                type: Number,
                default: null
            },

            /*  Jobcard header related properties  */
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            showAuthourizedStatus: {
                type: Boolean,
                default: true
            },  
            showProcessStatus: {
                type: Boolean,
                default: true
            }, 
            
             /*  Jobcard body related properties  */
            showTitle: {
                type: Boolean,
                default: true
            }, 
            showDescription: {
                type: Boolean,
                default: true
            }, 
            showDeadline: {
                type: Boolean,
                default: true
            }, 
            showStartDate: {
                type: Boolean,
                default: true
            }, 
            showEndDate: {
                type: Boolean,
                default: true
            }, 
            showPriority: {
                type: Boolean,
                default: true
            }, 
            showCategory: {
                type: Boolean,
                default: true
            }, 
            showCostCenters: {
                type: Boolean,
                default: true
            },
            showCreatedBy: {
                type: Boolean,
                default: true
            }, 
            showCreatedByDate: {
                type: Boolean,
                default: true
            }, 
            showAuthourizedBy: {
                type: Boolean,
                default: true
            }, 
            showAuthourizedByDate: {
                type: Boolean,
                default: true
            },
            showResourceTags: {
                type: Boolean,
                default: true     
            },

            /*  Footer properties    */
            showViewBtn: {
                type: Boolean,
                default: true
            }, 
            showDownloadBtn: {
                type: Boolean,
                default: true
            }, 
            showSendBtn: {
                type: Boolean,
                default: true
            }, 
            showPublicBtn: {
                type: Boolean,
                default: true
            },
        },
        data(){
            return {
                isLoading: false
            }
        },
        computed: {
            JobcardExists: function(){
                if(this.jobcard){
                    return Object.keys(this.jobcard).length;
                }

                return false;
            }
        },
        methods: {
            fetch(){

                if(this.jobcardId != null){
                 
                    const self = this;

                    //  Additional data to eager load along with the jobcard found
                    var connections = 'connections=priority,categories,costcenters,quotations';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/jobcards/'+this.jobcardId+'?'+connections)
                        .then(({data}) => {

                            //  Stop loader
                            self.isLoading = false;

                            //  Emit an status of updated to the parent v-model so that we capture 
                            //  a new company object to display.
                            self.$emit('updated', data);
                
                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                }
            },
            getJobcard: function(){
                
                //  Check if the jobcard object is empty
                if(!this.JobcardExists){
                    
                   //  Check if the jobcard id is set
                    if(this.jobcardId != null){
                        
                        //  Make an Api call to get the jobcard information
                        this.fetch();

                    }else{
                        
                        //  Stop loading if we don't have anything at all
                        this.isLoading = false;
                    }

                }else{
                    
                    //  Stop loading if we have the jobcard object
                    this.isLoading = false;
                }
            }
        },
        created(){
            
            //  Start Loader
            this.isLoading = true;

            //  Get the jobcard using the Jobcard Object or Jobcard Id
            this.getJobcard();
        }
    };
  
</script>