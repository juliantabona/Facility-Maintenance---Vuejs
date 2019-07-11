<style scoped>
    
    .el-form-item{
        margin-bottom: 0px;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }
</style>
<template>

    <!-- Register Form-->
    <el-form :model="registerForm" :rules="registerFormRules" ref="registerForm" autocomplete="off">

        <Row :gutter="12">
           <Col :span="24">
                <Row :gutter="24">
                    <Col :span="12">
                        <!-- First Name -->
                        <el-form-item label="First Name" prop="first_name" class="mb-2" :error="registerCustomErrors.first_name">
                            <el-input v-model="registerForm.first_name" size="small" style="width:100%" placeholder="Enter first name"></el-input>
                        </el-form-item>
                    </Col>

                    <Col :span="12">
                        <!-- Last Name -->
                        <el-form-item label="Last Name" prop="last_name" class="mb-2">
                            <el-input v-model="registerForm.last_name" size="small" style="width:100%" placeholder="Enter last name"></el-input>
                        </el-form-item>
                    </Col>
                </Row>
            </Col>

           <Col :span="24">
                <Row :gutter="24">

                    <Col :span="12">
                        <!-- Email -->
                        <el-form-item label="Email" prop="email">
                            <el-input type="email" v-model="registerForm.email" size="small" style="width:100%" placeholder="Enter email"></el-input>
                        </el-form-item>
                    </Col>

                    <Col :span="12">
                        <!-- Mobile -->
                        <el-form-item label="Mobile" prop="phones">
                            <el-input v-model="registerForm.phone" size="small" style="width:100%" placeholder="Enter mobile number"></el-input>
                        </el-form-item>
                    </Col>

                </Row>
            </Col>

           <Col :span="24">
                <Row :gutter="24">
                    <Col :span="12">
                        <!-- Address 1 -->
                        <el-form-item label="Physical Address" prop="address_1" class="mb-2">
                            <el-input v-model="registerForm.address_1" size="small" style="width:100%" placeholder="Enter primary address" autocomplete="off"></el-input>
                        </el-form-item>
                    </Col>

                    <Col :span="12">
                        <!-- Country Selector -->
                        <el-form-item label="Country" prop="country" class="mb-2">
                            <countrySelector
                                :selectedCountry="registerForm.country"
                                @updated="registerForm.country = $event">
                            </countrySelector>
                        </el-form-item>
                    </Col>
                </Row>
            </Col>

            <Col :span="24">
                <Row :gutter="24">
                    <Col :span="12">
                        <!-- Provience Selector -->
                        <el-form-item label="Provience" prop="provience" class="mb-2">
                            <Alert v-if="!registerForm.country" type="warning">Select country first</Alert>
                            <provinceSelector
                                v-else
                                :selectedCountry="registerForm.country"
                                :selectedProvience="registerForm.provience"
                                @updated="registerForm.provience = $event">
                            </provinceSelector>
                        </el-form-item>
                    </Col>
                    <Col :span="12">
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
                </Row>
            </Col>

           <Col :span="24">
                <Row :gutter="24">
                    <Col :span="12">
                        <!-- Password -->
                        <el-form-item label="Password" prop="password" class="mb-2">
                            <el-input type="password" v-model="registerForm.password" size="small" style="width:100%" placeholder="Enter password"></el-input>
                        </el-form-item>
                    </Col>

                    <Col :span="12">
                        <!-- Confirm Password -->
                        <el-form-item label="Confirm Password" prop="confirm_password" class="mb-2">
                            <el-input type="password" v-model="registerForm.confirm_password" size="small" style="width:100%" placeholder="Confirm password"></el-input>
                        </el-form-item>
                    </Col>
                </Row>
            </Col>

            <Col :span="24">
                <!-- Loader -->
                <Loader v-if="isRegistering" :loading="true" type="text" class="text-left mt-2">Creating account...</Loader>

                <!-- Register Button -->
                <basicButton
                    v-else
                    class="float-right mt-2 mb-2 ml-3 pl-3 pr-3" 
                    type="success" size="large" 
                    :ripple="true"
                    @click.native="handleRegistration()">
                    <span>Register And Pay</span>
                </basicButton>
            </Col>

        </Row>

    </el-form>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../loaders/Loader.vue'; 

    /*  Buttons  */
    import basicButton from './../../buttons/basicButton.vue';

    /*  Selectors   */
    import citySelector from './../../selectors/citySelector.vue'; 
    import provinceSelector from './../../selectors/provinceSelector.vue'; 
    import countrySelector from './../../selectors/countrySelector.vue'; 

    const registerHandle = require('./main.js').default;

    export default {
        components: { Loader, basicButton, citySelector, provinceSelector, countrySelector },
        props: {
            identity: {
                type: String,
                default: null
            },
            password: {
                type: String,
                default: null
            }
        },
        data(){
            return {
                registerForm: registerHandle.getRegisterFormFields(),
                registerFormRules: registerHandle.getRegisterFormRules(),
                registerCustomErrors: registerHandle.getRegisterCustomErrorFields(),
                isRegistering: false
            }
        },
        watch: {
            identity: {
                handler: function (val, oldVal) {
                    this.registerForm.identity = val;
                },
                deep: true
            },
            password: {
                handler: function (val, oldVal) {
                    this.registerForm.password = val;
                },
                deep: true
            }
        },
        computed: {
            registrationProgress(){
                var progress = 0;
                var fields = ['first_name', 'last_name', 'email', 'phone', 
                              'address', 'country', 'provience', 'city', 'password', 'confirm_password'];
                
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
                    If the register if is a success we will return the data of the auth user
                    with token and user details. The token will already be set for 
                    future requests that require the auth token. We can use the then()
                    hook to determine what to do next if the register is successful. In this
                    case we only want to alert the parent on the success of the register.
                */
                var self = this;
                var registerResponse = registerHandle.initiateRegister(this);

                console.log('registerResponse');
                console.log(registerResponse);
                
                //  If we have a register response
                if(registerResponse){
                    console.log('Hook In 1');
                    //  Hook into the response
                    registerResponse.then( data => {
                        //  If we have the register data
                        console.log('Notify The Parent');
                        console.log(registerResponse);
                        //  Notify the parent and pass the register data
                        self.$emit('registerSuccess', data);
                    });
                    
                    console.log('Hook Out 2');
                }

            }
        },
        created(){

        }
    }

</script>