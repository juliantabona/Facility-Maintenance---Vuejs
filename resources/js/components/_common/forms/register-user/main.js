function getRegisterFormRules() {
    return {
        first_name: [
            { required: true, message: 'Enter your first name', trigger: 'blur' }
        ],
        last_name: [
            { required: true, message: 'Enter your last name', trigger: 'blur' }
        ],
        username: [
            { required: true, message: 'Enter your username', trigger: 'blur' }
        ],
        email: [
            { required: true, message: 'Enter your email', trigger: 'blur' }
        ],
        phone: [
            { required: true, message: 'Enter your mobile number', trigger: 'blur' }
        ],
        address_1: [
            { required: true, message: 'Enter your physical address', trigger: 'blur' }
        ],
        country: [
            { required: true, message: 'Select your country', trigger: 'blur' }
        ],
        provience: [
            { required: true, message: 'Selct your provience', trigger: 'blur' }
        ],
        city: [
            { required: true, message: 'Select your city', trigger: 'blur' }
        ],
        password: [
            { required: true, message: 'Enter your password', trigger: 'blur' }
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
        country: null,
        provience: null,
        city: null,
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

        //  Lets validate the current form
        self.$refs['registerForm'].validate((valid) => {
            //  If the form is not valid
            if(!valid){
                //  Return false
                return false;
            }
        });

        /*  IF THIS FORM IS VALID LETS SUBMIT THE LOGIN INFO */

        //  Reset all our custom server errors
        resetCustomErrors(self);

        //  Start loader
        self.isRegistering = true;

        console.log('Attempt to register using the following...');   

        //  Register data to send
        let registerData = self.registerForm;

        console.log(registerData);

        //  Use the api call() function located in resources/js/api.js
        return api.call('post', '/api/register', registerData)
            .then(({data}) => {

                //  Stop loader
                self.isRegistering = false;
                
                //  Get the token and authenticated user
                let token = data.auth.access_token;
                let user = data.user;

                //  Save token and user
                //  Include token in all further axios api calls
                auth.register(token, user);
            })         
            .catch(response => { 
                console.log('Register.vue - Error registering...');
                console.log(response);

                //  Stop loader
                self.isRegistering = false;     
            
                /* 
                *  403: Forbidden. The user must activate their account first
                *  422: Validation failed. Incorrect credentials
                *  429: Too many register attempts.
                */
                if(response.status === 422 || response.status === 429){
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

            });

    }
}