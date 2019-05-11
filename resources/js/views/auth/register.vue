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

    .register-form {
        margin: 10px auto;
        padding: 50px 100px;
    }

    .register-form .logo{
        margin: 0 auto;
        display: block;
    }


    .register-form h5 {
        text-align: left; 
        position:relative;
    }

    .register-form h5:before {
        content: "";
        position: absolute;
        width: 65%;
        left: 70px;
        top: 13px;
        border-top: 1px solid #ccc;
    }

    .register-form button[type="button"] {
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
                            <p>Job management has just got even easier! Now you can send your customers register details to track job progress while also giving valuable feeback!</p>
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
            Right Side - Contains register form for signing up
        -->
        <Col :span="12" class="col-6 pl-5 pr-5">
            <!-- Register Form -->
            <div class="register-form">
                

                <!-- Company Logo -->
                <img src="/images/assets/logo/OQ-INFINITE-B-150X84.gif" alt="logo" class="logo mb-4">
                
                <div>

                    <!-- Register Title & Fields -->
                    <h5 class="mb-2">Register</h5>

                    <el-form :model="ruleForm" :rules="rules" ref="ruleForm" class="demo-ruleForm">
                        <el-form-item label="" prop="first_name">
                            <el-input v-model="ruleForm.first_name" placeholder="First Name"></el-input>
                        </el-form-item>
                        <el-form-item label="" prop="last_name">
                            <el-input v-model="ruleForm.last_name" placeholder="Last Name"></el-input>
                        </el-form-item>
                        <el-form-item label="" prop="username" :error="customErrors.username">
                            <el-input v-model="ruleForm.username" placeholder="Username"></el-input>
                        </el-form-item>
                        <el-form-item label="" prop="email" :error="customErrors.email">
                            <el-input v-model="ruleForm.email" placeholder="Email"></el-input>
                        </el-form-item>
                        <el-form-item label="" prop="password" :error="customErrors.password">
                            <el-input type="password" v-model="ruleForm.password" placeholder="Password"></el-input>
                        </el-form-item>
                        <el-form-item label="" prop="confirm_password" :error="customErrors.confirm_password">
                            <el-input type="password" v-model="ruleForm.confirm_password" placeholder="Confirm Password"></el-input>
                        </el-form-item>
                        <div v-show="isRegistering" class="mt-4">
                            <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                            Creating account...
                        </div>
                        <el-form-item v-show="!isRegistering">
                            <el-button type="primary" @click="register" class="btn btn-primary mt-2">Register</el-button>
                        </el-form-item>
                    </el-form>

                    <!-- Login Link -->
                    <div class="mt-4 text-center">
                        <span class="mr-2">Already have an account?</span>
                        <router-link :to="{ name: 'login'}">Login</router-link>
                    </div>

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

            hasRegistered: false,
            isRegistering:false,

            //  Form input fileds
            ruleForm: {
                first_name: '',
                last_name: '',
                username: '',
                email: '',
                password: '',
                confirm_password: ''
            },

            //  Form validation rules
            rules: {
                first_name: [
                    { required: true, message: 'Enter your first name', trigger: 'blur' }
                ],
                last_name: [
                    { required: true, message: 'Enter your last name', trigger: 'blur' }
                ],
                username: [
                    { required: true, message: 'Enter your username', trigger: 'blur' }
                ],
                email: [
                    { required: true, message: 'Enter your email', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: 'Enter your password', trigger: 'blur' }
                ],
                confirm_password: [
                    { required: true, message: 'Confirm password', trigger: 'blur' }
                ],
            },
            
            //  Custom server error
            customErrors: {
                username: null,
                email: null
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
    methods: {
        register() {
            const self = this;

            //  Lets validate the current form
            this.$refs['ruleForm'].validate((valid) => {
                //  If this form is valid lets submit
                if (valid) {

                    //  Reset all our custom server errors
                    self.resetCustomErrors();

                    //  Start loader
                    self.isRegistering = true;

                    console.log('Attempt to register using the following...');   

                    //  Register data to send
                    let registerData = {
                        first_name: this.ruleForm.first_name,
                        last_name: this.ruleForm.last_name,
                        username: this.ruleForm.username,
                        email: this.ruleForm.email,
                        password: this.ruleForm.password
                    };

                    console.log(registerData);

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', '/api/register', registerData)
                        .then(({data}) => {

                            //  Stop loader
                            self.isRegistering = false;

                            self.$router.push({ name:'activate-account', query:{ user_id: data.id } });

                        })         
                        .catch(response => { 
                            console.log('Register.vue - Error registering...');
                            console.log(response);        
                        });

                } else {
                    return false;
                }
            });
        },
        resetCustomErrors() {
            this.customErrors = {
                username: null,
                email: null
            }
        }
    },
}
</script>