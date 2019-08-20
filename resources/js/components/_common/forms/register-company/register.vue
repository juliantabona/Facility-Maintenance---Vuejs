<style scoped>
    
    .el-form-item{
        margin-bottom: 0px;
    }

    .el-form-item >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }

</style>

<template>

    <!-- Register Form-->
    <el-form v-if="registerForm" :model="registerForm" :rules="registerFormRules" ref="registerForm" autocomplete="off">
    
        <Row :gutter="20" class="mb-1">

            <!-- Company logo -->
            <Col :span="8" :offset="8" v-if="!hiddenFields.includes('logo')">
            
                <el-form-item prop="logo" class="mb-2" :error="registerCustomErrors.logo">
                    <imageUploader 
                        uploadMsg="Upload or change logo"
                        :allowUpload="true"
                        :multiple="false"
                        :docUrl=" this.existingCompany ? '/api/companies/'+(this.existingCompany || {}).id+'/logo' : null"
                        :postData="{ 
                            modelId: this.existingCompany ? (this.existingCompany || {}).id : null,
                            modelType: 'company',
                            location:  'company_logos', 
                            type: 'logo',
                            replaceable: true
                        }"
                        :thumbnailStyle="{ width:'200px', height:'auto' }"
                        @fileBeforeUpload="handleFileAdded('logo', $event)">
                    </imageUploader>
                </el-form-item>

            </Col>

            <!-- Client/Supplier Selector -->
            <Col :span="24" v-if="!hiddenFields.includes('relationship')">
            
                <el-form-item label="Relationship" prop="relationship" class="mt-2 mb-2">
                    <clientOrSupplierSelector class="mb-2" 
                        :selectedClientType="defaultRelationship"
                        @on-change="registerForm.relationship = $event">
                    </clientOrSupplierSelector>
                </el-form-item>

            </Col>

            <!-- Name -->
            <Col :span="12" v-if="!hiddenFields.includes('name')">
                <el-form-item label="Name" prop="name" class="mb-2" :error="registerCustomErrors.name">
                    <el-input v-model="registerForm.name" size="small" style="width:100%" 
                                placeholder="Enter company/organisation name">
                    </el-input>
                </el-form-item>
            </Col>

            <!-- Description -->
            <Col v-if="!summaryMode && !hiddenFields.includes('description')" :span="12">
                <el-form-item label="Description" prop="description" class="mb-2" :error="registerCustomErrors.description">
                    <el-input v-model="registerForm.description" size="small" style="width:100%" 
                                placeholder="Enter company/organisation description">
                    </el-input>
                </el-form-item>
            </Col>

            <!-- Type Selector e.g) Private, Government, Parastatal, Non Profit Organisation -->
            <Col :span="12" v-if="!hiddenFields.includes('type')">
                <span class="form-label mb-1 d-block">Type</span>
                <el-form-item prop="type" class="mb-2" :error="registerCustomErrors.type">
                    <companyPrivateOrGovernmentSelector 
                        class="mb-2"
                        :selectedType="registerForm.type"
                        @updated="registerForm.type = $event">
                    </companyPrivateOrGovernmentSelector>
                </el-form-item>
            </Col>

            <!-- Date Of Incorporation -->
            <Col :span="12" v-if="!summaryMode && !hiddenFields.includes('date_of_incorporation')">
                <el-form-item label="Date Of Incorporation" prop="date_of_incorporation" class="mb-2" :error="registerCustomErrors.date_of_incorporation">
                    <el-date-picker v-model="registerForm.date_of_incorporation" type="date" 
                                    placeholder="Enter date of incorporation" style="width:100%" 
                                    format="yyyy-MM-dd" value-format="yyyy-MM-dd">
                    </el-date-picker>
                </el-form-item>
            </Col>

            <!-- Email -->
            <Col :span="summaryMode ? 24 : 12" v-if="!hiddenFields.includes('email')">
                <el-form-item label="Email" prop="email" class="mb-2" :error="registerCustomErrors.email">
                    <el-input v-model="registerForm.email" size="small" style="width:100%" placeholder="Enter email address"></el-input>
                </el-form-item>
            </Col>

            <!-- Additional Email -->
            <Col :span="12" v-if="!summaryMode && !hiddenFields.includes('additional_email')">
                <el-form-item label="Additional Email" prop="additional_email" class="mb-2" :error="registerCustomErrors.additional_email">
                    <el-input v-model="registerForm.additional_email" size="small" style="width:100%"placeholder="Enter addittional email"></el-input>
                </el-form-item>
            </Col>

        </Row>

        <Row :gutter="20" class="mb-1">

            <!-- Website Link -->
            <Col :span="12" v-if="!hiddenFields.includes('website_link')">
                <el-form-item label="Website Link" prop="website_link" class="mb-2" :error="registerCustomErrors.website_link">
                    <el-input v-model="registerForm.website_link" size="small" style="width:100%" placeholder="Enter website link"></el-input>
                </el-form-item>
            </Col>

            <!-- Facebook Link -->
            <Col :span="12" v-if="!summaryMode && !hiddenFields.includes('facebook_link')">
                <el-form-item label="Facebook Link" prop="facebook_link" class="mb-2" :error="registerCustomErrors.facebook_link">
                    <el-input v-model="registerForm.facebook_link" size="small" style="width:100%"placeholder="Enter Facebook link"></el-input>
                </el-form-item>
            </Col>

            <!-- Twitter Link -->
            <Col :span="12" v-if="!summaryMode && !hiddenFields.includes('twitter_link')">
                <el-form-item label="Twitter Link" prop="twitter_link" class="mb-2" :error="registerCustomErrors.twitter_link">
                    <el-input v-model="registerForm.twitter_link" size="small" style="width:100%"placeholder="Enter Twitter link"></el-input>
                </el-form-item>
            </Col>

            <!-- linkedIn Link -->
            <Col :span="12" v-if="!summaryMode && !hiddenFields.includes('linkedin_link')">
                <el-form-item label="LinkedIn Link" prop="linkedin_link" class="mb-2" :error="registerCustomErrors.linkedin_link">
                    <el-input v-model="registerForm.linkedin_link" size="small" style="width:100%"placeholder="Enter LinkedIn link"></el-input>
                </el-form-item>
            </Col>

            <!-- Instagram Link -->
            <Col :span="12" v-if="!summaryMode && !hiddenFields.includes('instagram_link')">
                <el-form-item label="Instagram Link" prop="instagram_link" class="mb-2" :error="registerCustomErrors.instagram_link">
                    <el-input v-model="registerForm.instagram_link" size="small" style="width:100%"placeholder="Enter Instagram link"></el-input>
                </el-form-item>
            </Col>

        </Row>

        <Row :gutter="20" class="mb-1" v-if="!hiddenFields.includes('phones')">

            <!-- Phones -->
            <Col :span="24">
                <span class="form-label mb-1 d-block">Phone(s):</span>
                <phoneInput 
                    v-if="registerForm"
                    class="mb-2"  
                    :modelId="registerForm.id" 
                    :modelType="registerForm.model_type" 
                    :phones="registerForm.phones" 
                    :suggestedPhones="{}"
                    :minLimit="1"
                    :maxLimit="3"
                    selectedType="mobile"
                    :disabledTypes="[]"                                                        
                    :removable="false"
                    :deletable="true"
                    :hidedable="false"
                    :editable="true"
                    :showPhoneType="true"
                    :removeDuplicates="true"
                    :showIcon="false" 
                    onIcon="" offIcon="" 
                    title="" onText="" offText="" 
                    poptipMsg=""
                    @updated="registerForm.phones = $event">
                </phoneInput>

            </Col>

        </Row>
                
        <Row :gutter="20">
            
            <!-- Address --> 
            <Col :span="12" v-if="!summaryMode && !hiddenFields.includes('address_1')">
                <el-form-item label="Address" prop="address_1" class="mb-2" :error="registerCustomErrors.address_1">
                    <el-input v-model="registerForm.address_1" size="small" style="width:100%" placeholder="Enter address"></el-input>
                </el-form-item>
            </Col>

            <!-- Country Selector -->
            <Col :span="12" v-if="!summaryMode && !hiddenFields.includes('country')">
                <span class="form-label mb-1 d-block">Country</span>
                <el-form-item prop="country" class="mb-2" :error="registerCustomErrors.country">
                    <countrySelector
                        :selectedCountry="registerForm.country"
                        @updated="registerForm.country = $event">
                    </countrySelector>
                </el-form-item>
            </Col>

        </Row>

        <Row v-if="!summaryMode" :gutter="20" class="mb-1">
            
            <!-- Province Selector -->
            <Col :span="12" v-if="registerForm.country && !hiddenFields.includes('province')">
                <span class="form-label mb-1 d-block">State/Province/District</span>
                <el-form-item prop="province" class="mb-2" :error="registerCustomErrors.province">
                    <provinceSelector
                        :selectedCountry="registerForm.country"
                        :selectedProvince="registerForm.province"
                        @updated="registerForm.province = $event">
                    </provinceSelector>
                </el-form-item>
            </Col>

            <!-- Cities Selector -->
            <Col :span="12" v-if="registerForm.country && !hiddenFields.includes('city')">
                <span class="form-label mb-1 d-block">City/Town</span>
                <el-form-item prop="city" class="mb-2" :error="registerCustomErrors.city">
                    <citySelector
                        :selectedCountry="registerForm.country"
                        :selectedCity="registerForm.city"
                        @updated="registerForm.city = $event">
                    </citySelector>
                </el-form-item>
            </Col>

        </Row>
    
        <!-- Show More/Less Toggle -->
        <Row v-if="!hideSummaryToggle" :gutter="20">
            <Col :span="24">
                <span class="btn btn-link d-block font mt-0 pt-0 text-center" @click="summaryMode = !summaryMode">
                    <Icon :type="summaryMode ? 'ios-eye-outline' : 'ios-eye-off-outline'" :size="24" class="mr-1" />
                    <span>{{ summaryMode ? 'Show more' : 'Show less' }}</span>
                </span>
            </Col>
        </Row>

        <Row :gutter="20">
            <Col :span="24">
                <!-- Register Button -->
                <basicButton
                    v-if="!isRegistering"
                    class="float-right mt-2 mb-2 pl-2 pr-3" 
                    type="success" size="large" 
                    :ripple="true"
                    @click.native="handleRegistration()">
                    <span>{{ registerBtnText }}</span>
                </basicButton>
            </Col>
        </Row>

    </el-form>

