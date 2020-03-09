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
                    
                    <el-tabs value="general">

                        <el-tab-pane label="General" name="general">
                            
                            <!-- Ussd Interface Update Form -->
                            <ussdInterfaceUpdateForm
                                nameLabel="Store Name"
                                :ussdInterface="ussdInterface"
                                namePlaceholder="Enter store name"
                                @updateSuccess="handleMobileStoreUpdateSuccess($event)">
                            </ussdInterfaceUpdateForm>

                        </el-tab-pane>

                        <el-tab-pane label="Products" name="products">
                            
                            <!-- Ussd Interface Update Form -->
                            <mobileStoreProductsWidget
                                :store="store"
                                :ussdInterface="ussdInterface"
                                @updateSuccess="handleMobileStoreProductsUpdateSuccess($event)">
                            </mobileStoreProductsWidget>
                            
                        </el-tab-pane>

                        <el-tab-pane label="FAQ" name="faq">

                            <!-- What Is A Mobile Store -->
                            <Alert type="success" @click.native="showMobileStoreInfo = !showMobileStoreInfo" style="cursor:pointer;">
                                
                                <h6 :class="'font-weight-bold'+((showMobileStoreInfo) ? ' mb-2': ' p-0')">What is a mobile store?</h6>
                                
                                <template slot="desc">
                                    <div v-show="showMobileStoreInfo">
                                        <p>A Mobile Store is an online store but that can be accessed by anyone using any Mobile Phone, without any need
                                        for Internet Connection, Data Bundles or Mobile Apps. It allows your customers to buy and pay for any goods/services
                                        anywhere and anytime. To access your Mobile Store for example you have to dial e.g 
                                        <span class="font-weight-bold text-primary">{{ ussdInterface.customer_access_code}}</span>
                                        </p>
                                    </div>

                                </template>

                            </Alert>

                            <!-- How do customers access my mobile store? -->
                            <Alert type="success" @click.native="showCustomerAccessInstructions = !showCustomerAccessInstructions" style="cursor:pointer;">
                                
                                <h6 :class="'font-weight-bold'+((showCustomerAccessInstructions) ? ' mb-2': ' p-0')">How do customers access my mobile store?</h6>
                                
                                <template slot="desc">
                                    <div v-show="showCustomerAccessInstructions">
                                        <p>To allow customers to place orders and make payments on your Mobile Store using any Mobile Phone, without any need
                                        for Internet Connection, Data Bundles or Mobile Apps
                                        </p>

                                        <Divider orientation="left">Simply instruct your customers to</Divider>

                                        <p class="mt-2">
                                            <span class="d-block"><span class="font-weight-bold text-dark">Step 1: </span>Dial <span class="font-weight-bold text-primary">{{ ussdInterface.customer_access_code}}</span> to access your store</span>
                                            <span class="d-block"><span class="font-weight-bold text-dark">Step 2: </span>Select Option (1) to start shopping</span>
                                        </p>
                                    </div>

                                </template>

                            </Alert>

                            <!-- How does staff/management access my mobile store? -->
                            <Alert type="success" @click.native="showStaffAccessInstructions = !showStaffAccessInstructions" style="cursor:pointer;">
                                
                                <h6 :class="'font-weight-bold'+((showStaffAccessInstructions) ? ' mb-2': ' p-0')">How does staff/management access my mobile store?</h6>
                                
                                <template slot="desc">
                                    <div v-show="showStaffAccessInstructions">
                                        <p>To allow staff members or management to gain access to your Mobile Store using any Mobile Phone, without any need
                                        for Internet Connection, Data Bundles or Mobile Apps.
                                        </p>

                                        <Divider orientation="left">Simply instruct your team to</Divider>

                                        <p class="mt-2">
                                            <span class="d-block"><span class="font-weight-bold text-dark">Step 1: </span>Dial <span class="font-weight-bold text-primary">{{ ussdInterface.team_access_code }}</span> to access your store</span>
                                            <span class="d-block"><span class="font-weight-bold text-dark">Step 2: </span>Login using Email/Mobile number and Password</span>
                                        </p>
                                    </div>

                                </template>

                            </Alert>

                        </el-tab-pane>

                    </el-tabs>

                </Col>

                <!-- Mobile Store Access Info -->
                <Col v-if="ussdInterface" :span="12" class="pl-5">
                    
                    <ussdSimulator 
                        :showStaffSimulator="true"
                        :showCustomerSimulator="true"
                        :ussdInterface="ussdInterface" 
                        :default_ussd_reply="default_ussd_reply">
                    </ussdSimulator>

                </Col>
            </Row>
            
        </Card>

    </div>

</template>

<script>
    
    /*  Ussd Interface Update Form  */
    import ussdInterfaceUpdateForm from './../../../components/_common/forms/ussd-interface/ussdInterface.vue';

    /*  Mobile Store Products Widget  */
    import mobileStoreProductsWidget from './mobileStoreProductsWidget.vue';

    /*  Ussd Simulator  */
    import ussdSimulator from './../../../components/_common/simulators/ussdSimulator.vue';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue'; 

    export default {
        props: {
            store: {
                type: Object,
                default: null
            }
        }, 
        components: { 
            ussdInterfaceUpdateForm, mobileStoreProductsWidget, ussdSimulator, 
            basicButton, Loader
        },
        data(){
            return {
                
                accordion: '1',
                ussdInterface: null,
                isLoadingUssdInterface: false,
                localUssdInterfaceUrl: (this.store._links['oq:ussd_interface'] || {}).href,

                showCustomerAccessInstructions: false,
                showStaffAccessInstructions: false,
                showMobileStoreInfo: false
 
            }
        },
        computed:{
            default_ussd_reply(){

                return '1*' + (this.localUssdInterface || {}).code;

            }
        },
        methods: {
            handleMobileStoreUpdateSuccess(updatedUssdInterface){

                this.$Notice.success({
                    title: 'Updated successfully'
                });

                this.ussdInterface = updatedUssdInterface;

            },
            handleUpdateSuccess(ussdInterface){

                this.$Notice.success({
                    title: 'Updated successfully'
                });

            },
            fetchUssdInterface() {

                if( this.localUssdInterfaceUrl ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingUssdInterface = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting ussd interface...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', this.localUssdInterfaceUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingUssdInterface = false;

                            //  Store the customer data
                            self.ussdInterface = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingUssdInterface = false;

                            //  Console log Error Location
                            console.log('resources/js/widgets/store/show/ussdInterfaceWidget.vue - Error getting ussd interface...');

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