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

            <Col :span="24">
                <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading...</Loader>
                <el-form label-position="top" label-width="100px" :model="formData">
                    <Row :gutter="20" class="mb-1">

                        <!-- Edit mode switch -->
                        <Col :span="24">
                            <detailModeSwitch v-bind:detailMode.sync="detailMode" :ripple="false" class="float-right mb-2 mt-3"></detailModeSwitch>
                        </Col>
                            
                        <Col v-if="detailMode" :span="24" class="mb-2">
                            <Alert>Provide as much or as little information as you’d like. We will never share or sell individual personal information or personally identifiable details.</Alert>
                        </Col>

                        <!-- Client/Supplier Selector -->
                        <Col :span="24" v-if="showDirectoryAllocation">
                            <el-form-item label="Relationship:" prop="relationship" class="mb-2">
                                <clientOrSupplierSelector class="mb-2" 
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
                    <Row v-if="detailMode" :gutter="20" class="mb-1">

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
                        
                        <Col :span="detailMode ? '12' : '24'">
                            <!-- Email -->
                            <el-form-item label="Email:" prop="email" class="mb-2">
                                <el-input v-model="formData.email" size="small" style="width:100%" placeholder="Enter email address"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Additional Email -->
                            <el-form-item label="Additional Email:" prop="additional_email" class="mb-2">
                                <el-input v-model="formData.additional_email" size="small" style="width:100%"placeholder="Enter addittional email"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20" class="mb-1">

                        <Col :span="24">
                            <!-- Calling Codes Selector -->
                            <span class="form-label mb-1 d-block">Phone</span>
                            
                            <phoneInput class="mb-2" 
                                :modelId="localUser.id"
                                modelType="user"
                                :phones="formData.phones"
                                @updated="updatePhoneChanges($event)">
                            </phoneInput>
                        </Col>

                    </Row>
                    
                    <Row :gutter="20" class="mb-1">
                        
                        <Col :span="detailMode ? '12' : '24'">
                            <!-- Address -->
                            <el-form-item label="Address:" prop="address" class="mb-2">
                                <el-input v-model="formData.address" size="small" style="width:100%" placeholder="Enter address"></el-input>
                            </el-form-item>
                        </Col>

                        <Col v-if="detailMode" :span="12">
                            <!-- Country Selector -->
                            <span class="form-label mb-1 d-block">Country</span>
                            <countrySelector
                                :selectedCountry="formData.country"
                                @updated="updateCountryChanges($event)">
                            </countrySelector>
                        </Col>

                    </Row>

                    <Row v-if="detailMode" :gutter="20" class="mb-1">

                        <Col :span="12">
                            <!-- Provience Selector -->
                            <span class="form-label mb-1 d-block">State/Provience/District</span>
                            <provinceSelector
                                :selectedCountry="formData.country"
                                :selectedProvience="formData.provience"
                                @updated="updateProvienceChanges($event)">
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
                            <el-form-item label="Say Something About yourself:" prop="bio" class="mb-2">
                                <el-input type="textarea" v-model="formData.bio" size="small" style="width:100%" placeholder="Write something..."></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row v-if="!hideSaveBtn">
                        <Col :span="24">
                            <hr class="mt-2" />
                            <!-- Save Changes Button -->
                            <Button class="float-right mt-2" type="success" size="large" @click="saveProfile()">
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
    
    /*  Switches   */
    import detailModeSwitch from './../../../components/_common/switches/detailModeSwitch.vue'; 
    

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        props: {
            user: { 
                type: Object,
                default: () => { return {} }
            },
            /*
             *  createUser checks if the parent has permitted for the user
             *  to be saved to the databse. If createUser is set to true
             *  we will perform an ajax request to create the new user
             *  using our formData information.
             */
            createUser:{
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
            showDirectoryAllocation: { 
                type: Boolean,
                default: false
            },
            activateSummaryMode:{
                type: Boolean,
                default: false
            }
        },
        components: { 
            Loader, genderSelector, phoneInput, countrySelector, provinceSelector, citySelector, clientOrSupplierSelector,
            detailModeSwitch
        },
        data(){
            return {
                localUser: this.user,
                detailMode: this.activateSummaryMode,
                isLoading: false,
                formData: {
                    relationship: '',       //  e.g) client, supplier
                    first_name: '',
                    last_name: '',
                    date_of_birth: '',
                    gender: '',

                    address: '',
                    country: '',
                    provience: '',
                    city: '',
                    postal_or_zipcode: '',

                    email: '',
                    additional_email: '',
                    phones: [],

                    bio: '',

                },
                ruleForm: {

                },
                fetchedCountries: [],
                fetchedStates: []
            }
        },
        watch: {

            //  Watch for changes on the createUser value
            createUser: {
                handler: function (val, oldVal) {

                    //  Create a new user if createUser is set to true
                    if(val){
                        this.createNewUser();
                    }

                }
            }
        },
        methods: {
            fetch(){
                 
                if( !_.isEmpty( this.localUser , true) ){
                    alert('fetched user!');
                    const self = this;
                    

                    //  Additional data to eager load along with the jobcard found
                    var connections = '?connections=phones';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/user'+connections)
                        .then(({data}) => {

                            //  Stop loader
                            self.isLoading = false;
                            console.log('Users profile');
                            console.log(data);

                            self.formData.first_name = data.first_name;
                            self.formData.last_name = data.last_name;
                            self.formData.date_of_birth = data.date_of_birth;
                            self.formData.gender = data.gender;

                            self.formData.address = data.address;
                            self.formData.country = data.country;
                            //self.formData.provience = data.provience;
                            self.formData.city = data.city;
                            //self.formData.postal_or_zipcode = data.postal_or_zipcode;

                            self.formData.email = data.email;
                            self.formData.additional_email = data.additional_email;
                            self.formData.phones = data.phones;

                            self.formData.bio = data.bio;
                
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
            saveProfile() {
                const self = this;

                //  Start loader
                self.isSaving = true;

                console.log('Attempt to save profile details...');

                //  Profile data to send
                let profileData = {
                    profile: this.formData
                };

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/users/'+this.localUser.id, profileData)
                    .then(({data}) => {

                        //  Stop loader
                        self.isSaving = false;
                        console.log(data);
                        //  Alert creation success
                        self.$Message.success('Profile saved sucessfully!');

                    })         
                    .catch(response => { 
                        console.log('profileWidget.vue - Error saving profile...');
                        console.log(response);

                        //  Stop loader
                        self.isLoggingIn = false;     
    
                    });

            },
            createNewUser() {
                const self = this;

                //  Start loader
                self.isCreating = true;

                console.log('Attempt to create new user...');

                //  Profile data to send
                let profileData = {
                    profile: this.formData
                };

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/users', profileData)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSaving = false;
                        
                        //  Alert creation success
                        self.$Message.success('Created sucessfully!');

                        self.$emit('created:user', data);

                    })         
                    .catch(response => { 
                        console.log('profileWidget.vue - Error creating user...');
                        console.log(response);

                        //  Stop loader
                        self.isLoggingIn = false;     
    
                    });

            },
        },
        created(){
            this.fetch();
        }
    };
  
</script>