</template>

<script>

    /*  Loaders   */
    import clockLoader from './../../loaders/clockLoader.vue'; 

    /*  Buttons  */
    import basicButton from './../../buttons/basicButton.vue'; 

    /*  Inputs   */
    import phoneInput from './../../inputs/phoneInput.vue'; 

    /*  Selectors   */
    import companyPrivateOrGovernmentSelector from './../../selectors/companyPrivateOrGovernmentSelector.vue'; 
    import provinceSelector from './../../selectors/provinceSelector.vue'; 
    import citySelector from './../../selectors/citySelector.vue'; 
    import countrySelector from './../../selectors/countrySelector.vue'; 
    import clientOrSupplierSelector from './../../selectors/clientOrSupplierSelector.vue';

    /*  Image Uploader  */
    import imageUploader from './../../uploaders/imageUploader.vue';

    const registerHandle = require('./main.js').default;

    export default {
        components: { 
            clockLoader, basicButton, phoneInput, companyPrivateOrGovernmentSelector, provinceSelector, 
            citySelector, countrySelector, clientOrSupplierSelector, imageUploader 
        },
        props: {
            existingCompany:{
                type: Object,
                default: null
            },
            route: {
                type: String,
                default: '/api/companies'
            },
            registerBtnText:{
                type:String,
                default: 'Register'
            },
            hiddenFields: {
                type: Array,
                default: function(){
                    return []
                }
            },
            additionalParams: {
                type: Array,
                default: function(){
                    return []
                }
            },
            showLoader: {
                type: Boolean,
                default: false
            },
            showCancelBtn: {
                type: Boolean,
                default: false
            },
            defaultRelationship:{
                type: String,
                default: ''
            },
            summaryMode:{
                type: Boolean,
                default: false
            },
            hideSummaryToggle:{
                type: Boolean,
                default: false
            }
        },
        data(){
            return {
                registerForm: null,
                registerFormRules: registerHandle.getRegisterFormRules(),
                registerCustomErrors: registerHandle.getRegisterCustomErrorFields(),
                isRegistering: false,

            }
        },
        watch: {
            showLoader: {
                handler: function (val, oldVal) {
                    this.isRegistering = val;
                },
                deep: true
            }
        },
        computed: {
            registrationProgress(){
                var progress = 0;
                var fields = ['first_name', 'last_name', 'email', 'phone', 
                              'address', 'country', 'province', 'city', 'password', 'confirm_password'];
                
                for(var x=0; x < fields.length; x++){
                    if(this.formData[fields[x]] != null && this.formData[fields[x]] != '' ){
                        progress = progress + ( 1/fields.length * 100 )
                    }
                }

                return progress;
            }
        },
        methods: {
            handleRegistration(){
                /*  Start the register process by calling the function initiateRegister() from
                    the registerHandle to handle the register api request. We must pass "this" 
                    current vue instance in order to access data proprties of this register
                    form. The initiateRegister function will handle all validation of the 
                    register form as well as return all the errors if any to the form fields.
                    If the register if is a success we will return the data of the company
                    We can use the then() hook to determine what to do next if the register 
                    is successful. In this case we only want to alert the parent on the 
                    success of the register.
                */
               
                var self = this;
                var registerResponse = registerHandle.initiateRegister(this);
                
                //  If we have a register response
                if(registerResponse){
                    
                    //  Hook into the response
                    registerResponse.then( data => {
                        //  If not false
                        if( data !== false ){
                            //  If we have the register data
                            //  Notify the parent and pass the register data
                            self.$emit('success', data);
                        }
                    });
                }

            }
        },
        created(){

            //  Get the form fields
            var registerFormFields = registerHandle.getRegisterFormFields();

            //  Foreach registration form field
            for(var x = 0; x < _.size(registerFormFields); x++){
                
                //  Get the current registration field key
                var key = Object.keys(registerFormFields)[x];

                /*
                *  Vue.set()
                *  We use Vue.set to set a new object property. This method ensures the  
                *  property is created as a reactive property and triggers view updates:
                */

                //  If we have an existing company, then overide the form field values
                if(this.existingCompany){
                    //  Set the key and value of the existing company to the allowed form fields
                    this.$set(registerFormFields, key, this.existingCompany[key]);
                }
            }

            //  Remove the hidden fields from the available register form fields
            this.hiddenFields.forEach(key => delete registerFormFields[key]);

            //  Update the register form fields
            this.registerForm = registerFormFields;

        }
    }

</script>