<style scoped>

    .el-form--label-top >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }

</style>

<template>
 
    <!--  Login/Register Account Details --> 
    <Tabs type="card" :value="selectedAccountTab">

        <!--  Login Tab -->  
        <TabPane label="Login" name="login">
            
            <Row :gutter="12" class="pt-4 pb-4 pr-2 pl-2 mr-1 ml-1" :style="{ background: '#f5f7f9', borderRadius:'10px' }">
                <Col :span="24">
                    <Row>
                        <Col :span="24">
                            <!-- Authenticated User Details. Show if...
                                    1 - We have the user details and
                                    2 - We set showLoginForm = false -->
                            <Row v-if="user && showLoginForm == false" :gutter="12">
                                <Col :span="14" :offset="5">
                                    <!-- Account Summary -->
                                    <Card>
                                        
                                        <!-- Change/Edit Account buttons -->
                                        <div class="clearfix">
                                            <span @click="selectedAccountTab = 'register'" class="float-right btn btn-link d-inline-block m-0 p-0">Edit</span>
                                            <span class="float-right ml-1 mr-1">|</span>
                                            <span @click="showLoginForm = true" class="float-right btn btn-link d-inline-block m-0 p-0">Change Account</span>
                                        </div>
                                        
                                        <!-- Account Name -->
                                        <div class="pb-1 mt-1 mb-1" style="border-bottom: 1px dashed #d6d9dc;">
                                            <span class="font-weight-bold">Account: <span>{{ user.first_name }} {{ user.last_name }}</span></span>
                                        </div>

                                        <!-- Account Contacts -->
                                        <div class="pb-1 mb-1" style="border-bottom: 1px dashed #d6d9dc;">
                                            <span class="font-weight-bold d-block mb-1">Email: <span>{{ user.email }}</span></span>
                                            <span class="font-weight-bold d-block mb-1">Mobile: <span>(+267) 75993221</span></span>
                                        </div>
                                            
                                        <!-- Checkout As A Company Button -->

                                        <!-- Continue button -->
                                        <div class="mt-2 clearfix">
                                            <basicButton
                                                class="float-right mb-2 ml-3" 
                                                type="success" size="large" 
                                                :ripple="true"
                                                @click.native="updateCheckoutProgress()">
                                                <span>Continue</span>
                                                <Icon type="md-arrow-forward" class="ml-1" />
                                            </basicButton>

                                            <span @click="true" class="float-right btn btn-link d-inline-block mt-1 p-0">
                                                <Poptip word-wrap width="300" trigger="hover"
                                                        content="Allows you to checkout as a company so that company details appear on the order, invoices and receipts instead of your personal details">
                                                    Checkout As Company?
                                                </Poptip>
                                            </span>

                                        </div>
                                    </Card>
                                </Col>

                            </Row>

                            <!-- Login Form. Show if...
                                    1 - We we don't have the user details or
                                    2 - We set showLoginForm = true -->
                            <Row v-else :gutter="12">
                                <Col :span="12" :offset="6">
                                    
                                    <!-- Instructions -->
                                    <span v-if="!user" class="d-block mb-3">Login or <span @click="selectedAccountTab = 'register'" class="btn btn-link d-inline-block m-0 p-0">Create Account</span></span>
                                    <span v-else class="d-block mb-3">
                                        Login
                                        <span>/</span>
                                        <span @click="showLoginForm = false" class="btn btn-link d-inline-block m-0 p-0">
                                            <Icon type="ios-contact-outline" :size="30" />
                                            <span>{{ user.first_name }} {{ user.last_name }}</span>
                                        </span> 
                                        <span>/</span>
                                        <span @click="selectedAccountTab = 'register'" class="btn btn-link d-inline-block m-0 p-0">Create Account</span>
                                    </span>
                                    
                                    <!-- Login Form -->
                                    <checkoutLoginForm
                                        :resetPasswordRedirectTo="{ name: 'checkout' }"
                                        @loginSuccess="handleLoginSuccess()">
                                    </checkoutLoginForm>

                                </Col>
                            </Row>
                        </Col>

                    </Row>
                </Col>
            </Row>

        </TabPane>

        <!--  Register Tab --> 
        <TabPane label="Create Account" name="register">
        
            <!-- Register Form -->
            <Row :gutter="12" class="pt-4 pb-5 pr-3 pl-3 mr-1 ml-1" :style="{ background: '#f5f7f9', borderRadius:'10px' }">
                <Col :span="24">
                    <Row :gutter="12">

                        <Col :span="24" class="mb-3">
                            <span>Create account or <span href="#" @click="selectedAccountTab = 'login'" class="btn btn-link d-inline-block m-0 p-0">Login</span></span>
                        </Col>

                        <Col :span="24">
                            <!-- Register Form -->
                            <checkoutRegisterForm
                                registerBtnText="Register And Pay"
                                route="/api/register"
                                @success="handleRegisterSuccess()">
                            </checkoutRegisterForm>
                        </Col>

                    </Row>
                </Col>
            </Row>

        </TabPane>
    </Tabs>

</template>

<script>
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Forms  */
    import checkoutLoginForm from './../../../components/_common/forms/login-user/checkout-login.vue';
    import checkoutRegisterForm from './../../../components/_common/forms/register-user/checkout-register.vue';

    export default {
        components: { 
            basicButton, checkoutLoginForm, checkoutRegisterForm
        },
        props: {
            products: {
                type: Array,
                default: function(){
                    return [];
                }
            },
            checkoutProgress: {
                type: Number,
                default: 0
            }
        },
        data(){
            return {
                user: auth.user,
                selectedAccountTab:'login',

                localProducts: this.products,
                localCheckoutProgress: this.checkoutProgress,

                showLoginForm: false,

                resetToken: ((this.$route || {}).query || {}).resetToken
            }
        },
        watch: {
            products: {
                handler: function (val, oldVal) {
                    this.localProducts = val;
                },
                deep: true
            },
            checkoutProgress: {
                handler: function (val, oldVal) {
                    this.localCheckoutProgress = val;
                },
                deep: true
            },
        },
        methods: {
            handleLoginSuccess(){
                //  Lets update the user details
                this.user = auth.user;

                //  Hide the login form
                this.showLoginForm = false;
            },
            handleRegisterSuccess(){
                //  Lets update the user details
                this.user = auth.user;

                //  Hide the login form
                this.showLoginForm = false;

                //  Go to the login tab
                this.selectedAccountTab = 'login';
            },
            updateCheckoutProgress(){
                this.$emit('proceed');
            }
        },
        created(){
            //  If we have the reset token
            if(this.resetToken){
                //  show the login form
                this.showLoginForm = true;

            }
        }
    };
  
</script>