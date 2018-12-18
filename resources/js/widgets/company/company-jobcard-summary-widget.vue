<template>

    <Card class="client-work-summary-widget" :style="{ width: '100%' }">

        <div slot="title">
            <h5>
                <Icon type="ios-time-outline" :size="18" class="mr-2"></Icon> 
                {{ modelType == 'client' ? 'Recent Work' : 'Contracted Jobs' }}
            </h5>
        </div>

        <Loader v-if="isLoading" :loading="isLoading"></Loader>
        
        <Alert v-if="!isLoading && !jobcards.length" type="warning">
            No information found
        </Alert>

        <Row v-if="jobcards.length">
            <Col v-for="jobcard in jobcards" :key="jobcard.id">
                <jobcardSummaryWidget :style="{ marginBottom: '10px' }"
                    :jobcard="jobcard"

                    :showMenuBtn="false" :showMenuEditBtn="true" :showRemoveBtn="true" :showMenuAddClientBtn="true"
                    :showMenuAddSupplierBtn="true" :showMenuAddLabourBtn="true" :showMenuAddAssetBtn="true"
                    
                    :showHeaderSection="false" :showDescriptionSection="true" :showStatusSection="false" 
                    :showPublishSection="false" :showResourceSection="false" :showActionToolbalSection="true"
                    
                    :showAuthourizedStatus="true" :showProcessStatus="true"
                    :showTitle="true" :showDescription="true" :showDeadline="false" :showStartDate="false" 
                    :showEndDate="false" :showPriority="false" :showCategory="false" :showCostCenters="false"
                    :showCreatedBy="false" :showCreatedByDate="false" :showAuthourizedBy="false" 
                    :showAuthourizedByDate="false">
                </jobcardSummaryWidget>
                <Divider />
            </Col>

        </Row> 

    </Card>

</template>

<script>

    import Loader from './../../components/Loader.vue';
    import jobcardSummaryWidget from './../jobcard/jobcard-summary-widget.vue';

    export default {
        components: { Loader, jobcardSummaryWidget },
        props: {
            modelId: {              //  id of client or supplier
                type: Number,
                default: null
            },
            modelType: {            //  client, supplier
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
                if(this.modelId != null && this.modelType != ''){

                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/jobcards?model='+this.modelType+'&&modelId='+this.modelId+'&&limit='+this.limit)
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