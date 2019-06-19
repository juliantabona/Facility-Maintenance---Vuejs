<style scoped>

    .el-form-item__error {
        line-height: 0.8;
        padding-top: 0px;
    }

</style>
 <template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="700"
            :isSaving="false" 
            :hideModal="hideModal"
            :showCloseBtn="false"
            :modalClosable="false"
            title=""
            okText="" cancelText=""
            @on-ok="" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">
                
                <!-- Activity Number Card -->
                <Row :gutter="20">
                    
                    <Col v-if="currentStage == 1" :span="24">
                        
                        <div class="mb-4">
                            <h1 class="mt-4 mb-4 text-center">Welcome To OQ Suite</h1>
                            <p class="text-center">Hi Julian, Welcome to your Mobile Office. We would personally like to give you a guided tour of our platform.</p> 
                            <p class="text-center mb-3">It's really easy to connect and stay engaged with your customers using OQ Suite. </p>
                            <p class="font-weight-bold text-center">- Lets get started with the basics -</p>  
                        </div>

                        <!-- Focus Ripple  -->
                        <focusRipple color="blue" :ripple="true" class="d-block">

                            <!-- Create Company Button  -->
                            <Button type="primary" size="large" @click="nextStep()"
                                    class="button_1 d-block mr-auto ml-auto mt-2">
                                <span class="mr-1">Setup Your Mobile Office</span>
                                <Icon type="ios-arrow-round-forward" />
                            </Button>

                        </focusRipple>

                        <span class="d-block mt-4 mb-4 text-center">Takes less than a minute</span>
                        
                    </Col>

                    <Col v-if="currentStage == 2" :span="24">
                        
                        <div class="mb-4">
                            <h1 class="mt-4 mb-4 text-center">What Would You Like To Manage?</h1>
                            <p class="text-center">OQ Suite offers a range of tools to help you run your business smoothly. Tick the tools you would like to use.</p> 
                            <p class="font-weight-bold text-center mt-2 mb-4">- You can always change this later -</p>  
                        </div>

                        <Row :gutter="12" class="mb-5">
                            <Col v-for="(tool, i) in availableTools" :key="i" :span="8">
                                <IconAndCounterCard :title="tool.name" :icon="tool.icon" 
                                                    class="mb-2" type="success"
                                                    :showCheckMark="true"
                                                    :checkMarkVisibility="selectedTools.includes(tool.name)"
                                                    @click.native="toggleOption(tool.name, i)"
                                                    v-on:mouseover="updateHoveredTool(tool)"
                                                    v-on:mouseout="updateHoveredTool(tool)">
                                </IconAndCounterCard>
                            </Col>
                        </Row>

                        <Row :gutter="12">
                            <Col :span="24">{{ hoveredTool }}
                                <span v-if="hoveredTool" class="p-3">
                                    {{ hoveredTool.description }}
                                </span>
                            </Col>
                        </Row>

                        <!-- Focus Ripple  -->
                        <focusRipple v-if="selectedTools.length >= 1" color="blue" :ripple="true" class="d-block mb-3">

                            <!-- Create Company Button  -->
                            <Button type="primary" size="large" @click="nextStep()"
                                    class="d-block mr-auto ml-auto mt-2">
                                <span class="mr-1">Continue</span>
                                <Icon type="ios-arrow-round-forward" />
                            </Button>

                        </focusRipple>
                        
                    </Col>

                    <Col v-if="currentStage == 3" :span="24">
                        
                        <div class="mb-4">
                            <h1 class="mt-4 mb-4 text-center">Lets Understand Your Business?</h1>
                            <p class="text-center">OQ Suite will make your tools more relevant to your business based on what you do.</p> 
                            <p class="font-weight-bold text-center mt-2 mb-4">- You can always change this later -</p>  
                        </div>

                        <Row :gutter="12" class="mb-2">

                            <Col :span="24">

                                <el-form :model="ruleForm" :rules="rules" ref="ruleForm" class="demo-ruleForm">

                                    <Row v-if="!isCreatingOrUpdatingCompany" :gutter="12">
                                        <Col :span="8">

                                            <!-- Company logo -->
                                            <imageUploader 
                                                uploadMsg="Upload Company logo"
                                                :thumbnailStyle="{ width:'200px', height:'auto' }"
                                                :allowUpload="true"
                                                :multiple="false"
                                                :imageList="[]">
                                            </imageUploader>

                                        </Col>
                                        <Col :span="16">
                                            <Row :gutter="12">
                                                <Col :span="12">
                                                    <!-- Company Name -->
                                                    <el-form-item label="" prop="company_name" class="mb-0">
                                                        <el-input v-model="ruleForm.company_name" placeholder="Company Name" size="mini"></el-input>
                                                    </el-form-item>
                                                </Col>
                                                <Col :span="12">
                                                    <!-- Company Short Name -->
                                                    <el-form-item label="" prop="company_short_name" class="mb-0">
                                                        <el-input v-model="ruleForm.company_short_name" placeholder="Abbreviation (Optional)" max="10" size="mini"></el-input>
                                                    </el-form-item>
                                                </Col>
                                            </Row>
                                            <Row :gutter="12">
                                                <Col :span="24">
                                                    <!-- Company Email -->
                                                    <el-form-item label="" prop="company_email" class="mb-0">
                                                        <el-input v-model="ruleForm.company_email" placeholder="Company Email" size="mini"></el-input>
                                                    </el-form-item>
                                                    <!-- Company Address -->
                                                    <el-form-item label="" prop="company_address" class="mb-0">
                                                        <el-input v-model="ruleForm.company_address" size="mini" style="width:100%" placeholder="Company Physical Address"></el-input>
                                                    </el-form-item>
                                                    <!-- Industry Selector -->
                                                    <industrySelector
                                                        :selectedIndustry="ruleForm.company_industry"
                                                        @updated="ruleForm.company_industry = $event">
                                                    </industrySelector>
                                                </Col>

                                                <Col :span="12" class="mt-1">
                                                    <!-- Country Selector -->
                                                    <span>
                                                        <countrySelector
                                                            :selectedCountry="ruleForm.company_country"
                                                            @updated="ruleForm.company_country = $event">
                                                        </countrySelector>
                                                    </span>
                                                </Col>

                                                <Col :span="12" class="mt-1">
                                                    <!-- Cities Selector -->
                                                    <span>
                                                        <citySelector
                                                            v-if="ruleForm.company_country"
                                                            :selectedCountry="ruleForm.company_country"
                                                            :selectedCity="ruleForm.company_city"
                                                            @updated="ruleForm.company_city = $event">
                                                        </citySelector>
                                                    </span>
                                                </Col>

                                                <Col :span="24">
                                                    <el-form-item label="" prop="company_phones" class="mb-2">
                                                        <!-- Calling Codes Selector -->
                                                        <span class="form-label d-block" style="font-size:13px;">Company Phone(s):</span>
                                                        <phoneInput 
                                                                    class="mb-2"  
                                                                    :phones="ruleForm.company_phones" 
                                                                    :suggestedPhones="{}"
                                                                    :minLimit="1"
                                                                    :maxLimit="3"
                                                                    :setStatus="true"
                                                                    :selectedType="null"
                                                                    :disabledTypes="[]"                                                        
                                                                    :removable="true"
                                                                    :deletable="false"
                                                                    :hidedable="false"
                                                                    :editable="true"
                                                                    :showPhoneType="true"
                                                                    :removeDuplicates="true"
                                                                    :showExistingPhonesTab="false"
                                                                    :showIcon="false" 
                                                                    onIcon="" offIcon="" 
                                                                    title="" onText="" offText="" 
                                                                    poptipMsg=""
                                                                    @updated="ruleForm.company_phones = $event">
                                                        </phoneInput>  
                                                    </el-form-item>
                                                </Col>

                                            </Row>
                                        </Col>
                                    </Row>

                                    <Row v-if="isCreatingOrUpdatingCompany" :gutter="12">
                                        <Col :span="8" :offset="8">
                                            <div class="mt-3 mb-5">
                                                <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                                                {{ this.user.company_id ? 'Updating': 'Creating' }} company...
                                            </div>
                                        </Col>
                                    </Row>

                                </el-form>

                            </Col>

                        </Row>

                        <!-- Focus Ripple  -->
                        <focusRipple v-if="!isCreatingOrUpdatingCompany" color="blue" :ripple="true" class="d-block mb-3">

                            <!-- Create Company Button  -->
                            <Button type="primary" size="large" @click="createOrUpdateCompany()"
                                    class="d-block mr-auto ml-auto mt-2">
                                <span class="mr-1">{{ this.user.company_id ? 'Update': 'Create' }} My Company</span>
                            </Button>

                        </focusRipple>
                        
                    </Col>

                    <Col v-if="currentStage == 4" :span="24">
                        
                        <div class="mb-4">
                            <h1 class="mt-4 mb-4 text-center">Go Mobile & Stay Connected!</h1>
                            <p class="text-center">OQ Suite also works offline via SMS but this requires your mobile number. You can use this number to receive notifications, reports as well as connect to mobile money services e.g) Orange Money & MyZaka to pay or receive payments.</p> 
                            <p class="font-weight-bold text-center mt-2 mb-4">- You can always change this later -</p>  
                        </div>

                        <Row :gutter="12" class="mb-2">

                            <Col :span="24">
                                <Row v-if="!isSavingUserPhones" :gutter="12">
                                    <Col :span="14" :offset="5">
                                        <!-- Calling Codes Selector -->
                                        <span class="form-label mb-1 d-block" style="font-size:13px;">My Mobile Number(s):</span>
                                        
                                        <phoneInput 
                                                    class="mb-2"  
                                                    :phones="userMobilePhones" 
                                                    :suggestedPhones="{ type: 'mobile', count: 1 }"
                                                    :minLimit="1"
                                                    :maxLimit="1"
                                                    :setStatus="true"
                                                    selectedType="mobile"
                                                    :disabledTypes="['tel', 'fax']"                                                     
                                                    :removable="false"
                                                    :deletable="true"
                                                    :hidedable="false"
                                                    :editable="true"
                                                    :showPhoneType="true"
                                                    :removeDuplicates="true"
                                                    :showExistingPhonesTab="false"
                                                    :showIcon="false" 
                                                    onIcon="" offIcon="" 
                                                    title="" onText="" offText="" 
                                                    poptipMsg=""
                                                    @updated="updateUserPhones($event)">
                                        </phoneInput> 
                                    </Col>
                                </Row>

                                <Row v-if="isSavingUserPhones" :gutter="12">
                                    <Col :span="8" :offset="8">
                                        <div class="mt-4">
                                            <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                                            Saving mobile...
                                        </div>
                                    </Col>
                                </Row>

                            </Col>

                        </Row>

                        <!-- Focus Ripple  -->
                        <focusRipple v-if="!isSavingUserPhones && (userMobilePhones || {}).length" color="blue" :ripple="true" class="d-block mb-3">

                            <!-- Add Mobile Number Button  -->
                            <Button type="primary" size="large" @click="saveUserMobilePhones()"
                                    class="d-block mr-auto ml-auto mt-2">
                                <span class="mr-1">Continue</span>
                            </Button>

                        </focusRipple>
                        
                    </Col>

                    <Col v-if="currentStage == 50" :span="24">
                        
                        <div>
                            <h1 class="mt-4 mb-0 text-center">Account Completed!</h1>
                        </div>

                        <Row v-if="!isSendingSampleSms" :gutter="20">
                            <Col span="24">
                                <Divider orientation="left">
                                    <Icon type="ios-send-outline" class="d-inline-block mr-1" :size="30"></Icon>
                                    <span class="font-weight-bold">Lets Test</span>
                                </Divider>
                                <p class="pl-5 pr-5 mb-3">
                                    <span>Great! You are all set up. Lets test to see if everything works.</span>
                                    <br>
                                    <span>
                                        Make sure your phone using the number 
                                        <span class="d-inline-block" style="min-width: 180px;">
                                            <existingPhoneSelector
                                                :availablePhones="userMobilePhones"
                                                :selectedPhone="userSelectedMobile"
                                                @selected:phone="userSelectedMobile = $event">
                                            </existingPhoneSelector>
                                        </span> is turned on.
                                    </span>
                                </p> 

                                <div v-if="hasSentSampleSms">
                                    <Alert type="success" show-icon closable class="ml-5 mr-5 mb-3">
                                        SMS Sent
                                        <span slot="desc">We sent an Sms to your phone. <br>If you didn't see it just wait a few minutes.</span>
                                        <br>
                                        <span class="btn btn-link d-inline-block m-0 p-0 mt-3" style="font-size: 12px; float: right;">
                                            <Icon type="ios-remove" :size="20" />
                                            <span>Change Phone</span>
                                        </span>
                                    </Alert>
                                </div>
                            </Col>
                        </Row>

                        <Row v-if="!isSendingSampleSms && !hasSentSampleSms" :gutter="20">
                            <Col span="8" offset="8">
                                <IconAndCounterCard title="Quotation" class="mb-2" 
                                                    type="success"
                                                    :icon="null" 
                                                    imageSrc="/images/assets/icons/paper-and-calculator.png" 
                                                    :imageStyle="{ width: '60px', margin: '0 auto', display: 'block' }"
                                                    :showBtn="true"
                                                    btnText="Send Example SMS"
                                                    :showCheckMark="false"
                                                    :checkMarkVisibility="false"
                                                    @click.native="sendSampleSms()">
                                </IconAndCounterCard>
                            </Col>
                        </Row>

                        <Row v-if="isSendingSampleSms">
                            <Col :span="8" :offset="8">
                                <div class="mt-4">
                                    <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                                    Sending example sms...
                                </div>

                            </Col>
                        </Row>

                        <!-- Focus Ripple  -->
                        <focusRipple v-if="hasSentSampleSms" color="blue" :ripple="true" class="d-block float-right mb-3 mr-5">

                            <!-- Add Mobile Number Button  -->
                            <Button type="primary" size="large" @click="nextStep()">
                                <span class="mr-1">Continue</span>
                            </Button>

                        </focusRipple>

                        <!-- Add Mobile Number Button  -->
                        <Button v-if="hasSentSampleSms" type="default" size="large" @click="sendSampleSms()"
                                class="d-block float-right mb-3 mr-2">
                            <span class="mr-1">Resend Sms</span>
                        </Button>
                        
                    </Col>

                    <Col v-if="currentStage == 5" :span="24">

                        <Row :gutter="20" class="mb-3">
                            
                            <Col :span="24">
                                <img src="/images/assets/icons/25-free-sms-stamp.png" :style="{ width: '180px' }" class="d-block ml-auto mr-auto mt-4 mb-2">
                                <div>
                                     <h1 class="mt-2 mb-2 text-center">Congradulations!</h1>
                                    <p class="text-center">You have been credited with <strong style="font-size: 15px;">25 free SMS's</strong> to get your business up and running.</p>
                                </div>
                            </Col>

                        </Row>

                        <Row v-if="isCompletingSetup || isRefreshingPage">
                            <Col :span="8" :offset="8">
                                <div class="mt-4">
                                    <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                                    {{ isCompletingSetup ? 'Completing setup...' : '' }}
                                    {{ isRefreshingPage ? 'Refreshing... please wait' : '' }}
                                </div>
                            </Col>
                        </Row>

                        <!-- Focus Ripple  -->
                        <focusRipple v-if="!isCompletingSetup && !isRefreshingPage" color="blue" :ripple="true" class="d-block mb-3">

                            <!-- Add Mobile Number Button  -->
                            <Button type="primary" size="large" @click="finalStep()"
                                    class="d-block mr-auto ml-auto mt-4">
                                <span class="mr-1">Start My Tour</span>
                            </Button>

                        </focusRipple>
                        
                    </Col>

                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Modal Structure  */
    import mainModal from './main.vue';

    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    /*  Cards  */
    import IconAndCounterCard from './../cards/IconAndCounterCard.vue';

    /*  Inputs   */
    import phoneInput from './../inputs/phoneInput.vue'; 

    /*  Selectors   */
    import industrySelector from './../../../components/_common/selectors/industrySelector.vue';
    import countrySelector from './../../../components/_common/selectors/countrySelector.vue';  
    import citySelector from './../../../components/_common/selectors/citySelector.vue'; 
    import existingPhoneSelector from './../../../components/_common/selectors/existingPhoneSelector.vue'; 


    /*  Image Uploader  */
    import imageUploader from './../../../components/_common/uploaders/imageUploader.vue';

    export default {
        components: { 
            mainModal, focusRipple, IconAndCounterCard, phoneInput, industrySelector, imageUploader, 
            countrySelector, citySelector, existingPhoneSelector 
        },
        data(){
            return{
                //  General data
                currentStage: 1,
                hideModal: false,
                user: auth.user,

                //  Stage 2 data
                hoveredTool: null,
                availableTools: [
                        {
                            name: 'Accounts & Billing',
                            icon: 'ios-cash-outline',
                            description: 'Allows you to create, save and send invoices, quotations and receipts via SMS/Email to your clients. Your client will also be able to pay using their phones as well as view their receipts.',
                        },
                        {
                            name: 'Appointments',
                            icon: 'ios-calendar-outline',
                            description: 'Allows you to create, save and send appointments to your clients via SMS/Email to your clients. Your client will also be able to accept or request appointment reschedules as well as view all appointments in the past using their phone',
                        },
                        {
                            name: 'Jobcard Management',
                            icon: 'ios-briefcase-outline',
                            description: 'Easily track and update jobcards with automation to alert clients via SMS/Email when jobs are completed. Your client will also be able to submit new jobs as well as track the progress of existing ones.',
                        }
                    ],
                selectedTools: [],

                //  Stage 3 data
                ruleForm: {
                    company_name: '',
                    company_short_name: '',
                    company_email: '',
                    company_address: '',
                    company_industry: '',
                    company_country: '',
                    company_city: '',
                    company_phones: [],
                },

                //  Stage 4 data
                userSelectedMobile: null,
                userMobilePhones: null,
                gettingUserMobilePhones: false,
                isSavingUserPhones: false,
                isSendingSampleSms: false,
                hasSentSampleSms: false,
                isCompletingSetup: false,
                isRefreshingPage: false,

                //  Form validation rules
                rules: {
                    company_name: [
                        { required: true, message: '', trigger: 'blur' }
                    ],
                    company_email: [
                        { required: true, message: '', trigger: 'blur' }
                    ]
                },

                isCreatingOrUpdatingCompany: false
            }
        },
        methods: {
            toggleOption(name, index){
                if( this.selectedTools.includes(name) ){
                    
                    var index;
                    
                    for(var x = 0; x < this.selectedTools.length; x++){
                        if( this.selectedTools[x] == name ){
                            index = x;
                        }
                    }

                    if( this.selectedTools.length == 1 ){
                        this.$Notice.warning({
                            desc: 'You must have atleast one option selected'
                        });
                    }else{
                        this.selectedTools.splice(index, 1);
                    }
                    
                }else{
                    this.selectedTools.push(name);
                }
            },
            showStep2Phones(){
                this.ruleForm.company_name
            },
            nextStep(){
                this.currentStage = this.currentStage + 1;
            },
            updateHoveredTool(tool){
                console.log(tool);
                this.hoveredTool = tool;
            },
            closeModal(){
                this.hideModal = true;
            },
            fetchCompany(){
                
                if( this.user.company_id ){
                 
                    //  Start loader
                    this.isLoadingCompanyDetails = true;

                    const self = this;

                    //  Additional data to eager load along with the company found
                    var connections = '';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.user.company_id+connections)
                        .then(({data}) => {

                            console.log(data);

                            //  Stop loader
                            self.isLoadingCompanyDetails = false;

                            //  Update local company details
                            self.ruleForm.company_name = data.name;
                            self.ruleForm.company_short_name = data.abbreviation;
                            self.ruleForm.company_email = data.email;
                            self.ruleForm.company_address = data.address;
                            self.ruleForm.company_industry = data.industry;
                            self.ruleForm.company_country = data.country;
                            self.ruleForm.company_city = data.city;
                            self.ruleForm.company_phones = data.phones;

                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoadingCompanyDetails = false;
                        });

                    }
            },
            createOrUpdateCompany(){

                var self = this;

                //  Start loader
                self.isCreatingOrUpdatingCompany = true;

                console.log('Attempt to create company...');

                //  Form data to send
                let companyData = { company: {
                    name: this.ruleForm.company_name,
                    abbreviation: this.ruleForm.company_short_name,
                    email: this.ruleForm.company_email,
                    address: this.ruleForm.company_address,
                    industry: this.ruleForm.company_industry,
                    country: this.ruleForm.company_country,
                    city: this.ruleForm.company_city,
                    phones: this.ruleForm.company_phones,
                } };
                
                //  Check if the user has a company id assigned. If yes, then get the company id
                //  so that we can use it to update the existing company instead of creating a
                //  completely new company.
                var companyId = this.user.company_id ? ('/' + this.user.company_id) : ''; 

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies'+companyId+'?auth_assign=1', companyData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingOrUpdatingCompany = false;

                        //  Alert creation success
                        self.$Message.success('Company created sucessfully!');

                        //  Update the local user details
                        auth.getUser();

                        // Go to the next step 
                        self.nextStep();

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingOrUpdatingCompany = false;

                        console.log('companySummaryWidget.vue - Error creating company...');
                        console.log(response);
                    });
            },
            getUserMobilePhones() {
                const self = this;

                //  Start loader
                self.gettingUserMobilePhones = true;

                console.log('Start getting the users existing phones...');

                //  Model id e.g 1,2,3, e.t.c
                var modelId = (this.user || {}).id;

                //  Model type e.g user/company
                var modelType = 'user';

                //  Additional data to eager load along with each mobile phone found
                var connections = '&connections=';

                //  Settings to prevent pagination
                var pagination = (connections ? '&': '') + 'paginate=0';

                if(modelId && modelType){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/phones?modelId='+modelId+'&modelType='+modelType+connections+pagination)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.gettingUserMobilePhones = false;

                            //  Update local phones phones
                            self.updateUserPhones(data)
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.gettingUserMobilePhones = false;

                            console.log('updateProfileAfterSignUpModal.vue - Error getting the users existing phones...');
                            console.log(response);    
                        });

                }
            },
            updateUserPhones(phones){
                this.userMobilePhones = phones;
                this.userSelectedMobile = phones.length ? phones[0] : null;

                console.log('phones____________________________');
                console.log(phones);
                console.log('selected phone____________________________');
                console.log(phones[0]);
            },
            saveUserMobilePhones(){

                const self = this;

                //  Login data to send
                let phoneData = {
                    modelId: this.user.id,
                    modelType: 'user',
                    phones: this.userMobilePhones,
                    replace: true
                };

                if(this.user.id){
                    
                    //  Start loader
                    self.isSavingUserPhones = true;

                    console.log('Attempt to create phone details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', '/api/phones', phoneData)
                        .then(({data}) => {
                            
                        //  Stop loader
                        self.isSavingUserPhones = false;

                        //  Alert creation success
                        self.$Message.success('Phone created sucessfully!');

                        // Go to the next step 
                        self.nextStep();

                        })         
                        .catch(response => { 
                            console.log('updateProfileAfterSignUpModal.vue - Error creating phone details...');
                            console.log(response);

                            //  Stop loader
                            self.isSavingUserPhones = false;     
        
                        });

                }else{
                    //  Alert parent and pass phone data
                    this.$emit('created',  self.localPhone);

                    //  Close modal
                    this.closeModal();

                }

            },
            sendSampleSms(){

                const self = this;

                //  Login data to send
                let phoneData = {
                    type: 'invoice',
                    phones: self.userMobilePhones
                };

                //  Start loader
                self.isSendingSampleSms = true;
                self.hasSentSampleSms = false;

                console.log('Attempt to send sample sms...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/sample-sms', phoneData)
                    .then(({data}) => {
                        
                    //  Stop loader
                    self.isSendingSampleSms = false;
                    self.hasSentSampleSms = true;

                    //  Alert creation success
                    self.$Message.success('Sms sent sucessfully!');

                    // Go to the next step 
                    //self.nextStep();

                    })         
                    .catch(response => { 
                        console.log('updateProfileAfterSignUpModal.vue - Error creating phone details...');
                        console.log(response);

                        //  Stop loader
                        self.isSendingSampleSms = false;     
    
                    });
            },
            finalStep(){

                const self = this;

                //  Start loader
                self.isCompletingSetup = true;

                console.log('Attempt to send sample sms...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/setup-completed')
                    .then(({data}) => {
                        
                    //  Stop loader
                    self.isCompletingSetup = false;


                    //  Start loader
                    self.isRefreshingPage = true;

                    //  Alert completion success
                    self.$Message.success('Acessing Dashboard!');

                    //  Update the local user details
                    auth.getUser().then(function(){
                        //  Refresh the page
                        self.$router.go();
                    });



                    })         
                    .catch(response => { 
                        console.log('updateProfileAfterSignUpModal.vue - Error creating phone details...');
                        console.log(response);

                        //  Stop loader
                        self.isCompletingSetup = false;     
    
                    });
            },
        },
        created(){

            //  Update the local user details
            auth.getUser();

            if( this.user ){
                //  Get  the users mobile phones
                this.userMobilePhones = this.getUserMobilePhones();
            }

            //  Get  the users company if it exists
            this.fetchCompany();

            /*
            //  initialize the tour instance
            var enjoyhint_instance = new this.$enjoyhint;

            //  Set the tour instructions
            var enjoyhint_script_steps = [
                    {
                        'click .button_1' : 'Click the "New" button to start creating your project'
                    }
                ];

            //  Set the instructions to the tour instance
            enjoyhint_instance.set(enjoyhint_script_steps);

            //  Start the tour
            enjoyhint_instance.run();
            */
        }
    }
</script>