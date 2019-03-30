<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSaving" 
            :hideModal="hideModal"
            title="Update Lifecycle Stage"
            okText="Save" cancelText="Cancel"
            @on-ok="updateLifecycle()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Loader -->
                <Loader v-if="isSaving" :loading="isSaving" type="text" class="text-left">Saving Lifecycle...</Loader>

                <div v-else>

                    <div v-if="localSelectedStage.type == 'payment'">
                        <Row>
                            <Col :span="(localSelectedStage || {}).linked_invoice_id ? 18 : 24">
                                <span class="font-weight-bold mb-1 d-block">Link payment to invoice:</span>
                                <invoiceSelector
                                    :selectedInvoiceId="(localSelectedStage || {}).linked_invoice_id"
                                    @updated:invoice="updatePaymentDetails($event)">
                                </invoiceSelector>
                            </Col>
                            <Col :span="(localSelectedStage || {}).linked_invoice_id ? 6 : 24">
                                <div v-if="(localSelectedStage || {}).linked_invoice_id">
                                    <span class="btn btn-link mb-2 float-right mt-4" @click="localSelectedStage.linked_invoice_id = null">Unlink Invoice</span>
                                    <div class="clearfix"></div>
                                </div>
                            </Col>
                        </Row>

                        <Row>
                            <Col :span="24">
                                <div v-if="(localSelectedStage || {}).linked_invoice_id">
                                    <h4 class="mb-2 border-top pt-3">Invoice #{{ localSelectedStage.linked_invoice_id }}</h4>
                                    <p>Amount Paid: {{ localSelectedStage.payment_amount | currency(((localSelectedStage.currency_type || {}).currency || {}).symbol) || '___' }}</p>
                                    <p>Payment Method: {{ localSelectedStage.payment_method || '___' }}</p>

                                </div>
                            </Col>
                        </Row>

                        <div v-if="!(localSelectedStage || {}).linked_invoice_id">

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

                    <div v-if="localSelectedStage.type == 'job_started'">

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

                    <div v-if="localSelectedStage.type == 'closed'">

                        <Row :gutter="20" class="mb-3">
                            
                            <Col :span="24">
                                <img src="/images/assets/icons/complete-stamp.png" :style="{ width: '150px' }" class="d-block ml-auto mr-auto mb-2">
                                <div>
                                    <p class="text-center">Save to mark this jobcard as "Closed"</p>
                                    <p class="text-center">This means the job has been completed successfully</p>
                                </div>
                            </Col>

                        </Row>

                    </div>

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
            },

            updateLifecycle(){

                var self = this;

                //  Start loader
                self.isSaving = true;

                console.log('Attempt to update jobcard lifecycle...');
                console.log( self.localSelectedStage );

                if( this.localSelectedStage ){

                    //  Stage data to save
                    let stageData = {
                        stage: this.localSelectedStage
                    };

                    //  Additional data to eager load along with the jobcard found
                    var connections = '?connections=lifecycle,priority,categories,costcenters,assignedStaff';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', '/api/jobcards/' + self.localJobcard.id + '/lifecycle/stages'+connections, stageData)
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

                            console.log('jobcardSummaryWidget.vue - Error updating jobcard lifecycle...');
                            console.log(response);
                        });

                }

            }
        }
    }
</script>