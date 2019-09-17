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
        <Row>
            <Col :span="24">
                <!-- Fade loader - Shows when creating quotation  -->
                <fadeLoader :loading="isCreatingQuotation && createMode" msg="Creating, please wait..." class="mt-3 mb-1"></fadeLoader>
                <!-- Fade loader - Shows when saving quotation  -->
                <fadeLoader :loading="isSavingQuotation && createMode" msg="Saving, please wait..." class="mt-3 mb-1"></fadeLoader>
            </Col>
        </Row>
        
        <transition-group name="fade">

            <!-- Activity cards & Quotation Steps -->
            <Row :gutter="20" key="activity_n_steps" class="animated mr-0 ml-0" :style="(isSavingQuotation ? 'padding-top: 15px;' : '')">
                <!-- White overlay when creating/saving quotation -->
                <Spin size="large" fix v-if="(isSavingQuotation || isCreatingQuotation) && localQuotation.has_approved" style="border-radius: 15px;">
                    <!-- Icon to show as loader  -->
                    <clockLoader></clockLoader>
                </Spin>

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
                    <!-- Fade loader - Shows when creating quotation  -->
                    <fadeLoader :loading="isCreatingQuotation" msg="Creating, please wait..." class="mt-4 mb-4"></fadeLoader>
                    <!-- Fade loader - Shows when saving quotation  -->
                    <fadeLoader :loading="isSavingQuotation" msg="Saving, please wait..." class="mt-4 mb-4"></fadeLoader>
                </Col>
            </Row>

            <!-- Quotation View/Editor -->
            <Row id="quotation-summary"  key="quotation_template" class="animated mb-5">
                <Col :span="24">
                    <Card :style="{ width: '100%' }">
                        
                        <!-- White overlay when creating/saving quotation -->
                        <Spin size="large" fix v-if="isSavingQuotation || isCreatingQuotation" style="border-radius: 15px 15px 4px 4px;">
                            <!-- Icon to show as loader  -->
                            <clockLoader></clockLoader>
                        </Spin>

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

                            <Col span="6">

                                <!-- Company logo -->
                                <imageUploader 
                                    uploadMsg="Upload or change logo"
                                    :allowUpload="editMode"
                                    :multiple="false"
                                    :docUrl="'/api/companies/'+user.company_id+'/logo'"
                                    :postData="{ 
                                        modelId: ((this.localQuotation || {}).customized_company_details || {}).id,
                                        modelType: 'company',
                                        location:  'company_logos', 
                                        type: 'logo',
                                        replaceable: true
                                    }"
                                    :thumbnailStyle="{ width:'200px', height:'auto' }">
                                </imageUploader>
                                
                            </Col>

                            <Col v-if="company" span="12" :offset="6" class="pr-4">
                                
                                <!-- Quotation Title -->
                                <mainTitle :quotation="localQuotation" :editMode="editMode"></mainTitle>
                                
                                <!-- Company information -->
                                <companyOrIndividualDetails 
                                    :editMode="editMode"
                                    refName="Company"
                                    :profile="localQuotation.customized_company_details" 
                                    align="right"
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
                                    :editMode="editMode"
                                    refName="Client"
                                    :profile="localQuotation.customized_client_details" 
                                    :profileId="( createMode ? $route.query.clientId : null )" 
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="true"
                                    @updated:companyOrIndividual="updateClient($event)"
                                    @updated:phones="updatePhoneChanges(localQuotation.customized_client_details, $event)"
                                    @reUpdateParent="storeOriginalQuotation()">
                                </companyOrIndividualDetails>

                                <!-- Client selector -->
                                <clientOrVendorSelector 
                                    v-if="editMode"
                                    :style="{maxWidth: '250px'}" class="mt-2"
                                    :companyId="user.company_id"
                                    @updated="changeClient($event)">
                                </clientOrVendorSelector>

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
    import clientOrVendorSelector from './../../../components/_common/selectors/clientOrVendorSelector.vue';   

    /*  Image Uploader  */
    import imageUploader from './../../../components/_common/uploaders/imageUploader.vue';
    
    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    /*  Countdown  */
    import basicCoutdown from './../../../components/_common/countdowns/basicCoutdown.vue';

    /*  Fade Loader  */
    import fadeLoader from './../../../components/_common/loaders/fadeLoader.vue';
    import clockLoader from './../../../components/_common/loaders/clockLoader.vue';

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            overview, steps, mainHeader, 
            mainTitle, companyOrIndividualDetails, summaryDetails, toolbar,
            items, totalBreakDown, notes, mainFooter,
            basicButton, toggleSwitch, editModeSwitch,
            Loader, imageUploader, clientOrVendorSelector, IconAndCounterCard, basicCoutdown,
            fadeLoader, clockLoader
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
                        created_date: '',
                        expiry_date_title: '',
                        expiry_date: '',
                        sub_total_title: '',
                        sub_total_value: 0,
                        grand_total_title: '',
                        grand_total: 0,
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
                isLoadingQuotationTemplate: false,

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
                        title: 'Client changed to ' + 
                                (newClient.first_name || newClient.last_name) ? ((newClient.first_name || '') +  ' ' + (newClient.last_name || '')) : 'unkwown' 
                    });

                }else if(newClient.model_type == 'company'){
                    this.$Notice.success({
                        title: 'Client changed to ' + (newClient.name || 'unkwown')
                    });
                }

                this.client = this.$set(this.localQuotation, 'customized_client_details', newClient);
                
                this.quotationHasChanged = this.checkIfquotationHasChanged();

            },
            updateClient(newClientDetails){

                this.client = this.$set(this.localQuotation, 'customized_client_details', newClientDetails);

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
                            var template = (data || {}).details;

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
                
                var generalSettings = ((template || {}).general || {});
                var quotationSettings = ((template || {}).quotationTemplate || {});

                //  Update Quotation Object Using Template Data

                this.localQuotation.status = quotationSettings.status;
                this.localQuotation.heading = quotationSettings.heading;
                this.localQuotation.reference_no_title = quotationSettings.reference_no_title;
                this.localQuotation.created_date_title = quotationSettings.created_date_title;
                this.localQuotation.expiry_date_title = quotationSettings.expiry_date_title;
                this.localQuotation.sub_total_title = quotationSettings.sub_total_title;
                this.localQuotation.grand_total_title = quotationSettings.grand_total_title;
                this.localQuotation.currency_type = generalSettings.currency_type;
                this.localQuotation.quotation_to_title = quotationSettings.quotation_to_title;
                this.localQuotation.table_columns = quotationSettings.table_columns;
                this.localQuotation.items = quotationSettings.items;
                this.localQuotation.notes = quotationSettings.notes;
                this.localQuotation.colors = quotationSettings.colors;
                this.localQuotation.footer = quotationSettings.footer;

                //  Update Quotation Dates Using Current Dates
                
                this.localQuotation.created_date = yy+'-'+mm+'-'+dd;
                this.localQuotation.expiry_date = yy+'-'+mm+'-'+('0' + (date.getDate() + 7) ).slice(-2);

                //  Update Quotation Shorthands

                this.currencySymbol = this.localQuotation.currency_type.currency.symbol;
                
                if(!this.company){
                    var self = this;
                    this.fetchCompanyInfo(this.user.company_id).then( data => {
                        self.company = self.localQuotation.customized_company_details = data;
                    });
                }
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
            fetchCompanyInfo(companyId) {
                
                if(companyId){
                    
                    const self = this;

                    //  Start loader
                    self.isLoadingCompanyInfo = true;

                    console.log('Start getting company details...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', '/api/companies/'+companyId+'?model=Company')
                            .then(({data}) => {
                                
                                console.log(data);

                                //  Stop loader
                                self.isLoadingCompanyInfo = false;

                                if(data){
                                    //  Format the company details
                                    return data;
                                }
                            })         
                            .catch(response => { 

                                //  Stop loader
                                self.isLoadingCompanyInfo = false;

                                console.log('invoiceSummaryWidget.vue - Error getting company details...');
                                console.log(response);    
                            });
                }
            }
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {

                //  If we don't have the company details
                if( !this.localQuotation.customized_company_details ){
                    //  Fetch the associated company
                    var self = this;
                    this.fetchCompanyInfo(this.user.company_id).then( data => {
                        self.company = self.$set(self.localQuotation, 'customized_company_details', data);
                    });
                }

                if(this.createMode){
                    //  Activate create mode
                    this.activateCreateMode();

                    //  If the client id has been provided on the url
                    if( this.$route.query.clientId ){
                        //  Fetch the associated client if specified
                        var self = this;
                        this.fetchCompanyInfo(this.$route.query.clientId).then( data => {
                            self.client = self.$set(self.localQuotation, 'customized_client_details', data);
                        });
                    }
                    
                }

                //  Store the current state of the quotation as the original quotation
                console.log('Now lets store that original quotation!');
                this.storeOriginalQuotation();

                //  Update the quotation change status
                this.quotationHasChanged = this.checkIfquotationHasChanged();


            })
        }
    };
  
</script>