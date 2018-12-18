<style>

    .activate-page .overlay {
        border-bottom: 3px solid #fff;
        position: relative;
        min-height: 120vh;
        background-size: cover;
    }

    .activate-page .overlay:before {
        position: absolute;
        z-index: 0;
        content: '';
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: .9;
        background: #0e1c3a;
    }
    
    .activate-box{
        position: relative;
        background: #fff;
        overflow: hidden;
        min-height: 400px;
    }

    .mail{
        margin: auto;
        max-height: 200px;
        display: block;
    }

</style>
<template>
    <div class="activate-page">

        <!-- activate Section -->
        <div class="overlay">
            <div class="container-fluid pt-2">
                <div class="row mt-5">
                    <div class="activate-box col-lg-6 mx-auto p-5">

                        <!-- Image -->
                        <img class="mail" src="/images/assets/icons/closed-envelope.png">

                        <!-- Verification Error Message -->
                        <Alert v-if="verificationError.msg" type="error" class="mt-3 mb-2" show-icon closable>
                            {{ verificationError.msg }}
                        </Alert>

                        <!-- Activation Email Sent Success Message -->
                        <Alert v-if="isEmailSentSuccess" type="success" class="mt-3 mb-2" show-icon closable>
                            {{ isEmailSentMsg }}
                        </Alert>

                        <div v-if = "!isVerifying">
                            <h2 class="font-weight-light mt-4 text-center">Activate Account</h2>
                            <p style="text-align:center;">Visit your email to activate your account!</p>

                            <!-- Resend Activation Email Button -->
                            <Button :loading="isSendingEmail" @click="resendActivationEmail" type="primary" class="d-block mt-4 mr-auto ml-auto" icon="ios-send">
                                <span v-if="!isSendingEmail">Resend Activation</span>
                                <span v-else>Resending Activation Email...</span>
                            </Button>
                        </div>

                        <!-- Verification Loader -->
                        <div v-show="isVerifying" class="mt-4">
                            <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
                            Verifying...
                        </div>
                    </div>  

                </div>
            </div>
        </div>
        
    </div>
</template>

<script>

export default {
    data() {
        return {
            token: null,
            isSendingEmail: false,
            isEmailSentSuccess: false,
            isEmailSentMsg: '',
            isVerifying: false,
            verificationError: {
                msg: '',
            }
        };
    },
    created () {
        // Verify the token if provided
        console.log('verification details');

        if(this.$route.query.token){
            this.token = this.$route.query.token;
        }
        
        this.verifyToken();
    },
    watch: {
        // call again the method if the route changes
        '$route': 'verifyToken'
    },
    // All slick methods can be used too, example here
    methods: {
        verifyToken () {

            const self = this;
            
            if(self.token){

                //  Start loader
                self.isVerifying = true;
                self.verificationError.msg = '';

                axios.post('/api/activate-account?token='+self.token)
                    .then(response => {
                        console.log(response.data);

                        //  Stop loader
                        self.isVerifying = false;

                        //  Navigate to the login page
                        //  With a query to indicate that login was successful

                        self.$router.push({ name: 'login', 
                            query: { 
                                activated: response.data.user.username
                            }
                        });
                    

                    }).catch(({response}) => { 
                        //console.log(response); 
                        console.log(JSON.stringify(response.data.message));

                        //  Stop loader
                        self.isVerifying = false;   
                            
                        if(response.status === 422){
                            //  Grab errors              
                            self.verificationError.msg = response.data.message;
                            

                        }

                    });
            }
        },
        resendActivationEmail() {
            const self = this;

            //  Start loader
            self.isSendingEmail = true;

            //  Reset the email sent status
            self.isEmailSentSuccess = false;

            let userId = this.$route.query.user_id || null ;

            if(userId){

                let activationData = {
                    user_id: userId
                };
                    
                axios.post('/api/resend-activation', activationData)
                    .then(response => {
                        console.log(response.data);
                    
                        //  Stop loader
                        self.isSendingEmail = false;

                        if(response.status == 200){
                            self.isEmailSentMsg = response.data.message;
                            self.isEmailSentSuccess = true;
                        }

                    })         
                    .catch(response => { 
                        if(response){
                            console.error(response);
                            //  Stop loader
                            self.isSendingEmail = false;     
                            //  Grab errors              
                            self.registerErrors = response.data.errors;
                        }
                    });

            }else{
                //  Navigate to the login Page
                self.$router.push('/login');
            }        
        
        }
    },
}
</script>