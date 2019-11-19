<style>

    .slider {
        /*height: 100%;*/
        text-align:center;
    }

    .slider .slider-box {
        height: 100vh;
    }

    .slider .slider-box article {
        margin: 50px 0 15px;
        padding: 0 22px;
        box-sizing: border-box;
        display: inline-block;
        width: 100%;
        z-index:1;
    }

    .slider .slider-box article h2 {
        font-size: 36px;
        text-transform: capitalize;
        font-family: proximaNova_semibold,Arial,Helvetica;
        margin-top: 9px;
    }

    .slider .slider-box article h2, 
    .slider .slider-box article p {
        color:#fff;
    }

    .slider .slider-box img {
        display: block;
        margin: 0 auto;
    }

    .slider .slick-dots {
        bottom: 60px;
    }

    .slider .slick-dots li button:before{
        display:none;
    }

    .slider .slick-dots li button {
        background-color: #c5c5c5!important;
        border-radius: 100%;
        width: 7px!important;
        height: 7px!important;
    }

    .slider .slick-dots li.slick-active button {
        background-color: #fff!important;
    }

    .login-form {
        margin: 10px auto;
        padding: 50px 100px;
    }

    .login-form .logo{
        margin: 0 auto;
        display: block;
    }


    .login-form h5 {
        text-align: left; 
        position:relative;
    }

    .login-form h5:before {
        content: "";
        position: absolute;
        width: 65%;
        left: 70px;
        top: 13px;
        border-top: 1px solid #ccc;
    }

    .login-form button[type="button"] {
        width: 100%;
        height: 50px;
        line-height: 50px;
        color: #ffffff;
        border-radius:3px;
        font-size: 14px;
        padding: 0;
        border-color: #ffb400 !important;
        background-color: #ffb400 !important;
    }

    .promo-box{
        background: #e2e2e2;
        padding: 30px 20px 20px;
        color: #5a5a5a;
    }


    .el-switch{
        float: left;
        margin-top: 10px !important;
    }


</style>

<template>
    <Row style="position:absolute;top:0;bottom:0;left:0;right:0;overflow:hidden;">

        <!-- 
            Left Side - Contains information sliders for advertising 
            latest features and updates
        -->
        <Col :span="12">
            <slick ref="slick" :options="slickOptions" class="slider">
                <a href="#">
                    <div class="slider-box pl-3 pr-3" style="background:#121058;">
                        <article flex="" class="flex">
                            <h2>Better Management Anytime - Anywhere On Your Phone</h2>
                            <p>Start managing your jobs, clients, workers and more with our Mobile App. And better yet, we support all IPhone, Android and Windows devices</p>
                        </article>
                        <div class="flex-100 layout-align-center-center layout-row" layout="row" layout-align="center center">
                            <img src="/images/backgrounds/download-app.png" alt="">
                        </div>
                        <div class="flex-100 layout-align-center-center layout-row" layout="row" layout-align="center center">
                            <el-button type="primary" class="mt-5 mb-5" style="min-width:130px;">Download APP</el-button>
                        </div>
                    </div>
                </a>
                
                <a href="#">
                    <div class="slider-box pl-3 pr-3" style="background:#060e49;">
                        <article flex="" class="flex">
                            <h2>Get Customer Feedback<br>On The Job</h2>
                            <p>Job management has just got even easier! Now you can send your customers login details to track job progress while also giving valuable feeback!</p>
                        </article>
                        <div class="flex-100 layout-align-center-center layout-row" layout="row" layout-align="center center">
                            <img src="/images/backgrounds/rating.png" alt="">
                        </div>
                        <div class="flex-100 layout-align-center-center layout-row" layout="row" layout-align="center center">
                            <el-button type="primary" class="mt-5 mb-5" style="min-width:130px;">Read More</el-button>
                        </div>
                    </div>
                </a>
            </slick>
        </Col>

        <!-- 
            Right Side - Contains login form for signing in 
        -->
        <Col :span="12" class="col-6 pl-5 pr-5">
            <!-- Login Form -->
            <div class="login-form">
                
                <!-- Company Logo -->
                <img src="/images/assets/logo/OQ-INFINITE-B-150X84.gif" alt="logo" class="logo mb-4">
                
                <!-- Account Activated Alert -->
                <Alert v-if="accountVerified" type="success" show-icon>
                    Account Activated!
                    <template slot="desc">
                        Login and access your dashboard
                    </template>
                </Alert>
                
                <!-- Login Title & Fields -->
                <h5 v-if="!accountVerified" class="mb-2">Login</h5>

                <el-form :model="ruleForm" :rules="rules" ref="ruleForm" class="demo-ruleForm">
                    <el-form-item label="" prop="identity" :error="customErrors.identity">
                        <el-input v-model="ruleForm.identity" placeholder="Email/Username"></el-input>
                    </el-form-item>
                    <el-form-item label="" prop="password" :error="customErrors.password">
                        <el-input type="password" v-model="ruleForm.password" placeholder="Password"></el-input>
                    </el-form-item>
                    <el-form-item label="Remember Me" prop="rememberMe">
                        <el-switch v-model="ruleForm.rememberMe" active-color="#13ce66"></el-switch>
                    </el-form-item>
                    <div v-show="isLoggingIn" class="mt-4">
                        <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                        Logging in...
                    </div>
                    <el-form-item v-show="!isLoggingIn">
                        <el-button type="primary" @click="login" class="btn btn-primary mt-2">Login</el-button>
                    </el-form-item>
                </el-form>

                <!-- Forgot Password And Create Acconut Links -->
                <div class="mt-4 text-center">
                    <router-link :to="{ name: 'register'}" class="mr-3">Create Account</router-link>
                    <router-link :to="{ name: 'register'}">Forgot password?</router-link>
                </div>

                <!-- Promotion information -->
                <div class="promo-box mt-5 text-center">
                    <b>Terms And Conditions: </b>
                    <p>You will get <b>20% OFF</b> on your next 4 invoices</p>
                </div>

            </div>

        </Col>

    </Row>
