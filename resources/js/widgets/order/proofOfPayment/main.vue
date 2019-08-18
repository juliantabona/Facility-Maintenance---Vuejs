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
                <Col v-if="localOrder && !isLoadingOrder" :span="24">
                    
                    <Row :gutter="20">
                        <Col v-if="localOrder && !isLoadingOrder" :span="6">
                            <!--    Show the customers details   -->
                            <customerSummaryCard :customer="localOrder.billing_info"></customerSummaryCard>
                        </Col>

                        <Col v-if="localOrder && !isLoadingOrder" :span="12">
                            <div class="mb-3">
                                <Card>

                                    <!-- If the order status is curently pending payment -->
                                    <div v-if="isPendingPayment" class="mt-3 mb-3 pb-3 clearfix">
                                        <span class="font-weight-bold d-block mb-2">Hello, {{ localOrder.billing_info.name || localOrder.billing_info.first_name }}</span>
                                        <span class="d-block mb-4">
                                            Please attach a pdf/image as proof of payment to order #{{ localOrder.id }} 
                                            then hit "Save" and we will review and approve your order.
                                        </span>

                                        <el-form :model="formData" label-position="top" label-width="100px">
                                            <!-- Image/Pdf Uploader -->
                                            <imageUploader 
                                                class="ml-4 mr-4"
                                                :allowUpload="true"
                                                :multiple="true"
                                                :docUrl=" localOrder ? '/api/orders/'+localOrder.id+'/documents?type=payment_proof' : null"
                                                :postData="{ 
                                                    modelId: localOrder ? (localOrder || {}).id : null,
                                                    modelType: 'order',
                                                    location:  'payment_proof', 
                                                    type: 'payment_proof',
                                                    replaceable: false
                                                }"
                                                uploadBtnText="Upload Image/Pdf"
                                                changeUplodBtnText="Change Image/Pdf"
                                                noUploadFoundText="No Image/Pdf Found"
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

                                        <h1 class="mb-3">Order #{{ localOrder.id }} </h1>

                                        <!-- Order Lifecycle -->
                                        <orderLifecycle 
                                            :model="localOrder" 
                                            :modelId="localOrder.id" 
                                            :modelType="localOrder.model_type"
                                            :canEditLifecycle="false"
                                            resourceName="Order" 
                                            @updated:lifecycle="updateOrder($event)">
                                        </orderLifecycle> 

                                        <span class="d-block mt-2">
                                            Please note that verification of your payment by our staff may take anytime between
                                            2-6 hours. You will be alerted via sms once your order is verified.
                                        </span>

                                    </div>

                                    <Row :gutter="12">

                                        <Col :span="24">

                                            <!-- Loader -->
                                            <Loader v-if="isLoadingReviews" type="text" class="text-left d-inline-block p-0" theme="white">Loading review</Loader>

                                            <template v-if="!isLoadingReviews && isCompleted">
                                                <span class="font-weight-bold d-block border-top mb-2 mt-4 pt-3">Rate Our Service</span> 
                                                
                                                <div v-if="(localReviews || {}).length" class="tt-review-block">
                                                    <scrollBox class="border mb-4">
                                                        <div class="tt-review-comments pr-3 pl-3" style="max-height: 300px;">
                                                            <div v-for="(comment, i) in localReviews" class="tt-item">
                                                                <div class="tt-avatar">
                                                                    <img src="images/backgrounds/review-comments-img-01.jpg" :alt="(comment.user || {}).full_name">
                                                                </div>
                                                                <div class="tt-content">
                                                                    
                                                                    <Rate v-if="comment.rating_value"  :disabled="true"
                                                                        :value="comment.rating_value" class="tt-rating-stars" />

                                                                    <div class="tt-comments-info">
                                                                        <span class="username">by <span>{{ (comment.user || {}).full_name }}</span></span>
                                                                        <span class="time">on {{ comment.created_at | moment('DD MMM YYYY') || '___' }}</span>
                                                                    </div>
                                                                    <p>{{ comment.text }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </scrollBox>
                                                </div>

                                                <commentCreator 
                                                    v-if="!(localReviews || {}).length"
                                                    :urlParams="{
                                                            commentType: 'review',
                                                            orderId: localOrder.id
                                                        }"
                                                    requiredTextError="Enter your review"
                                                    btnText="SUBMIT REVIEW"
                                                    btnClass="w-100 mt-2"
                                                    placeholder="Enter your review"
                                                    loaderText="Adding review"
                                                    fieldType="textarea"
                                                    :canRate="true"
                                                    @commentSuccess="updateReviews($event)">
                                                </commentCreator>
                                            </template>
                                            
                                        </Col>

                                        <Col :span="24">

                                            <!-- Loader -->
                                            <Loader v-if="isLoadingMessages" type="text" class="text-left d-inline-block p-0" theme="white">Loading messages</Loader>

                                            <template v-if="!isLoadingMessages">

                                                <span v-if="(localMessages || {}).length" 
                                                      class="font-weight-bold d-block border-top mb-2 mt-4 pt-3">
                                                      Messages
                                                </span> 
                                                
                                                <div v-if="(localMessages || {}).length" class="tt-review-block">
                                                    <messageChatBox
                                                        :messages="localMessages"
                                                        :urlParams="{
                                                            commentType: 'message',
                                                            orderId: localOrder.id
                                                        }"
                                                        :showAsStaff="false"
                                                        :showContactList="false"
                                                        :showMessages="false"
                                                        :showReplyBox="!isCompleted"
                                                        :chatBoxStyle="{
                                                            maxHeight:'250px'
                                                        }">
                                                    </messageChatBox>
                                                </div>

                                                <template v-if="!(localMessages || {}).length">
                                                    <span class="font-weight-bold d-block border-top mb-2 mt-4 pt-3">Need Help?</span> 
                                                    <span class="d-block mt-2 mb-2">
                                                        Send us a message if you need more information or want to ask a question.
                                                    </span>
                                                </template>

                                            </template>

                                        </Col>

                                    </Row>

                                </Card>
                            </div>
                        </Col>

                    </Row>

                </Col>

                <!-- Show when we don't have the order -->
                <Col v-if="!localOrder && !isLoadingOrder" :span="12" :offset="6">
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

    /*  scrollBox  */
    import commentCreator from './../../../components/_common/forms/comment/comment.vue'; 

    /*  scrollBox  */
    import scrollBox from './../../../components/_common/scrollBox/scrollBox.vue'; 

    import messageChatBox from './../../store/overview/messageChatBox.vue';

    import moment from 'moment';

    export default {
        components: { 
            basicButton, Loader, imageUploader, paymentMethodSelector, orderLifecycle, customerSummaryCard,
            commentCreator, scrollBox, messageChatBox
        },
        data(){
            return {
                
                moment: moment,

                localOrder: null,
                orderId: this.$route.params.orderId,
                isLoadingOrder: false,
                isSubmitting: false,
                isDoneUploading: false,
                localMessages: [],
                localReviews: [],
                isLoadingReviews: false,
                isLoadingMessages: false,
                formData: {
                    payment_amount: null,
                    payment_method: null,
                    other_payment_method: null
                }
            }
        },
        computed: {
            isPendingPayment(){
                return ((this.localOrder || {}).current_lifecycle_stage  || {}).activity.pending_status || false;
            },
            isCompleted(){
                return ( ((this.localOrder || {}).current_lifecycle_main_status  || {}).title == 'Completed' ) || false;
            }
        },
        methods: {
            updateMessages(message){
                //  Add rview to top of existing messages
                this.localMessages.push(message);
            },
            updateReviews(review){
                //  Add rview to top of existing messages
                this.localReviews.push(review);
            },
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
                            self.localOrder = data;

                            //  Update the form data
                            self.formData.payment_amount = ((((self.localOrder || {}).current_lifecycle_stage || {}).activity || {}).meta || {}).payment_amount,
                            self.formData.payment_method = ((((self.localOrder || {}).current_lifecycle_stage || {}).activity || {}).meta || {}).payment_method

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
            fetchMessages() {

                var orderId = (this.$route.params.orderId);
                
                if(orderId){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingMessages = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting messages...');

                    var page = (this.$route.query.page) ? this.$route.query.page : 1;

                    var urlParams = {
                            orderId: orderId,
                            commentType: 'message',
                            page: page
                        }

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/comments', null, urlParams)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingMessages = false;

                            //  Store the product data
                            self.localMessages = (data || {}).data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingMessages = false;

                            //  Console log Error Location
                            console.log('dashboard/product/show/main.vue - Error getting messages...');

                            //  Log the responce
                            console.log(response);    
                        });
                }
            },
            fetchReviews() {

                var orderId = (this.$route.params.orderId);
                
                if(orderId){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingReviews = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting reviews...');

                    var page = (this.$route.query.page) ? this.$route.query.page : 1;

                    var urlParams = {
                            orderId: orderId,
                            commentType: 'review',
                            page: page
                        }

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/comments', null, urlParams)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingReviews = false;

                            //  Store the product data
                            self.localReviews = (data || {}).data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingReviews = false;

                            //  Console log Error Location
                            console.log('dashboard/product/show/main.vue - Error getting reviews...');

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
                            self.localOrder = data;

                            //  Update the form data
                            self.formData.payment_amount = ((((self.localOrder || {}).current_lifecycle_stage || {}).activity || {}).meta || {}).payment_amount,
                            self.formData.payment_method = ((((self.localOrder || {}).current_lifecycle_stage || {}).activity || {}).meta || {}).payment_method

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

            var self = this;

            //  Fetch the order
            this.fetchOrder().then( data => {

                 //  Fetch the order reviews
                self.fetchReviews();

                //  Fetch the order messages
                self.fetchMessages();

            });
        }
    };
  
</script>