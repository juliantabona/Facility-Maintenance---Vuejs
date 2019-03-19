<style scoped>

    .company-widget{
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

    <div id="company-widget">

        <!-- Get the summary header to display the company #, status, due date, amount due and menu options -->
        <overview 
            v-if="!createMode && localCompany.has_approved"
            :company="localCompany" 
            :editMode="editMode" 
            :createMode="createMode"
            @toggleEditMode="toggleEditMode($event)">
        </overview>

        <!-- Loaders for creating/saving company -->
        <Row>
            <Col :span="24">
                <div v-if="isCreatingCompany" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingCompany" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>

        
        <transition-group name="fade">

            <Row v-if="localCompany.has_approved"
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
                                    <IconAndCounterCard title="Proposals" icon="ios-filing-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-company-proposals', params: { id: localCompany.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Quotations" icon="ios-browsers-outline" :count="localCompany.quotation_count.total" class="mb-2" type="success"
                                                        :route="{ name: 'show-company-quotations', params: { id: localCompany.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Invoices" icon="ios-cash-outline" :count="localCompany.invoice_count.total" class="mb-2" type="success"
                                                        :route="{ name: 'show-company-invoices', params: { id: localCompany.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Jobcards" icon="ios-briefcase-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-company-invoices', params: { id: localCompany.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Promotions" icon="ios-megaphone-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-company-invoices', params: { id: localCompany.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Appointments" icon="ios-calendar-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-company-activities', params: { id: localCompany.id } }">
                                    </IconAndCounterCard>

                                </Col>

                                <Col span="8">

                                    <!-- Activity Number Card -->
                                    <IconAndCounterCard title="Users" icon="ios-contact-outline" class="mb-2" type="success"
                                                        :route="{ name: 'show-company-activities', params: { id: localCompany.id } }">
                                    </IconAndCounterCard>

                                </Col>


                            </Row>
                        </Col>
                    </Row>

                </Col>

            </Row>

            <!-- Activity cards & Company Steps -->
            <Row :gutter="20" key="activity_n_steps" class="animated">
                <!-- White overlay when creating/saving company -->
                <Spin size="large" fix v-if="isSavingCompany || isCreatingCompany"></Spin>

                <!-- Acitvity cards for showing summary of activities, sent companies, and sent receipt -->
                <Col v-if="localCompany.has_approved" :span="5">
                
                    <!-- Activity Number Card -->
                    <IconAndCounterCard title="Activity" icon="ios-pulse-outline" :count="localCompany.activity_count.total" class="mb-2" type="success"
                                        :route="{ name: 'show-company-activities', params: { id: localCompany.id } }">
                    </IconAndCounterCard>
                
                </Col>

                <!-- Company steps, Approval step, Sending step and Payment step -->
                <Col :span="localCompany.has_approved ? 19 : 24">
                    <!-- Get the staging toolbar to display the company approved, sent/re-send and record payment stages -->
                    <steps 
                        v-if="!createMode"
                        :company="localCompany" :editMode="editMode" :createMode="createMode" 
                        @toggleEditMode="toggleEditMode($event)" 
                        @approved="updateCompanyData($event)">
                    </steps>
                </Col>

            </Row>

            <!-- Loaders for creating/saving company -->
            <Row key="create_n_save_loaders" class="animated">
                <Col :span="24">
                    <div v-if="isCreatingCompany" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                    <div v-if="isSavingCompany" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
                </Col>
            </Row>

            <!-- Company View/Editor -->
            <Row id="company-summary"  key="company_template" class="animated mb-5">
                <Col :span="24">
                    <Card :style="{ width: '100%' }">
                        
                        <!-- White overlay when creating/saving company -->
                        <Spin size="large" fix v-if="isSavingCompany || isCreatingCompany"></Spin>

                        <!-- Main header -->
                        <div slot="title">
                            <h5>Company Summary</h5>
                        </div>

                        <!-- Company options -->
                        <div slot="extra" v-if="showMenuBtn && !createMode">
                            
                            <mainHeader :company="localCompany" :editMode="editMode" @toggleEditMode="toggleEditMode($event)"></mainHeader>

                        </div>

                        <Row>

                            <Col span="24" class="pr-4">

                                <!-- Create button -->
                                <basicButton v-if="createMode" 
                                                class="float-right mb-2 ml-3" 
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="createCompany()">
                                    Create Company
                                </basicButton>

                                <!-- Save changes button -->
                                <basicButton  v-if="!createMode && companyHasChanged" 
                                                class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="saveCompany()">
                                    Save Changes
                                </basicButton>

                                <!-- Edit mode switch -->
                                <editModeSwitch v-bind:editMode.sync="editMode" :ripple="false" class="float-right mb-2"></editModeSwitch>

                                <div class="clearfix"></div>

                            </Col>

                        </Row>

                        <Row>
                            <Col span="24" class="pl-2">
                                            
                                <!-- Create/Edit Company -->
                                <companyWidget 
                                    :editMode="editMode"
                                    :companyId="null"
                                    v-bind:company.sync="localCompany"
                                    @update:company="localCompany = $event"
                                    :showClientOrSupplierSelector="true"
                                    :hideBio="false" 
                                    :hideSaveBtn="true"
                                    :hideSummaryToggle="true" 
                                    :activateSummaryMode="true"
                                    :canSaveOnCreate="false"
                                    @created:company=""
                                    @updated:company="">
                                </companyWidget>

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
    import companyWidget from './companyDetails.vue'; 
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';
    import editModeSwitch from './../../../components/_common/switches/editModeSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            overview, steps, mainHeader, companyWidget,
            basicButton, toggleSwitch, editModeSwitch,
            Loader, IconAndCounterCard
        },
        props: {
            company: {
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
                isSavingCompany: false,
                isCreatingCompany: false,

                //  Local Company and state changes
                localCompany: (this.company || {}),
                _localCompanyBeforeChange: {},
                companyHasChanged: false,
                showActionCards: true
                
            }
        },
        watch: {
            localCompany: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    console.log(val);
                    console.log('checkIfcompanyHasChanged - 1');
                    this.companyHasChanged = this.checkIfcompanyHasChanged(val);
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

                //var cancelScroll = VueScrollTo.scrollTo('company-summary', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#company-summary', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            activateCreateMode: function(){
                //  Activate edit mode
                self.editMode = true;
            },
            checkIfcompanyHasChanged: function(updatedCompany = null){
                
                var currentCompany = _.cloneDeep(updatedCompany || this.localCompany);
                var isNotEqual = !_.isEqual(currentCompany, this._localCompanyBeforeChange);

                console.log('currentCompany');
                console.log(currentCompany);
                console.log('_localCompanyBeforeChange');
                console.log(this._localCompanyBeforeChange);
                console.log('isNotEqual:' +isNotEqual);

                return isNotEqual;
            },
            storeOriginalCompany(){
                //  Store the original company
                this._localCompanyBeforeChange = _.cloneDeep(this.localCompany);
                console.log('stored _localCompanyBeforeChange');
                console.log(this._localCompanyBeforeChange);
            },
            saveCompany(){
                
                var self = this;

                //  Start loader
                self.isSavingCompany = true;

                console.log('Attempt to save company...');

                console.log( self.localCompany );

                //  Form data to send
                let companyData = { company: self.localCompany };

                console.log(companyData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies/'+self.localCompany.id, companyData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingCompany = false;

                        /*
                        *  updateCompanyData() : This method ensures the property is
                        *  updated as a reactive property and triggers future view updates:
                        */
                        self.updateCompanyData(data);

                        //  Alert creation success
                        self.$Message.success('Company saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingCompany = false;

                        console.log('companySummaryWidget.vue - Error saving company...');
                        console.log(response);
                    });
            },
            createCompany(){

                var self = this;

                //  Start loader
                self.isCreatingCompany = true;

                console.log('Attempt to create company...');

                console.log( self.localCompany );

                //  Form data to send
                let companyData = { company: self.localCompany };

                console.log(companyData);

                var associatedModel = (this.modelType && this.modelId) ? '?model='+this.modelType+'&modelId='+this.modelId: '';
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies'+associatedModel, companyData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingCompany = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the current state of the company as the original company
                        self.storeOriginalCompany();
                        
                        console.log('checkIfcompanyHasChanged - 3');
                        self.companyHasChanged = self.checkIfcompanyHasChanged();

                        //  Alert creation success
                        self.$Message.success('Company created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('companyCreated', data);

                        //  Go to company
                        self.$router.push({ name: 'show-company', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingCompany = false;

                        console.log('companySummaryWidget.vue - Error creating company...');
                        console.log(response);
                    });
            },
            updateCompanyData(newCompany){
                
                //  Disable edit mode
                this.editMode = false;
                
                /*
                 *  Vue.set()
                 *  We use Vue.set to set a new object property. This method ensures the  
                 *  property is created as a reactive property and triggers view updates:
                 */
            
                for(var x = 0; x <  _.size(newCompany); x++){
                    var key = Object.keys(this.localCompany)[x];
                    this.$set(this.localCompany, key, newCompany[key]);
                }

                //  Store the current state of the company as the original company
                this.storeOriginalCompany();

                console.log('checkIfcompanyHasChanged - 4');

                //  Update the company change status
                this.companyHasChanged = this.checkIfcompanyHasChanged();

            },
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Store the current state of the company as the original company
                console.log('Now lets store that original company!');
                this.storeOriginalCompany();

                //  Update the company change status
                this.companyHasChanged = this.checkIfcompanyHasChanged();

                if(this.createMode){
                    this.activateCreateMode();
                }

            })
        }
    };
  
</script>