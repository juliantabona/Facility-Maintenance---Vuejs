<style scoped>

    .invoice-widget{
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

    <div id="invoice-widget">

        <!-- Get the summary header to display the invoice #, status, due date, amount due and menu options -->
        <overview 
            v-if="!createMode && localInvoice.has_approved"
            :invoice="localInvoice" :editMode="editMode" :createMode="createMode"
            @toggleEditMode="toggleEditMode($event)">
        </overview>

        <!-- Loaders for creating/saving invoice -->
        <Row>
            <Col :span="24">
                <div v-if="isCreatingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>
        
        <transition-group name="fade">
            
            <Row :gutter="20" key="recurring_details" class="animated">

                <!-- Recurring toggle switch, Recurring settings toolbox, Save changes button -->
                <Col :span="24">

                    <!-- Save changes button -->
                    <basicButton  v-if="!createMode && invoiceHasChanged" 
                                    class="float-right pt-2 ml-4" :style="{ position:'relative' }"
                                    type="success" size="small" 
                                    :ripple="true"
                                    @click.native="saveInvoice()">
                        Save Changes
                    </basicButton>

                    <!-- Recurring Settings Icon Button -->
                    <span v-if="localInvoice.isRecurring" class="float-right d-block pt-2">
                        <div @click="showRecurringSettings = !showRecurringSettings" :style="{ position: 'relative', zIndex: '1' }">
                            <Icon :style="showRecurringSettings ? { fontSize: '20px',height: '33px',color: '#2d8cf0',background: '#eee',borderRadius: '50% 50% 0 0',padding: '3px 6px',marginTop: '-3px',boxShadow: '#c8c8c8 1px 1px 1px inset',cursor: 'pointer' }: { cursor: 'pointer' }"
                                type="ios-settings-outline" :size="20" />
                        </div>
                    </span>

                    <!-- Make recurring switch -->
                    <toggleSwitch v-bind:toggleValue.sync="localInvoice.isRecurring" 
                        @update:toggleValue="updateReccuring($event)"
                        :ripple="false" :showIcon="true" onIcon="ios-repeat" offIcon="ios-repeat" 
                        title="Make Recurring:" onText="Yes" offText="No" poptipMsg="Turn on to make recurring"
                        class="float-right p-2">
                    </toggleSwitch>

                    <div class="clearfix"></div>

                    <!-- Next recurring invoice countdown -->
                    <basicCoutdown 
                        v-if="localInvoice.has_approved_recurring_settings" 
                        class="p-3 mb-3 float-right bg-warning"
                        :date="nextRecurringInvoiceDate"
                        text="Sending next invoice in: "
                        textAtZero="Sent the scheduled invoice!">
                    </basicCoutdown>
                    
                    <!-- Make recurring settings -->
                    <Row v-show="showRecurringSettings" key="dynamic" class="animated mb-3">

                        <!-- White overlay when creating/saving invoice -->
                        <Spin size="large" fix v-if="isSavingInvoice || isCreatingInvoice"></Spin>

                        <Col span="24">
                            <div style="background:#eee;padding: 20px">

                                <!-- Get the staging toolbar to display the recurring schedule settigns, 
                                     configure payment methods aswell as automated/manual sending stages -->
                                <recurringSettingsSteps v-if="!createMode"
                                    :invoice="localInvoice" 
                                    @saved="updateInvoiceData($event)">
                                </recurringSettingsSteps>
                                
                            </div>
                        </Col>
                    </Row>

                </Col>

            </Row>

            <!-- Activity cards & Invoice Steps -->
            <Row :gutter="20" key="activity_n_steps" class="animated">
                <!-- White overlay when creating/saving invoice -->
                <Spin size="large" fix v-if="isSavingInvoice || isCreatingInvoice"></Spin>

                <!-- Acitvity cards for showing summary of activities, sent invoices, and sent receipt -->
                <Col v-if="localInvoice.has_approved" :span="5">
                
                    <!-- Activity Number Card -->
                    <IconAndCounterCard title="Activity" icon="ios-pulse-outline" :count="localInvoice.activity_count.total" class="mb-2" type="success"
                                        :route="{ name: 'show-invoice-activities', params: { id: localInvoice.id } }">
                    </IconAndCounterCard>

                    <!-- Sent Incoices Number Card -->
                    <IconAndCounterCard title="Sent Invoices" icon="ios-send-outline" :count="localInvoice.sent_invoice_activity_count.total" class="mb-2"
                                        :route="{ name: 'show-invoice-activities', params: { id: localInvoice.id } , query: { activity_type: 'sent' } }">
                    </IconAndCounterCard>

                    <!-- Sent Recipts Number Card -->
                    <IconAndCounterCard title="Sent Receipts" icon="ios-paper-outline" :count="localInvoice.sent_receipt_activity_count.total" class="mb-2"
                                        :route="{ name: 'show-invoice-activities', params: { id: localInvoice.id } , query: { activity_type: 'sent_receipt' } }">
                    </IconAndCounterCard>
                
                </Col>

                <!-- Invoice steps, Approval step, Sending step and Payment step -->
                <Col :span="localInvoice.has_approved ? 19 : 24">
                    <!-- Get the staging toolbar to display the invoice approved, sent/re-send and record payment stages -->
                    <steps 
                        v-if="!createMode"
                        :invoice="localInvoice" :editMode="editMode" :createMode="createMode" 
                        @toggleEditMode="toggleEditMode($event)" @approved="updateInvoiceData($event)" 
                        @sent="updateInvoiceData($event)" @skipped="updateInvoiceData($event)"
                        @paid="updateInvoiceData($event)" @cancelled="updateInvoiceData($event)" 
                        @reminderSet="updateInvoiceData($event)">
                    </steps>
                </Col>

            </Row>

            <!-- Loaders for creating/saving invoice -->
            <Row key="create_n_save_loaders" class="animated">
                <Col :span="24">
                    <div v-if="isCreatingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                    <div v-if="isSavingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
                </Col>
            </Row>

            <!-- Invoice View/Editor -->
            <Row id="invoice-summary"  key="invoice_template" class="animated mb-5">
                <Col :span="24">
                    <Card :style="{ width: '100%' }">
                        
                        <!-- White overlay when creating/saving invoice -->
                        <Spin size="large" fix v-if="isSavingInvoice || isCreatingInvoice"></Spin>

                        <!-- Main header -->
                        <div slot="title">
                            <h5>Invoice Summary</h5>
                        </div>

                        <!-- Invoice options -->
                        <div slot="extra" v-if="showMenuBtn && !createMode">
                            
                            <mainHeader :invoice="localInvoice" :editMode="editMode" @toggleEditMode="toggleEditMode($event)"></mainHeader>

                        </div>

                        <Row>

                            <Col span="24" class="pr-4">

                                <!-- Create button -->
                                <basicButton v-if="createMode" 
                                                class="float-right mb-2 ml-3" 
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="createInvoice()">
                                    Create Invoice
                                </basicButton>

                                <!-- Save changes button -->
                                <basicButton  v-if="!createMode && invoiceHasChanged" 
                                                class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="saveInvoice()">
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
                                            'url': '/images/assets/logo/OQ-B.png'
                                        }]">
                                </imageUploader>
                            </Col>

                            <Col v-if="company" span="12" class="pr-4">
                                
                                <!-- Invoice Title -->
                                <mainTitle :invoice="localInvoice" :editMode="editMode"></mainTitle>
                                
                                <!-- Company information -->
                                <companyOrIndividualDetails 
                                    :editMode="editMode"
                                    refName="Company"
                                    :profile="localInvoice.customized_company_details" 
                                    align="right"
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="false"
                                    @updated:companyOrIndividual="updateCompany($event)"
                                    @updated:phones="updatePhoneChanges(localInvoice.customized_company_details, $event)"
                                    @reUpdateParent="storeOriginalInvoice()">
                                </companyOrIndividualDetails>

                            </Col>

                        </Row>

                        <Divider dashed class="mt-3 mb-3" />

                        <Row>
                            <Col span="12" class="pl-2">
                                <h3 v-if="!editMode" class="text-dark mb-3">{{ localInvoice.invoice_to_title ? localInvoice.invoice_to_title+':' : '' }}</h3>
                                <el-input v-if="editMode" placeholder="Invoice heading" v-model="localInvoice.invoice_to_title" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>

                                <!-- Client information -->
                                <companyOrIndividualDetails 
                                    :editMode="editMode"
                                    refName="Client"
                                    :profile="localInvoice.customized_client_details" 
                                    :profileId="( createMode ? $route.query.clientId : null )" 
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="true"
                                    @updated:companyOrIndividual="updateClient($event)"
                                    @updated:phones="updatePhoneChanges(localInvoice.customized_client_details, $event)"
                                    @reUpdateParent="storeOriginalInvoice()">
                                </companyOrIndividualDetails>

                                <!-- Client selector -->
                                <clientSelector :style="{maxWidth: '250px'}" class="mt-2"
                                    @updated="changeClient($event)">
                                </clientSelector>

                            </Col>
                            
                            <Col span="12">
                                <!-- Invoice details e.g) Reference #, created date, due date, grand total -->
                                <summaryDetails :invoice="localInvoice" :editMode="editMode" :createMode="createMode"></summaryDetails>
                            </Col>
                        
                            <Col span="24">
                                <!-- Edit mode toolbar e.g) Currency selector, primary/secondary color picker -->
                                <toolbar v-if="editMode" :invoice="localInvoice" :editMode="editMode" class="mt-2"></toolbar>
                            </Col>

                            <!-- Invoice list items (products/services) -->
                            <Col span="24">
                                <items :invoice="localInvoice" :editMode="editMode"></items>
                            </Col>

                        </Row>

                        <Divider dashed class="mt-0 mb-4" />

                        <!-- Total details e.g) Sub/grand total and tax amounts -->
                        <Row>
                            <Col span="12" offset="12" class="pr-4">
                                <totalBreakDown :invoice="localInvoice" :editMode="editMode"></totalBreakDown>
                            </Col>
                            <Col span="24">
                                <!-- Invoice footer notes e.g) For noting payment details/terms and conditions -->
                                <notes :invoice="localInvoice" :editMode="editMode"></notes>
                            </Col>

                        </Row>

                        <!-- Invoice page footer -->
                        <mainFooter :invoice="localInvoice" :editMode="editMode"></mainFooter>

                    </Card>
                </Col>
            </Row>

        </transition-group>

    </div>

