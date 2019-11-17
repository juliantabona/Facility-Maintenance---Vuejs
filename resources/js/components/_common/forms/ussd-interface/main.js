
function getFormFields() {
    return {
        name: null,
        call_to_action: null,
        contact_us: null,
        about_us: null
    }
}

function getFormRules() {
    return {
        name: [
            { required: true, message: 'Enter name' }
        ],
        call_to_action: [
            { required: true, message: 'Enter call to action' }
        ],
        contact_us: [
            { required: true, message: 'Provide contact us information' }
        ],
        about_us: [
            { required: true, message: 'Provide about us information' }
        ]
    }
}

function getCustomErrorFields() {
    return getFormFields();
}

function resetCustomErrorFields(self) {
    self.customErrors = getCustomErrorFields();
}

export default {
    getFormRules,
    getFormFields,
    getCustomErrorFields,
    initiateUpdate(self) {
        
        var validation;

        //  Lets validate the current form
        self.$refs['formData'].validate((valid) => {
            //  If the form is not valid
            if(valid){
                //  Set validation to true
                validation = true;
            }else{
                //  Set validation to false
                validation = false;
            }    
        });

        /*  IF THIS FORM IS VALID LETS UPDATE THE USSD INTERFACE INFO */
        if(validation){
            
            //  Reset all our custom server errors
            resetCustomErrorFields(self);

            //  Start loader
            self.isUpdating = true;

            console.log('Attempt to update Ussd Interface using the following...');   

            //  Ussd Interface data to update
            let updateData = {
                name: self.formData.name,
                call_to_action: self.formData.call_to_action,
                contact_us: self.formData.contact_us,
                about_us: self.formData.about_us
            };

            console.log(updateData);

            if(self.ussdInterface['_links'].self.href){

                //  Use the api call() function located in resources/js/api.js
                return api.call('post', self.ussdInterface['_links'].self.href, updateData)
                    .then(({data}) => {

                        //  Reset the custom errors
                        self.customErrors = getCustomErrorFields();

                        //  Stop the loader
                        self.isUpdating = false;

                        //  Reset form fields so that the form does not show errors
                        setTimeout(()=>{
                            
                            self.$refs['formData'].resetFields();
                        
                            //  Update the form fields
                            self.updateFormFieldValues(data);

                            //  Store the current form data as the original form
                            self.storeOriginalFormData();


                        },10);

                        return data;
                        
                    })         
                    .catch(response => { 
                    
                        console.log(response);

                        //  Stop loader
                        self.isUpdating = false;     
        
                    });

            }

        }

        return false;

    }
}