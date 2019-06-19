<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSending" 
            :hideModal="hideModal"
            title="Add/Change Mobile Money Account"
            okText="Save" cancelText="Cancel"
            @on-ok="addNewAccount()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">
                
                <Tabs :animated="false" class="pt-3" :style="{ minHeight: '150px' }">

                    <!-- Select Existing Account -->
                    <TabPane label="Existing Accounts" :style="{ minHeight: '150px' }">
                        
                        <div :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                            <b class="d-block mb-1">Select Account</b>
                            <mobileMoneyAccountSelector
                                @updated="updateMobileMoneyChanges($event)">
                            </mobileMoneyAccountSelector>
                        </div>

                    </TabPane>

                    <TabPane label="Create Account" :style="{ minHeight: '150px' }">

                        <Row :gutter="20">
                            <Col :span="24">
                                <Alert show-icon>
                                    <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                    <template slot="desc">Enter your Mobile Money number and pin to activate payment directly to your account e.g) Using Orange Money, MyZaka, e.t.c .</template>
                                </Alert>
                                
                                <div :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                                    <span class="d-block mb-1" ><b>Phone Number</b></span>
                                    <!-- Mobile Money Phone Number editor -->
                                    <phoneInput class="mb-2"  
                                                :modelId="null" 
                                                :modelType="null" 
                                                :phones="[]" 
                                                :minLimit="1"
                                                :maxLimit="1"
                                                selectedType="mobile"
                                                :disabledTypes="['tel', 'fax']"   
                                                selectedServiceProvider="Orange"
                                                :disabledServiceProviders="['BeMobile']"                                                     
                                                :deletable="false"
                                                :hidedable="false"
                                                :editable="true"
                                                :showIcon="false" 
                                                onIcon="" offIcon="" 
                                                title="" onText="" offText="" 
                                                poptipMsg=""
                                                @updated="localInvoice.recurringSettings.deliveryPlan.sms.phones = $event">
                                    </phoneInput>

                                    <b class="d-block mt-2 mb-1">Pin (4 digits):</b>
                                    <el-input type="password" :maxlength="4" size="mini" class="password_input mb-1" placeholder="Enter Pin">
                                        <Icon slot="prepend" type="ios-lock-outline" :size="20"/>
                                    </el-input>
                                    <Button type="primary" class="float-right mt-2">
                                        <span>Connect</span>
                                        <Icon type="ios-log-in" :size="24" :style="{ marginTop: '-4px' }"/>
                                    </Button>
                                    <div class="clearfix"></div>
                                </div>
                            </Col>
                        </Row>

                    </TabPane>

                </Tabs>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Main Modal   */
    import mainModal from './main.vue';

    /*  Loaders   */
    import Loader from './../loaders/Loader.vue';

    /*  Inputs   */
    import phoneInput from './../../../components/_common/inputs/phoneInput.vue'; 

    /*  Selectors   */
    import mobileMoneyAccountSelector from './../../../components/_common/selectors/mobileMoneyAccountSelector.vue'; 

    export default {
        props: {},
        components: { mainModal, Loader, phoneInput, mobileMoneyAccountSelector },
        data(){
            return{
                hideModal: false,
                isSending: false
            }
        },
        methods: {
            updateMobileMoneyChanges(){

            },
            addNewAccount(){
                
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>