<style scoped>
    
    .el-form-item{
        margin-bottom: 0px;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }
</style>
<template>

    <div>
        <!-- Login Form-->
        <el-form v-if="!showForgotPasswordForm && !resetToken" :model="loginForm" :rules="loginFormRules" ref="loginForm">

            <!-- identity -->
            <el-form-item prop="identity" :error="loginCustomErrors.identity">
                <el-input type="identity" v-model="loginForm.identity" size="small" style="width:100%" placeholder="Email/Username"></el-input>
            </el-form-item>

            <!-- Password -->
            <el-form-item prop="password" :error="loginCustomErrors.password">
                <el-input type="password" v-model="loginForm.password" size="small" style="width:100%" placeholder="Password"></el-input>
            </el-form-item>

            <!-- Loader -->
            <Loader v-if="isLoggingIn" :loading="true" type="text" class="text-left mt-2">Logging in...</Loader>
            
            <span class="float-left btn btn-link d-block mt-2" @click="showForgotPasswordForm = true">Forgot Password?</span>

            <!-- Login Button -->
            <basicButton
                class="float-right mt-2 mb-2 ml-3 pl-3 pr-3" 
                type="success" size="large" 
                :ripple="true"
                @click.native="handleLogin()">
                <span>Login</span>
            </basicButton>

        </el-form>

        <!-- Forgot Password Form-->
        <el-form v-else :model="forgotPasswordForm" :rules="forgotPasswordFormRules" ref="forgotPasswordForm">
            
            <Alert show-icon>{{ resetToken ? 'Provide your new password and save' : 'Provide your account email to receive your password reset link.' }}</Alert>
            
            <!-- Email -->
            <el-form-item v-if="!resetToken" label="Email" prop="email" :error="forgotPasswordCustomErrors.email">
                <el-input type="email" v-model="forgotPasswordForm.email" size="small" style="width:100%" placeholder="Enter email"></el-input>
            </el-form-item>

            <!-- Password -->
            <el-form-item v-if="resetToken" label="Password" prop="password" class="mb-2">
                <el-input type="password" v-model="forgotPasswordForm.password" size="small" style="width:100%" placeholder="Enter password"></el-input>
            </el-form-item>

            <!-- Confirm Password -->
            <el-form-item v-if="resetToken" label="Confirm Password" prop="confirm_password" class="mb-2">
                <el-input type="password" v-model="forgotPasswordForm.confirm_password" size="small" style="width:100%" placeholder="Confirm password"></el-input>
            </el-form-item>

            <!-- Forgot Password Button -->
            <basicButton
                v-if="!resetToken"
                class="float-right mt-2 mb-2 pl-2" 
                type="success" size="large" 
                :ripple="true"
                @click.native="handleSendPasswordReset()">
                <span>Send Reset Email</span>
            </basicButton>

            <!-- Cancel Button -->
            <basicButton
                v-if="!resetToken"
                class="float-right mt-2 mb-2" 
                type="default" size="large" 
                :ripple="false"
                @click.native="showForgotPasswordForm = false">
                <span>Cancel</span>
            </basicButton>

            <basicButton
                v-if="resetToken"
                class="float-right mt-2 mb-2 pl-2" 
                type="success" size="large" 
                :ripple="true"
                @click.native="handleSendPasswordReset()">
                <span>Save Password</span>
            </basicButton>

        </el-form>

    </div>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../loaders/Loader.vue'; 

    /*  Buttons  */
    import basicButton from './../../buttons/basicButton.vue';

    const loginHandle = require('./main.js').default;

    export default {
        components: { Loader, basicButton },
        props: {
            identity: {
                type: String,
                default: null
            },
            password: {
                type: String,
                default: null
            },
            resetPasswordRedirectTo: {
                type: Object,
                default:function(){
                    return { name: 'login' }
                }
            },
        },
        data(){
            return {
                //  Login Details
                loginForm: loginHandle.getLoginFormFields(),
                loginFormRules: loginHandle.getLoginFormRules(),
                loginCustomErrors: loginHandle.getLoginCustomErrorFields(),
                isLoggingIn: false,
                
                //  Forgot Password Details
                forgotPasswordForm: loginHandle.getForgotPasswordFormFields(),
                forgotPasswordFormRules: loginHandle.getForgotPasswordFormRules(),
                forgotPasswordCustomErrors: loginHandle.getForgotPasswordCustomErrorFields(),
                isSendingForgotPasswordEmail: false,

                showForgotPasswordForm: false,

                resetToken: ((this.$route || {}).query || {}).resetToken
            }
        },
        watch: {
            identity: {
                handler: function (val, oldVal) {
                    this.loginForm.identity = val;
                },
                deep: true
            },
            password: {
                handler: function (val, oldVal) {
                    this.loginForm.password = val;
                },
                deep: true
            }
        },
        methods: {
            handleLogin(){
                /*  Start the login process by calling the function initiateLogin() from
                    the loginHandle to handle the login api request. We must pass "this" 
                    current vue instance in order to access data proprties of this login
                    form. The initiateLogin function will handle all validation of the 
                    login form as well as return all the errors if any to the form fields.
                    If the login if is a success we will return the data of the auth user
                    with token and user details. The token will already be set for 
                    future requests that require the auth token. We can use the then()
                    hook to determine what to do next if the login is successful. In this
                    case we only want to alert the parent on the success of the login.
                */
                var self = this;
                var loginResponse = loginHandle.initiateLogin(this);
                
                //  If we have a login response
                if(loginResponse){
                    
                    //  Hook into the response
                    loginResponse.then( data => {
                        
                        //  If not false
                        if( data !== false ){
                            //  If we have the login data
                            //  Notify the parent and pass the login data
                            self.$emit('loginSuccess', data);
                        }
                    });
                }

            },
            handleSendPasswordReset(){
                /*  Start the password reset process by calling the function initiateLogin() from
                    the loginHandle to handle the login api request. We must pass "this" 
                    current vue instance in order to access data proprties of this login
                    form. The initiateLogin function will handle all validation of the 
                    login form as well as return all the errors if any to the form fields.
                    If the login if is a success we will return the data of the auth user
                    with token and user details. The token will already be set for 
                    future requests that require the auth token. We can use the then()
                    hook to determine what to do next if the login is successful. In this
                    case we only want to alert the parent on the success of the login.
                */
                var self = this;
                var resetResponse = loginHandle.initiateSendPasswordResetLink(this);
                
                //  If we have a login response
                if(resetResponse){
                    
                    //  Hook into the response
                    resetResponse.then( data => {
                        
                        //  If not false
                        if( data !== false ){
                            //  If we have the login data
                            //  Notify the parent
                            self.$emit('resetSuccess');
                        }
                    });
                }

            }
        },
        created(){
            
        }
    }

</script>