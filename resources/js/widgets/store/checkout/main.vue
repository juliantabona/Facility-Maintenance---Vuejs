<style scoped>

    .el-form--label-top >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }



</style>

<template>

    <Row :gutter="20">
        <Col :span="24">
            
            <Row :gutter="12">
                <Col :xs="24" :sm="18" :md="18" :lg="18" class="mb-2">

                    <h1 class="text-center pt-3 pb-4">CHECKOUT</h1>

                    <Card class="ml-1 mr-1">
                        <Steps :current="checkoutProgress">
                            <Step title="Account" content="Complete basic account details"></Step>
                            <Step title="Delivery" content="Add location details for deliveries"></Step>
                            <Step title="Payment" content="Pay for goods"></Step>
                        </Steps>
                    </Card>

                    <Carousel v-model="checkoutProgress" dots="none" arrow="never" class="pb-5 mt-2 mb-2">
                        
                        <!--    Account Details -->
                        <CarouselItem>
                            <Card class="ml-1 mr-1">                               
                                <el-form label-position="top" label-width="100px" :model="formData">

                                    <Tabs type="card" :value="selectedAccountTab">
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
                                                                                @click.native="checkoutProgress = checkoutProgress + 1">
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
                                </el-form>
                            </Card>
                        </CarouselItem>

                        <!--    Delivery Details -->
                        <CarouselItem>
                            <Card class="ml-1 mr-1">                                   
                                <el-form label-position="top" label-width="100px" :model="formData">
                                    <Row :gutter="12" class="pt-4 pb-4 pr-2 pl-2 mr-1 ml-1" :style="{ background: '#f5f7f9', borderRadius:'10px' }">
                                        <Col :span="24">
                                            
                                            <!-- Authenticated User Delivery Details. Show if...
                                                    1 - We have the user details and
                                                    2 - We set showDeliveryForm = false -->
                                            <Row v-if="user && !showDeliveryForm" :gutter="12" class="mb-4">
                                                <Col :span="14" :offset="5">
                                                    <!-- Account Summary -->
                                                    <Card>
                                                        
                                                        <!-- Change/Edit Account buttons -->
                                                        <div class="clearfix">
                                                            <span @click="showDeliveryForm = true" class="float-right">
                                                                <Icon type="ios-create-outline" :size="20" class="mr-1" />
                                                                <span>Edit</span>
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- Receiver Name -->
                                                        <div class="pb-2 mt-1 mb-2" style="border-bottom: 1px dashed #d6d9dc;">
                                                            <span class="font-weight-bold">Deliver To: <span>{{ user.first_name }} {{ user.last_name }}</span></span>
                                                        </div>
                                                            
                                                        <!-- Receiver Address -->
                                                        <div class="pb-2 mb-2" style="border-bottom: 1px dashed #d6d9dc;">
                                                            <span class="font-weight-bold d-block mb-1">Address: <span>{{ user.address_1 }}</span></span>
                                                            <span class="font-weight-bold d-block mb-1">Country: <span>{{ user.country }}</span></span>
                                                            <span class="font-weight-bold d-block mb-1">Province: <span>{{ user.province }}</span></span>
                                                            <span class="font-weight-bold d-block mb-1">City: <span>{{ user.city }}</span></span>
                                                        </div>

                                                        <div class="mt-2 clearfix">
                                                            <!-- Continue button -->
                                                            <basicButton
                                                                class="float-right mb-2 ml-3" 
                                                                type="success" size="large" 
                                                                :ripple="true"
                                                                @click.native="checkoutProgress = checkoutProgress + 1">
                                                                <span>Continue</span>
                                                                <Icon type="md-arrow-forward" class="ml-1" />
                                                            </basicButton>

                                                            <!-- Back button -->
                                                            <basicButton
                                                                class="float-right mb-2 ml-3" 
                                                                type="default" size="large" 
                                                                :ripple="false"
                                                                @click.native="checkoutProgress = checkoutProgress - 1">
                                                                <Icon type="md-arrow-back" class="mr-1" />
                                                                <span>Back</span>
                                                            </basicButton>
                                                        </div>
                                                    </Card>
                                                </Col>
                                            </Row>

                                            <!-- Delivery Form. Show if...
                                                    1 - We we don't have the user delivery details or
                                                    2 - We set showDeliveryForm = true -->
                                            <Row v-else :gutter="12">
                                                <!-- Instructions -->
                                                <Col :span="24" class="mb-3">
                                                    <h5 class="d-block pb-2">Change delivery details</h5>
                                                </Col>
                                                <Col :span="24">
                                                    <!-- Register Form -->
                                                    <checkoutRegisterForm
                                                        registerBtnText="Save Address"
                                                        :existingUser="user"
                                                        :showLoader="showDeliveryFormLoader"
                                                        :route="user ? '/api/users/'+user.id : null"
                                                        :hiddenFields="['first_name', 'last_name', 'email', 'username', 'phone', 'password', 'confirm_password']"
                                                        :additionalParams="[]"
                                                        :showCancelBtn="true"
                                                        @cancel="showDeliveryForm = false"
                                                        @success="handleSavedAddressSuccess()">
                                                    </checkoutRegisterForm>
                                                </Col>
                                            </Row>

                                        </Col>
                                    </Row>
                                </el-form>
                            </Card>
                        </CarouselItem>

                        <!--    Payment Details -->
                        <CarouselItem>
                            <Card class="ml-1 mr-1">                                   
                                <el-form label-position="top" label-width="100px" :model="formData">
                                    <Row :gutter="12">
                                        <Col :span="24" class="mb-5">
                                            <Alert class="mb-4">
                                                <span class="font-weight-bold">Select Payment Method</span>
                                                <template slot="desc">How would yout like to pay? Select any prefered payment method to pay</template>
                                            </Alert>
                                            <Row :gutter="20">

                                                <Col :span="24">
                                                    <Divider orientation="left">Online Payments</Divider>
                                                </Col>

                                                <Col :span="8">
                                                    <IconAndCounterCard 
                                                        title="Credit/Debit Card" 
                                                        imageSrc="/images/assets/graphics/credit-card.png"
                                                        :imageStyle="{ width:'100%' }"
                                                        :innerBoxStyle="{ padding: '0' }"
                                                        :titleStyle="{ marginTop:'10px', padding:'0' }"
                                                        type="success"
                                                        :showCheckMark="true"
                                                        :checkMarkVisibility="(selectedPaymentMethod == 'card')"
                                                        @click.native="selectedPaymentMethod = 'card'">
                                                    </IconAndCounterCard>
                                                </Col>

                                                <Col :span="8">
                                                    <IconAndCounterCard 
                                                        title="Orange Money" 
                                                        imageSrc="/images/samples/orange_money_logo.png"
                                                        :imageStyle="{ width:'65%', margin: '0 auto', display: 'block' }"
                                                        :innerBoxStyle="{ padding: '0' }"
                                                        :titleStyle="{ marginTop:'10px', padding:'0' }"
                                                        type="success"
                                                        :showCheckMark="true"
                                                        :checkMarkVisibility="(selectedPaymentMethod == 'orange')"
                                                        @click.native="selectedPaymentMethod = 'orange'">
                                                    </IconAndCounterCard>
                                                </Col>

                                                <Col :span="8">
                                                    <IconAndCounterCard 
                                                        title="MyZaka" 
                                                        imageSrc="/images/samples/myzaka_logo.png"
                                                        :imageStyle="{ width:'65%', margin: '0 auto', display: 'block' }"
                                                        :innerBoxStyle="{ padding: '0' }"
                                                        :titleStyle="{ marginTop:'10px', padding:'0' }"
                                                        type="success"
                                                        :showCheckMark="true"
                                                        :checkMarkVisibility="(selectedPaymentMethod == 'mascom')"
                                                        @click.native="selectedPaymentMethod = 'mascom'">
                                                    </IconAndCounterCard>
                                                </Col>

                                            </Row>

                                            <Row :gutter="20">

                                                <Col :span="24" class="mt-4">
                                                    <Divider orientation="left">Offline Payments</Divider>
                                                </Col>

                                                <Col :span="8">
                                                    <IconAndCounterCard 
                                                        title="Cash Deposit" 
                                                        imageSrc="/images/assets/graphics/bank-atm-machine.png"
                                                        :imageStyle="{ width:'60%',display:'block', margin:'auto' }"
                                                        :innerBoxStyle="{ padding: '0' }"
                                                        :titleStyle="{ marginTop:'10px', padding:'0' }"
                                                        type="success"
                                                        :showCheckMark="true"
                                                        :checkMarkVisibility="(selectedPaymentMethod == 'cash-deposit')"
                                                        @click.native="selectedPaymentMethod = 'cash-deposit'">
                                                    </IconAndCounterCard>
                                                </Col>

                                                <Col :span="8">
                                                    <IconAndCounterCard 
                                                        title="Bank Transfer" 
                                                        imageSrc="/images/assets/graphics/bank-atm-machine.png"
                                                        :imageStyle="{ width:'60%',display:'block', margin:'auto' }"
                                                        :innerBoxStyle="{ padding: '0' }"
                                                        :titleStyle="{ marginTop:'10px', padding:'0' }"
                                                        type="success"
                                                        :showCheckMark="true"
                                                        :checkMarkVisibility="(selectedPaymentMethod == 'bank-transfer')"
                                                        @click.native="selectedPaymentMethod = 'bank-transfer'">
                                                    </IconAndCounterCard>
                                                </Col>

                                                <Col :span="8">
                                                    <IconAndCounterCard 
                                                        title="Cheque" 
                                                        imageSrc="/images/assets/graphics/cheque.png"
                                                        :imageStyle="{ width:'100%', margin:' 20px 0 10px 0' }"
                                                        :innerBoxStyle="{ padding: '0' }"
                                                        :titleStyle="{ marginTop:'10px', padding:'0' }"
                                                        type="success"
                                                        :showCheckMark="true"
                                                        :checkMarkVisibility="(selectedPaymentMethod == 'cheque')"
                                                        @click.native="selectedPaymentMethod = 'cheque'">
                                                    </IconAndCounterCard>
                                                </Col>

                                            </Row>

                                            <!-- VCS Form -->
                                            <form ref="vcsform" method="POST" action="https://www.vcs.co.za/vvonline/vcspay.aspx">
                                                <input type="hidden" name="_token" :value="csrf_token" />
                                                <input type="hidden" name="p1" value="3463"> 
                                                <input type="hidden" name="p2" value="12345"> 
                                                <input type="hidden" name="p3" value="Flared Shift Dress"> 
                                                <input type="hidden" name="p4" value="2580"> 
                                                <input type="hidden" name="UrlsProvided" value="Y">  
                                                <input type="hidden" name="ApprovedUrl" value="/paymentSuccessful">  
                                                <input type="hidden" name="DeclinedUrl" value="/paymentUnSuccessful">   
                                                <input type="hidden" name="p10" value="/paymentUnSuccessful"> 
                                            </form>

                                        </Col>

                                        <Col :span="24" class="clearfix">
                                            <!-- Continue button -->
                                            <basicButton
                                                v-if="selectedPaymentMethod"
                                                class="float-right mb-2 ml-3" 
                                                type="success" size="large" 
                                                :ripple="true"
                                                @click.native="handleProceedToPayment()">
                                                <span>Proceed To Payment</span>
                                                <Icon type="md-arrow-forward" class="ml-1" />
                                            </basicButton>

                                            <!-- Back button -->
                                            <basicButton
                                                class="float-right mb-2 ml-3" 
                                                type="default" size="large" 
                                                :ripple="false"
                                                @click.native="checkoutProgress = checkoutProgress - 1">
                                                <Icon type="md-arrow-back" class="mr-1" />
                                                <span>Back</span>
                                            </basicButton>
                                        </Col>

                                    </Row>
                                </el-form>
                            </Card>
                        </CarouselItem>
                    </Carousel>

                    <div class="tt-shopcart-btn">
                        <div class="col-left">
                            <span class="btn btn-link"><i class="icon-e-19"></i>CONTINUE SHOPPING</span>
                        </div>
                    </div>
                </Col>
                <Col :xs="24" :sm="6" :md="6" :lg="6" class="mb-2">
                    
                    <cartWidget cartType="widget-cart" :hideCheckoutBtn="true"></cartWidget>

                </Col>
            </Row>
        </Col>
    </Row>

