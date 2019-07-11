function getLoginFormRules() {
    return {
            identity: [
                { required: true, message: 'Enter your Email/Username', trigger: 'blur' }
            ],
            password: [
                { required: true, message: 'Enter your password', trigger: 'blur' }
            ],
        }
}

function getLoginCustomErrors() {
    return {
        identity: null,
        password: null
    }
}

function resetCustomErrors(self) {
    self.customErrors = getLoginCustomErrors();
}


export default {
    getLoginFormRules,
    getLoginCustomErrors,
    initiateLogin(self) {

        //  Lets validate the current form
        self.$refs['loginForm'].validate((valid) => {
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
        self.isLoggingIn = true;

        console.log('Attempt to login using the following...');   

        //  Login data to send
        let loginData = {
            identity: self.loginForm.identity,
            password: self.loginForm.password
        };

        console.log(loginData);

        //  Use the api call() function located in resources/js/api.js
        return api.call('post', '/api/login', loginData)
            .then(({data}) => {

                //  Stop loader
                self.isLoggingIn = false;
                
                //  Get the token and authenticated user
                let token = data.auth.access_token;
                let user = data.user;

                //  Save token and user
                //  Include token in all further axios api calls
                auth.login(token, user);
            })         
            .catch(response => { 
                console.log('Login.vue - Error loggin in...');
                console.log(response);

                //  Stop loader
                self.isLoggingIn = false;     
            
                /* 
                *  403: Forbidden. The user must activate their account first
                *  422: Validation failed. Incorrect credentials
                *  429: Too many login attempts.
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
                            self.loginCustomErrors[prop] = value;
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