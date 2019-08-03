<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSaving" 
            :hideModal="hideModal"
            title="Update Status"
            okText="Save" cancelText="Cancel"
            @on-ok="updateLifecycle()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Loader -->
                <Loader v-if="isSaving" :loading="true" type="text" class="text-left">Updating status...</Loader>

                <div v-else>
                    
                    <template v-if="localSelectedStage.cancelled_status">

                        <!--  To set as cancelled -->
                        <Row v-if="localSelectedStage.cancelled_status">
                            <Col :span="24">  
                            
                                <span class="font-weight-bold mb-1 d-block">Reason for cancellation:</span>
                                <cancelReasonSelector
                                    :selectedReason="localSelectedStage.cancelled_status_reason"
                                    @updated="localSelectedStage.cancelled_status_reason = $event">
                                </cancelReasonSelector>

                                <!-- Input For Other Custom Skipping Method -->
                                <el-input v-if="localSelectedStage.cancelled_status_reason == 'Other'"
                                            v-model="localSelectedStage.other_cancelled_status_reason" 
                                            type="text" placeholder="Enter your reason"
                                            :maxlength="100" size="mini" class="mt-2 mb-2" :style="{ maxWidth:'100%' }">
                                </el-input>

                                <Checkbox 
                                    v-if="localSelectedStage.cancelled_status_reason == 'Other' && localSelectedStage.other_cancelled_status_reason"
                                    label="Save reason as an option for future orders" class="mt-2">
                                </Checkbox>

                            </Col>
                            
                        </Row>

                    </template>

                    <template v-else>
                        <!--  If this is a record payment stage -->
                        <div v-if="localSelectedStage.type == 'payment'">
                            
                            <!--  To set as paid -->
                            <Row v-if="!localSelectedStage.pending_status && !localSelectedStage.skip_status">
                                <Col :span="((localSelectedStage || {}).meta || {}).linked_invoice_id ? 18 : 24">   
                                    <span class="font-weight-bold mb-1 d-block">Link payment to invoice:</span>
                                    <invoiceSelector
                                        :ApiParams="{
                                            modelId: modelId,
                                            modelType: modelType,
                                            paginate: 0
                                        }"
                                        :selectedInvoiceId="((localSelectedStage || {}).meta || {}).linked_invoice_id"
                                        @updated:invoice="updatePaymentDetails($event)">
                                    </invoiceSelector>
                                </Col>
                                <Col :span="((localSelectedStage || {}).meta || {}).linked_invoice_id ? 6 : 24">
                                    <div v-if="((localSelectedStage || {}).meta || {}).linked_invoice_id">
                                        <span class="btn btn-link mb-2 float-right mt-4" @click="((localSelectedStage || {}).meta || {}).linked_invoice_id = null">Unlink Invoice</span>
                                        <div class="clearfix"></div>
                                    </div>
                                </Col>

                                <Col :span="24">
                                    <div v-if="((localSelectedStage || {}).meta || {}).linked_invoice_id">
                                        <h4 class="mb-2 border-top pt-3">Invoice #{{ localSelectedStage.meta.linked_invoice_id }}</h4>
                                        <p>Amount Paid: {{ localSelectedStage.meta.payment_amount | currency(((localSelectedStage.meta.currency_type || {}).currency || {}).symbol) || '___' }}</p>
                                        <p>Payment Method: {{ localSelectedStage.meta.payment_method || '___' }}</p>

                                    </div>

                                    <div v-if="!((localSelectedStage || {}).meta || {}).linked_invoice_id">

                                        <span class="font-weight-bold mt-3 mb-1 d-block">Or complete manually:</span>
                                        
                                        <!-- Title -->
                                        <el-input  
                                                v-if="!((localSelectedStage || {}).meta || {}).linked_invoice_id"
                                                type="text"  
                                                placeholder="Enter amount paid e.g) 2500.00"
                                                @keypress.native="isNumber($event)" :maxlength="10"
                                                v-model="((localSelectedStage || {}).meta || {}).payment_amount" 
                                                size="mini" class="mb-2" :style="{ maxWidth:'100%' }">
                                        </el-input>
                                        
                                        <!-- Payment Method -->
                                        <paymentMethodSelector
                                            :selectedPaymentMethod="((localSelectedStage || {}).meta || {}).payment_method"
                                            @updated="localSelectedStage.meta.payment_method = $event">
                                        </paymentMethodSelector>
                                    
                                        <!-- Input For Other Custom Delivery Method -->
                                        <el-input v-if="((localSelectedStage || {}).meta || {}).payment_method == 'Other'"
                                                    v-model="localSelectedStage.meta.other_payment_method" 
                                                    type="text" placeholder="Enter alternative payment method"
                                                    :maxlength="10" size="mini" class="mt-2 mb-2" :style="{ maxWidth:'100%' }">
                                        </el-input>

                                    </div>

                                </Col>
                            </Row>

                            <!--  To set as pending payment -->
                            <Row v-if="localSelectedStage.pending_status">
                                <Col :span="24">  
                                
                                    <span class="font-weight-bold mb-1 d-block">Reason for pending payment:</span>
                                    <pendingPaymentReasonSelector
                                        :selectedReason="localSelectedStage.pending_status_reason"
                                        @updated="localSelectedStage.pending_status_reason = $event">
                                    </pendingPaymentReasonSelector>

                                    <!-- Input For Other Custom Pennding Method -->
                                    <el-input v-if="localSelectedStage.pending_status_reason == 'Other'"
                                                v-model="localSelectedStage.other_pending_status_reason" 
                                                type="text" placeholder="Enter your reason"
                                                :maxlength="100" size="mini" class="mt-2 mb-2" :style="{ maxWidth:'100%' }">
                                    </el-input>

                                    <Checkbox 
                                        v-if="localSelectedStage.pending_status_reason == 'Other' && localSelectedStage.other_pending_status_reason"
                                        label="Save reason as an option for future orders" class="mt-2"></Checkbox>

                                </Col>
                                
                            </Row>

                            <!--  To set as skip payment -->
                            <Row v-if="localSelectedStage.skip_status">
                                <Col :span="24">  
                                
                                    <span class="font-weight-bold mb-1 d-block">Reason to skip payment:</span>
                                    <skipPaymentReasonSelector
                                        :selectedReason="localSelectedStage.skip_status_reason"
                                        @updated="localSelectedStage.skip_status_reason = $event">
                                    </skipPaymentReasonSelector>

                                    <!-- Input For Other Custom Skipping Method -->
                                    <el-input v-if="localSelectedStage.skip_status_reason == 'Other'"
                                                v-model="localSelectedStage.other_skip_status_reason" 
                                                type="text" placeholder="Enter your reason"
                                                :maxlength="100" size="mini" class="mt-2 mb-2" :style="{ maxWidth:'100%' }">
                                    </el-input>

                                    <Checkbox 
                                        v-if="localSelectedStage.skip_status_reason == 'Other' && localSelectedStage.other_skip_status_reason"
                                        label="Save reason as an option for future orders" class="mt-2"></Checkbox>

                                </Col>
                                
                            </Row>

                        </div>

                        <div v-if="localSelectedStage.type == 'delivery'">

                            <!--  To set as delivered -->
                            <Row v-if="!localSelectedStage.pending_status && !localSelectedStage.skip_status">
                                <Col :span="12">
                                    <!-- Delivery Date -->
                                    <span>
                                        <strong class="text-dark">Delivery Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="'Select the date this job started'">
                                            <el-date-picker 
                                                v-model="((localSelectedStage || {}).meta || {}).date_delivered" 
                                                type="datetime" placeholder="Select date" size="small"
                                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                                            </el-date-picker>
                                        </Poptip>
                                    </span>
                                </Col>

                                <Col :span="12">
                                    <!-- Delivery Date -->
                                    <span>
                                        <strong class="text-dark">Delivery Time: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="'Select the time the job started'">
                                            <el-time-picker 
                                                v-model="((localSelectedStage || {}).meta || {}).time_delivered"
                                                placeholder="Select time" size="small"
                                                format="HH:mm" value-format="HH:mm:ss">
                                            </el-time-picker>
                                        </Poptip>
                                    </span>
                                </Col>

                                <Col :span="24" class="mt-3">
                                    <Row>
                                        <Col :span="24">
                                            <!-- Select Delivery Method -->
                                            <span class="font-weight-bold mb-1 d-block">Select Delivery Method:</span>
                                            <courierMethodSelector
                                                :selectedCourier="((localSelectedStage || {}).meta || {}).courier"
                                                @updated="((localSelectedStage || {}).meta || {}).courier = $event">
                                            </courierMethodSelector>
                                        </Col>

                                        <Col :span="24" v-if="!((localSelectedStage || {}).meta || {}).courier == 'Other'">
                                            <!-- Input For Other Custom Delivery Reason -->
                                            <el-input  
                                                    v-model="((localSelectedStage || {}).meta || {}).other_courier" 
                                                    type="text" placeholder="Enter alternative delivery method"
                                                    :maxlength="10" size="mini" class="mb-2" :style="{ maxWidth:'100%' }">
                                            </el-input>
                                        </Col>

                                        <Col :span="24" v-if="((localSelectedStage || {}).meta || {}).courier" class="mt-3">
                                            <!-- Summary -->
                                            <span class="font-weight-bold mb-1 d-block">Summary:</span>
                                            <p v-if="((localSelectedStage || {}).meta || {}).courier == 'Other'">Delivered via <span class="font-weight-bold">{{ ((localSelectedStage || {}).meta || {}).other_courier }}</span> on {{ ((localSelectedStage || {}).meta || {}).date_delivered | moment('DD MMM YYYY') || '___' }} at {{ ((localSelectedStage || {}).meta || {}).time_delivered | moment('H:mm') || '___' }}</p>
                                            <p v-if="((localSelectedStage || {}).meta || {}).courier != 'Other'">Delivered via <span class="font-weight-bold">{{ ((localSelectedStage || {}).meta || {}).courier }}</span> on {{ ((localSelectedStage || {}).meta || {}).date_delivered | moment('DD MMM YYYY') || '___' }} at {{ ((localSelectedStage || {}).meta || {}).time_delivered | moment('H:mm') || '___' }}</p>
                                        </Col>  
                                    </Row>
                                </Col>
                            </Row>

                            <!--  To set as pending delivery -->
                            <Row v-if="localSelectedStage.pending_status">
                                <Col :span="24">  
                                
                                    <span class="font-weight-bold mb-1 d-block">Reason for pending delivery:</span>
                                    <pendingDeliveryReasonSelector
                                        :selectedReason="localSelectedStage.pending_status_reason"
                                        @updated="localSelectedStage.pending_status_reason = $event">
                                    </pendingDeliveryReasonSelector>

                                    <!-- Input For Other Custom Pennding Reason -->
                                    <el-input v-if="localSelectedStage.pending_status_reason == 'Other'"
                                                v-model="localSelectedStage.other_pending_status_reason" 
                                                type="text" placeholder="Enter your reason"
                                                :maxlength="100" size="mini" class="mt-2 mb-2" :style="{ maxWidth:'100%' }">
                                    </el-input>

                                    <Checkbox 
                                        v-if="localSelectedStage.pending_status_reason == 'Other' && localSelectedStage.other_pending_status_reason"
                                        label="Save reason as an option for future orders" class="mt-2">
                                    </Checkbox>

                                </Col>
                                
                            </Row>

                            <!--  To set as skipped delivery -->
                            <Row v-if="localSelectedStage.skip_status">
                                <Col :span="24">  
                                
                                    <span class="font-weight-bold mb-1 d-block">Reason to skip delivery:</span>
                                    <skipDeliveryReasonSelector
                                        :selectedReason="localSelectedStage.skip_status_reason"
                                        @updated="localSelectedStage.skip_status_reason = $event">
                                    </skipDeliveryReasonSelector>

                                    <!-- Input For Other Custom Skipping Reason -->
                                    <el-input v-if="localSelectedStage.skip_status_reason == 'Other'"
                                                v-model="localSelectedStage.other_skip_status_reason" 
                                                type="text" placeholder="Enter your reason"
                                                :maxlength="100" size="mini" class="mt-2 mb-2" :style="{ maxWidth:'100%' }">
                                    </el-input>

                                    <Checkbox 
                                        v-if="localSelectedStage.skip_status_reason == 'Other' && localSelectedStage.other_skip_status_reason"
                                        label="Save reason as an option for future orders" class="mt-2"></Checkbox>

                                </Col>
                                
                            </Row>

                        </div>

                        <div v-if="localSelectedStage.type == 'job_started'">

                            <Row :gutter="20" class="mb-3">
                                
                                <Col :span="12">
                                    <!-- Job Start Date -->
                                    <span>
                                        <strong class="text-dark">Start Date: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="'Select the date this job started'">
                                            <el-date-picker 
                                                v-model="((localSelectedStage || {}).meta || {}).date_started" 
                                                type="datetime" placeholder="Select date" size="small"
                                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                                            </el-date-picker>
                                        </Poptip>
                                    </span>
                                </Col>

                                <Col :span="12">
                                    <!-- Job Start Date -->
                                    <span>
                                        <strong class="text-dark">Start Time: </strong>
                                        <Poptip word-wrap width="200" trigger="hover" :content="'Select the time the job started'">
                                            <el-time-picker 
                                                v-model="((localSelectedStage || {}).meta || {}).time_started"
                                                placeholder="Select time" size="small"
                                                format="HH:mm" value-format="HH:mm:ss">
                                            </el-time-picker>
                                        </Poptip>
                                    </span>
                                </Col>

                            </Row>


                            <span class="font-weight-bold mb-1 d-block">Select assigned staff:</span>
                            <assignedStaffSelector
                                :selectedStaff="localJobcard.assigned_staff"
                                @updated:staff="localJobcard.assigned_staff = $event">
                            </assignedStaffSelector>

                        </div>

                        <div v-if="localSelectedStage.type == 'closed'">

                            <Row :gutter="20" class="mb-3">
                                
                                <Col :span="24">
                                    <img src="/images/assets/icons/complete-stamp.png" :style="{ width: '120px' }" class="d-block ml-auto mr-auto mb-2">
                                    <div>
                                        <p class="font-weight-bold mt-3 text-center">SAVE TO MARK AS COMPLETED</p>
                                    </div>
                                </Col>

                            </Row>

                        </div>
                    </template>

                </div>
                
            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Main Modal   */
    import mainModal from './main.vue';

    /*  Loaders   */
    import Loader from './../loaders/Loader.vue'; 

    /*  Selectors   */
    import invoiceSelector from './../selectors/invoiceSelector.vue'; 
    import pendingPaymentReasonSelector from './../selectors/pendingPaymentReasonSelector.vue'; 
    import skipPaymentReasonSelector from './../selectors/skipPaymentReasonSelector.vue'; 
    import pendingDeliveryReasonSelector from './../selectors/pendingDeliveryReasonSelector.vue'; 
    import skipDeliveryReasonSelector from './../selectors/skipDeliveryReasonSelector.vue'; 
    import cancelReasonSelector from './../selectors/cancelReasonSelector.vue'; 
    import courierMethodSelector from './../selectors/courierMethodSelector.vue'; 
    import paymentMethodSelector from './../selectors/paymentMethodSelector.vue'; 
    import assignedStaffSelector from './../selectors/assignedStaffSelector.vue'; 

    /*  Buttons  */
    import basicButton from './../buttons/basicButton.vue';


    export default {
        props: {
            model: {
                type: Object,
                default: null
            },
            postURL:{
                type: String,
                default: '/api/lifecycles/stage'
            },
            modelId:{
                type: Number,
                default: null
            },
            modelType:{
                type: String,
                default: null
            },
            selectedStage: {
                type: Object,
                default: null
            }
        },
        components: { 
            mainModal, Loader, invoiceSelector, pendingPaymentReasonSelector, pendingDeliveryReasonSelector,
            skipPaymentReasonSelector, skipDeliveryReasonSelector, cancelReasonSelector, 
            courierMethodSelector, paymentMethodSelector, 
            assignedStaffSelector, basicButton 
        },
        data(){
            return{
                localJobcard: this.model,
                localSelectedStage: this.selectedStage,
                hideModal: false,
                isSaving: false
            }
        },
        methods: {
            updatePaymentDetails(invoice){
                (this.localSelectedStage.meta || {}).linked_invoice_id = invoice.id;
                (this.localSelectedStage.meta || {}).currency_type = invoice.currency_type;
                (this.localSelectedStage.meta || {}).payment_amount = invoice.grand_total_value;
                (this.localSelectedStage.meta || {}).payment_method = invoice.payment_method;
            },
            closeModal(){
                this.hideModal = true;
            },
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            },

            updateLifecycle(){

                var self = this;

                //  Start loader
                self.isSaving = true;

                console.log('Attempt to update model lifecycle...');
                console.log( self.localSelectedStage );

                if( this.localSelectedStage && this.postURL && this.modelId && this.modelType ){

                    //  Stage data to save
                    let stageData = {
                        modelId: this.modelId,
                        modelType: this.modelType,
                        stage: this.localSelectedStage
                    };

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', this.postURL, stageData)
                        .then(({ data }) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isSaving = false;
                            
                            //  Alert creation success
                            self.$Message.success('Lifecycle updated sucessfully!');

                            self.$emit('updated', data);

                        })         
                        .catch(response => { 
                            //  Stop loader
                            self.isSaving = false;

                            console.log('modelSummaryWidget.vue - Error updating model lifecycle...');
                            console.log(response);
                        });

                }

            }
        }
    }
</script>