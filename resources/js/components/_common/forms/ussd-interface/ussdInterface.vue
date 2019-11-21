<style scoped>
    
    .el-form-item{
        margin-bottom: 0px;
    }

    .live_mode_switch >>> .el-form-item__label {
        line-height: 2.9em !important;
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

        <!-- Form -->
        <el-form :model="formData" :rules="formDataRules" ref="formData">

            <Row :gutter="12">

                <Col :span="12">

                    <!-- Live Mode -->
                    <el-form-item label="Live Mode" prop="live_mode" class="live_mode_switch mb-2" :error="customErrors.live_mode">
                        <i-switch 
                            true-color="#13ce66" 
                            false-color="#ff4949" 
                            class="ml-1" size="large"
                            :disabled="isUpdating"
                            :value="formData.live_mode" 
                            :before-change="handleBeforeChange"
                            @on-change="formData.live_mode = $event">
                            <span slot="open">On</span>
                            <span slot="close">Off</span>
                        </i-switch>
                    </el-form-item>

                </Col>
                
                <Col :span="12">
                    
                    <!-- Loader -->
                    <Loader v-if="isUpdating" :loading="true" type="text" class="text-left mt-2 mb-2">{{ loaderText }}</Loader>

                    <div v-if="!isUpdating" class="clearfix">

                        <!-- Save Button -->
                        <basicButton 
                            class="float-right"
                            :customClass="btnClass" type="success" size="large" 
                            :disabled="!formHasChanged"
                            :ripple="formHasChanged"
                            @click.native="handleUpdate()">
                            <span>{{ btnText }}</span>
                        </basicButton>

                    </div>
                </Col>
            </Row>

            <div style="position:relative;">

                <!-- Updating Spinner  -->
                <Spin v-if="isUpdating" size="large" fix></Spin>

                <!-- Name -->
                <el-form-item :label="nameLabel" prop="name" class="mb-2" :error="customErrors.name">
                    <el-input type="text" v-model="formData.name" size="small" style="width:100%" :placeholder="namePlaceholder"></el-input>
                </el-form-item>
                
                <!-- Call To Action -->
                <el-form-item label="Call To Action" prop="call_to_action" class="mb-2" :error="customErrors.call_to_action">
                    <el-input type="text" v-model="formData.call_to_action" size="small" style="width:100%" placeholder="Mobile store call to action"></el-input>
                </el-form-item>
                
                <!-- About Us -->
                <el-form-item label="About Us" prop="about_us" class="mb-2" :error="customErrors.about_us">
                    <el-input type="textarea" v-model="formData.about_us" size="small" style="width:100%" placeholder="Mobile store about us" :rows="3"></el-input>
                </el-form-item>
                
                <!-- Contact Us -->
                <el-form-item label="Contact Us" prop="contact_us" :error="customErrors.contact_us">
                    <el-input type="textarea" v-model="formData.contact_us" size="small" style="width:100%" placeholder="Mobile store contact us" :rows="3"></el-input>
                </el-form-item>

            </div>

        </el-form>

    </div>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../loaders/Loader.vue'; 

    /*  Buttons  */
    import basicButton from './../../buttons/basicButton.vue';

    const formHandle = require('./main.js').default;

    export default {
        components: { Loader, basicButton },
        props: {
            ussdInterface:{
                type: Object,
                default: null
            },
            nameLabel: {
                type: String,
                default: 'Name'
            },
            namePlaceholder: {
                type: String,
                default: 'Enter name...'
            },
            btnText: {
                type: String,
                default: 'Save Changes'
            },
            btnClass:{
                type: String,
                default: 'pr-2 pl-2'
            },
            loaderText:{
                type: String,
                default: 'Updating...'
            }
        },
        data(){
            return {
                formHasChanged:false,
                formDataBeforeChange: null,
                formData: formHandle.getFormFields(),
                formDataRules: formHandle.getFormRules(),
                customErrors: formHandle.getCustomErrorFields(),
                isUpdating: false,
            }
        },

        watch: {
            /*  Keep track of changes on the formData.  */
            formData: {

                handler: function (val, oldVal) {

                    /*  Check if the the form data has changed  */
                    this.formHasChanged = this.checkIfFormHasChanged(val);

                },
                deep: true

            }
        },

        methods: {
            handleUpdate(){

                /*  Call the initiateUpdate() function from the formHandle in order to make 
                 *  an update request. We must pass "this" current vue instance in order to
                 *  access the vue data properties. The initiateUpdate() function will handle
                 *  all validation of the form and will return the updated information after
                 *  a successful update request. We can use the then() hook to determine what 
                 *  to do next. In this case we update the parent vue component and pass the
                 *  updated information.
                */
                var self = this;
                var response = formHandle.initiateUpdate(this);
                
                //  If we have a response
                if(response){
                    
                    //  Hook into the response
                    response.then( data => {
                        
                        //  If not false
                        if( data !== false ){

                            //  Notify the parent and pass the data
                            self.$emit('updateSuccess', data);

                        }
                    });
                }

            },
            updateFormFieldValues(currentFormData = this.ussdInterface)
            {
                //  Get the form fields
                var formFields = formHandle.getFormFields();

                //  Foreach form field
                for(var x = 0; x < _.size(formFields); x++){
                    
                    //  Get the current field key e.g name, description, e.t.c
                    var key = Object.keys(formFields)[x];

                    /*
                    *  Vue.set()
                    *  We use Vue.set to set a new object property. This method ensures the  
                    *  property is created as a reactive property and triggers view updates:
                    */

                    //  If we have form data, then overide the form field values
                    if(currentFormData){

                        //  Update the form data fields using the current form data
                        this.$set(this.formData, key, currentFormData[key]);

                    }
                }

            },
            checkIfFormHasChanged(formData){
                
                var currentForm = _.cloneDeep(formData || this.formData);
                var isNotEqual = !_.isEqual(currentForm, this.formDataBeforeChange);

                return isNotEqual;
            },
            storeOriginalFormData(){
                //  Store the original form data
                this.formDataBeforeChange = _.cloneDeep(this.formData);
            },
            handleBeforeChange () {
                return new Promise((resolve) => {
                    var title = (this.formData.live_mode == true) 
                            ? 'Go Offline'
                            : 'Go Online'

                    var msg = (this.formData.live_mode == true) 
                            ? 'Are you sure you want to put your Mobile Store Offline. Customers will not be able to access your mobile store. '+
                              'We will display a "Currently Offline" message to your customers.'
                            : 'Are you sure you want to put your Mobile Store Online. Customers will be able to access your mobile store, '+
                              'view products/services, place orders and make payments."'

                    this.$Modal.confirm({
                        title: title,
                        content: msg,
                        onOk: () => {
                            resolve();
                        }
                    });
                });
            }
        },
        created(){

            //  Update the form fields with the current Ussd Interface field values
            this.updateFormFieldValues();

            //  Store the original form data before editing
            this.storeOriginalFormData();

        }
    }

</script>