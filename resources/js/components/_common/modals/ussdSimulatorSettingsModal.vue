<style scoped>

    .el-form-item{
        margin-bottom: 0px;
    }

    .el-form-item >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }

    .el-form-item.phone-number >>> .el-form-item__content {
        line-height: inherit;
        display: inline-block;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }
</style>

<template>
    <!-- Modal -->
    <mainModal 
        v-bind="$props" 
        title="Settings"
        :isSaving="false" 
        :hideModal="hideModal"
        @on-ok="saveSettings()" 
        okText="Save" cancelText="Cancel"
        @visibility="$emit('visibility', $event)">

        <template slot="content">

            <!-- Email Subject -->
            <Row :gutter="20">
                
                <Col :span="24">

                    <el-form>

                        <!-- Ussd customer name -->
                        <el-form-item label="Customer Name" prop="customer_name" class="mb-2">
                            <el-input type="text" v-model="localSettings.customerName" size="small" style="width:100%" placeholder="Enter customer name"></el-input>
                        </el-form-item>

                        <!-- Ussd customer phone number -->
                        <el-form-item label="Mobile Number" prop="mobile_number" class="phone-number mb-3">

                            <Poptip trigger="hover" content="This mobile number will be used to send the order payment confirmation SMS" 
                                    word-wrap width="300">
                                
                                <vue-phone-number-input 
                                    v-model="localSettings.customerPhone.number" 
                                    default-country-code="BW"
                                    :preferred-countries="['BW']"
                                    @update="updatePhone"
                                />

                            </Poptip>

                        </el-form-item>

                        <!-- Create Button -->
                        <basicButton 
                            type="success" size="large" 
                            :disabled="!(phonePayload.isValid == true)"
                            :ripple="(phonePayload.isValid == true)"
                            @click.native="false">
                            <span>Save Changes</span>
                        </basicButton>

                    </el-form>

                </Col>

            </Row>

        </template>
        
    </mainModal>
</template>
<script>

    /*  Main Modal   */
    import mainModal from './main.vue';

    /*  Buttons  */
    import basicButton from './../buttons/basicButton.vue';

    export default {
        props:{
            settings: {
                type: Object,
                default: function(){
                    return {}
                }
            }
        },
        components: { mainModal, basicButton },
        data(){
            return{
                localSettings: this.settings,
                phonePayload: {},
                hideModal: false
            }
        },
        methods: {
            updatePhone(payload){
                this.phonePayload = payload;
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>