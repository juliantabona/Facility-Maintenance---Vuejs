function getRegisterFormRules() {
    return {
        first_name: [
            { required: true, message: 'Enter first name', trigger: 'blur' }
        ],
        last_name: [
            { required: true, message: 'Enter last name', trigger: 'blur' }
        ],
        username: [
            { required: true, message: 'Enter username', trigger: 'blur' }
        ],
        email: [
            { required: true, message: 'Enter email', trigger: 'blur' }
        ],
        phone: [
            { required: true, message: 'Enter mobile number', trigger: 'blur' }
        ],
        address_1: [
            { required: true, message: 'Enter physical address', trigger: 'blur' }
        ],
        country: [
            { required: true, message: 'Select country', trigger: 'blur' }
        ],
        provience: [
            { required: true, message: 'Selct provience', trigger: 'blur' }
        ],
        city: [
            { required: true, message: 'Select city', trigger: 'blur' }
        ],
        postal_or_zipcode: [
            { required: true, message: 'Enter your postal or zip code', trigger: 'blur' }
        ],
        password: [
            { required: true, message: 'Enter password', trigger: 'blur' }
        ],
        confirm_password: [
            { required: true, message: 'Confirm password', trigger: 'blur' }
        ],
        }
}

function getRegisterFormFields() {
    return {
        first_name: null,
        last_name: null,
        username: null,
        email: null,
        phone: null,
        address_1: null,
        address_2: null,
        country: null,
        provience: null,
        city: null,
        postal_or_zipcode: null,
        password: null,
        confirm_password: null
    }
}

function getRegisterCustomErrorFields() {
    return getRegisterFormFields();
}

function resetCustomErrors(self) {
    self.customErrors = getRegisterCustomErrorFields();
}


export default {
    getRegisterFormFields,
    getRegisterFormRules,
    getRegisterCustomErrorFields,
    initiateRegister(self) {
        
        var validation;

        //  Lets validate the current form
        self.$refs['registerForm'].validate((valid) => {
            //  If the form is not valid
            if(valid){
                //  Set validation to true
                validation = true;
            }else{
                //  Set validation to false
                validation = false;
            }    
        });

        /*  IF THIS FORM IS VALID LETS SUBMIT THE REGISTER INFO */
        if(validation){
            
            //  Reset all our custom server errors
            resetCustomErrors(self);

            //  Start loader
            self.isRegistering = true;

            console.log('Register form using the following...');   

            //  If we have additional parameters
            if(_.size(self.additionalParams)){
                //  Foreach parameter
                for (var i = 0; i < self.additionalParams.length; i++) {
                    //  Get the name of the key e.g 'save_as_alternative_delivery'
                    var prop = Object.keys(self.additionalParams[i])[0];
                    //  Get the value pf the key e.g 'true'
                    var value = Object.values(self.additionalParams[i])[0];

                    self.$set(self.registerForm, prop, value);
                }

            }

            //  Register data to send
            let registerData = self.registerForm;

            console.log(registerData);

            if( self.route ){

                //  Use the api call() function located in resources/js/api.js
                return api.call('post', self.route, registerData)
                    .then(({data}) => {

                        //  Stop loader
                        self.isRegistering = false;
                        
                        //  If we have access to an authentication token
                        if( data.auth ){
                            //  Get the token and authenticated user
                            let token = data.auth.access_token;
                            let user = data.user;

                            //  Save token and user
                            //  Include token in all further axios api calls
                            auth.login(token, user);
                        }

                        return data;
                    })         
                    .catch(response => { 
                        console.log('components/_common/forms/register-user/main.vue - Error in register form...');
                        console.log(response);

                        //  Stop loader
                        self.isRegistering = false;     
                    
                        /* 
                        *  403: Forbidden. The user must activate their account first
                        *  422: Validation failed. Incorrect credentials
                        *  429: Too many login attempts.
                        */
                        if(response.status === 422 || response.status === 404){
                            var errors = (((response || {}).data || {}).errors || {});

                            //  If we have errors
                            if(_.size(errors)){
                                //  Foreach error
                                for (var i = 0; i < _.size(errors); i++) {
                                    //  Get the error key e.g 'identity', 'password'
                                    var prop = Object.keys(errors)[i];
                                    //  Get the error value e.g 'These credentials do not match our records.'
                                    var value = Object.values(errors)[i][0];
                                    //  Dynamically update the customErrors for Element UI to display the error to the user
                                    self.registerCustomErrors[prop] = value;
                                }

                            }
                        }

                        //  If account not activated
                        if(response.status === 403){

                            console.log('Activate account...');

                            //  Redirect to account activation page
                            var userId = response.data.user.id;
                            
                            self.$router.push({ path: 'activate-account', 
                                query: { 
                                    user_id: userId
                                }
                            });
                        }

                        return false;

                    });

            }
        }
        
        return false;

    }
}