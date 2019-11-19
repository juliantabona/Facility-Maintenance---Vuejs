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

            <!-- Verification Code -->
            <el-form-item prop="token" class="mb-2" :error="customErrors.token">
                <el-input type="text" v-model="formData.token" size="large" style="width:100%" placeholder="Enter verification code"></el-input>
            </el-form-item>

            <!-- Loader -->
            <Loader v-if="isVerifying" :loading="true" type="text" class="text-left mt-2">{{ loaderText }}</Loader>
            
            <div v-if="!isVerifying" class="clearfix">

                <!-- Save Button -->
                <basicButton 
                    class="float-right"
                    :customClass="btnClass" type="success" size="large" 
                    :disabled="!formHasChanged || !isValidVerificationCode"
                    :ripple="formHasChanged && isValidVerificationCode"
                    @click.native="handleUpdate()">
                    <span>{{ btnText }}</span>
                </basicButton>

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
            phone:{
                type: Object,
                default: null
            },
            btnText: {
                type: String,
                default: 'Verify'
            },
            btnClass:{
                type: String,
                default: 'pr-5 pl-5'
            },
            loaderText:{
                type: String,
                default: 'Verifying...'
            }
        },
        data(){
            return {
                formHasChanged:false,
                formDataBeforeChange: null,
                formData: formHandle.getFormFields(),
                formDataRules: formHandle.getFormRules(),
                customErrors: formHandle.getCustomErrorFields(),
                isVerifying: false,
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
        computed: {
            isValidVerificationCode(){
                return (this.formData.token.length == 6);
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

                            if( data['verification_status'] == true ){

                                //  Notify the parent and pass the data
                                self.$emit('success', data);

                                this.$Notice.success({
                                    desc: 'Phone verified!'
                                });


                            }else{

                                this.$Notice.warning({
                                    desc: 'Incorrect verification code'
                                });

                            }

                        }
                    });
                }

            },
            updateFormFieldValues(currentFormData = this.phone)
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