</template>

<script>
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /*  Inputs   */
    import phoneInput from './../../../components/_common/inputs/phoneInput.vue'; 

    /*  Selectors   */
    import citySelector from './../../../components/_common/selectors/citySelector.vue'; 
    import provinceSelector from './../../../components/_common/selectors/provinceSelector.vue'; 
    import countrySelector from './../../../components/_common/selectors/countrySelector.vue'; 

    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    /*  Forms  */
    import checkoutLoginForm from './../../../components/_common/forms/login-user/checkout-login.vue';
    import checkoutRegisterForm from './../../../components/_common/forms/register-user/checkout-register.vue';

    /*  Cart Widget  */
    import cartWidget from './../cart/main.vue';

    export default {
        components: { 
            basicButton, toggleSwitch, Loader, phoneInput, citySelector, provinceSelector, countrySelector, IconAndCounterCard,
            checkoutLoginForm, checkoutRegisterForm, cartWidget
        },
        props: {
            products: {
                type: Array,
                default: function(){
                    return [];
                }
            }
        },
        data(){
            return {
                user: auth.user,
                csrf_token: window.axios.defaults.headers.common['X-CSRF-TOKEN'],
                selectedAccountTab:'login',
                selectedPaymentMethod: '',

                localProducts: this.products,
                checkoutProgress: 0,

                showLoginForm: false,
                showDeliveryForm: false,
                showDeliveryFormLoader: false,
                formData: {

                    /*  Basic Info  */
                    first_name: null, 
                    last_name: null, 
                    gender: null, 
                    date_of_birth: null, 
                    bio: null, 
                    
                    /*  Address Info  */
                    address_1: null, 
                    address_2: null, 
                    country: null, 
                    province: null, 
                    city: null, 
                    postal_or_zipcode: null, 
                    
                    /*  Address Info  */
                    email: null, 
                    additional_email: null,  
                    username: null, 
                    password: null, 
                    verified: null, 
                    setup: null, 
                    
                    /*  Social Info  */
                    facebook_link: null, 
                    twitter_link: null, 
                    linkedin_link: null, 
                    instagram_link: null, 
                    youtube_link: null,

                    /*  Company Info  */
                    company_branch_id: null, 
                    company_id: null,

                    /*  Relationship  */
                    relationship: null,

                    /*  Profile Image  */
                    profile_image: null,

                    /*  Phones  */
                    phones: [],

                    /*  Passwords  */
                    password: '',
                    confirm_password: ''
                }, 

                resetToken: ((this.$route || {}).query || {}).resetToken
            }
        },
        watch: {
            products: {
                handler: function (val, oldVal) {
                    this.localProducts = val;
                },
                deep: true
            }
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
            handleSavedAddressSuccess(user){

                var self = this;

                //  Show the delivery form loader
                this.showDeliveryFormLoader = true;

                //  Update the auth user details
                auth.getUser().then( data => {

                    //  Lets update the user details
                    self.user = auth.user;

                    //  Hide the delivery form loader
                    self.showDeliveryFormLoader = false;

                    //  Hide the delivery address form
                    self.showDeliveryForm = false;
                });
            },
            handleProceedToPayment(){
                //  To submit vcs form
                //  this.$refs.vcsform.submit()

                console.log('Attempt to create product...');

                console.log( this.localProduct );

                //  Form data to send
                let orderData = { 
                        //  General details
                        number: '1005',
                        order_key: 'dm_order_58d2d042d1d',
                        status: 'pending-payment',
                        currency_type: {
                            country: 'Botswana',
                            currency: {
                                iso: {
                                code: 'BWP',
                                number: '072'
                                },
                                name: 'Pula',
                                symbol: 'P'
                            }
                        },
                        //  Item Info
                        line_items: [
                            {
                                id: 36,
                                name: 'Rolex wrist watch',
                                description: 'Stylish x3 series rolex watch',
                                type: 'product',
                                taxes: [],
                                purchasePrice: 1250,
                                unit_price: 1800,
                                total_price: 3600,
                                quantity: '2'
                            }
                        ],

                        //  Shipping Info
                        shipping_lines: null,

                        //  Grand Total, Subtotal, Shipping Total, Discount Total
                        cart_total: 10.00,
                        shipping_total: 0.00,
                        discount_total: 0.00,
                        grand_total: 15.00,

                        //  Tax Info
                        cart_tax: 2.00,
                        shipping_tax: 0.00,
                        discount_tax: 0.00,
                        grand_total_tax: 3.00,
                        prices_include_tax: 0,
                        tax_lines: null,

                        //  Customer Info
                        client_id: 91,
                        client_type: 'company',
                        customer_ip_address: null,
                        customer_user_agent: null,
                        customer_note: 'Deliver before end of this week',
                        
                        billing_info: {
                            first_name: 'Julian',
                            last_name: 'Tabona',
                            address_1: 'Plot 4567, Extension 12',
                            country: 'Botswana',
                            province: 'South-East',
                            city: 'Gaborone',
                            postal_or_zipcode:"PO Box 456 AAH Masa",
                            email: 'brandontabona@gmail.com',
                            additional_email: 'brandontabona@yahoo.com',
                            phones: [
                                {
                                id: 164,
                                type: 'tel',
                                calling_code: {
                                    country: 'Botswana',
                                    calling_code: '267',
                                    flag: '<span class="flag-icon flag-icon-bw"></span>'
                                },
                                number: 3990960,
                                provider: null,
                                trackable_id: 91,
                                trackable_type: 'company',
                                created_by: 55,
                                company_branch_id: 46,
                                company_id: 49,
                                created_at: '2019-06-18 17:22:03',
                                updated_at: '2019-06-18 17:22:03',
                                }
                            ]
                            },
                            shipping_info: {
                                first_name: 'Bonolo',
                                last_name: 'Sesiane',
                                address_1: 'Plot 6721, Block 8',
                                country: 'Botswana',
                                province: 'South-East',
                                city: 'Gaborone',
                                email: 'bonolosesiane@gmail.com',
                                additional_email: 'bonolosesiane@yahoo.com',
                                postal_or_zipcode:"PO Box 623 CAA Masa"
                            },

                        //  Payment Info
                        payment_method: 'bank_deposit',
                        payment_method_title: 'Bank Deposit',
                        transaction_id: null,
                        date_paid: null,

                        //  Store, Company & Branch Info
                        store_id: 1,

                            mail: {
                                primaryEmails: ['brandontabona@gmail.com'],
                                ccEmails: [],
                                bccEmails: []
                                //subject: this.locaSubject,
                                //message: this.locaMessage
                            },
                            deliveryMethods: ['Email']

                 };

                console.log(orderData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/orders', orderData)
                    .then(({ data }) => {

                        //  Alert creation success
                        self.$Message.success('Order saved sucessfully!');

                    })         
                    .catch(response => { 

                        console.log('productSummaryWidget.vue - Error saving product...');
                        console.log(response);
                    });
            },
        },
        created(){
            if(this.resetToken){
                this.showLoginForm = true;
            }
        }
    };
  
</script>