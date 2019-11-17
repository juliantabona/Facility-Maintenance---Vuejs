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
        
        <Row :gutter="12">

            <!-- White overlay when creating/saving invoice -->
            <Spin size="large" fix v-if="isRegistering" style="border-radius: 10px;">
                <!-- Icon to show as loader  -->
                <clockLoader></clockLoader>
            </Spin>

            <Col :span="12" v-if="!hiddenFields.includes('first_name')">
                <!-- First Name -->
                <el-form-item label="First Name" prop="first_name" class="mb-2" :error="registerCustomErrors.first_name">
                    <el-input v-model="registerForm.first_name" size="small" style="width:100%" placeholder="Enter first name"></el-input>
                </el-form-item>
            </Col>

            <Col :span="12" v-if="!hiddenFields.includes('last_name')">
                <!-- Last Name -->
                <el-form-item label="Last Name" prop="last_name" class="mb-2" :error="registerCustomErrors.last_name">
                    <el-input v-model="registerForm.last_name" size="small" style="width:100%" placeholder="Enter last name"></el-input>
                </el-form-item>
            </Col>
        
            <Col :span="12" v-if="!hiddenFields.includes('username')">
                <!-- Last Name -->
                <el-form-item label="Username" prop="username" class="mb-2" :error="registerCustomErrors.username">
                    <el-input v-model="registerForm.username" size="small" style="width:100%" placeholder="Enter username"></el-input>
                </el-form-item>
            </Col>

            <Col :span="12" v-if="!hiddenFields.includes('email')">
                <!-- Email -->
                <el-form-item label="Email" prop="email" :error="registerCustomErrors.email">
                    <el-input type="email" v-model="registerForm.email" size="small" style="width:100%" placeholder="Enter email"></el-input>
                </el-form-item>
            </Col>

            <Col :span="12" v-if="!hiddenFields.includes('phone')" :error="registerCustomErrors.phone">
                <!-- Mobile -->
                <el-form-item label="Mobile" prop="phones">
                    <el-input v-model="registerForm.phone" size="small" style="width:100%" placeholder="Enter mobile number"></el-input>
                </el-form-item>
            </Col>
            
            <Col :span="12" v-if="!hiddenFields.includes('address_1')" :error="registerCustomErrors.address_1">
                <!-- Address 1 -->
                <el-form-item label="Physical Address" prop="address_1" class="mb-2">
                    <el-input v-model="registerForm.address_1" size="small" style="width:100%" placeholder="Enter primary address" autocomplete="off"></el-input>
                </el-form-item>
            </Col>

            <Col :span="12" v-if="!hiddenFields.includes('address_2')" :error="registerCustomErrors.address_2">
                <!-- Address 2 -->
                <el-form-item label="Physical Address 2 (Optional)" prop="address_2" class="mb-2">
                    <el-input v-model="registerForm.address_2" size="small" style="width:100%" placeholder="Enter primary address" autocomplete="off"></el-input>
                </el-form-item>
            </Col>

            <Col :span="12" v-if="!hiddenFields.includes('country')" :error="registerCustomErrors.country">
                <!-- Country Selector -->
                <el-form-item label="Country" prop="country" class="mb-2">
                    <countrySelector
                        :selectedCountry="registerForm.country"
                        @updated="registerForm.country = $event">
                    </countrySelector>
                </el-form-item>
            </Col>
            
            <Col :span="12" v-if="!hiddenFields.includes('province')" :error="registerCustomErrors.province">
                <!-- Province Selector -->
                <el-form-item label="Province" prop="province" class="mb-2">
                    <Alert v-if="!registerForm.country" type="warning">Select country first</Alert>
                    <provinceSelector
                        v-else
                        :selectedCountry="registerForm.country"
                        :selectedProvince="registerForm.province"
                        @updated="registerForm.province = $event">
                    </provinceSelector>
                </el-form-item>
            </Col>
            <Col :span="12" v-if="!hiddenFields.includes('city')" :error="registerCustomErrors.city">
                <!-- Cities Selector -->
                <el-form-item label="City/Town" prop="city" class="mb-2">
                    <Alert v-if="!registerForm.country" type="warning">Select country first</Alert>
                    <citySelector
                        v-else
                        :selectedCountry="registerForm.country"
                        :selectedCity="registerForm.city"
                        @updated="registerForm.city = $event">
                    </citySelector>
                </el-form-item>
            </Col>
            
            <Col :span="12" v-if="!hiddenFields.includes('postal_or_zipcode')" :error="registerCustomErrors.postal_or_zipcode">
                <!-- Postal Code / Zip Code -->
                <el-form-item label="Postal/ZipCode" prop="postal_or_zipcode" class="mb-2">
                    <el-input v-model="registerForm.postal_or_zipcode" size="small" style="width:100%" placeholder="Enter postal/zipcode" autocomplete="off"></el-input>
                </el-form-item>
            </Col>

            <Col :span="12" v-if="!hiddenFields.includes('password')" :error="registerCustomErrors.password">
                <!-- Password -->
                <el-form-item label="Password" prop="password" class="mb-2">
                    <el-input type="password" v-model="registerForm.password" size="small" style="width:100%" placeholder="Enter password"></el-input>
                </el-form-item>
            </Col>

            <Col :span="12" v-if="!hiddenFields.includes('confirm_password')" :error="registerCustomErrors.confirm_password">
                <!-- Confirm Password -->
                <el-form-item label="Confirm Password" prop="confirm_password" class="mb-2">
                    <el-input type="password" v-model="registerForm.confirm_password" size="small" style="width:100%" placeholder="Confirm password"></el-input>
                </el-form-item>
            </Col>

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

                <!-- Cancel Button -->
                <basicButton
                    v-if="showCancelBtn"
                    class="float-right mt-2 mb-2" 
                    type="default" size="large" 
                    :ripple="false"
                    @click.native="$emit('cancel')">
                    <span>Cancel</span>
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

    /*  Selectors   */
    import citySelector from './../../selectors/citySelector.vue'; 
    import provinceSelector from './../../selectors/provinceSelector.vue'; 
    import countrySelector from './../../selectors/countrySelector.vue'; 

    const registerHandle = require('./main.js').default;

    export default {
        components: { clockLoader, basicButton, citySelector, provinceSelector, countrySelector },
        props: {
            existingUser:{
                type: Object,
                default: null
            },
            route: {
                type: String,
                default: '/api/register'
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
            }
        },
        data(){
            return {
                registerForm: null,
                registerFormRules: registerHandle.getRegisterFormRules(),
                registerCustomErrors: registerHandle.getRegisterCustomErrorFields(),
                isRegistering: false
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
        methods: {
            handleRegistration(){
                /*  Start the register process by calling the function initiateRegister() from
                    the registerHandle to handle the register api request. We must pass "this" 
                    current vue instance in order to access data proprties of this register
                    form. The initiateRegister function will handle all validation of the 
                    register form as well as return all the errors if any to the form fields.
                    If the register if is a success we will return the data of the auth user
                    with token and user details. The token will already be set for 
                    future requests that require the auth token. We can use the then()
                    hook to determine what to do next if the register is successful. In this
                    case we only want to alert the parent on the success of the register.
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

                //  If we have an existing user, then overide the form field values
                if(this.existingUser){
                    //  Set the key and value of the existing user to the allowed form fields
                    this.$set(registerFormFields, key, this.existingUser[key]);
                }
            }

            //  Remove the hidden fields from the available register form fields
            this.hiddenFields.forEach(key => delete registerFormFields[key]);

            //  Update the register form fields
            this.registerForm = registerFormFields;

        }
    }

</script>