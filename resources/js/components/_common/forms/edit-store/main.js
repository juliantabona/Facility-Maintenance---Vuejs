
function getFormFields() {
    return {
        name: '',
        mobile_number: ''
    }
}

function getFormRules() {
    return {
        name: [
            { required: true, message: 'Enter store name' }
        ],
        mobile_number: [
            { required: true, message: 'Enter mobile number' }
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
    initiateSave(self) {
        
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
            self.isCreating = true;

            console.log('Attempt to update store using the following...');   

            //  Store data
            let updateData = {
                name: self.formData.name,
                phone: {
                    calling_code: self.phonePayload.countryCallingCode,
                    number: self.phonePayload.nationalNumber,
                    type: 'mobile',
                }
            };

            console.log(updateData);

            if(self.postURL){

                //  Use the api call() function located in resources/js/api.js
                return api.call('put', self.postURL, updateData)
                    .then(({data}) => {

                        console.log('Updated Store');
                        console.log(data);  

                        //  Reset the custom errors
                        self.customErrors = getCustomErrorFields();

                        //  Stop the loader
                        self.isCreating = false;

                        //  Reset form fields so that the form does not show errors
                        setTimeout(()=>{
                            
                            self.$refs['formData'].resetFields();

                            //  Get the updated store
                            self.localStore = data;
                        
                            //  Update the form fields
                            self.updateFormFieldValues();

                            //  Store the current form data as the original form
                            self.storeOriginalFormData();

                            self.$Notice.success({
                                title: 'Store updated'
                            });


                        },10);

                        return data;
                        
                    })         
                    .catch(response => { 
                    
                        console.log(response);

                        //  Stop loader
                        self.isCreating = false;     
        
                    });

            }

        }

        return false;

    }
}