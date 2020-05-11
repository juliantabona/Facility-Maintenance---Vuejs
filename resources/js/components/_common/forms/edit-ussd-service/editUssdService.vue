<template>

    <div>

        <!-- Form -->
        <el-form :model="formData" :rules="formDataRules" ref="formData">

            <!-- Name -->
            <el-form-item prop="name" class="mb-3" :error="customErrors.name">
                <el-input type="text" v-model="formData.name" size="small" style="width:100%" placeholder="Enter service name">
                    <span slot="prepend" class="font-weight-bold text-dark">Name</span>
                </el-input>
            </el-form-item>

            <!-- Loader -->
            <Loader v-if="isCreating" :loading="true" type="text" class="text-left mt-2">{{ loaderText }}</Loader>
            
            <div v-if="!isCreating" class="clearfix">

                <!-- Save Button -->
                <basicButton 
                    class="float-right"
                    :customClass="btnClass" type="success" size="large" 
                    :disabled="!formHasChanged"
                    :ripple="formHasChanged"
                    @click.native="handleSave()">
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
            postURL:{
                type: String,
                default: null
            },
            ussdService:{
                type: Object,
                default: null
            },
            btnText: {
                type: String,
                default: 'Save Changes'
            },
            btnClass:{
                type: String,
                default: 'mt-3 d-block'
            },
            loaderText:{
                type: String,
                default: 'Saving service...'
            }
        },
        data(){
            return {
                customErrors: formHandle.getCustomErrorFields(),
                formDataRules: formHandle.getFormRules(),
                formData: formHandle.getFormFields(),
                localUssdService: this.ussdService,
                isFirstPhoneUpdate: true,
                formBeforeChange: null,
                isCreating: false,
            }
        },

        computed: {

            formHasChanged(){
                var now = _.cloneDeep(this.formData);
                var before = (this.formBeforeChange);

                var isNotEqual = !_.isEqual(now, before);

                return isNotEqual;
            },
            
            ussdServiceData(){
                return {
                    name: this.localUssdService.name
                }
            }

        },
        methods: {
            handleSave(){

                /*  Call the initiateSave() function from the formHandle in order to make an post request.
                 *  We must pass "this" current vue instance in order to access the vue data properties. The
                 *  initiateSave() function will handle all validation of the form and will return the 
                 *  updated Ussd Service information after a successful put request. We can use the then() hook 
                 *  to determine what to do next. In this case we update the parent vue component and 
                 *  pass the updated Ussd Service information.
                */
                var self = this;
                var response = formHandle.initiateSave(this);
                
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
            updateFormFieldValues()
            {
                var currentFormData = this.ussdServiceData;

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
            storeOriginalFormData(){
                //  Store the original form data
                this.formBeforeChange = _.cloneDeep(this.formData);
            },
        },
        created(){

            //  Update the form fields with the current Ussd Interface field values
            this.updateFormFieldValues();

            //  Store the original form data before editing
            this.storeOriginalFormData();

        }
    }

</script>