</template>

<script>

    /*  Local components    */
    import overview from './overview.vue';
    import recurringSettingsSteps from './recurringSettingsSteps.vue';
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
            overview, recurringSettingsSteps, steps, mainHeader, 
            mainTitle, companyOrIndividualDetails, summaryDetails, toolbar,
            items, totalBreakDown, notes, mainFooter,
            basicButton, toggleSwitch, editModeSwitch,
            Loader, imageUploader, clientSelector, IconAndCounterCard, basicCoutdown
        },
        props: {
            invoice: {
                type: Object,
                default: function () { 
                    return {
                        status: '',
                        heading: '',
                        invoice_to_title: '',
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
                isSavingInvoice: false,
                isCreatingInvoice: false,

                //  Local Invoice and state changes
                localInvoice: (this.invoice || {}),
                _localInvoiceBeforeChange: {},
                invoiceHasChanged: false,

                //  Invoice Shorthands
                company: this.invoice.customized_company_details,
                client: this.invoice.customized_client_details,
                currencySymbol: ((this.invoice.currency_type || {}).currency || {}).symbol,
                
            }
        },
        watch: {
            localInvoice: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    console.log(val);
                    console.log('checkIfinvoiceHasChanged - 1');
                    this.invoiceHasChanged = this.checkIfinvoiceHasChanged(val);
                },
                deep: true
            }
        },
        computed: {
            nextRecurringInvoiceDate: function(){
                if( this.localInvoice.recurringSettings ){
                    if( this.localInvoice.recurringSettings.schedulePlan.nextDate ){
                        return this.localInvoice.recurringSettings.schedulePlan.nextDate;
                    }else{
                        return this.localInvoice.recurringSettings.schedulePlan.startDate; 
                    }
                }

                return null;
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

                //var cancelScroll = VueScrollTo.scrollTo('invoice-summary', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#invoice-summary', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            updateReccuring(val){
                
                this.localInvoice.isRecurring = val ? 1 : 0;
                
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

                this.client = this.localInvoice.customized_client_details = newClient;
                
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },

            updateClient(newClientDetails){

                this.client = this.localInvoice.customized_client_details = newClientDetails;

                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },

            updateCompany(newCompanyDetails){
                
                this.company = this.localInvoice.customized_company_details = newCompanyDetails;

                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },

            updatePhoneChanges(companyOrIndividual, phones){
                
                companyOrIndividual.phones = phones;
                
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },
            activateCreateMode: function(){
                this.fetchInvoiceTemplate();
            },
            fetchInvoiceTemplate() {
                if(this.user.company_id){
                    const self = this;

                    //  Start loader
                    self.isLoadingInvoiceTemplate = true;

                    console.log('Start getting invoice template from company settings...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+self.user.company_id+'/settings')
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingInvoiceTemplate = false;

                            //  Get currencies
                            var template = (((data || {}).details || {}).invoiceTemplate || null);

                            if(template){
                                //  Activate edit mode
                                self.editMode = true;
                                console.log('Updaing the local invoice with template');
                                console.log(self.localInvoice);
                                self.populateInvoiceTemplate(template);
                            }
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingInvoiceTemplate = false;

                            console.log('invoiceSummaryWidget.vue - Error getting invoice template from company settings...');
                            console.log(response);    
                        });
                }
            },
            populateInvoiceTemplate(template){
                console.log('Populating invoice template with deault settings');
                var date = new Date();
                var dd = ('0' + date.getDate()).slice(-2);
                var mm = ('0' + (date.getMonth() + 1)).slice(-2);
                var yy = date.getFullYear();
                
                //  Update Invoice Object Using Template Data

                this.localInvoice.status = template.status;
                this.localInvoice.heading = template.heading;
                this.localInvoice.reference_no_title = template.reference_no_title;
                this.localInvoice.created_date_title = template.created_date_title;
                this.localInvoice.expiry_date_title = template.expiry_date_title;
                this.localInvoice.sub_total_title = template.sub_total_title;
                this.localInvoice.grand_total_title = template.grand_total_title;
                this.localInvoice.currency_type = template.currency_type;
                this.localInvoice.invoice_to_title = template.invoice_to_title;
                this.localInvoice.table_columns = template.table_columns;
                this.localInvoice.items = template.items;
                this.localInvoice.notes = template.notes;
                this.localInvoice.colors = template.colors;
                this.localInvoice.footer = template.footer;

                //  Update Invoice Dates Using Current Dates
                
                this.localInvoice.created_date_value = yy+'-'+mm+'-'+dd;
                this.localInvoice.expiry_date_value = yy+'-'+mm+'-'+('0' + (date.getDate() + 7) ).slice(-2);

                //  Update Invoice Shorthands

                this.currencySymbol = this.localInvoice.currency_type.currency.symbol;
                
                if(!this.company){
                    var self = this;
                    this.fetchCompanyInfo(this.user.company_id).then( data => {
                        self.company = self.localInvoice.customized_company_details = data;
                    });
                }
            },
            checkIfinvoiceHasChanged: function(updatedInvoice = null){
                
                var currentInvoice = _.cloneDeep(updatedInvoice || this.localInvoice);
                var isNotEqual = !_.isEqual(currentInvoice, this._localInvoiceBeforeChange);

                console.log('currentInvoice');
                console.log(currentInvoice);
                console.log('_localInvoiceBeforeChange');
                console.log(this._localInvoiceBeforeChange);
                console.log('isNotEqual:' +isNotEqual);

                return isNotEqual;
            },
            storeOriginalInvoice(){
                //  Store the original invoice
                this._localInvoiceBeforeChange = _.cloneDeep(this.localInvoice);
                console.log('stored _localInvoiceBeforeChange');
                console.log(this._localInvoiceBeforeChange);
            },
            saveInvoice(){
                
                var self = this;

                //  Start loader
                self.isSavingInvoice = true;

                console.log('Attempt to save invoice...');

                console.log( self.localInvoice );

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                console.log(invoiceData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id, invoiceData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingInvoice = false;

                        /*
                        *  updateInvoiceData() : This method ensures the property is
                        *  updated as a reactive property and triggers future view updates:
                        */
                        self.updateInvoiceData(data);

                        //  Alert creation success
                        self.$Message.success('Invoice saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error saving invoice...');
                        console.log(response);
                    });
            },
            createInvoice(){

                var self = this;

                //  Start loader
                self.isCreatingInvoice = true;

                console.log('Attempt to create invoice...');

                console.log( self.localInvoice );

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                console.log(invoiceData);

                var associatedModel = (this.modelType && this.modelId) ? '?model='+this.modelType+'&modelId='+this.modelId: '';
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices'+associatedModel, invoiceData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingInvoice = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the current state of the invoice as the original invoice
                        self.storeOriginalInvoice();
                        
                        console.log('checkIfinvoiceHasChanged - 3');
                        self.invoiceHasChanged = self.checkIfinvoiceHasChanged();

                        //  Alert creation success
                        self.$Message.success('Invoice created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('invoiceCreated', data);

                        //  Go to invoice
                        self.$router.push({ name: 'show-invoice', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error creating invoice...');
                        console.log(response);
                    });
            },
            updateInvoiceData(newInvoice){
                
                //  Disable edit mode
                this.editMode = false;
                
                /*
                 *  Vue.set()
                 *  We use Vue.set to set a new object property. This method ensures the  
                 *  property is created as a reactive property and triggers view updates:
                 */
            
                for(var x = 0; x <  _.size(newInvoice); x++){
                    var key = Object.keys(this.localInvoice)[x];
                    this.$set(this.localInvoice, key, newInvoice[key]);
                }

                //  Store the current state of the invoice as the original invoice
                this.storeOriginalInvoice();

                console.log('checkIfinvoiceHasChanged - 4');
                //  Update the invoice change status
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            },
            fetchCompanyInfo(companyId) {
                
                if(companyId){
                    
                    const self = this;

                    //  Start loader
                    self.isLoadingCompanyInfo = true;

                    console.log('Start getting company details...');

                    //  Additional data to eager load along with company found
                    var connections = '&connections=phones';
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', '/api/companies/'+companyId+'?model=Company'+connections)
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
            },
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {

                if(this.createMode){
                    //  Activate create mode
                    this.activateCreateMode();

                    //  If the client id has been provided on the url
                    if( this.$route.query.clientId ){
                        //  Fetch the associated client if specified
                        var self = this;
                        this.fetchCompanyInfo(this.$route.query.clientId).then( data => {
                            self.client = self.localInvoice.customized_client_details = data;
                        });
                    }
                    
                }

                //  Store the current state of the invoice as the original invoice
                console.log('Now lets store that original invoice!');
                this.storeOriginalInvoice();

                //  Update the invoice change status
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

            })
        }
    };
  
</script>