</template>

<script>

import Slick from 'vue-slick';

export default {

    components: { Slick },

    data() {
        return {

            isLoggingIn:false,

            //  Is the account verified
            accountVerified: false,

            //  Form input fileds
            ruleForm: {
                identity: '',
                password: '',
                rememberMe: false,
            },

            //  Form validation rules
            rules: {
                identity: [
                    { required: true, message: 'Enter your Email/Username', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: 'Enter your password', trigger: 'blur' }
                ],
            },
            
            //  Custom server error
            customErrors: {
                identity: null,
                password: null
            },

            //  Slick slider options
            slickOptions: {
                dots: true,
                speed: 1000,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                fade: true,
            },
        };
    },
    created () {
        //  Empty the fields even if autofill is set
        this.ruleForm.identity = '';
        this.ruleForm.password = '';
        //  If the account was activated and the user was redirected to this login page
        //  Check the URL to confirm if the account was activated. It should have a
        //  Parameter called activated that is equal to the username of the 
        //  activated account e.g) "app-domain.com/login?activated=juliantabona" 
        if(this.$route.query.activated){
            this.accountVerified = true;
            this.ruleForm.identity = this.$route.query.activated;
        }
    },
    methods: {
        login() {
            const self = this;

            //  Lets validate the current form
            this.$refs['ruleForm'].validate((valid) => {
                //  If this form is valid lets submit
                if (valid) {

                    //  Reset all our custom server errors
                    self.resetCustomErrors();

                    //  Start loader
                    self.isLoggingIn = true;

                    console.log('Attempt to login using the following...');   

                    //  Login data to send
                    let loginData = {
                        identity: this.ruleForm.identity,
                        password: this.ruleForm.password
                    };

                    console.log(loginData);

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', '/api/login', loginData)
                        .then(({data}) => {

                            //  Stop loader
                            self.isLoggingIn = false;
                            
                            //  Get the token and authenticated user
                            let token = data.auth.access_token;
                            let user = data.user;

                            //  Save token and user
                            //  Include token in all further axios api calls
                            auth.login(token);

                            console.log('Go to overview...');

                            //  Navigate to the dashboard
                            self.$router.push({name: 'overview'});
                        })         
                        .catch(response => { 
                            console.log('Login.vue - Error loggin in...');
                            console.log(response);

                            //  Stop loader
                            self.isLoggingIn = false;     
                        
                            /* 
                            *  403: Forbidden. The user must activate their account first
                            *  422: Validation failed. Incorrect credentials
                            *  429: Too many login attempts.
                            */
                            if(response.status === 422 || response.status === 429){
                                var errors = (((response || {}).data || {}).errors || {});

                                //  If we have errors
                                if(_.size(errors)){
                                    //  Foreach error
                                    for (var i = 0; i < _.size(errors); i++) {
                                        //  Get the error key e.g 'identity', 'password'
                                        var prop = Object.keys(errors)[i];
                                        //  Get the error value e.g 'These credentials do not match our records.'
                                        var value = Object.values(errors)[i][0];
                                        //  Dynamically update the customErrors for Element UI to display the error to the user
                                        self.customErrors[prop] = value;
                                    }

                                }
                            }

                            //  If account not activated
                            if(response.status === 403){

                                console.log('Activate account...');

                                //  Redirect to account activation page
                                var userId = response.data.user.id;
                                
                                self.$router.push({ path: 'activate-account', 
                                    query: { 
                                        user_id: userId
                                    }
                                });
                            }
        
                        });

                } else {
                    return false;
                }
            });
        },
        resetCustomErrors() {
            this.customErrors = {
                identity: null,
                password: null
            }
        }
    },
}
</script>