//  LOGIN FUNCTIONS

function getLoginFormFields() {
    return {
        identity: null,
        password: null
    }
}


function getLoginFormRules() {
    return {
        identity: [
            { required: true, message: 'Enter your Email/Username', trigger: 'blur' }
        ],
        password: [
            { required: true, message: 'Enter your password', trigger: 'blur' }
        ]
    }
}

function getLoginCustomErrorFields() {
    return getLoginFormFields();
}

function resetLoginCustomErrorFields(self) {
    self.loginCustomErrors = getLoginCustomErrorFields();
}

//  FORGOT PASSWORD FUNCTIONS

function getForgotPasswordFormFields() {
    return {
        email: null,
        password: null,
        confirm_password: null
    }
}

function getForgotPasswordFormRules() {
    return {
        email: [
            { required: true, message: 'Enter your email', trigger: 'blur' }
        ],
        password: [
            { required: true, message: 'Enter your password', trigger: 'blur' }
        ],
        confirm_password: [
            { required: true, message: 'Confirm your password', trigger: 'blur' }
        ],
    }
}

function getForgotPasswordCustomErrorFields() {
    return getForgotPasswordFormFields();
}

function resetForgotPasswordCustomErrorFields(self) {
    self.forgotPasswordCustomErrors = getForgotPasswordCustomErrorFields();
}

function resetURL(self){
    //  Change the browser url to remove the reset token and email
    let query = Object.assign({}, self.$route.query);
    //  Delete the reset token
    delete query.resetToken;
    //  Delet the reset email
    delete query.resetEmail;
    //  Update the url
    self.$router.replace({ query });
}

export default {
    getLoginFormFields,
    getLoginFormRules,
    getLoginCustomErrorFields,
    getForgotPasswordFormFields,
    getForgotPasswordFormRules,
    getForgotPasswordCustomErrorFields,
    resetURL,
    initiateLogin(self) {
        
        var validation;

        //  Lets validate the current form
        self.$refs['loginForm'].validate((valid) => {
            //  If the form is not valid
            if(valid){
                //  Set validation to true
                validation = true;
            }else{
                //  Set validation to false
                validation = false;
            }    
        });

        /*  IF THIS FORM IS VALID LETS SUBMIT THE LOGIN INFO */
        if(validation){
            
            //  Reset all our custom server errors
            resetLoginCustomErrorFields(self);

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
                    
                    console.log('components/_common/login-user/main.js - Error loggin in...');
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

                    return false;

                });

        }

        return false;

    },
    initiateSendPasswordResetLink(self) {
        
        var validation;

        //  Lets validate the current form
        self.$refs['forgotPasswordForm'].validate((valid) => {
            //  If the form is not valid
            if(valid){
                //  Set validation to true
                validation = true;
            }else{
                //  Set validation to false
                validation = false;
            }    
        });

        /*  IF THIS FORM IS VALID LETS SUBMIT THE PASSWORD RESET INFO */
        if(validation){
            
            //  Reset all our custom server errors
            resetForgotPasswordCustomErrorFields(self);

            //  Start loader
            self.isSendingForgotPasswordEmail = true;

            console.log('Attempt to send password reset email using the following...');   

            //  Login data to send
            let resetData = {
                email: self.forgotPasswordForm.email,
                redirectTo: window.location.origin + "/" + self.$router.resolve(self.resetPasswordRedirectTo).href
            };

            console.log(resetData);

            //  Use the api call() function located in resources/js/api.js
            return api.call('post', '/api/password/email', resetData)
                .then(({data}) => {

                    //  Stop loader
                    self.isSendingForgotPasswordEmail = false;

                    console.log(data);
                    
                })         
                .catch(response => { 
                    
                    console.log('components/_common/login-user/main.js - Error sending password reset email...');
                    console.log(response);

                    //  Stop loader
                    self.isSendingForgotPasswordEmail = false;     
                
                    /* 
                    *  422: Validation failed.
                    */
                    if(response.status === 422){
                        
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
                                self.forgotPasswordCustomErrors[prop] = value;
                            }

                        }
                    }

                    return false;

                });

        }

        return false;

    },
    initiateSavePasswordReset(self) {
        
        var validation;

        //  Lets validate the current form
        self.$refs['forgotPasswordForm'].validate((valid) => {
            //  If the form is not valid
            if(valid){
                //  Set validation to true
                validation = true;
            }else{
                //  Set validation to false
                validation = false;
            }    
        });

        /*  IF THIS FORM IS VALID LETS SUBMIT THE PASSWORD RESET INFO */
        if(validation){
            
            //  Reset all our custom server errors
            resetForgotPasswordCustomErrorFields(self);

            //  Start loader
            self.isSavingForgotPassword = true;

            console.log('Attempt to save password using the following...');   

            //  Login data to send
            let resetData = {
                password: self.forgotPasswordForm.password,
                email: self.resetEmail,
                token: self.resetToken,
                redirectTo: window.location.origin + "/" + self.$router.resolve(self.resetPasswordRedirectTo).href
            };

            console.log(resetData);

            //  Use the api call() function located in resources/js/api.js
            return api.call('post', '/api/password/reset', resetData)
                .then(({data}) => {

                    //  Stop loader
                    self.isSavingForgotPassword = false;

                    console.log(data);
                    
                })         
                .catch(response => { 
                    
                    console.log('components/_common/login-user/main.js - Error sending password reset email...');
                    console.log(response);

                    //  Stop loader
                    self.isSavingForgotPassword = false;     
                
                    /* 
                    *  422: Validation failed.
                    */
                    if(response.status === 422 || response.status === 404){
                        
                        var errors = (((response || {}).data || {}).errors || {});

                        console.log('response.status: ' + response.status)
                        console.log('errors: ' + errors)

                        //  If we have errors
                        if(_.size(errors)){
                            
                            //  Foreach error
                            for (var i = 0; i < _.size(errors); i++) {
                                //  Get the error key e.g 'identity', 'password'
                                var prop = Object.keys(errors)[i];
                                //  Get the error value e.g 'These credentials do not match our records.'
                                var value = Object.values(errors)[i][0];
                                console.log('prop: ' + prop)
                                console.log('value: ' + value)
                                //  Dynamically update the customErrors for Element UI to display the error to the user
                                self.forgotPasswordCustomErrors[prop] = value;
                                console.log(self.forgotPasswordCustomErrors)
                            }

                        }
                    }

                    return false;

                });

        }

        return false;

    }
}