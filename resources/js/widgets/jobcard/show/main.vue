<style scoped>

    .jobcard-widget{
        position: relative;
    }

    .fade-enter,
    .fade-leave-active {
        opacity: 0;
        transform: translateX(50px);
    }
    .fade-leave-active {
        position: absolute;
    }
 
    .animated {
        transition: all 0.5s;
        display: flex;
        width: 100%;
    }

</style>

<template>

    <div id="jobcard-widget">

        <!-- Get the summary header to display the jobcard #, status, due date, amount due and menu options -->
        <overview 
            v-if="!createMode && localJobcard.has_approved"
            :jobcard="localJobcard" 
            :editMode="editMode" 
            :createMode="createMode"
            @toggleEditMode="toggleEditMode($event)">
        </overview>

        <!-- Loaders for creating/saving jobcard -->
        <Row>
            <Col :span="24">
                <div v-if="isCreatingJobcard" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingJobcard" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>

        
        <transition-group name="fade">

            <Row v-if="localJobcard.has_approved"
                 :gutter="20" key="take_action_cards" class="animated">

                <!-- Recurring toggle switch, Recurring settings toolbox, Save changes button -->
                <Col :span="24">

                    <!-- Make recurring switch -->
                    <toggleSwitch v-bind:toggleValue.sync="showActionCards" 
                        @update:toggleValue="showActionCards == $event"
                        :ripple="false" :showIcon="true" onIcon="ios-flash-outline" offIcon="ios-flash-outline" 
                        title="Take Action:" onText="Yes" offText="No" poptipMsg="Turn to see actions you can take"
                        class="float-right p-2">
                    </toggleSwitch>

                    <div class="clearfix"></div>

                    <!-- Make recurring settings -->
                    <Row v-show="showActionCards" key="dynamic" class="animated mb-3">
                        

                        <Col span="24">
                            <Row :gutter="20" style="background:#eee;padding: 20px">

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Contractors" icon="ios-people-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-jobcard-proposals', params: { id: localJobcard.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Client Quotations" icon="ios-browsers-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-jobcard-quotations', params: { id: localJobcard.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Client Invoices" icon="ios-cash-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-jobcard-invoices', params: { id: localJobcard.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Request For Quotes" icon="ios-list-box-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-jobcard-proposals', params: { id: localJobcard.id } }">
                                    </IconAndCounterCard>

                                </Col>

                            </Row>
                        </Col>
                    </Row>

                </Col>

            </Row>

            <!-- Activity cards & Jobcard Steps -->
            <Row :gutter="20" key="activity_n_steps" class="animated">
                <!-- White overlay when creating/saving jobcard -->
                <Spin size="large" fix v-if="isSavingJobcard || isCreatingJobcard"></Spin>

                <!-- Acitvity cards for showing summary of activities, sent companies, and sent receipt -->
                <Col v-if="localJobcard.has_approved" :span="5">
                
                    <!-- Activity Number Card -->
                    <IconAndCounterCard title="Activity" icon="ios-pulse-outline" :count="localJobcard.activity_count.total" class="mb-2" type="success"
                                        :route="{ name: 'show-jobcard-activities', params: { id: localJobcard.id } }">
                    </IconAndCounterCard>
                
                </Col>

                <!-- Jobcard steps, Approval step, Sending step and Payment step -->
                <Col :span="localJobcard.has_approved ? 19 : 24">
                    <!-- Get the staging toolbar to display the jobcard approved, sent/re-send and record payment stages -->
                    <steps 
                        v-if="!createMode"
                        :jobcard="localJobcard" :editMode="editMode" :createMode="createMode" 
                        @toggleEditMode="toggleEditMode($event)" 
                        @approved="updateJobcardData($event)">
                    </steps>
                </Col>

            </Row>

            <!-- Loaders for creating/saving jobcard -->
            <Row key="create_n_save_loaders" class="animated">
                <Col :span="24">
                    <div v-if="isCreatingJobcard" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                    <div v-if="isSavingJobcard" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
                </Col>
            </Row>

            <!-- Jobcard View/Editor -->
            <Row id="jobcard-summary"  key="jobcard_template" class="animated mb-5">
                <Col :span="24">
                    <Card :style="{ width: '100%' }">
                        
                        <!-- White overlay when creating/saving jobcard -->
                        <Spin size="large" fix v-if="isSavingJobcard || isCreatingJobcard"></Spin>

                        <!-- Main header -->
                        <div slot="title">
                            <h5>Jobcard Summary</h5>
                        </div>

                        <!-- Jobcard options -->
                        <div slot="extra" v-if="showMenuBtn && !createMode">
                            
                            <mainHeader :jobcard="localJobcard" :editMode="editMode" @toggleEditMode="toggleEditMode($event)"></mainHeader>

                        </div>


                        <Row>

                            <Col span="24" class="pr-4">

                                <!-- Create button -->
                                <basicButton v-if="createMode" 
                                                class="float-right mb-2 ml-3" 
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="createJobcard()">
                                    Create Jobcard
                                </basicButton>

                                <!-- Save changes button -->
                                <basicButton  v-if="!createMode && jobcardHasChanged" 
                                                class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="saveJobcard()">
                                    Save Changes
                                </basicButton>

                                <!-- Edit mode switch -->
                                <editModeSwitch v-bind:editMode.sync="editMode" :ripple="false" class="float-right mb-2"></editModeSwitch>

                                <div class="clearfix"></div>

                            </Col>

                        </Row>

                        <Row>

                            <Col span="12" class="mb-3 ml-2">

                                <!-- Client information -->
                                <companyOrIndividualDetails 
                                    :style="{ background: '#f5f7fa',borderRadius: '10px', padding: '15px' }"
                                    :editMode="editMode"
                                    refName="Client"
                                    :profile="localJobcard.client" 
                                    :modelId="( createMode ? $route.query.clientId : localJobcard.client_id )" 
                                    :modelType="( createMode ? $route.query.clientId : localJobcard.client_type )" 
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="true"
                                    @updated:companyOrIndividual="updateClient($event)"
                                    @updated:phones="updatePhoneChanges(localJobcard.client, $event)"
                                    @reUpdateParent="storeOriginalInvoice()">
                                </companyOrIndividualDetails>

                                <!-- Client selector -->
                                <clientSelector v-if="editMode" :style="{maxWidth: '250px'}" class="mt-2"
                                    @updated="changeClient($event)">
                                </clientSelector>

                            </Col>

                        </Row>

                        <Row>
                            <Col span="24" class="pl-2">
                                            
                                <!-- Create/Edit Jobcard -->
                                <jobcardWidget 
                                    :editMode="editMode"
                                    :jobcardId="null"
                                    v-bind:jobcard.sync="localJobcard"
                                    @update:jobcard="localJobcard = $event"
                                    :showClientOrSupplierSelector="true"
                                    :hideBio="false" 
                                    :hideSaveBtn="true"
                                    :hideSummaryToggle="true" 
                                    :activateSummaryMode="true"
                                    :canSaveOnCreate="false"
                                    @created:jobcard=""
                                    @updated:jobcard="">
                                </jobcardWidget>

                            </Col>

                        </Row>

                    </Card>
                </Col>
            </Row>

        </transition-group>

    </div>

