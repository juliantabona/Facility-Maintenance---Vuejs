<template>

    <div>

        <!-- Fade loader - Shows when Converting jobcard  -->
        <fadeLoader :loading="isSavingNextStepJobcard" msg="Saving lifecycle, please wait..." class="mt-1 mb-3"></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="stageNumber" :showHeader="false" 
            :disabled="isSavingNextStepJobcard || (!localJobcard.has_approved)" :showVerticalLine="true"
            :leftWidth="24" :rightWidth="24">

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 class="text-secondary">{{ selectedStage ? selectedStage.name : 'Update Lifecycle' }}</h4>
                <Alert v-if="!selectedStage" type="warning" class="d-flex mt-2">
                    Update the jobcard lifecycle to the next stage until the job is successfully closed. Make sure to select stages in order of how your company works :)
                </Alert>

                <Alert v-else class="d-flex mt-2 mb-0">
                    <div v-if="selectedStage.name == 'Deposit Paid'">
                        <p class="mb-1">Amount Paid: {{ selectedStage.payment_amount | currency(((selectedStage.currency_type || {}).currency || {}).symbol) || '___' }}</p>
                        <p class="mb-1">Payment Method: {{ selectedStage.payment_method || '___' }}</p>
                        <p v-if="selectedStage.linked_invoice_id">Linked to: <router-link :to="{ name: 'show-invoice', params: { id: selectedStage.linked_invoice_id } }">Invoice #{{ selectedStage.linked_invoice_id }}</router-link></p>
                    </div>
                </Alert>

                
            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <Row>

                    <Col :span="selectedStage ? 24 : 16">
                        <div v-if="!selectedStage">
                            <!-- Gender Selector -->
                            <span class="font-weight-bold mb-1 d-block">Select Next Stage</span>
                            <jobcardLifecycleStageSelector
                                :jobcard="localJobcard"
                                :selectedStage="selectedStage"
                                @updated="">
                            </jobcardLifecycleStageSelector>
                        </div>

                    </Col>

                    <Col :span="selectedStage ? 24 : 8">

                        <!-- Focus Ripple  -->
                        <focusRipple :ripple="!selectedStage" color="blue" class="mt-3 float-right">

                            <!-- Convert Jobcard Button  -->
                            <Button :type="(!selectedStage) ? 'primary' : 'default' " size="large" @click="approveJobcard()">
                                <span>{{ (!selectedStage) ? 'Update' : 'Edit' }}</span>
                            </Button>

                        </focusRipple>

                    </Col>

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

    /*  Selectors   */
    import jobcardLifecycleStageSelector from './../selectors/jobcardLifecycleStageSelector.vue'; 

    export default {
        components: { fadeLoader, stagingCard, focusRipple, jobcardLifecycleStageSelector },
        props: {
            jobcard: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            },
            selectedStage: {
                type: Object,
                default: null
            },
            stageNumber: {
                type: Number,
                default: 1 
            }
        },
        data(){
            return {
                isSavingNextStepJobcard: false,
                localJobcard: this.jobcard,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the jobcard
            jobcard: {
                handler: function (val, oldVal) {

                    //  Update the local jobcard value
                    this.localJobcard = val;

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
            
            approveJobcard(){

                var self = this;

                //  Start loader
                self.isSavingNextStepJobcard = true;

                console.log('Attempt to approve jobcard...');
                console.log( self.localJobcard );

                //  Additional data to eager load along with the jobcard found
                var connections = '?connections=lifecycle,priority,categories,costcenters,assignedStaff';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/jobcards/'+self.localJobcard.id+'/convert'+connections)
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSavingNextStepJobcard = false;

                        //  Disable edit mode
                        self.editMode = false;
                        
                        //  Alert creation success
                        self.$Message.success('Jobcard converted sucessfully!');

                        self.$router.push({ name: 'show-invoice', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingNextStepJobcard = false;

                        console.log('jobcardSummaryWidget.vue - Error Converting jobcard...');
                        console.log(response);
                    });
            }
        }
    }
</script>
