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

    <Card :bordered="false">
        <h3 slot="title">Profile Settings</h3>
        <Row>
            <Col :span="24" class="mt-3 mb-2">
                <Alert>Provide as much or as little information as you’d like. We will never share or sell individual personal information or personally identifiable details.</Alert>
            </Col>
            <Col :span="24">
            {{ formData }}
                <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading...</Loader>
                <el-form label-position="top" label-width="100px" :model="formData">
                    <Row :gutter="20" class="mb-1">

                        <Col :span="12">
                            <el-form-item label="First Name:" prop="first_name" class="mb-2">
                                <el-input v-model="formData.first_name" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <el-form-item label="Last Name:" prop="last_name" class="mb-2">
                                <el-input v-model="formData.last_name" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>
                    <Row :gutter="20" class="mb-1">

                        <Col :span="12">
                            <!-- Date Of Birth -->
                            <el-form-item label="Date Of Birth" prop="date_of_birth" class="mb-2">
                                <el-date-picker v-model="formData.date_of_birth" type="date" placeholder="Date Of Birth" style="width:100%" 
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
                        
                        <Col :span="12">
                            <el-form-item label="Email:" prop="email" class="mb-2">
                                <el-input v-model="formData.email" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <el-form-item label="Additional Email:" prop="additional_email" class="mb-2">
                                <el-input v-model="formData.additional_email" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20" class="mb-1">

                        <Col :span="24">
                            <!-- Calling Codes Selector -->
                            <span class="form-label mb-1 d-block">Phone</span>
                            
                            <phoneInput class="mb-2" 
                                :modelId="user.id"
                                modelType="user"
                                :phones="formData.phones"
                                @updated="updatePhoneChanges($event)">
                            </phoneInput>
                        </Col>

                        <Col :span="12">
                            <!-- Country Selector -->
                            <span class="form-label mb-1 d-block">Country</span>
                            <countrySelector
                                :selectedCountry="formData.country"
                                @updated="updateCountryChanges($event)">
                            </countrySelector>
                        </Col>

                    </Row>
                    
                    <Row :gutter="20" class="mb-1">
                        
                        <Col :span="12">
                            <el-form-item label="Address:" prop="address" class="mb-2">
                                <el-input v-model="formData.address" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row :gutter="20" class="mb-1">

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

                    <Row :gutter="20" class="mt-1 mb-1">
                        
                        <Col :span="24">
                            <el-form-item label="Say Something About yourself:" prop="bio" class="mb-2">
                                <el-input type="textarea" v-model="formData.bio" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>

                    <Row>
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
    </Card>

</template>

<script>
    import Loader from './../../components/_common/loader/Loader.vue'; 
    import genderSelector from './genderSelector.vue'; 
    import phoneInput from './phoneInput.vue'; 
    import provinceSelector from './provinceSelector.vue'; 
    import citySelector from './citySelector.vue'; 
    import countrySelector from './countrySelector.vue'; 

    export default {
        components: { 
            Loader, genderSelector, phoneInput, countrySelector, provinceSelector, citySelector
        },
        data(){
            return {
                user: auth.user,
                isLoading: false,
                formData: {
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
        methods: {
            fetch(){
                 
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

                //  Login data to send
                let profileData = {
                    profile: this.formData
                };

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/user/'+this.user.id, profileData)
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
        },
        created(){
            this.fetch();
        }
    };
  
</script>