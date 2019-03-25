<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSaving" 
            :hideModal="hideModal"
            title="Update Lifecycle Stage"
            okText="Save" cancelText="Cancel"
            @on-ok="sendSms()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <div v-if="localSelectedStage.type.substring(0, 1) == 'p'">

                    <span class="font-weight-bold mb-1 d-block">Link payment to invoice:</span>
                    <invoiceSelector
                        :selectedInvoiceId="(localSelectedStage || {}).linked_invoice_id"
                        @updated:invoice="updatePaymentDetails($event)">
                    </invoiceSelector>
                    <div v-if="(localSelectedStage || {}).linked_invoice_id">
                        <span class="btn btn-link mb-2 float-right" @click="localSelectedStage.linked_invoice_id = null">Unlink Invoice</span>
                        <div class="clearfix"></div>
                    </div>

                    <div v-if="(localSelectedStage || {}).linked_invoice_id">

                    <Alert class="d-flex mt-2">
                        Amount Paid: {{ localSelectedStage.payment_amount | currency(((localSelectedStage.currency_type || {}).currency || {}).symbol) || '___' }}
                    </Alert>

                    </div>

                    <div v-else>

                        <span class="font-weight-bold mt-3 mb-1 d-block">Or complete manually:</span>
                        
                        <!-- Title -->
                            <el-input  
                                    v-if="!(localSelectedStage || {}).linked_invoice_id"
                                    type="text"  
                                    placeholder="Enter amount paid e.g) 2500.00"
                                    @keypress.native="isNumber($event)" :maxlength="10"
                                    v-model="(localSelectedStage || {}).payment_amount" 
                                    size="mini" class="mb-2" :style="{ maxWidth:'100%' }">
                            </el-input>
                        
                        <!-- Payment Method -->
                        <paymentMethodSelector
                            :selectedPaymentMethod="(localSelectedStage || {}).payment_method"
                            @updated="(localSelectedStage || {}).payment_method">
                        </paymentMethodSelector>

                    </div>

                </div>

                <div v-if="localSelectedStage.type.substring(0, 2) == 'js'">

                    <Row :gutter="20" class="mb-3">
                        
                        <Col :span="12">
                            <!-- Job Start Date -->
                            <span>
                                <strong class="text-dark">Start Date: </strong>
                                <Poptip word-wrap width="200" trigger="hover" :content="'Select the date this job started'">
                                    <el-date-picker 
                                        v-model="localSelectedStage.date_started" 
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
                                        v-model="localSelectedStage.time_started"
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
    import paymentMethodSelector from './../selectors/paymentMethodSelector.vue'; 
    import assignedStaffSelector from './../selectors/assignedStaffSelector.vue'; 

    /*  Buttons  */
    import basicButton from './../buttons/basicButton.vue';


    export default {
        props: {
            jobcard: {
                type: Object,
                default: null
            },
            selectedStage: {
                type: Object,
                default: null
            }
        },
        components: { mainModal, Loader, invoiceSelector, paymentMethodSelector, assignedStaffSelector, basicButton },
        data(){
            return{
                localJobcard: this.jobcard,
                localSelectedStage: this.selectedStage,
                hideModal: false,
                isSaving: false
            }
        },
        methods: {
            updatePaymentDetails(invoice){
                (this.localSelectedStage || {}).linked_invoice_id = invoice.id;
                (this.localSelectedStage || {}).currency_type = invoice.currency_type;
                (this.localSelectedStage || {}).payment_amount = invoice.grand_total_value;
                (this.localSelectedStage || {}).payment_method = invoice.payment_method;
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
            }
        }
    }
</script>