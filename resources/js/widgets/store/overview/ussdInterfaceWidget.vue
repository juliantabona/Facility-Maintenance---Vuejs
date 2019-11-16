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

    <div>

        <!-- Loader -->
        <Loader v-if="isLoadingUssdInterface" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading mobile store...</Loader>
        
        <!-- No mobile store message -->
        <Alert v-if="!isLoadingUssdInterface && !ussdInterface" type="info" :style="{ maxWidth: '250px' }" show-icon>No mobile store found</Alert>
        
        <!-- Mobile Store -->
        <Card v-if="!isLoadingUssdInterface && ussdInterface" class="mb-3">
            
            <Row :gutter="20">
                <Col :span="12">
                
                    <Divider orientation="left">My Mobile Store</Divider>

                    <!-- Login Form-->
                    <el-form ref="loginForm">
                        
                        <!-- identity -->
                        <el-form-item label="Store Name" prop="name" class="mb-2">
                            <el-input type="text" v-model="ussdInterface.name" size="small" style="width:100%" placeholder="Mobile store name"></el-input>
                        </el-form-item>
                        
                        <!-- identity -->
                        <el-form-item label="Call To Action" prop="call_to_action" class="mb-2">
                            <el-input type="text" v-model="ussdInterface.call_to_action" size="small" style="width:100%" placeholder="Mobile store call to action"></el-input>
                        </el-form-item>
                        
                        <!-- identity -->
                        <el-form-item label="About Us" prop="about_us" class="mb-2">
                            <el-input type="textarea" v-model="ussdInterface.about_us" size="small" style="width:100%" placeholder="Mobile store about us" :rows="3"></el-input>
                        </el-form-item>
                        
                        <!-- identity -->
                        <el-form-item label="Contact Us" prop="contact_us">
                            <el-input type="textarea" v-model="ussdInterface.contact_us" size="small" style="width:100%" placeholder="Mobile store contact us" :rows="3"></el-input>
                        </el-form-item>

                        <!-- Login Button -->
                        <basicButton
                            class="float-right mt-2 mb-2 ml-3 pl-3 pr-3" 
                            type="success" size="large" 
                            :ripple="true"
                            @click.native="handleLogin()">
                            <span>Save</span>
                        </basicButton>

                    </el-form>

                </Col>

                <!-- Mobile Store Access Info -->
                <Col :span="12">

                    <!-- Password Reset Success Alert -->
                    <Alert type="success" @click.native="showMobileStoreInfo = !showMobileStoreInfo" style="cursor:pointer;">
                        
                        <h6 :class="'font-weight-bold'+((showMobileStoreInfo) ? ' mb-2': ' p-0')">What is a mobile store?</h6>
                        
                        <template slot="desc">
                            <div v-show="showMobileStoreInfo">
                                <p>A Mobile Store is an online store but that can be accessed by anyone using any Mobile Phone, without any need
                                for Internet Connection, Data Bundles or Mobile Apps. It allows your customers to buy and pay for any goods/services
                                anywhere and anytime. To access a Mobile Store you have to dial a number e.g 
                                <span class="font-weight-bold text-primary">*175*{{ ussdInterface.code }}#</span>
                                </p>
                            </div>

                        </template>

                    </Alert>

                    <!-- Password Reset Success Alert -->
                    <Alert type="success" @click.native="showCustomerAccessInstructions = !showCustomerAccessInstructions" style="cursor:pointer;">
                        
                        <h6 :class="'font-weight-bold'+((showCustomerAccessInstructions) ? ' mb-2': ' p-0')">How do customers access my mobile store?</h6>
                        
                        <template slot="desc">
                            <div v-show="showCustomerAccessInstructions">
                                <p>To allow customers to place orders and make payments on your Mobile Store using any Mobile Phone, without any need
                                for Internet Connection, Data Bundles or Mobile Apps
                                </p>

                                <Divider orientation="left">Simply instruct your customers to</Divider>

                                <p class="mt-2">
                                    <span class="d-block"><span class="font-weight-bold text-dark">Step 1: </span>Dial <span class="font-weight-bold text-primary">*175*{{ ussdInterface.code }}#</span> to access your store</span>
                                    <span class="d-block"><span class="font-weight-bold text-dark">Step 2: </span>Select Option (1) to start shopping</span>
                                </p>
                            </div>

                        </template>

                    </Alert>

                    <!-- Password Reset Success Alert -->
                    <Alert type="success" @click.native="showStaffAccessInstructions = !showStaffAccessInstructions" style="cursor:pointer;">
                        
                        <h6 :class="'font-weight-bold'+((showStaffAccessInstructions) ? ' mb-2': ' p-0')">How does staff/management access my mobile store?</h6>
                        
                        <template slot="desc">
                            <div v-show="showStaffAccessInstructions">
                                <p>To allow staff members or management to gain access to your Mobile Store using any Mobile Phone, without any need
                                for Internet Connection, Data Bundles or Mobile Apps.
                                </p>

                                <Divider orientation="left">Simply instruct your team to</Divider>

                                <p class="mt-2">
                                    <span class="d-block"><span class="font-weight-bold text-dark">Step 1: </span>Dial <span class="font-weight-bold text-primary">*185*{{ ussdInterface.code }}#</span> to access your store</span>
                                    <span class="d-block"><span class="font-weight-bold text-dark">Step 2: </span>Login using Email/Mobile number and Password</span>
                                </p>
                            </div>

                        </template>

                    </Alert>

                </Col>
            </Row>
            
        </Card>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue'; 

    import moment from 'moment';

    export default {
        props: {
            ussdInterfaceUrl: {
                type: String,
                default: null
            }
        }, 
        components: { 
            basicButton, Loader
        },
        data(){
            return {
                //  Customers
                ussdInterface: null,
                isLoadingUssdInterface: false,
                ussdInterfaceUrl: this.ussdInterfaceUrl || this.$route.params.ussdInterfaceUrl,

                showCustomerAccessInstructions: false,
                showStaffAccessInstructions: false,
                showMobileStoreInfo: false
 
            }
        },
        methods: {
            fetchUssdInterface() {

                if( this.ussdInterfaceUrl ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingUssdInterface = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting ussd interface...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', this.ussdInterfaceUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingUssdInterface = false;

                            //  Customer the customer data
                            self.ussdInterface = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingUssdInterface = false;

                            //  Console log Error Location
                            console.log('resources/js/widgets/store/overview/ussdInterfaceWidget.vue - Error getting ussd interface...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){
            //  Fetch the Ussd Interface
            this.fetchUssdInterface();
        }
    };
  
</script>