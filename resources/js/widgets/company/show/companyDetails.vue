<style scoped>

    .el-form-item >>> .el-form-item__label{
        margin:0 !important;
        padding:0 !important;
        line-height: 24px !important;
    }

    .form-label{
        font-size:14px;
    }

    .info-highlight-box{
        background: #f5f7fa;
        border-radius: 10px;
        padding: 15px;
    }

</style>

<template>

        <Row>

            <Col :span="24">

                <!-- Loader -->
                <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading...</Loader>
                
                <div v-if="!localEditMode">

                    <Row :gutter="20" class="mb-1">

                        <!-- Company logo -->
                        <Col :span="12">

                            <imageUploader 
                                uploadMsg="Upload or change logo"
                                :thumbnailStyle="{ width: '150px', height:'auto' }"
                                :allowUpload="editMode"
                                :multiple="false"
                                :imageList="
                                    [{
                                        'name': 'Company Logo',
                                        'url': '/images/assets/logo/OQ-B.png'
                                    }]">
                            </imageUploader>

                        </Col>

                        <Col :span="24" class="mb-2">
                            <h4 class="ml-1 text-dark">Summary</h4>
                        </Col>

                        <Col :span="24">
                            <Row class="info-highlight-box mb-3">
                                <Col :span="12">

                                    <!-- Name -->
                                    <p class="text-dark"><strong>Name:</strong> {{ formData.name ? formData.name : '___' }}</p>
                                    <!-- Relationship -->
                                    <p class="text-dark"><strong>Relationship:</strong> {{ formData.relationship ? formData.relationship : '___' }}</p>
                                    <!-- Description -->
                                    <p class="text-dark"><strong>Description:</strong> {{ formData.description ? formData.description : '___' }}</p>
                            
                                </Col>

                                <Col :span="12">

                                    <!-- Type Selector e.g) Private, Government, Parastatal, Non Profit Organisation -->
                                    <p class="text-dark"><strong>Type:</strong> {{ formData.type ? formData.type : '___' }}</p>
                                    <!-- Date Of Incorporation -->
                                    <p class="text-dark"><strong>Date Of Incorporation:</strong> {{ formData.date_of_incorporation | moment('MMM DD YYYY') || '___' }}</p>
                                    
                                </Col>

                            </Row>
                        </Col>

                        <Col :span="24" class="mt-2 mb-2">
                            <h4 class="ml-1 text-dark">Contacts</h4>
                        </Col>

                        <Col :span="24">
                            <Row class="info-highlight-box mb-3">

                                <Col :span="detailMode ? '12' : '24'">
                                    <!-- Email -->
                                    <p class="text-dark"><strong>Email:</strong> {{ formData.email ? formData.email : '___' }}</p>
                                </Col>

                                <Col v-if="detailMode" :span="12">
                                    <!-- Additional Email -->
                                    <p v-if="!localEditMode" class="text-dark"><strong>Additional Email:</strong> {{ formData.additional_email ? formData.additional_email : '___' }}</p>
                                    <el-form-item v-if="localEditMode" label="Additional Email:" prop="additional_email" class="mb-2">
                                        <el-input v-model="formData.additional_email" size="small" style="width:100%"placeholder="Enter addittional email"></el-input>
                                    </el-form-item>
                                </Col>

                                <Col :span="24">
                                    <!-- Calling Codes Selector -->
                                    <span class="form-label text-dark mt-2 mb-2 d-block"><strong>Phone(s):</strong></span>
                                    <phoneInput class="mb-2"  
                                                :modelId="localCompany.id" 
                                                :modelType="localCompany.model_type" 
                                                :phones="formData.phones" 
                                                :suggestedPhones="{}"
                                                :numberLimit="5"
                                                selectedType="mobile"
                                                :disabledTypes="[]"                                                        
                                                :removable="false"
                                                :deletable="localEditMode"
                                                :hidedable="false"
                                                :editable="localEditMode"
                                                :showPhoneType="true"
                                                :removeDuplicates="true"
                                                :showIcon="false" 
                                                onIcon="" offIcon="" 
                                                title="" onText="" offText="" 
                                                poptipMsg=""
                                                @updated="updatePhoneChanges($event)">
                                    </phoneInput>

                                </Col>

                            </Row>
                        </Col>

                        <Col :span="24" class="mt-2 mb-2">
                            <h4 class="ml-1 text-dark">Location</h4>
                        </Col>
                        
                        <Col :span="24">
                            <Row class="info-highlight-box mb-3">

                                <Col :span="detailMode ? '12' : '24'">
                                    <!-- Address -->
                                    <p class="text-dark"><strong>Address:</strong> {{ formData.address ? formData.address : '___' }}</p>
                                </Col>

                                <Col v-if="detailMode" :span="12">
                                    <!-- Country Selector -->
                                    <p class="text-dark"><strong>Country:</strong> {{ formData.country ? formData.country : '___' }}</p>
                                </Col>

                                <Col :span="12">
                                    <!-- Provience Selector -->
                                    <p class="text-dark"><strong>State/Provience/District:</strong> {{ formData.provience ? formData.provience : '___' }}</p>
                                </Col>

                                <Col :span="12">
                                    <!-- Cities Selector -->
                                    <p class="text-dark"><strong>City/Town:</strong> {{ formData.city ? formData.city : '___' }}</p>
                                </Col>
                                
                                <Col :span="24">
                                    <p class="text-dark"><strong>About:</strong> {{ formData.bio ? formData.bio : '___' }}</p>
                                </Col>

                            </Row>
                        </Col>

                        <Col :span="24" class="mt-2 mb-2">
                            <h4 class="ml-1 text-dark">Social Pages</h4>
                        </Col>

                        <Col :span="24">
                            <Row class="info-highlight-box mb-3">

                                <Col :span="detailMode ? '12' : '24'">
                                    <!-- Website Link -->
                                    <p class="text-dark"><strong>Website Link:</strong> 
                                        <a v-if="formData.website_link" :href="formData.website_link" target="_blank">{{ formData.website_link }}</a>
                                        <span v-else>___</span>
                                    </p>
                                </Col>

                                <Col v-if="detailMode" :span="12">
                                    <!-- Facebook Link -->
                                    <p class="text-dark"><strong>Facebook Link:</strong> 
                                        <a v-if="formData.facebook_link" :href="formData.facebook_link" target="_blank">{{ formData.facebook_link }}</a>
                                        <span v-else>___</span>
                                    </p>
                                </Col>

                                <Col v-if="detailMode" :span="12">
                                    <!-- Twitter Link -->
                                    <p class="text-dark"><strong>Twitter Link:</strong> 
                                        <a v-if="formData.twitter_link" :href="formData.twitter_link" target="_blank">{{ formData.twitter_link }}</a>
                                        <span v-else>___</span>
                                    </p>
                                </Col>

                                <Col v-if="detailMode" :span="12">
                                    <!-- linkedIn Link -->
                                    <p class="text-dark"><strong>LinkedIn Link:</strong> 
                                        <a v-if="formData.linkedin_link" :href="formData.linkedin_link" target="_blank">{{ formData.linkedin_link }}</a>
                                        <span v-else>___</span>
                                    </p>
                                </Col>

                                <Col v-if="detailMode" :span="12">
                                    <!-- Instagram Link -->
                                    <p class="text-dark"><strong>Instagram Link:</strong> 
                                        <a v-if="formData.instagram_link" :href="formData.instagram_link" target="_blank">{{ formData.instagram_link }}</a>
                                        <span v-else>___</span>
                                    </p>
                                </Col>

                            </Row>
                        </Col>

                    </Row>

                </div>

                <el-form v-if="localEditMode" 
                         label-position="top" label-width="100px" 
                         :model="formData">
                    
                    <Row :gutter="20" class="mb-1">

                        <!-- Edit mode switch -->
                        <Col v-if="!hideSummaryToggle" :span="24">
                            <detailModeSwitch v-bind:detailMode.sync="detailMode" :ripple="false" class="float-right mt-2"></detailModeSwitch>
                        </Col>
                            
                        <Col v-if="detailMode && !hideSummaryToggle" :span="24" class="mt-1 mb-2">
                            <Alert>Provide as much or as little information as you’d like. We will never share or sell individual personal information or personally identifiable details.</Alert>
                        </Col>

                        <!-- Company logo -->
                        <Col :span="24" v-if="showClientOrSupplierSelector">

                            <imageUploader 
                                uploadMsg="Upload or change logo"
                                :thumbnailStyle="{ width: '200px', height:'auto' }"
                                :allowUpload="editMode"
                                :multiple="false"
                                :imageList="
                                    [{
                                        'name': 'Company Logo',
                                        'url': '/images/assets/logo/OQ-B.png'
                                    }]">
                            </imageUploader>

                        </Col>

                        <!-- Client/Supplier Selector -->
                        <Col :span="24" v-if="showClientOrSupplierSelector">
   
                            <el-form-item label="Relationship:" prop="relationship" class="mt-2 mb-2">
                                <clientOrSupplierSelector class="mb-2" 
                                    @on-change="localCompany.relationship = $event">
                                </clientOrSupplierSelector>
                            </el-form-item>

                        </Col>

                        <Col :span="detailMode ? '12' : '24'">
                            <!-- Name -->
                            <el-form-item label="Name:" prop="name" class="mb-2">
                                <el-input v-model="localCompany.name" size="small" style="width:100%" placeholder="Enter company/organisation name"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Description -->
                            <el-form-item label="Description:" prop="description" class="mb-2">
                                <el-input v-model="localCompany.description" size="small" style="width:100%" placeholder="Enter company/organisation description"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>
                    
                    <Row :gutter="20" class="mb-1">

                        <Col :span="detailMode ? '12' : '24'">
                            <!-- Type Selector e.g) Private, Government, Parastatal, Non Profit Organisation -->
                            <span class="form-label mb-1 d-block">Type</span>
                            <companyPrivateOrGovernmentSelector 
                                 v-if="localEditMode"
                                class="mb-2"
                                :selectedType="localCompany.type"
                                @updated="localCompany.type = $event">
                            </companyPrivateOrGovernmentSelector>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Date Of Birth -->
                            <el-form-item label="Date Of Incorporation" prop="date_of_incorporation" class="mb-2">
                                <el-date-picker v-model="localCompany.date_of_incorporation" type="date" placeholder="Enter date of incorporation" style="width:100%" 
                                    format="yyyy-MM-dd" value-format="yyyy-MM-dd">
                                </el-date-picker>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20" class="mb-1">

                        <Col :span="detailMode ? '12' : '24'">
                            <!-- Email -->
                            <el-form-item label="Email:" prop="email" class="mb-2">
                                <el-input v-model="localCompany.email" size="small" style="width:100%" placeholder="Enter email address"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Additional Email -->
                            <el-form-item label="Additional Email:" prop="additional_email" class="mb-2">
                                <el-input v-model="localCompany.additional_email" size="small" style="width:100%"placeholder="Enter addittional email"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20" class="mb-1">

                        <Col :span="detailMode ? '12' : '24'">
                            <!-- Website Link -->
                            <el-form-item label="Website Link:" prop="website_link" class="mb-2">
                                <el-input v-model="localCompany.website_link" size="small" style="width:100%" placeholder="Enter website link"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Facebook Link -->
                            <el-form-item label="Facebook Link:" prop="facebook_link" class="mb-2">
                                <el-input v-model="localCompany.facebook_link" size="small" style="width:100%"placeholder="Enter Facebook link"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Twitter Link -->
                            <el-form-item label="Twitter Link:" prop="twitter_link" class="mb-2">
                                <el-input v-model="localCompany.twitter_link" size="small" style="width:100%"placeholder="Enter Twitter link"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- linkedIn Link -->
                            <el-form-item label="LinkedIn Link:" prop="linkedin_link" class="mb-2">
                                <el-input v-model="localCompany.linkedin_link" size="small" style="width:100%"placeholder="Enter LinkedIn link"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Instagram Link -->
                            <el-form-item label="Instagram Link:" prop="instagram_link" class="mb-2">
                                <el-input v-model="localCompany.instagram_link" size="small" style="width:100%"placeholder="Enter Instagram link"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20" class="mb-1">

                        <Col :span="24">
                            <!-- Calling Codes Selector -->
                            <span class="form-label mb-1 d-block">Phone(s):</span>
                            <phoneInput class="mb-2"  
                                        :modelId="localCompany.id" 
                                        :modelType="localCompany.model_type" 
                                        :phones="localCompany.phones" 
                                        :suggestedPhones="{}"
                                        :numberLimit="5"
                                        selectedType="mobile"
                                        :disabledTypes="[]"                                                        
                                        :removable="false"
                                        :deletable="localEditMode"
                                        :hidedable="false"
                                        :editable="localEditMode"
                                        :showPhoneType="true"
                                        :removeDuplicates="true"
                                        :showIcon="false" 
                                        onIcon="" offIcon="" 
                                        title="" onText="" offText="" 
                                        poptipMsg=""
                                        @updated="updatePhoneChanges($event)">
                            </phoneInput>

                        </Col>

                    </Row>
                    
                    <Row :gutter="20" class="mb-1">
                        
                        <Col :span="detailMode ? '12' : '24'">
                            <!-- Address -->
                            <el-form-item label="Address:" prop="address" class="mb-2">
                                <el-input v-model="localCompany.address" size="small" style="width:100%" placeholder="Enter address"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Country Selector -->
                            <span v-if="localEditMode">
                                <span class="form-label mb-1 d-block">Country</span>
                                <countrySelector
                                    :selectedCountry="localCompany.country"
                                    @updated="updateCountryChanges($event)">
                                </countrySelector>
                            </span>
                        </Col>

                    </Row>

                    <Row v-if="detailMode" :gutter="20" class="mb-1">

                        <Col :span="12">
                            <!-- Provience Selector -->
                            <span v-if="localEditMode">
                                <span class="form-label mb-1 d-block">State/Provience/District</span>
                                <provinceSelector
                                    :selectedCountry="localCompany.country"
                                    :selectedProvience="localCompany.provience"
                                    @updated="updateProvienceChanges($event)">
                                </provinceSelector>
                            </span>
                        </Col>

                        <Col :span="12">
                            <!-- Cities Selector -->
                            <span v-if="localEditMode">
                                <span class="form-label mb-1 d-block">City/Town</span>
                                <citySelector
                                    :selectedCountry="localCompany.country"
                                    :selectedCity="localCompany.city"
                                    @updated="updateCityChanges($event)">
                                </citySelector>
                            </span>
                        </Col>

                    </Row>

                    <Row v-if="!hideBio" :gutter="20" class="mt-2 mb-1">
                        
                        <Col :span="24">
                            <el-form-item label="Say something about this company/organisation:" prop="bio" class="mb-2">
                                <el-input type="textarea" v-model="localCompany.bio" size="small" style="width:100%" placeholder="Write something..."></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row v-if="!hideSaveBtn">
                        <Col :span="24">
                            <hr class="mt-2" />
                            <!-- Save Changes Button -->
                            <Button class="float-right mt-2" type="success" size="large" @click="saveCompany()">
                                <span>Save Changes</span>
                            </Button>
                        </Col>
                    </Row>

                </el-form>

            </Col>
            
        </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../components/_common/loaders/Loader.vue'; 

    /*  Inputs   */
    import phoneInput from './../../../components/_common/inputs/phoneInput.vue'; 

    /*  Selectors   */
    import companyPrivateOrGovernmentSelector from './../../../components/_common/selectors/companyPrivateOrGovernmentSelector.vue'; 
    import provinceSelector from './../../../components/_common/selectors/provinceSelector.vue'; 
    import citySelector from './../../../components/_common/selectors/citySelector.vue'; 
    import countrySelector from './../../../components/_common/selectors/countrySelector.vue'; 
    import clientOrSupplierSelector from './../../../components/_common/selectors/clientOrSupplierSelector.vue';
    
    /*  Switches   */
    import detailModeSwitch from './../../../components/_common/switches/detailModeSwitch.vue'; 

    /*  Image Uploader  */
    import imageUploader from './../../../components/_common/uploaders/imageUploader.vue';

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            companyId: { 
                type: Number,
                default: null
            },
            company: { 
                type: Object,
                default: null
            },
            /*
             *  canSaveOnCreate checks if the parent has permitted for the company
             *  to be saved to the databse. If canSaveOnCreate is set to true
             *  we will perform an ajax request to create the new company
             *  using our formData information.
             */
            canSaveOnCreate:{
                type: Boolean,
                default: false          
            },
            hideBio: { 
                type: Boolean,
                default: false
            },
            hideSaveBtn: { 
                type: Boolean,
                default: false
            },
            showClientOrSupplierSelector: { 
                type: Boolean,
                default: false
            },
            hideSummaryToggle:{
                type: Boolean,
                default: false
            },
            activateSummaryMode:{
                type: Boolean,
                default: false
            }
        },
        components: { 
            Loader, imageUploader, companyPrivateOrGovernmentSelector, phoneInput, countrySelector, provinceSelector, 
            citySelector, clientOrSupplierSelector, detailModeSwitch
        },
        data(){
            return {
                localCompany: {},
                detailMode: this.activateSummaryMode,
                isLoading: false,
                ruleForm: { 
                    //  VALIDATION RULES

                },
                fetchedCountries: [],
                fetchedStates: [],
                
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the canSaveOnCreate value
            canSaveOnCreate: {
                handler: function (val, oldVal) {

                    //  Create a new company if canSaveOnCreate is set to true
                    if(this.companyId){
                        this.saveCompany();
                    }else{
                        this.createNewCompany();
                    }

                }
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {

                    //  Update the edit mode value
                    this.localEditMode = val;
                
                }
            }
        },
        computed:{
            formData(){
                return  {
                    relationship: this.localCompany.relationship,       //  e.g) client, supplier
                    name: this.localCompany.name,
                    description: this.localCompany.description,
                    date_of_incorporation: this.localCompany.date_of_incorporation,
                    type: this.localCompany.type,

                    address: this.localCompany.address,
                    country: this.localCompany.country,
                    provience: this.localCompany.provience,
                    city: this.localCompany.city,
                    postal_or_zipcode: this.localCompany.postal_or_zipcode,

                    email: this.localCompany.email,
                    additional_email: this.localCompany.additional_email,
                    website_link: this.localCompany.website_link,
                    facebook_link: this.localCompany.facebook_link,
                    twitter_link: this.localCompany.twitter_link,
                    linkedin_link: this.localCompany.linkedin_link,
                    instagram_link: this.localCompany.instagram_link,
                    phones: this.localCompany.phones,

                    logo_url: this.localCompany.logo_url,
                    bio: this.localCompany.bio,
                }
            }
        },
        methods: {
            fetch(){
                
                if( this.companyId ){
                 
                    const self = this;

                    //  Additional data to eager load along with the company found
                    var connections = '?connections=phones';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.companyId+connections)
                        .then(({data}) => {

                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            self.localCompany = data;

                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                    }
            },
            updatePhoneChanges(newVal){
                console.log(newVal);
                this.formData.phones = newVal;
            },
            updateCountryChanges(newVal){
                this.formData.country = newVal;
            },
            updateProvienceChanges(newVal){
                this.formData.provience = newVal;
            },
            updateCityChanges(newVal){
                this.formData.city = newVal;
            },
            saveCompany() {
                const self = this;

                //  Start loader
                self.isSaving = true;

                console.log('Attempt to save company details...');

                //  Company data to send
                let companyData = {
                    company: this.formData
                };

                //  Additional data to eager load along with the jobcard found
                var connections = '?connections=phones';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies/'+this.localCompany.id + connections, companyData)
                    .then(({data}) => {

                        console.log(data);

                        //  Stop loader
                        self.isSaving = false;
                        
                        //  Alert creation success
                        self.$Message.success('Company saved sucessfully!');

                        self.$emit('updated:company', data);

                    })         
                    .catch(response => { 
                        console.log('widgets/company/show/main.vue - Error saving company details...');
                        console.log(response);

                        //  Stop loader
                        self.isLoggingIn = false;     
    
                    });

            },
            createNewCompany() {
                const self = this;

                //  Start loader
                self.isCreating = true;

                console.log('Attempt to create new user...');

                //  Company data to send
                let companyData = {
                    company: this.formData
                };

                //  Additional data to eager load along with the jobcard found
                var connections = '?connections=phones';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/companies'+connections, companyData)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSaving = false;
                        
                        //  Alert creation success
                        self.$Message.success('Created sucessfully!');

                        self.$emit('created:company', data);

                    })         
                    .catch(response => { 
                        console.log('widgets/company/show/main.vue - Error creating company...');
                        console.log(response);

                        //  Stop loader
                        self.isLoggingIn = false;     
    
                    });

            },
        },
        created(){
            if(this.company){
                this.localCompany = this.company;
            }else{
                this.fetch();
            }
            
        }
    };
  
</script>