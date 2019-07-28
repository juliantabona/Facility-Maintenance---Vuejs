<style scoped>

    .el-form-item >>> .el-form-item__label{
        margin:0 !important;
        padding:0 !important;
        line-height: 24px !important;
    }

    .form-label{
        font-size:14px;
    }

</style>

<template>

        <Row>

            <Col v-if="isLoading" :span="8" :offset="8">
                <Loader :loading="isLoading" type="text" class="text-left mt-4 mb-4">Loading...</Loader>
            </Col>

            <Col v-else :span="24">
                <el-form label-position="top" label-width="100px" :model="formData">
                    <Row :gutter="20" class="mb-1">

                        <!-- Profile Image -->
                        <Col v-if="!isLoading" :span="8" :offset="8">
                            <imageUploader
                                uploadMsg="Upload or change profile image"
                                :allowUpload="localEditMode"
                                :multiple="false"
                                :docUrl=" localUser ? '/api/users/'+(localUser || {}).id+'/image' : null"
                                :postData="{ 
                                        modelId: localUser ? (localUser || {}).id : null,
                                        modelType: 'user',
                                        location:  'profile_images', 
                                        type: 'profile_image',
                                        replaceable: true
                                    }"
                                :thumbnailStyle="{ width:'200px', height:'auto' }"
                                @fileBeforeUpload="handleFileAdded('profile_image', $event)"
                            ></imageUploader>
                        </Col>

                        <!-- Client/Supplier Selector -->
                        <Col :span="24" v-if="showClientOrSupplierSelector">
                            <el-form-item label="Relationship:" prop="relationship" class="mb-2">
                                <clientOrSupplierSelector class="mb-2" 
                                    :selectedClientType="defaultRelationship"
                                    @on-change="formData.relationship = $event">
                                </clientOrSupplierSelector>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- First Name -->
                            <el-form-item label="First Name:" prop="first_name" class="mb-2">
                                <el-input v-model="formData.first_name" size="small" style="width:100%" placeholder="Enter first name"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- Last Name -->
                            <el-form-item label="Last Name:" prop="last_name" class="mb-2">
                                <el-input v-model="formData.last_name" size="small" style="width:100%" placeholder="Enter last name"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>
                    <Row v-if="summaryMode" :gutter="20" class="mb-1">

                        <Col :span="12">
                            <!-- Date Of Birth -->
                            <el-form-item label="Date Of Birth" prop="date_of_birth" class="mb-2">
                                <el-date-picker v-model="formData.date_of_birth" type="date" placeholder="Date of birth" style="width:100%" 
                                    format="yyyy-MM-dd" value-format="yyyy-MM-dd">
                                </el-date-picker>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- Gender Selector -->
                            <span class="form-label mb-1 d-block">Gender</span>
                            <genderSelector
                                :selectedGender="formData.gender"
                                @updated="updateGenderChanges($event)">
                            </genderSelector>
                        </Col>

                    </Row>
                    <Row :gutter="20" class="mb-1">
                        
                        <Col :span="summaryMode ? '12' : '24'">
                            <!-- Email -->
                            <el-form-item label="Email:" prop="email" class="mb-2">
                                <el-input v-model="formData.email" size="small" style="width:100%" placeholder="Enter email address"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="summaryMode" :span="12">
                            <!-- Additional Email -->
                            <el-form-item label="Additional Email:" prop="additional_email" class="mb-2">
                                <el-input v-model="formData.additional_email" size="small" style="width:100%"placeholder="Enter addittional email"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20" class="mb-1">

                        <Col v-if="summaryMode" :span="12">
                            <!-- Facebook Link -->
                            <el-form-item label="Facebook Link:" prop="facebook_link" class="mb-2">
                                <el-input v-model="formData.facebook_link" size="small" style="width:100%"placeholder="Enter Facebook link"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="summaryMode" :span="12">
                            <!-- Twitter Link -->
                            <el-form-item label="Twitter Link:" prop="twitter_link" class="mb-2">
                                <el-input v-model="formData.twitter_link" size="small" style="width:100%"placeholder="Enter Twitter link"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="summaryMode" :span="12">
                            <!-- linkedIn Link -->
                            <el-form-item label="linkedIn Link:" prop="linkedin_link" class="mb-2">
                                <el-input v-model="formData.linkedin_link" size="small" style="width:100%"placeholder="Enter LinkedIn link"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="summaryMode" :span="12">
                            <!-- Instagram Link -->
                            <el-form-item label="Instagram Link:" prop="instagram_link" class="mb-2">
                                <el-input v-model="formData.instagram_link" size="small" style="width:100%"placeholder="Enter Instagram link"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20" class="mb-1">

                        <Col :span="24">
                            <!-- Calling Codes Selector -->
                            <span class="form-label mb-1 d-block">Phone(s):</span>
                            <phoneInput class="mb-2"  
                                        :modelId="localUser ? (localUser || {}).id : null" 
                                        :modelType="localUser ? (localUser || {}).model_type : null" 
                                        :phones="formData.phones" 
                                        :minLimit="1"
                                        :maxLimit="3"
                                        :deletable="true"
                                        :hidedable="false"
                                        :editable="true"
                                        @updated="updatePhoneChanges($event)">
                            </phoneInput>

                        </Col>

                    </Row>
                    
                    <Row :gutter="20" class="mb-1">
                        
                        <Col :span="summaryMode ? '12' : '24'">
                            <!-- Address -->
                            <el-form-item label="Address:" prop="address" class="mb-2">
                                <el-input v-model="formData.address" size="small" style="width:100%" placeholder="Enter address"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="summaryMode" :span="12">
                            <!-- Country Selector -->
                            <span class="form-label mb-1 d-block">Country</span>
                            <countrySelector
                                :selectedCountry="formData.country"
                                @updated="updateCountryChanges($event)">
                            </countrySelector>
                        </Col>

                    </Row>

                    <Row v-if="summaryMode" :gutter="20" class="mb-1">

                        <Col :span="12">
                            <!-- Province Selector -->
                            <span class="form-label mb-1 d-block">State/Province/District</span>
                            <provinceSelector
                                :selectedCountry="formData.country"
                                :selectedProvince="formData.province"
                                @updated="updateProvinceChanges($event)">
                            </provinceSelector>
                        </Col>

                        <Col :span="12">
                            <!-- Cities Selector -->
                            <span class="form-label mb-1 d-block">City/Town</span>
                            <citySelector
                                :selectedCountry="formData.country"
                                :selectedCity="formData.city"
                                @updated="updateCityChanges($event)">
                            </citySelector>
                        </Col>

                    </Row>

                    <Row v-if="!hideBio" :gutter="20" class="mt-1 mb-1">
                        
                        <Col :span="24">
                            <el-form-item label="Say somthing about this company/organisation:" prop="bio" class="mb-2">
                                <el-input type="textarea" v-model="formData.bio" size="small" style="width:100%" placeholder="Write something..."></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20">
                        <!-- Show/Hide More -->
                        <Col v-if="!hideSummaryToggle" :span="24">
                            <span  class="btn btn-link d-block font mt-0 pt-0 text-center"
                            @click="summaryMode = !summaryMode">
                            <Icon
                                :type="summaryMode ? 'ios-eye-outline' : 'ios-eye-off-outline'"
                                :size="24"
                                class="mr-1"/>
                            <span>{{ summaryMode ? 'Show more' : 'Show less' }}</span>
                            </span>
                        </Col>
                    </Row>

                    <Row v-if="!hideSaveBtn">
                        <Col :span="24">
                            <hr class="mt-2" />
                            <!-- Save Changes Button -->
                            <Button class="float-right mt-2" type="success" size="large" @click="saveUser()">
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
    import genderSelector from './../../../components/_common/selectors/genderSelector.vue'; 
    import provinceSelector from './../../../components/_common/selectors/provinceSelector.vue'; 
    import citySelector from './../../../components/_common/selectors/citySelector.vue'; 
    import countrySelector from './../../../components/_common/selectors/countrySelector.vue'; 
    import clientOrSupplierSelector from './../../../components/_common/selectors/clientOrSupplierSelector.vue';    

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
            userId: { 
                type: Number,
                default: null
            },
            /*
             *  canSaveOrCreate checks if the parent has permitted for the company
             *  to be saved to the databse. If canSaveOrCreate is set to true
             *  we will perform an ajax request to create the new company
             *  using our formData information.
             */
            canSaveOrCreate:{
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
            },
            defaultRelationship:{
                type: String,
                default: ''
            }
        },
        components: { 
            Loader, genderSelector, phoneInput, countrySelector, provinceSelector, citySelector, clientOrSupplierSelector, imageUploader
        },
        data(){
            return {
                localUser: null,
                summaryMode: this.activateSummaryMode,
                isLoading: false,
                formData: {
                    relationship: '',       //  e.g) client, supplier
                    first_name: '',
                    last_name: '',
                    date_of_birth: '',
                    gender: '',

                    address: '',
                    country: '',
                    province: '',
                    city: '',
                    postal_or_zipcode: '',

                    email: '',
                    additional_email: '',

                    facebook_link: '',
                    twitter_link: '',
                    linkedin_link: '',
                    instagram_link: '',

                    accessibility: '',
                    position: '',
                    bio: '',

                    profile_image: null,
                    phones: [],

                    

                },
                ruleForm: {

                },
                fetchedCountries: [],
                fetchedStates: [],
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the canSaveOrCreate value
            canSaveOrCreate: {
                handler: function (val, oldVal) {
                    
                    if(this.userId){
                        
                        this.saveUser();
                    }else{
                        
                        this.createNewUser();
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
        methods: {
            handleFileAdded(key, fileData){
                
                this.$set(this.formData, key, fileData);

            },
            fetch(){
                 
                if( this.userId ){
                    
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/users/'+this.userId)
                        .then(({data}) => {

                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            /*
                            *  Vue.set()
                            *  We use Vue.set to set a new object property. This method ensures the  
                            *  property is created as a reactive property and triggers view updates:
                            */
                            
                            for(var x = 0; x <  _.size(self.formData); x++){
                                var key = Object.keys(self.formData)[x];

                                //  data[key] != undefined if the key does not exist e.g) first_name, last_name, e.t.c
                                if(data[key] != undefined){
                                    self.$set(self.formData, key, data[key]);
                                }
                                
                            }
                            
                            //  Store the data as the localUser
                            self.localUser = data;
                
                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                    }
            },
            updateGenderChanges(newVal){
                this.formData.gender = newVal;
            },
            updatePhoneChanges(newVal){
                this.formData.phones = newVal;
            },
            updateCountryChanges(newVal){
                this.formData.country = newVal;
            },
            updateProvinceChanges(newVal){
                this.formData.province = newVal;
            },
            updateCityChanges(newVal){
                this.formData.city = newVal;
            },
            saveUser() {
                const self = this;

                //  Start loader
                self.isSaving = true;
                    
                var profileData = new FormData();

                Object.keys(this.formData).map(key => {
                    //  If its an object and also not a file or blob. Then we need to stringify it
                    if(typeof self.formData[key] === "object" && !(typeof (self.formData[key] || {}).name == 'string')){
                        profileData.append(key, JSON.stringify(self.formData[key]) );
                    }else{
                        profileData.append(key, self.formData[key] );
                    }
                });

                console.log('Attempt to save profile details...');
                console.log(profileData);

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/users/'+this.localUser.id, profileData)
                    .then(({data}) => {

                        console.log(data);

                        //  Stop loader
                        self.isSaving = false;
                        
                        //  Alert creation success
                        self.$Message.success('Profile saved sucessfully!');

                        self.$emit('updated:user', data);

                    })         
                    .catch(response => { 
                        console.log('widgets/user/show/main.vue - Error saving profile...');
                        console.log(response);

                        //  Stop loader
                        self.isSaving = false;

                    });

            },
            createNewUser() {
                const self = this;

                //  Start loader
                self.isCreating = true;
                    
                var profileData = new FormData();

                Object.keys(this.formData).map(key => {
                    //  If its an object and also not a file or blob. Then we need to stringify it
                    if(typeof self.formData[key] === "object" && !(typeof (self.formData[key] || {}).name == 'string')){
                        profileData.append(key, JSON.stringify(self.formData[key]) );
                    }else{
                        profileData.append(key, self.formData[key] );
                    }
                });

                console.log('Attempt to create new user...');
                console.log(profileData);

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/users', profileData)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isCreating = false;
                        
                        //  Alert creation success
                        self.$Message.success('Created sucessfully!');

                        self.$emit('created:user', data);

                    })         
                    .catch(response => { 
                        console.log('widgets/user/show/main.vue - Error creating user...');
                        console.log(response);

                        //  Stop loader
                        self.isCreating = false;

                    });
            },
        },
        created(){
            this.fetch();
        }
    };
  
</script>