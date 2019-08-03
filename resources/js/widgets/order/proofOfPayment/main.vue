<style scoped>



</style>

<template>

    <Row :gutter="20" class="mt-3">

        <Col :span="20" :offset="2">
 
            <Row :gutter="20">

                <!-- Show when we have stores -->
                <Col v-if="isLoadingOrder" :span="8" :offset="8">
                
                    <!-- Loader -->
                    <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading order...</Loader>

                </Col>

                <!-- Show when we have the order -->
                <Col v-if="order && !isLoadingOrder" :span="24">
                    
                    <Row :gutter="20">
                        <Col v-if="order && !isLoadingOrder" :span="6">
                            <!--    Show the customers details   -->
                            <customerSummaryCard :customer="order.billing_info"></customerSummaryCard>
                        </Col>

                        <Col v-if="order && !isLoadingOrder" :span="12">
                            <div class="mb-3">
                                <Card>

                                    <!-- If the order status is curently pending payment -->
                                    <div v-if="isPendingPayment" class="mt-3 mb-3 pb-3 clearfix">
                                        <span class="font-weight-bold d-block mb-2">Hello, {{ order.billing_info.name || order.billing_info.first_name }}</span>
                                        <span class="d-block mb-4">
                                            Please attach a pdf/image as proof of payment to order #{{ order.id }} 
                                            then hit "Save" and we will review and approve your order.
                                        </span>

                                        <el-form :model="formData" label-position="top" label-width="100px">
                                            <!-- Image/Pdf Uploader -->
                                            <imageUploader 
                                                class="ml-4 mr-4"
                                                :allowUpload="true"
                                                :multiple="true"
                                                :docUrl=" order ? '/api/orders/'+order.id+'/documents?type=payment_proof' : null"
                                                :postData="{ 
                                                    modelId: order ? (order || {}).id : null,
                                                    modelType: 'order',
                                                    location:  'payment_proof', 
                                                    type: 'payment_proof',
                                                    replaceable: false
                                                }"
                                                uploadBtnText="Upload Image/Pdf"
                                                changeUplodBtnText="Change Image/Pdf"
                                                noUplodFoundText="No Image/Pdf Found"
                                                uploadMsg="Attach here..."
                                                :thumbnailColSpan="12"
                                                :thumbnailStyle="{}"
                                                @completedAllUploads="isDoneUploading = $event">
                                            </imageUploader>

                                            <span class="font-weight-bold mt-3 mb-1 d-block">Payment Details:</span>
                                            
                                            <!-- Payment Amount -->
                                            <el-input type="text" placeholder="Enter amount paid e.g) 2500.00" :maxlength="10"
                                                    v-model="formData.payment_amount" 
                                                    size="mini" class="mb-2" :style="{ maxWidth:'100%' }">
                                            </el-input>
                                            
                                            <!-- Payment Method -->
                                            <paymentMethodSelector
                                                :selectedPaymentMethod="formData.payment_method"
                                                @updated="formData.payment_method = $event">
                                            </paymentMethodSelector>
                                        
                                            <!-- Input For Other Custom Delivery Method -->
                                            <el-input v-if="formData.payment_method == 'Other'"
                                                        v-model="formData.other_payment_method" 
                                                        type="text" placeholder="Enter alternative payment method"
                                                        :maxlength="10" size="mini" class="mt-2 mb-2" :style="{ maxWidth:'100%' }">
                                            </el-input>
                                            
                                            <div class="clearfix">
                                                <!-- Save Button -->
                                                <basicButton 
                                                    v-if="isDoneUploading"
                                                    @click.native="submitAttachments()" 
                                                    type="success" :ripple="true"
                                                    size="large" class="float-right mt-3 mb-3">
                                                    <span>Save Changes</span>
                                                </basicButton>
                                            </div>
                                        </el-form>
                                    </div>

                                    <!-- If the order status is curently not pending payment -->
                                    <div v-else class="mt-3 mb-3">

                                        <h1 class="mb-3">Order #{{ order.id }} </h1>

                                        <!-- Order Lifecycle -->
                                        <orderLifecycle 
                                            :model="order" 
                                            :modelId="order.id" 
                                            :modelType="order.model_type"
                                            :canEditLifecycle="false"
                                            resourceName="Order" 
                                            @updated:lifecycle="updateOrder($event)">
                                        </orderLifecycle> 

                                        <span class="d-block mt-2">
                                            Please note that verification of your payment by our staff may take anytime between
                                            2-6 hours. You will be alerted via sms once your order is verified.
                                        </span>

                                    </div>
                                </Card>
                            </div>
                        </Col>

                    </Row>

                </Col>

                <!-- Show when we don't have the order -->
                <Col v-if="!order && !isLoadingOrder" :span="12" :offset="6">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix">
                        <h1 class="mb-3">Order not found!</h1>
                        <p class="mb-3" style="font-size:14px;">
                            Sorry, we could not find your order. Try refreshing your page again otherwise your order may have been removed.
                        </p>
                    </div>
                </Col>
            </Row>

        </Col>

    </Row>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';

    /*  Image Uploader  */
    import imageUploader from './../../../components/_common/uploaders/imageUploader.vue';

    /*  Selectors   */
    import paymentMethodSelector from './../../../components/_common/selectors/paymentMethodSelector.vue'; 

    /*  Lifecycles  */
    import orderLifecycle from './../../../components/_common/lifecycles/orderLifecycle.vue';

    import customerSummaryCard from './../../../components/_common/cards/customerSummaryCard.vue';

    import moment from 'moment';

    export default {
        components: { 
            basicButton, Loader, imageUploader, paymentMethodSelector, orderLifecycle, customerSummaryCard
        },
        data(){
            return {
                
                moment: moment,

                order: null,
                orderId: this.$route.params.orderId,
                isLoadingOrder: false,
                isSubmitting: false,
                isDoneUploading: false,
                formData: {
                    payment_amount: null,
                    payment_method: null,
                    other_payment_method: null
                }
            }
        },
        computed: {
            isPendingPayment(){
                return ((this.order || {}).current_lifecycle_stage  || {}).activity.pending_status || false;
            }
        },
        methods: {
            fetchOrder() {

                if(this.orderId){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingOrder = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting order...');

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', '/api/orders/'+this.orderId)
                        .then(({ data }) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingOrder = false;

                            //  order the order data
                            self.order = data;

                            //  Update the form data
                            self.formData.payment_amount = ((((self.order || {}).current_lifecycle_stage || {}).activity || {}).meta || {}).payment_amount,
                            self.formData.payment_method = ((((self.order || {}).current_lifecycle_stage || {}).activity || {}).meta || {}).payment_method

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingOrder = false;

                            //  Console log Error Location
                            console.log('dashboard/order/show/main.vue - Error getting order...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            submitAttachments(){
                const self = this;

                //  Start loader
                self.isSubmitting = true;

                console.log('Attempt to save proof of payment...');
                
                var submitData = new FormData();

                Object.keys(this.formData).map(key => {
                    //  If its an object and also not a file or blob e.g) Phones object. Then we need to stringify it
                    if(typeof self.formData[key] === "object" && !(typeof (self.formData[key] || {}).name == 'string')){
                        submitData.append(key, JSON.stringify(self.formData[key]) );
                    }else{
                        submitData.append(key, self.formData[key] );
                    }
                });

                console.log(submitData); 

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/orders/'+this.orderId+'/proof-of-payment', submitData)
                    .then(({data}) => {

                        console.log(data);

                        //  Stop loader
                        self.isSubmitting = false;
                        
                        //  Alert creation success
                        self.$Message.success('Saved sucessfully!');

                            //  order the order data
                            self.order = data;

                            //  Update the form data
                            self.formData.payment_amount = ((((self.order || {}).current_lifecycle_stage || {}).activity || {}).meta || {}).payment_amount,
                            self.formData.payment_method = ((((self.order || {}).current_lifecycle_stage || {}).activity || {}).meta || {}).payment_method

                    })         
                    .catch(response => { 
                        console.log('widgets/company/show/main.vue - Error saving company details...');
                        console.log(response);

                        //  Stop loader
                        self.isSubmitting = false;
    
                    });
            },
        },
        created(){
            this.fetchOrder();
        }
    };
  
</script>