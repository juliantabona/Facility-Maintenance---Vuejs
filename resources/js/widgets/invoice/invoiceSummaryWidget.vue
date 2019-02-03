<style scoped>

    .invoice-widget{
        position: relative;
    }

</style>

<template>

    <div id="invoice-widget">
        
        <!-- Get the summary header to display the invoice #, status, due date, amount due and menu options -->
        <summaryHeader 
            v-if="!createMode && localInvoice.has_approved"
            :invoice="localInvoice" :editMode="editMode" :createMode="createMode"
            @toggleEditMode="toggleEditMode($event)">
        </summaryHeader>
        
        <!-- Get the staging toolbar to display the invoice approved, sent/re-send and record payment stages -->
        <stagingToolbar 
            v-if="!createMode"
            :invoice="localInvoice" :editMode="editMode" :createMode="createMode" 
            @toggleEditMode="toggleEditMode($event)" @approved="updateInvoiceData($event)" 
            @sent="updateInvoiceData($event)"
            @paid="updateInvoiceData($event)" @cancelled="updateInvoiceData($event)" 
            @reminderSet="updateInvoiceData($event)">
        </stagingToolbar>

        <!-- Loaders for creating/saving invoice -->
        <Row>
            <Col :span="24">
                <div v-if="isCreatingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>

        <!-- Invoice View/Editor -->
        <Row id="invoice-summary-1">
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
                            <createInvoiceBtn v-if="createMode" 
                                              class="float-right mb-2 ml-3" 
                                              type="success" size="small" 
                                              :ripple="true"
                                              @click.native="createInvoice()">
                            </createInvoiceBtn>

                            <!-- Save changes button -->
                            <saveInvoiceBtn  v-if="!createMode && invoiceHasChanged" 
                                             class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                             type="success" size="small" 
                                             :ripple="true"
                                             @click.native="saveInvoice()">
                            </saveInvoiceBtn>

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
                            
                            <!-- Invoice Title -->
                            <invoiceTitle :invoice="localInvoice" :editMode="editMode"></invoiceTitle>
                            
                            <!-- Company information -->
                            <companyContactInfo :company="localInvoice.customized_company_details" :editMode="editMode"></companyContactInfo>

                        </Col>

                    </Row>

                    <Divider dashed class="mt-3 mb-3" />

                    <Row>
                        <Col span="12" class="pl-2">
                            <h3 v-if="!editMode" class="text-dark mb-3">{{ localInvoice.invoice_to_title ? localInvoice.invoice_to_title+':' : '' }}</h3>
                            <el-input v-if="editMode" placeholder="Invoice heading" v-model="localInvoice.invoice_to_title" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>

                            <!-- Client information -->
                            <clientContactInfo :client="localInvoice.customized_client_details" :editMode="editMode"></clientContactInfo>

                            <!-- Client selector -->
                            <companySelector :style="{maxWidth: '250px'}" class="mt-2"
                                @updated="updateClientChanges($event)">
                            </companySelector>

                        </Col>
                        
                        <Col span="12">
                            <!-- Invoice details e.g) Reference #, created date, due date, grand total -->
                            <invoiceDetails :invoice="localInvoice" :editMode="editMode" :createMode="createMode"></invoiceDetails>
                        </Col>
                    
                        <Col span="24">
                            <!-- Edit mode toolbar e.g) Currency selector, primary/secondary color picker -->
                            <invoiceToolbar v-if="editMode" :invoice="localInvoice" :editMode="editMode"></invoiceToolbar>
                        </Col>

                        <!-- Invoice list items (products/services) -->
                        <Col span="24">
                            <invoiceItems :invoice="localInvoice" :editMode="editMode"></invoiceItems>
                        </Col>

                    </Row>

                    <Divider dashed class="mt-0 mb-4" />

                    <!-- Total details e.g) Sub/grand total and tax amounts -->
                    <Row>
                        <Col span="12" offset="12" class="pr-4">
                            <invoiceTotalBreakDown :invoice="localInvoice" :editMode="editMode"></invoiceTotalBreakDown>
                        </Col>
                        <Col span="24">
                            <!-- Invoice footer notes e.g) For noting payment details/terms and conditions -->
                            <invoiceFooterNotes :invoice="localInvoice" :editMode="editMode"></invoiceFooterNotes>
                        </Col>

                    </Row>

                    <!-- Invoice page footer -->
                    <invoiceFooter :invoice="localInvoice" :editMode="editMode"></invoiceFooter>

                </Card>
            </Col>
        </Row>

    </div>

</template>

