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
                                            <span @click="openUserRegisterForm()" class="float-right btn btn-link d-inline-block m-0 p-0">Edit</span>
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
                                            <span class="font-weight-bold d-block mb-1">Mobile: <span>(+267) 75993xxx</span></span>
                                        </div>

                                        
                                        <!-- heckout As Company -->
                                        <div class="pb-1 pt-1 mb-1" style="border-bottom: 1px dashed #d6d9dc;">
                                            <!-- Checkout As A Company Checkbox -->
                                            <span>
                                                <Poptip v-if="!chekoutAsCompany" word-wrap width="300" trigger="hover"
                                                        content="Allows you to checkout as a company so that company details appear on the order, invoices and receipts instead of your personal details">
                                                    <Checkbox v-model="chekoutAsCompany">Checkout As Company?</Checkbox>
                                                </Poptip>

                                                <Card v-if="chekoutAsCompany">
                                                    
                                                    <Checkbox v-model="chekoutAsCompany">Checkout As Company?</Checkbox>

                                                    <!-- Change/Edit Company Account Details -->
                                                    <div class="clearfix">
                                                        <span @click="openCompanyRegisterForm()" class="float-right btn btn-link d-inline-block m-0 p-0">Edit</span>
                                                        
                                                        <span v-if="!selectedCompany || !showChangeCompany" class="float-right ml-1 mr-1">|</span>

                                                        <span v-if="!selectedCompany || !showChangeCompany" 
                                                              @click="showChangeCompany = true" class="float-right btn btn-link d-inline-block m-0 p-0">
                                                              Change Company
                                                        </span>

                                                        <span v-if="selectedCompany && showChangeCompany" class="float-right ml-1 mr-1">|</span>

                                                        <span v-if="selectedCompany && showChangeCompany" 
                                                              @click="showChangeCompany = false" class="float-right btn btn-link d-inline-block m-0 p-0">
                                                              Done
                                                        </span>

                                                    </div>

                                                    <template v-if="selectedCompany && !showChangeCompany">
                                                        
                                                        <!-- Company Account Name -->
                                                        <div class="pb-1 mt-1 mb-1" style="border-bottom: 1px dashed #d6d9dc;">
                                                            <span class="font-weight-bold">Company: <span>{{ selectedCompany.name }}</span></span>
                                                        </div>

                                                        <!-- Company Account Contacts -->
                                                        <div class="pb-1 mb-1">
                                                            <span class="font-weight-bold d-block mb-1">Email: <span>{{ selectedCompany.email }}</span></span>
                                                            <span v-if="selectedCompany.phones" class="font-weight-bold d-block mb-1">
                                                                Phone(s): <span>{{ selectedCompany.phone_list }}</span>
                                                            </span>
                                                        </div>

                                                    </template>

                                                    <template v-else>
                                                        <!-- Company Selector -->
                                                        <companySelector 
                                                            class="mt-3 mb-2"
                                                            url="/api/companies"
                                                            :urlParams="{
                                                                userIds: user.id,
                                                                userTypes: '',
                                                                paginate: 0
                                                            }"
                                                            :selectedCompany="selectedCompany"
                                                            @updated:company="handleCompanyChange($event)">
                                                        </companySelector>  
                                                    </template>

                                                </Card>

                                            </span>
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
                                    <span v-if="!user" class="d-block mb-3">Login or <span @click="openUserRegisterForm()" class="btn btn-link d-inline-block m-0 p-0">Create Account</span></span>
                                    <span v-else class="d-block mb-3">
                                        Login
                                        <span>/</span>
                                        <span @click="showLoginForm = false" class="btn btn-link d-inline-block m-0 p-0">
                                            <Icon type="ios-contact-outline" :size="30" />
                                            <span>{{ user.first_name }} {{ user.last_name }}</span>
                                        </span> 
                                        <span>/</span>
                                        <span @click="openUserRegisterForm()" class="btn btn-link d-inline-block m-0 p-0">Create Account</span>
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
                    <Row v-if="registrationForm == 'user'" :gutter="12">

                        <Col :span="24" class="mb-3">
                            <span>Create account or <span href="#" @click="selectedAccountTab = 'login'" class="btn btn-link d-inline-block m-0 p-0">Login</span></span>
                        </Col>

                        <Col :span="24">
                            <!-- Register Form -->
                            <checkoutUserRegisterForm
                                registerBtnText="Register And Pay"
                                route="/api/register"
                                @success="handleUserRegisterSuccess()">
                            </checkoutUserRegisterForm>
                        </Col>

                    </Row>

                    <Row v-if="registrationForm == 'company'" :gutter="12">

                        <Col :span="24" class="mb-3">
                            <span>Register company or <span href="#" @click="selectedAccountTab = 'login'" class="btn btn-link d-inline-block m-0 p-0">Cancel</span></span>
                        </Col>

                        <Col :span="24">
                            <!-- Register Form -->
                            <checkoutCompanyRegisterForm
                                :summaryMode="false"
                                :hideSummaryToggle="true"
                                :hiddenFields="[
                                    'logo', 'relationship', 'description', 'date_of_incorporation', 'website_link', 
                                    'facebook_link',, 'twitter_link', 'linkedin_link', 'instagram_link'
                                ]"
                                registerBtnText="Register"
                                route="/api/companies"
                                @success="handleCompanyRegisterSuccess($event)">
                            </checkoutCompanyRegisterForm>
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
    import checkoutUserRegisterForm from './../../../components/_common/forms/register-user/register.vue';
    import checkoutCompanyRegisterForm from './../../../components/_common/forms/register-company/register.vue';

    /*  Selectors  */
    import companySelector from './../../../components/_common/selectors/companySelector.vue';

    export default {
        components: { 
            basicButton, checkoutLoginForm, checkoutUserRegisterForm, checkoutCompanyRegisterForm, companySelector
        },
        props: {
            checkoutProgress: {
                type: Number,
                default: 0
            }
        },
        data(){
            return {
                user: auth.user,
                selectedAccountTab:'login',
                localBillingInfo: null,
                chekoutAsCompany: false,
                selectedCompany: null,
                localCheckoutProgress: this.checkoutProgress,

                showLoginForm: false,
                showChangeCompany: false,
                registrationForm: 'user',

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
            localBillingInfo: {
                handler: function (val, oldVal) {
                    this.$emit('updated:billingInfo', val);
                },
                deep: true
            },
            chekoutAsCompany: {
                handler: function (val, oldVal) {
                    //  If the checkbox is set to true and we have a company already set
                    if(val && this.selectedCompany){
                        //  Then set the company info as the billing info
                        this.localBillingInfo = this.selectedCompany;
                    }else{
                        //  Then set the user info as the billing info
                        this.localBillingInfo = this.user;
                    }
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
            openUserRegisterForm(){
                this.selectedAccountTab = 'register';
                this.registrationForm = 'user';
            },
            openCompanyRegisterForm(){
                this.selectedAccountTab = 'register';
                this.registrationForm = 'company';
            },
            handleLoginSuccess(){
                //  Lets update the user details
                this.user = auth.user;

                //  Hide the login form
                this.showLoginForm = false;

                //  Update the billing info
                this.localBillingInfo = this.user;
            },
            handleUserRegisterSuccess(){
                //  Lets update the user details
                this.user = auth.user;

                //  Hide the login form
                this.showLoginForm = false;

                //  Go to the login tab
                this.selectedAccountTab = 'login';

                //  Update the billing info
                this.localBillingInfo = this.user;
            },
            handleCompanyRegisterSuccess(company){
                //  Lets update the company details
                this.selectedCompany = company;

                //  Hide the login form
                this.showLoginForm = false;

                //  Go to the login tab
                this.selectedAccountTab = 'login';

                //  Update the billing info
                this.localBillingInfo = this.selectedCompany;
            },
            handleCompanyChange(company){
                //  Lets update the company details
                this.selectedCompany = company;

                //  Hide the company selector widget
                this.showChangeCompany = false;

                //  Update the billing info
                this.localBillingInfo = this.selectedCompany;
            },
            updateCheckoutProgress(){
                this.$emit('proceed');
            }
        },
        created(){

            //  Update the billing info
            this.localBillingInfo = (auth || {}).user;

            //  If we have the reset token
            if(this.resetToken){
                //  show the login form
                this.showLoginForm = true;

            }
        }
    };
  
</script>