</template>

<script>


    /*  Local components    */
    import overview from './overview.vue';
    import steps from './steps.vue';
    import mainHeader from './header.vue';
    import jobcardWidget from './jobcardDetails.vue'; 
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';
    import editModeSwitch from './../../../components/_common/switches/editModeSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    /*  Selectors  */
    import companyOrIndividualDetails from './companyOrIndividualDetails.vue';
    import clientSelector from './../../../components/_common/selectors/clientSelector.vue'; 

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            overview, steps, mainHeader, jobcardWidget,
            basicButton, toggleSwitch, editModeSwitch,
            Loader, IconAndCounterCard, companyOrIndividualDetails,
            clientSelector
        },
        props: {
            jobcard: {
                type: Object,
                default: null
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            create: {
                type: Boolean,
                default: false
            },
            modelType:{
                type: String,
                default: ''
            },
            modelId:{
                type: Number,
                default: null
            }
        },
        data(){
            return {

                user: auth.user,

                //  Modes
                editMode: false,
                createMode: this.create,

                //  Loading States
                isSavingJobcard: false,
                isCreatingJobcard: false,

                //  Local Jobcard and state changes
                localJobcard: (this.jobcard || {}),
                _localJobcardBeforeChange: {},
                jobcardHasChanged: false,
                showActionCards: true
                
            }
        },
        watch: {
            localJobcard: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    console.log(val);
                    console.log('checkIfjobcardHasChanged - 1');
                    this.jobcardHasChanged = this.checkIfjobcardHasChanged(val);
                },
                deep: true
            }
        },
        methods: {
            toggleEditMode(activate = true){

                var self = this,
                    options = {
                        easing: 'ease-in-out',
                        offset: -100,
                        force: true,
                        cancelable: true,
                        onStart: function(element) {
                            // scrolling started
                        },
                        onDone: function(element) {
                            //  Activate edit mode
                            self.editMode = activate;
                        },
                        onCancel: function() {
                        // scrolling has been interrupted
                        },
                        x: false,
                        y: true
                    }

                //var cancelScroll = VueScrollTo.scrollTo('jobcard-summary', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#jobcard-summary', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            activateCreateMode: function(){
                //  Activate edit mode
                self.editMode = true;
            },
            changeClient(newClient){

                if(newClient.model_type == 'user'){
                    this.$Notice.success({
                        title: 'Client changed to ' + newClient.first_name +  ' ' + newClient.last_name
                    });

                }else if(newClient.model_type == 'company'){
                    this.$Notice.success({
                        title: 'Client changed to ' + newClient.name
                    });
                }

                this.client = this.localJobcard.client = newClient;
                
                this.jobcardHasChanged = this.checkIfjobcardHasChanged();

            },
            checkIfjobcardHasChanged: function(updatedJobcard = null){
                
                var currentJobcard = _.cloneDeep(updatedJobcard || this.localJobcard);
                var isNotEqual = !_.isEqual(currentJobcard, this._localJobcardBeforeChange);

                console.log('currentJobcard');
                console.log(currentJobcard);
                console.log('_localJobcardBeforeChange');
                console.log(this._localJobcardBeforeChange);
                console.log('isNotEqual:' +isNotEqual);

                return isNotEqual;
            },
            storeOriginalJobcard(){
                //  Store the original jobcard
                this._localJobcardBeforeChange = _.cloneDeep(this.localJobcard);
                console.log('stored _localJobcardBeforeChange');
                console.log(this._localJobcardBeforeChange);
            },
            saveJobcard(){
                
                var self = this;

                //  Start loader
                self.isSavingJobcard = true;

                console.log('Attempt to save jobcard...');

                console.log( self.localJobcard );

                //  Form data to send
                let jobcardData = { jobcard: self.localJobcard };

                console.log(jobcardData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies/'+self.localJobcard.id, jobcardData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingJobcard = false;

                        /*
                        *  updateJobcardData() : This method ensures the property is
                        *  updated as a reactive property and triggers future view updates:
                        */
                        self.updateJobcardData(data);

                        //  Alert creation success
                        self.$Message.success('Jobcard saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingJobcard = false;

                        console.log('jobcardSummaryWidget.vue - Error saving jobcard...');
                        console.log(response);
                    });
            },
            createJobcard(){

                var self = this;

                //  Start loader
                self.isCreatingJobcard = true;

                console.log('Attempt to create jobcard...');

                console.log( self.localJobcard );

                //  Form data to send
                let jobcardData = { jobcard: self.localJobcard };

                console.log(jobcardData);

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies'+associatedModel, jobcardData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingJobcard = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the current state of the jobcard as the original jobcard
                        self.storeOriginalJobcard();
                        
                        console.log('checkIfjobcardHasChanged - 3');
                        self.jobcardHasChanged = self.checkIfjobcardHasChanged();

                        //  Alert creation success
                        self.$Message.success('Jobcard created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('jobcardCreated', data);

                        //  Go to jobcard
                        self.$router.push({ name: 'show-jobcard', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingJobcard = false;

                        console.log('jobcardSummaryWidget.vue - Error creating jobcard...');
                        console.log(response);
                    });
            },
            updateJobcardData(newJobcard){
                
                //  Disable edit mode
                this.editMode = false;
                
                /*
                 *  Vue.set()
                 *  We use Vue.set to set a new object property. This method ensures the  
                 *  property is created as a reactive property and triggers view updates:
                 */
            
                for(var x = 0; x <  _.size(newJobcard); x++){
                    var key = Object.keys(this.localJobcard)[x];
                    this.$set(this.localJobcard, key, newJobcard[key]);
                }

                //  Store the current state of the jobcard as the original jobcard
                this.storeOriginalJobcard();

                console.log('checkIfjobcardHasChanged - 4');

                //  Update the jobcard change status
                this.jobcardHasChanged = this.checkIfjobcardHasChanged();

            },
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Store the current state of the jobcard as the original jobcard
                console.log('Now lets store that original jobcard!');

                this.storeOriginalJobcard();

                //  Update the jobcard change status
                this.jobcardHasChanged = this.checkIfjobcardHasChanged();

                if(this.createMode){
                    this.activateCreateMode();
                }

            })
        }
    };
  
</script>