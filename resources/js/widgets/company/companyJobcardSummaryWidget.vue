<template>

    <Card class="client-work-summary-widget" :style="{ width: '100%' }">

        <!-- Heading -->

        <div slot="title">
            <h5>
                <Icon type="ios-time-outline" :size="18" class="mr-2"></Icon> 
                {{ relationType == 'client' ? 'Recent Work' : 'Contracted Jobs' }}
            </h5>
        </div>

        <!-- Loader -->

        <Loader v-if="isLoading" :loading="isLoading"></Loader>
        

        <!-- No Jobcards Alert -->

        <Alert v-if="!isLoading && !jobcards.length" type="warning">
            No information found
        </Alert>
        
        <!-- Jobcards summarized -->

        <Row v-if="jobcards.length">
            <Col v-for="(jobcard, i) in jobcards" :key="jobcard.id">
            
                <!-- Get the jobcard summary details -->

                <jobcardSummaryWidget 

                    v-model="jobcards[i]" :jobcardId="null"
                    
                    :showMenuBtn="false" :showAuthourizedStatus="true" :showProcessStatus="true"
                    :showTitle="true" :showDescription="true" :showDeadline="false" :showStartDate="false" :showEndDate="false"
                    :showPriority="false" :showCategory="false" :showCostCenters="false" :showCreatedBy="false" :showCreatedByDate="false"
                    :showAuthourizedBy="false" :showAuthourizedByDate="false" :showResourceTags="false" 
                    :showViewBtn="true" :showDownloadBtn="true" :showSendBtn="true" :showPublicBtn="true">
                </jobcardSummaryWidget>


                <Divider />

            </Col>

        </Row> 

    </Card>

</template>

<script>

    import Loader from './../../components/_common/loader/Loader.vue';
    import jobcardSummaryWidget from './../jobcard/jobcardSummaryWidget.vue';

    export default {
        components: { Loader, jobcardSummaryWidget },
        props: {
            modelId: {              //  id of client or supplier
                type: Number,
                default: null
            },
            relationType: {         //  client, supplier
                type: String,
                default: ''
            },
            limit: {                //  Limit of jobcards to return
                type: Number,
                default: 3
            }
        },
        data() {
            return {
                isLoading: false,
                jobcards: []
            }
        },
        methods: {
            fetch(){
                if(this.modelId != null && this.relationType != ''){

                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/jobcards?model='+this.relationType+'&&modelId='+this.modelId+'&&limit='+this.limit)
                        .then(({data}) => {

                            //  Stop loader
                            self.isLoading = false;

                            self.jobcards = data.data;
                
                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                }
            }
        },
        created() {
            this.fetch();
        } 
    };
  
</script>