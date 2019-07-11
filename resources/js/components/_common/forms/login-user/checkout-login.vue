<style scoped>
    
    .el-form-item{
        margin-bottom: 0px;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }
</style>
<template>

    <!-- Login Form-->
    <el-form :model="loginForm" :rules="loginFormRules" ref="loginForm">

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

        <!-- Login Button -->
        <basicButton
            v-else
            class="float-right mt-2 mb-2 ml-3 pl-3 pr-3" 
            type="success" size="large" 
            :ripple="true"
            @click.native="handleLogin()">
            <span>Login</span>
        </basicButton>

    </el-form>

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
            }
        },
        data(){
            return {
                loginForm: {
                    identity: this.identity,
                    password: this.password,
                    rememberMe: false,
                },
                loginFormRules: loginHandle.getLoginFormRules(),
                loginCustomErrors: loginHandle.getLoginCustomErrors(),
                isLoggingIn: false
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

                console.log('loginResponse');
                console.log(loginResponse);
                
                //  If we have a login response
                if(loginResponse){
                    console.log('Hook In 1');
                    //  Hook into the response
                    loginResponse.then( data => {
                        //  If we have the login data
                        console.log('Notify The Parent');
                        console.log(loginResponse);
                        //  Notify the parent and pass the login data
                        self.$emit('loginSuccess', data);
                    });
                    
                    console.log('Hook Out 2');
                }

            }
        },
        created(){

        }
    }

</script>