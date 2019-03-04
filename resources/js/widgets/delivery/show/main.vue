<style scoped>

    .input-fix-append >>> .el-input-group__append{
        padding:0;
        border:none;
    }

    .input-fix-append >>> .el-input-group__append .ivu-select-selection {
        height: 27px;
        padding-top: 2px;
        border-radius: 3px;
    }

    .delivery-nav-tabs >>> .ivu-tabs-nav .ivu-tabs-tab .ivu-icon{
        font-size: 30px !important;
        margin-top: -20px !important;
        margin-right: 20px !important;
    }

</style>

<template>

    <div>

        <Row v-show="stage == 1" :gutter="20" type="flex" justify="center">
            <Col :span="24">
                <p class="mt-2 mb-3 text-center font-weight-bold">Select 1 or more ways to send</p>
            </Col>
            <Col :span="10" v-for="(option, i) in mainOptions" :key="option.name" class="mb-2">

                <IconAndCounterCard :title="option.name" :icon="option.icon" class="mb-2" type="success"
                                    :showCheckMark="true"
                                    :checkMarkVisibility="localDeliveryMethods.includes(option.name)"
                                    @click.native="toggleOption(option.name, i)">
                </IconAndCounterCard>

            </Col>
            <Col :span="16" class="mt-4" :offset="4">
                <Button type="primary" class="d-block float-right" @click="goToNextStep()">Continue</Button>
            </Col>
        </Row>
        <Row v-show="stage == 2">

            <Col :span="24">

                <span>
                    <span class="d-inline-block">Delivery Methods:</span>
                    <span class="d-inline-block">
                        <CheckboxGroup v-model="localDeliveryMethods"
                            @on-change="$emit('updatedDeliveryMethods', $event)">
                            <Checkbox label="Email" :disabled="localDeliveryMethods.length == 1 && localDeliveryMethods[0] == 'Email'"></Checkbox>
                            <Checkbox label="Sms" :disabled="localDeliveryMethods.length == 1 && localDeliveryMethods[0] == 'Sms'"></Checkbox>
                        </CheckboxGroup>
                    </span>
                </span>

                <Tabs :animated="false" class="delivery-nav-tabs pt-3" :key="localDeliveryMethods.length">

                    <TabPane v-if="localDeliveryMethods.includes('Sms')" label="SMS Delivery" 
                                icon="ios-phone-portrait" class="mt-4">
                        <Row :gutter="20">
                            <Col v-if="showSmsPhoneImg" :span="10">
                                <img style="width: 100%;" src="/images/samples/phone_animation.png">
                            </Col>
                            <Col :span="showSmsPhoneImg ? 14: 24">

                                <Alert show-icon>
                                    <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                    <template slot="desc">Enter the client mobile number(s). When the invoice is due it will be sent to all active numbers via SMS. Only numbers set to "Active" will receive the sms.</template>
                                </Alert>

                                <Button type="primary" class="float-right mt-2" @click="isOpenSendTestSmsModal = !isOpenSendTestSmsModal">
                                    <span>Send Test SMS</span>
                                    <Icon type="ios-send-outline" :size="24" :style="{ marginTop: '-4px' }"/>
                                </Button>
                                <div class="clearfix"></div>

                                <b>Client Mobile Number(s)</b>
                                <!-- Client Phones editor -->
                                <phoneInput class="mb-2"  
                                            :modelId="localClientDetails.id" 
                                            :modelType="localClientDetails.model_type" 
                                            :phones="localDeliveryPhones" 
                                            :suggestedPhones="{ type: 'mobile', count: 1 }"
                                            :setStatus="true"
                                            :numberLimit="3"
                                            selectedType="mobile"
                                            :disabledTypes="['tel', 'fax']"                                                        
                                            :removable="true"
                                            :deletable="false"
                                            :hidedable="true"
                                            :editable="true"
                                            :removeDuplicates="true"
                                            :showIcon="true" 
                                            onIcon="ios-checkmark" offIcon="" 
                                            title="Active:" onText="Yes" offText="No" 
                                            poptipMsg="Turn on to send the sms to this number"
                                            @updated="updatePhones($event)">
                                </phoneInput>

                                <b class="d-block mt-3">Delivery Message:</b>
                                <p :style="{ padding: '20px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                                    {{ localDeliverySmsMessage }}
                                </p>

                                <span :class="(localDeliverySmsMessage.length <= 160 ? 'text-success': 'text-danger') + ' text-right float-right mt-2'">Sms Characters {{ localDeliverySmsMessage.length }}/160</span>
                                <span class="btn btn-link float-right">Edit Message</span>
                                <div class="clearfix"></div>
                            </Col>
                        </Row>
                    </TabPane>

                    <TabPane v-if="localDeliveryMethods.includes('Email')" label="Email Delivery" 
                                icon="ios-mail-outline" class="mt-4">
                        

                        <Button type="primary" class="float-right mt-2" @click="isOpenSendTestSmsModal = !isOpenSendTestSmsModal">
                            <span>Send Test Email</span>
                            <Icon type="ios-send-outline" :size="24" :style="{ marginTop: '-4px' }"/>
                        </Button>
                        <div class="clearfix"></div>

                        <!-- Email Address -->
                        <Row :gutter="20" class="mt-1">
                            <Col :span="24">
                                <span class="d-block font-weight-bold mb-1">Send to:</span>
                                <el-input placeholder="Recipient email e.g) example@gmail.com" 
                                          v-model="localDeliveryMailAddress" size="mini" class="mb-1"
                                          @input.native="$emit('updated:deliveryMailAddress', $event.target.value)">
                                </el-input>
                            </Col>
                        </Row>

                        <!-- Email Subject -->
                        <Row :gutter="20">
                            
                            <Col :span="24">
                                <span class="d-block font-weight-bold mt-2 mb-1">Subject:</span>
                                <el-input placeholder="Email Subject" v-model="localDeliveryMailSubject" 
                                        size="mini" class="input-fix-append mb-1"
                                        @input.native="$emit('updated:deliveryMailSubject', $event.target.value)">
                                    <shortcodeselector slot="append"
                                        :style="{ marginLeft: '5px' }"
                                        :shortcodes="localshortcodes" @selected="localDeliveryMailSubject += $event">
                                    </shortcodeselector>
                                </el-input>

                            </Col>
                        </Row>
                        
                        <!-- Email Message -->
                        <Row :gutter="20">
                            <Col :span="24">
                                <span class="d-inline-block font-weight-bold mt-3 mb-2">
                                    Message:
                                </span>
                                <shortcodeselector
                                    :shortcodes="localshortcodes" @selected="localDeliveryMailMessage += $event">
                                </shortcodeselector>
                                <froalaEditor :content.sync="localDeliveryMailMessage" 
                                              :height="200" :heightMax="300"
                                              @update:content="$emit('updated:deliveryMailMessage', $event)">
                                </froalaEditor>                    
                            </Col>
                        </Row>

                    </TabPane>

                </Tabs>

                <!-- 
                    MODAL TO SEND TEST SMS
                -->
                <sendTestSmsModal 
                    v-if="isOpenSendTestSmsModal" 
                    :message="localDeliverySmsMessage" 
                    :url="localTestSmsUrl"
                    @visibility="isOpenSendTestSmsModal = $event"
                    @sent="$emit('sent', $event)">
                </sendTestSmsModal>

            </Col>

        </Row>
    </div>

