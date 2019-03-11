<style scoped>

    .quotation-widget{
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

    <div id="quotation-widget">

        <!-- Get the summary header to display the quotation #, status, due date, amount due and menu options -->
        <overview 
            v-if="!createMode && localQuotation.has_approved"
            :quotation="localQuotation" :editMode="editMode" :createMode="createMode"
            @toggleEditMode="toggleEditMode($event)">
        </overview>

        <!-- Loaders for creating/saving quotation -->
        <Row v-if="createMode">
            <Col :span="24">
                <div v-if="isCreatingQuotation" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingQuotation" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>
        
        <transition-group name="fade">

            <!-- Activity cards & Quotation Steps -->
            <Row :gutter="20" key="activity_n_steps" class="animated">
                <!-- White overlay when creating/saving quotation -->
                <Spin size="large" fix v-if="isSavingQuotation || isCreatingQuotation"></Spin>

                <!-- Acitvity cards for showing summary of activities, sent quotations, and sent receipt -->
                <Col v-if="localQuotation.has_approved" :span="5">
                
                    <!-- Activity Number Card -->
                    <IconAndCounterCard title="Activity" icon="ios-pulse-outline" :count="localQuotation.activity_count.total" class="mb-2" type="success"
                                        :route="{ name: 'show-quotation-activities', params: { id: localQuotation.id } }">
                    </IconAndCounterCard>

                    <!-- Sent Incoices Number Card -->
                    <IconAndCounterCard title="Sent Quotations" icon="ios-send-outline" :count="localQuotation.sent_quotation_activity_count.total" class="mb-2"
                                        :route="{ name: 'show-quotation-activities', params: { id: localQuotation.id } , query: { activity_type: 'sent_sms,sent_email' } }">
                    </IconAndCounterCard>

                    <!-- Sent Recipts Number Card -->
                    <IconAndCounterCard title="Linked Invoices" icon="ios-paper-outline" :count="localQuotation.converted_activity_count.total" class="mb-2"
                                        :route="{ name: 'show-quotation-activities', params: { id: localQuotation.id } , query: { activity_type: 'converted' } }">
                    </IconAndCounterCard>
                
                </Col>

                <!-- Quotation steps, Approval step, Sending step and Payment step -->
                <Col :span="localQuotation.has_approved ? 19 : 24">
                    <!-- Get the staging toolbar to display the quotation approved, sent/re-send and record payment stages -->
                    <steps 
                        v-if="!createMode"
                        :quotation="localQuotation" :editMode="editMode" :createMode="createMode" 
                        @toggleEditMode="toggleEditMode($event)" 
                        @approved="updateQuotationData($event)" 
                        @sent="updateQuotationData($event)" @skipped="updateQuotationData($event)"
                        @converted="updateQuotationData($event)">
                    </steps>
                </Col>

            </Row>

            <!-- Loaders for creating/saving quotation -->
            <Row v-if="!createMode" key="create_n_save_loaders" class="animated">
                <Col :span="24">
                    <div v-if="isCreatingQuotation" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                    <div v-if="isSavingQuotation" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
                </Col>
            </Row>

            <!-- Quotation View/Editor -->
            <Row id="quotation-summary"  key="quotation_template" class="animated mb-5">
                <Col :span="24">
                    <Card :style="{ width: '100%' }">
                        
                        <!-- White overlay when creating/saving quotation -->
                        <Spin size="large" fix v-if="isSavingQuotation || isCreatingQuotation"></Spin>

                        <!-- Main header -->
                        <div slot="title">
                            <h5>Quotation Summary</h5>
                        </div>

                        <!-- Quotation options -->
                        <div slot="extra" v-if="showMenuBtn && !createMode">
                            
                            <mainHeader :quotation="localQuotation" :editMode="editMode" @toggleEditMode="toggleEditMode($event)"></mainHeader>

                        </div>

                        <Row>

                            <Col span="24" class="pr-4">

                                <!-- Create button -->
                                <basicButton v-if="createMode" 
                                                class="float-right mb-2 ml-3" 
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="createQuotation()">
                                    Create Quotation
                                </basicButton>

                                <!-- Save changes button -->
                                <basicButton  v-if="!createMode && quotationHasChanged" 
                                                class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="saveQuotation()">
                                    Save Changes
                                </basicButton>

                                <!-- Edit mode switch -->
                                <editModeSwitch v-bind:editMode.sync="editMode" :ripple="false" class="float-right mb-2"></editModeSwitch>

                                <div class="clearfix"></div>

                            </Col>

                            <Col span="12">

                                <!-- Company logo -->
                                <imageUploader 
                                    uploadMsg="Upload or change logo"
                                    :thumbnailStyle="{ width:'200px', height:'auto' }"
                                    :allowUpload="editMode"
                                    :multiple="false"
                                    :imageList="
                                        [{
                                            'name': 'Company Logo',
                                            'url': 'https://wave-prod-accounting.s3.amazonaws.com/uploads/invoices/business_logos/7cac2c58-4cc1-471b-a7ff-7055296fffbc.png'
                                        }]">
                                </imageUploader>
                            </Col>

                            <Col v-if="company" span="12" class="pr-4">
                                
                                <!-- Quotation Title -->
                                <mainTitle :quotation="localQuotation" :editMode="editMode"></mainTitle>
                                
                                <!-- Company information -->
                                <companyOrIndividualDetails 
                                    :client="localQuotation.customized_company_details" :editMode="editMode" align="right"
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="false"
                                    @updated:companyOrIndividual="updateCompany($event)"
                                    @updated:phones="updatePhoneChanges(localQuotation.customized_company_details, $event)"
                                    @reUpdateParent="storeOriginalQuotation()">
                                </companyOrIndividualDetails>

                            </Col>

                        </Row>

                        <Divider dashed class="mt-3 mb-3" />

                        <Row>
                            <Col span="12" class="pl-2">
                                <h3 v-if="!editMode" class="text-dark mb-3">{{ localQuotation.quotation_to_title ? localQuotation.quotation_to_title+':' : '' }}</h3>
                                <el-input v-if="editMode" placeholder="Quotation heading" v-model="localQuotation.quotation_to_title" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>

                                <!-- Client information -->
                                <companyOrIndividualDetails 
                                    :client="localQuotation.customized_client_details" :editMode="editMode"
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="true"
                                    @updated:companyOrIndividual="updateClient($event)"
                                    @updated:phones="updatePhoneChanges(localQuotation.customized_client_details, $event)"
                                    @reUpdateParent="storeOriginalQuotation()">
                                </companyOrIndividualDetails>

                                <!-- Client selector -->
                                <clientSelector :style="{maxWidth: '250px'}" class="mt-2"
                                    @updated="changeClient($event)">
                                </clientSelector>

                            </Col>
                            
                            <Col span="12">
                                <!-- Quotation details e.g) Reference #, created date, due date, grand total -->
                                <summaryDetails :quotation="localQuotation" :editMode="editMode" :createMode="createMode"></summaryDetails>
                            </Col>
                        
                            <Col span="24">
                                <!-- Edit mode toolbar e.g) Currency selector, primary/secondary color picker -->
                                <toolbar v-if="editMode" :quotation="localQuotation" :editMode="editMode" class="mt-2"></toolbar>
                            </Col>

                            <!-- Quotation list items (products/services) -->
                            <Col span="24">
                                <items :quotation="localQuotation" :editMode="editMode"></items>
                            </Col>

                        </Row>

                        <Divider dashed class="mt-0 mb-4" />

                        <!-- Total details e.g) Sub/grand total and tax amounts -->
                        <Row>
                            <Col span="12" offset="12" class="pr-4">
                                <totalBreakDown :quotation="localQuotation" :editMode="editMode"></totalBreakDown>
                            </Col>
                            <Col span="24">
                                <!-- Quotation footer notes e.g) For noting payment details/terms and conditions -->
                                <notes :quotation="localQuotation" :editMode="editMode"></notes>
                            </Col>

                        </Row>

                        <!-- Quotation page footer -->
                        <mainFooter :quotation="localQuotation" :editMode="editMode"></mainFooter>

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
    import mainTitle from './title.vue';
    import companyOrIndividualDetails from './companyOrIndividualDetails.vue';
    import summaryDetails from './details.vue';
    import toolbar from './toolbar.vue';
    import items from './items.vue';
    import totalBreakDown from './totalBreakDown.vue';
    import notes from './notes.vue';
    import mainFooter from './footer.vue';
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';
    import editModeSwitch from './../../../components/_common/switches/editModeSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';

    /*  Selectors  */
    import clientSelector from './../../../components/_common/selectors/clientSelector.vue';   

    /*  Image Uploader  */
    import imageUploader from './../../../components/_common/uploaders/imageUploader.vue';
    
    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    /*  Countdown  */
    import basicCoutdown from './../../../components/_common/countdowns/basicCoutdown.vue';

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            overview, steps, mainHeader, 
            mainTitle, companyOrIndividualDetails, summaryDetails, toolbar,
            items, totalBreakDown, notes, mainFooter,
            basicButton, toggleSwitch, editModeSwitch,
            Loader, imageUploader, clientSelector, IconAndCounterCard, basicCoutdown
        },
        props: {
            quotation: {
                type: Object,
                default: function () { 
                    return {
                        status: '',
                        heading: '',
                        quotation_to_title: '',
                        reference_no_title: '',
                        reference_no_value: '',
                        created_date_title: '',
                        created_date_value: '',
                        expiry_date_title: '',
                        expiry_date_value: '',
                        sub_total_title: '',
                        sub_total_value: 0,
                        grand_total_title: '',
                        grand_total_value: 0,
                        currency_type: null,
                        customized_company_details: null,
                        customized_client_details: null,
                        client_id: null,
                        calculated_taxes: [],
                        table_columns: [],
                        items: [],
                        notes: {
                            title: '',
                            details: ''
                        },
                        colors: [],
                        footer: ''
                    }
                }
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

                showRecurringSettings: false,

                user: auth.user,

                //  Modes
                editMode: false,
                createMode: this.create,

                //  Loading States
                isSavingQuotation: false,
                isCreatingQuotation: false,

                //  Local Quotation and state changes
                localQuotation: (this.quotation || {}),
                _localQuotationBeforeChange: {},
                quotationHasChanged: false,

                //  Quotation Shorthands
                company: this.quotation.customized_company_details,
                client: this.quotation.customized_client_details,
                currencySymbol: ((this.quotation.currency_type || {}).currency || {}).symbol,
                
            }
        },
        watch: {
            localQuotation: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    console.log(val);
                    console.log('checkIfquotationHasChanged - 1');
                    this.quotationHasChanged = this.checkIfquotationHasChanged(val);
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

                //var cancelScroll = VueScrollTo.scrollTo('quotation-summary', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#quotation-summary', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            updateReccuring(val){
                
                this.localQuotation.isRecurring = val ? 1 : 0;
                
                this.showRecurringSettings = val;
                
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

                this.client = this.localQuotation.customized_client_details = newClient;
                
                this.quotationHasChanged = this.checkIfquotationHasChanged();

            },

            updateClient(newClientDetails){

                this.client = this.localQuotation.customized_client_details = newClientDetails;

                this.quotationHasChanged = this.checkIfquotationHasChanged();

            },

            updateCompany(newCompanyDetails){
                
                this.company = this.localQuotation.customized_company_details = newCompanyDetails;

                this.quotationHasChanged = this.checkIfquotationHasChanged();

            },

            updatePhoneChanges(companyOrIndividual, phones){
                
                companyOrIndividual.phones = phones;
                
                this.quotationHasChanged = this.checkIfquotationHasChanged();

            },
            activateCreateMode: function(){
                this.fetchQuotationTemplate();
            },
            fetchQuotationTemplate() {
                if(this.user.company_id){
                    const self = this;

                    //  Start loader
                    self.isLoadingQuotationTemplate = true;

                    console.log('Start getting quotation template from company settings...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+self.user.company_id+'/settings')
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingQuotationTemplate = false;

                            //  Get currencies
                            var template = (((data || {}).details || {}).quotationTemplate || null);

                            if(template){
                                //  Activate edit mode
                                self.editMode = true;
                                console.log('Updaing the local quotation with template');
                                console.log(self.localQuotation);
                                self.populateQuotationTemplate(template);
                            }
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingQuotationTemplate = false;

                            console.log('quotationSummaryWidget.vue - Error getting quotation template from company settings...');
                            console.log(response);    
                        });
                }
            },
            populateQuotationTemplate(template){
                console.log('Populating quotation template with deault settings');
                var date = new Date();
                var dd = ('0' + date.getDate()).slice(-2);
                var mm = ('0' + (date.getMonth() + 1)).slice(-2);
                var yy = date.getFullYear();
                
                //  Update Quotation Object Using Template Data

                this.localQuotation.status = template.status;
                this.localQuotation.heading = template.heading;
                this.localQuotation.reference_no_title = template.reference_no_title;
                this.localQuotation.created_date_title = template.created_date_title;
                this.localQuotation.expiry_date_title = template.expiry_date_title;
                this.localQuotation.sub_total_title = template.sub_total_title;
                this.localQuotation.grand_total_title = template.grand_total_title;
                this.localQuotation.currency_type = template.currency_type;
                this.localQuotation.quotation_to_title = template.quotation_to_title;
                this.localQuotation.table_columns = template.table_columns;
                this.localQuotation.items = template.items;
                this.localQuotation.notes = template.notes;
                this.localQuotation.colors = template.colors;
                this.localQuotation.footer = template.footer;

                //  Update Quotation Dates Using Current Dates
                
                this.localQuotation.created_date_value = yy+'-'+mm+'-'+dd;
                this.localQuotation.expiry_date_value = yy+'-'+mm+'-'+('0' + (date.getDate() + 7) ).slice(-2);

                //  Update Quotation Shorthands

                this.currencySymbol = this.localQuotation.currency_type.currency.symbol;
                
                this.fetchCompanyInfo();
            },
            checkIfquotationHasChanged: function(updatedQuotation = null){
                
                var currentQuotation = _.cloneDeep(updatedQuotation || this.localQuotation);
                var isNotEqual = !_.isEqual(currentQuotation, this._localQuotationBeforeChange);

                console.log('currentQuotation');
                console.log(currentQuotation);
                console.log('_localQuotationBeforeChange');
                console.log(this._localQuotationBeforeChange);
                console.log('isNotEqual:' +isNotEqual);

                return isNotEqual;
            },
            storeOriginalQuotation(){
                //  Store the original quotation
                this._localQuotationBeforeChange = _.cloneDeep(this.localQuotation);
                console.log('stored _localQuotationBeforeChange');
                console.log(this._localQuotationBeforeChange);
            },
            saveQuotation(){
                
                var self = this;

                //  Start loader
                self.isSavingQuotation = true;

                console.log('Attempt to save quotation...');

                console.log( self.localQuotation );

                //  Form data to send
                let quotationData = { quotation: self.localQuotation };

                console.log(quotationData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations/'+self.localQuotation.id, quotationData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingQuotation = false;

                        /*
                        *  updateQuotationData() : This method ensures the property is
                        *  updated as a reactive property and triggers future view updates:
                        */
                        self.updateQuotationData(data);

                        //  Alert creation success
                        self.$Message.success('Quotation saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingQuotation = false;

                        console.log('quotationSummaryWidget.vue - Error saving quotation...');
                        console.log(response);
                    });
            },
            createQuotation(){

                var self = this;

                //  Start loader
                self.isCreatingQuotation = true;

                console.log('Attempt to create quotation...');

                console.log( self.localQuotation );

                //  Form data to send
                let quotationData = { quotation: self.localQuotation };

                console.log(quotationData);

                var associatedModel = (this.modelType && this.modelId) ? '?model='+this.modelType+'&modelId='+this.modelId: '';
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations'+associatedModel, quotationData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingQuotation = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the current state of the quotation as the original quotation
                        self.storeOriginalQuotation();
                        
                        console.log('checkIfquotationHasChanged - 3');
                        self.quotationHasChanged = self.checkIfquotationHasChanged();

                        //  Alert creation success
                        self.$Message.success('Quotation created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('quotationCreated', data);

                        //  Go to quotation
                        self.$router.push({ name: 'show-quotation', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingQuotation = false;

                        console.log('quotationSummaryWidget.vue - Error creating quotation...');
                        console.log(response);
                    });
            },
            updateQuotationData(newQuotation){
                
                //  Disable edit mode
                this.editMode = false;
                
                /*
                 *  Vue.set()
                 *  We use Vue.set to set a new object property. This method ensures the  
                 *  property is created as a reactive property and triggers view updates:
                 */
            
                for(var x = 0; x <  _.size(newQuotation); x++){
                    var key = Object.keys(this.localQuotation)[x];
                    this.$set(this.localQuotation, key, newQuotation[key]);
                }

                //  Store the current state of the quotation as the original quotation
                this.storeOriginalQuotation();

                console.log('checkIfquotationHasChanged - 4');
                //  Update the quotation change status
                this.quotationHasChanged = this.checkIfquotationHasChanged();

            },
            fetchCompanyInfo() {
                if(!this.company && this.user.company_id){
                    const self = this;

                    //  Start loader
                    self.isLoadingCompanyInfo = true;

                    console.log('Start getting company details...');

                    //  Additional data to eager load along with company found
                    var connections = '&connections=phones';
                    
                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+self.user.company_id+'?model=Company'+connections)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingCompanyInfo = false;

                            if(data){
                                //  Format the company details
                                self.company = self.localQuotation.customized_company_details = data;
                            }
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingCompanyInfo = false;

                            console.log('quotationSummaryWidget.vue - Error getting company details...');
                            console.log(response);    
                        });
                }
            },
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Store the current state of the quotation as the original quotation
                console.log('Now lets store that original quotation!');
                this.storeOriginalQuotation();

                //  Update the quotation change status
                this.quotationHasChanged = this.checkIfquotationHasChanged();

                if(this.createMode){
                    this.activateCreateMode();
                }

            })
        }
    };
  
</script>