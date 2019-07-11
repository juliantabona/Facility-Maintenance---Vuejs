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
            <h1 class="tt-title-subpages noborder pt-3 pb-4">CHECKOUT</h1>
            <div class="row">
                <div class="col-sm-12 col-xl-8">
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
                                                                <Col :span="12" :offset="6">
                                                                    <!-- Account Summary -->
                                                                    <Card>
                                                                        
                                                                        <!-- Change/Edit Account buttons -->
                                                                        <div class="clearfix">
                                                                            <a href="#" @click="selectedAccountTab = 'register'" class="float-right">Edit</a>
                                                                            <span class="float-right ml-1 mr-1">|</span>
                                                                            <a href="#" @click="showLoginForm = true" class="float-right">Change Account</a>
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
                                                                    <span v-if="!user" class="d-block mb-3">Login or <a href="#" @click="selectedAccountTab = 'register'">Create Account</a></span>
                                                                    <span v-else class="d-block mb-3">
                                                                        Login
                                                                        <span>/</span>
                                                                        <a href="#" @click="showLoginForm = false">
                                                                            <Icon type="ios-contact-outline" :size="30" />
                                                                            <span>{{ user.first_name }} {{ user.last_name }}</span>
                                                                        </a> 
                                                                        <span>/</span>
                                                                        <a href="#" @click="selectedAccountTab = 'register'">Create Account</a>
                                                                    </span>
                                                                    
                                                                    <!-- Login Form -->
                                                                    <checkoutLoginForm
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
                                                            <span>Create account or <a href="#" @click="selectedAccountTab = 'login'">Login</a></span>
                                                        </Col>
                                                        <Col :span="24">
                                                            <!-- Register Form -->
                                                            <checkoutRegisterForm
                                                                @RegisterSuccess="handleRegisterSuccess()">
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
                                            
                                            <!-- Authenticated User Details -->
                                            <Row v-if="user" :gutter="12" class="mb-4">
                                                <Col :span="12" :offset="6">
                                                    <!-- Account Summary -->
                                                    <Card>
                                                        
                                                        <!-- Change/Edit Account buttons -->
                                                        <div class="clearfix">
                                                            <a href="#" @click="selectedAccountTab = 'register'" class="float-right">
                                                                <Icon type="ios-create-outline" :size="20" class="mr-1" />
                                                                <span>Edit</span>
                                                            </a>
                                                        </div>
                                                        
                                                        <!-- Receiver Name -->
                                                        <div class="pb-1 mt-1 mb-1" style="border-bottom: 1px dashed #d6d9dc;">
                                                            <span class="font-weight-bold">Deliver To: <span>{{ user.first_name }} {{ user.last_name }}</span></span>
                                                        </div>

                                                        <!-- Receiver Contacts -->
                                                        <div class="pb-1 mb-1" style="border-bottom: 1px dashed #d6d9dc;">
                                                            <span class="font-weight-bold d-block mb-1">Email: <span>{{ user.email }}</span></span>
                                                            <span class="font-weight-bold d-block mb-1">Mobile: <span>(+267) 759932xx</span></span>
                                                        </div>
                                                            
                                                        <!-- Receiver Address -->
                                                        <div class="pb-1 mb-1" style="border-bottom: 1px dashed #d6d9dc;">
                                                            <span class="font-weight-bold d-block mb-1">Address: <span>{{ user.address_1 }}</span></span>
                                                            <span class="font-weight-bold d-block mb-1">Country: <span>{{ user.country }}</span></span>
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

                                            <!-- Delivery Form -->
                                            <Row v-else :gutter="12">
                                                <Col :span="24">
                                                    <Alert>
                                                        <span class="font-weight-bold">Delivery Address</span>
                                                        <template slot="desc">Some products listed require delivery. Complete your information below and continue to payment</template>
                                                    </Alert>
                                                </Col>

                                                <Col :span="12">
                                                    <!-- First Name -->
                                                    <el-form-item label="First Name" prop="first_name" class="mb-2">
                                                        <el-input v-model="formData.first_name" size="small" style="width:100%" placeholder="Enter first name"></el-input>
                                                    </el-form-item>
                                                </Col>

                                                <Col :span="12">
                                                    <!-- Last Name -->
                                                    <el-form-item label="Last Name" prop="last_name" class="mb-2">
                                                        <el-input v-model="formData.last_name" size="small" style="width:100%" placeholder="Enter last name"></el-input>
                                                    </el-form-item>
                                                </Col>

                                                <Col :span="12">
                                                    <!-- Address 1 -->
                                                    <el-form-item label="Address 1" prop="address" class="mb-2">
                                                        <el-input v-model="formData.address" size="small" style="width:100%" placeholder="Enter primary address"></el-input>
                                                    </el-form-item>
                                                </Col>

                                                <Col :span="12">
                                                    <!-- Address 2 -->
                                                    <el-form-item label="Address 2 (Optional):" prop="address_2" class="mb-2">
                                                        <el-input v-model="formData.address_2" size="small" style="width:100%" placeholder="Enter secondary address"></el-input>
                                                    </el-form-item>
                                                </Col>

                                                <Col :span="12">
                                                    <!-- Country Selector -->
                                                    <el-form-item label="Country" prop="country" class="mb-2">
                                                        <countrySelector
                                                            :selectedCountry="formData.country"
                                                            @updated="updateCountryChanges($event)">
                                                        </countrySelector>
                                                    </el-form-item>
                                                </Col>

                                                <Col :span="12">
                                                    <!-- Cities Selector -->
                                                    <el-form-item label="City/Town" prop="city" class="mb-2">
                                                        <citySelector
                                                            :selectedCountry="formData.country"
                                                            :selectedCity="formData.city"
                                                            @updated="updateCityChanges($event)">
                                                        </citySelector>
                                                    </el-form-item>
                                                </Col>

                                                <Col :span="12">
                                                    <!-- Postal Code -->
                                                    <el-form-item label="Postal Code" prop="postal_or_zipcode" class="mb-2">
                                                        <el-input v-model="formData.postal_or_zipcode" size="small" style="width:100%" placeholder="Enter postal code"></el-input>
                                                    </el-form-item>
                                                </Col>

                                                <Col :span="24" class="clearfix">
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
                                                <template slot="desc">Select any prefered payment method to pay</template>
                                            </Alert>
                                            <Row :gutter="20">

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
                                                @click.native="$refs.vcsform.submit()">
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
                            <a class="btn-link" href="#"><i class="icon-e-19"></i>CONTINUE SHOPPING</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-4">
                    <div class="tt-shopcart-wrapper">
                        
                        <div class="tt-shopcart-box">
                            <table class="w-100">
                                <tbody>
                                    <tr v-for="(product, index) in [[1, 220], [2, 450], [3, 340]]" :key="index" class="mb-1">
                                        <td>
                                            <div class="tt-product-img">
                                                <img :src="'images/samples/dress_'+product[0]+'.jpg'" :data-src="'images/samples/dress_'+product[0]+'.jpg'" style="max-width:80px;max-height:80px;">
                                            </div>
                                        </td>
                                        <td>
                                            <span>Flared Shift Dress</span>
                                        </td>
                                        <td class="text-right">
                                            <div class="tt-price">P350</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tt-shopcart-box tt-boredr-large">
                            <table class="tt-shopcart-table01">
                                <tbody>
                                    <tr>
                                        <th>SUBTOTAL</th>
                                        <td>P350</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>GRAND TOTAL</th>
                                        <td>P350</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <a href="#" class="btn btn-border mb-3"><span class="icon icon-check_circle"></span>DOWNLOAD QUOTATION</a>
                        </div>
                    </div>
                </div>
            </div>
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

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            basicButton, toggleSwitch, Loader, phoneInput, citySelector, provinceSelector, countrySelector, IconAndCounterCard,
            checkoutLoginForm, checkoutRegisterForm
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
                    provience: null, 
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
            }
        }
    };
  
</script>