</template>
<script type="text/javascript">

    /*  Selectors  */
    import shortcodeselector from './../../../components/_common/selectors/shortcodeselector.vue';

    /*  Editors  */
    import froalaEditor from './../../../components/_common/wiziwigEditors/froalaEditor.vue';

    /*  Modals  */
    import sendTestSmsModal from './../../../components/_common/modals/sendTestSmsModal.vue';

    /*  Inputs   */
    import phoneInput from './../../../components/_common/inputs/phoneInput.vue'; 

    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    export default {
        components: { shortcodeselector, froalaEditor, sendTestSmsModal, phoneInput, IconAndCounterCard },
        props: {
            deliveryMethods: {
                type: Array,
                default: null
            },
            clientDetails: {
                type: Object,
                default: null
            },
            deliveryPhones: {
                type: Array,
                default: null
            },
            deliverySmsMessage: {
                type: String,
                default: ''
            },
            deliveryMailAddress: {
                type: String,
                default: ''
            },
            deliveryMailSubject: {
                type: String,
                default: ''
            },
            deliveryMailMessage: {
                type: String,
                default: ''
            },
            shortcodes: {
                type: Object,
                default: null
            },
            testSmsUrl: {
                type: String,
                default: ''
            },
            showSmsPhoneImg: {
                type: Boolean,
                default: true
            }
        },
        data(){
            return {
                localDeliveryMethods: this.deliveryMethods,
                localClientDetails: this.clientDetails,
                localDeliveryPhones: this.deliveryPhones,
                localDeliverySmsMessage: this.deliverySmsMessage,
                localDeliveryMailAddress: this.deliveryMailAddress,
                localDeliveryMailSubject: this.deliveryMailSubject,
                localDeliveryMailMessage: this.deliveryMailMessage,
                localshortcodes: this.shortcodes,
                localTestSmsUrl: this.testSmsUrl,

                isOpenSendTestSmsModal: false,

                stage: 1,
                mainOptions: [
                    {name:'Sms', icon:'ios-chatboxes-outline'},
                    {name:'Email', icon:'ios-mail-outline'}
                ]
    
            } 
        },
        methods: {
            updatePhones(phones){
                this.localDeliveryPhones = phones;
                this.$emit('updated:deliveryPhones', phones);
            },
            goToNextStep(){
                this.stage += 1;

                //  Alert parent on the changes of the stage
                this.$emit('updated:stage', this.stage);
            },
            toggleOption(name, index){
                if( this.localDeliveryMethods.includes(name) ){
                    
                    var index;
                    
                    for(var x = 0; x < this.localDeliveryMethods.length; x++){
                        if( this.localDeliveryMethods[x] == name ){
                            index = x;
                        }
                    }

                    if( this.localDeliveryMethods.length == 1 ){
                        this.$Notice.warning({
                            desc: 'You must have atleast one method selected'
                        });
                    }else{
                        this.localDeliveryMethods.splice(index, 1);
                    }
                    
                }else{
                    this.localDeliveryMethods.push(name);
                }
            }
        },
        created(){
            this.$emit('updated:stage', this.stage);
        }
    }
</script>