<script>

    import summaryHeader from './overview/main.vue';
    import stagingToolbar from './stagingToolbar/main.vue';
    import mainHeader from './header/mainHeader.vue';
    import invoiceTitle from './header/invoiceTitle.vue';
    import companyContactInfo from './header/companyContactInfo.vue';
    import clientContactInfo from './header/clientContactInfo.vue';
    import invoiceDetails from './header/invoiceDetails.vue';
    import invoiceToolbar from './header/invoiceToolbar.vue';
    import invoiceItems from './body/invoiceItems.vue';
    import invoiceTotalBreakDown from './footer/invoiceTotalBreakDown.vue';
    import invoiceFooterNotes from './footer/invoiceFooterNotes.vue';
    import invoiceFooter from './footer/invoiceFooter.vue';
    
    /*  Buttons  */
    import createInvoiceBtn from './buttons/createInvoiceBtn.vue';
    import saveInvoiceBtn from './buttons/saveInvoiceBtn.vue';

    /*  Switches  */
    import editModeSwitch from './switches/editModeSwitch.vue';

    /*  Loaders  */
    import Loader from './../../components/_common/loader/Loader.vue';

    /*  Selectors  */
    import companySelector from './selectors/companySelector.vue';   

    import imageUploader from './upload/imageUploader.vue';
    

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            summaryHeader, stagingToolbar, mainHeader, 
            invoiceTitle, companyContactInfo, clientContactInfo, invoiceDetails, invoiceToolbar,
            invoiceItems, invoiceTotalBreakDown, invoiceFooterNotes, invoiceFooter,
            createInvoiceBtn, saveInvoiceBtn, editModeSwitch,

            Loader, imageUploader,
            companySelector
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
            clientId:{
                type: Number,
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
                isSavingInvoice: false,
                isCreatingInvoice: false,
                isConvertingToInvoice: false,
                isOpenProductsAndServicesModal: false,

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
                    this.invoiceHasChanged = this.checkIfinvoiceHasChanged(val);
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

                //var cancelScroll = VueScrollTo.scrollTo('invoice-summary-1', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#invoice-summary-1', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            updateClientChanges(newClient){
                this.client = this.localInvoice.customized_client_details = this.formatCompanyDetails(newClient);

                this.$Notice.success({
                    title: 'Client changed to ' + newClient.name
                });
            },
            formatCompanyDetails(company){
                var companyDetails = {
                        id: company.id,
                        name: company.name || '',
                        email: company.email || '',
                        phone: company.phone || '',
                        additionalFields: []
                    }

                var x, include = ['website', 'address', 'city', 'country'];

                for(x = 0; x < include.length; x++){
                    if(company[ include[x] ]){
                        companyDetails.additionalFields.push({ value: company[ include[x] ] });
                    }
                }

                return companyDetails;
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
                
                this.fetchCompanyInfo();

                this.fetchClientInfo();
            },
            checkIfinvoiceHasChanged: function(updatedInvoice = null){
                
                var currentInvoice = _.cloneDeep(updatedInvoice || this.localInvoice);
                var isNotEqual = !_.isEqual(currentInvoice, this._localInvoiceBeforeChange);

                console.log('currentInvoice');
                console.log(currentInvoice);
                console.log('localInvoiceBeforeChange');
                console.log(this._localInvoiceBeforeChange);
                console.log(isNotEqual);

                return isNotEqual;
            },
            storeOriginalInvoice(){
                console.log('storing _localInvoiceBeforeChange');
                //  Store the original invoice
                this._localInvoiceBeforeChange = _.cloneDeep(this.localInvoice);
            },
            closeModal(){
                this.isOpenProductsAndServicesModal = !this.isOpenProductsAndServicesModal;
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

                //  Update the invoice change status
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

                //  Disable edit mode
                this.editMode = false;

            },
            fetchClientInfo() {
                if(!this.client && this.clientId){
                    const self = this;

                    //  Start loader
                    self.isLoadingClientInfo = true;

                    console.log('Start getting client company details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.clientId+'?model=Company')
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingClientInfo = false;
                            
                            if(data){
                                //  Format the company details
                                self.client = self.localInvoice.customized_client_details = self.formatCompanyDetails(data);
                            }
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingClientInfo = false;

                            console.log('invoiceSummaryWidget.vue - Error getting client company details...');
                            console.log(response);    
                        });
                }
            },
            fetchCompanyInfo() {
                if(!this.company && this.user.company_id){
                    const self = this;

                    //  Start loader
                    self.isLoadingCompanyInfo = true;

                    console.log('Start getting company details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+self.user.company_id+'?model=Company')
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingCompanyInfo = false;

                            if(data){
                                //  Format the company details
                                self.company = self.localInvoice.customized_company_details = self.formatCompanyDetails(data);
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
        created(){

            //  Store the current state of the invoice as the original invoice
            this.storeOriginalInvoice();

            if(this.createMode){
                this.activateCreateMode();
            }
        }
    };
  
</script>