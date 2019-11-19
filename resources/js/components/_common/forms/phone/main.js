
function getFormFields() {
    return {
        token: null
    }
}

function getFormRules() {
    return {
        token: [
            { required: true, message: 'Enter verification code' }
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

        /*  IF THIS FORM IS VALID LETS VERIFY THE PHONE NUMBER */
        if(validation){
            
            //  Reset all our custom server errors
            resetCustomErrorFields(self);

            //  Start loader
            self.isVerifying = true;

            console.log('Attempt to verify phone number using the following...');   

            //  Ussd Interface data to update
            let updateData = {
                token: self.formData.token
            };

            console.log(updateData);

            if(self.phone['_links']['oq:verify'].href){

                //  Use the api call() function located in resources/js/api.js
                return api.call('post', self.phone['_links']['oq:verify'].href, updateData)
                    .then(({data}) => {

                        //  Reset the custom errors
                        self.customErrors = getCustomErrorFields();

                        //  Stop the loader
                        self.isVerifying = false;

                        //  Reset form fields so that the form does not show errors
                        setTimeout(()=>{
                            
                            self.$refs['formData'].resetFields();
                        
                            //  Update the form fields
                            self.updateFormFieldValues(data);

                            //  Store the current form data as the original form
                            self.storeOriginalFormData();

                            self.formHasChanged = self.checkIfFormHasChanged();


                        },10);

                        return data;
                        
                    })         
                    .catch(response => { 
                    
                        console.log(response);

                        //  Stop loader
                        self.isVerifying = false;     
        
                    });

            }

        }

        return false;

    }
}