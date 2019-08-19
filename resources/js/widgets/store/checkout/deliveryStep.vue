<style scoped>

    .el-form--label-top >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }

</style>

<template>

    <!--  Delivery Details -->
    <Row :gutter="12">
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
                                @click.native="updateCheckoutProgress(1)">
                                <span>Continue</span>
                                <Icon type="md-arrow-forward" class="ml-1" />
                            </basicButton>

                            <!-- Back button -->
                            <basicButton
                                class="float-right mb-2 ml-3" 
                                type="default" size="large" 
                                :ripple="false"
                                @click.native="updateCheckoutProgress(0)">
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

</template>

<script>

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Forms  */
    import checkoutRegisterForm from './../../../components/_common/forms/register-user/checkout-register.vue';

    export default {
        components: { 
            basicButton, checkoutRegisterForm
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

                localProducts: this.products,
                localCheckoutProgress: this.checkoutProgress,

                showDeliveryForm: false,
                showDeliveryFormLoader: false
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
            updateCheckoutProgress(proceed){
                if(proceed){

                    this.$emit('proceed');

                }else{

                    this.$emit('back');
                    
                }
            }
        },
        created(){
            
        }
    };
  
</script>