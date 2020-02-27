<style scoped>
        
    /*  Import HTML5 mockups of popular devices
        - https://marvelapp.github.io/devices.css
        - https://github.com/pixelsign/html5-device-mockups
    */
    @import '/css/devices.min.css';

    .homescreen-content{
        position: absolute;
        padding: 0% 5% 0%;
        bottom: 0;
        right: 0;
        left: 0;
        top: 0;
    }

    .ussd-content-container{
        position: absolute;
        padding: 30% 5% 0%;
        bottom: 0;
        right: 0;
        left: 0;
        top: 0;
    }

    .ussd-content-container >>> .ussd-heading{
        top: 2px;
        z-index: 2;
        color: #fff;
        padding: 4px 10px;
        position: relative;
        border-radius: 5px 5px 0 0;
    }

    .ussd-content-container >>> .online{
        background: #19be6b;
    }

    .ussd-content-container >>> .offline{
        background: #ed4014;
    }

    .ussd-content-container >>> .ussd-content{
        z-index:2;
    }

    .ussd-content-container >>> .overlay{
        background: #00000050;
        position: absolute;
        z-index:1;
        bottom: 0;
        right: 0;
        left: 0;
        top: 0;
    }

    .el-form-item{
        margin-bottom: 0px;
    }

    .el-form-item >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }

    .el-form-item.phone-number >>> .el-form-item__content {
        line-height: inherit;
        display: inline-block;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }
</style>

<template>

    <div class="marvel-device nexus5">
        <div class="top-bar"></div>
        <div class="sleep"></div>
        <div class="volume"></div>
        <div class="camera"></div>
        <div class="screen">

            <!-- Homescreen info goes here -->
            <div v-if="!showUssdContentModal" class="homescreen-content">
                
                <Row :gutter="12" class="pt-5">

                    <Col :span="24" class="pr-2 pl-2 pb-2">

                        <Card>

                            <span class="font-weight-bold d-block">Access My Mobile Store</span>

                        </Card>

                    </Col>

                    <Col :span="24" class="pr-2 pl-2 pb-2">

                        <Card>
                            
                            <span class="font-weight-bold d-block">Customer Simulator</span>

                            <p class="mt-2">
                                <span class="d-block">
                                    Inform your customers to Dial 
                                    <span class="font-weight-bold text-primary">{{ localUssdInterface.customer_access_code}}</span> 
                                     to visit your store on their mobile phones. Click <span class="font-weight-bold text-primary">Launch Simulator</span> 
                                     to see how your customers view your store.
                                </span>
                            </p>

                            <!-- Launch Simulator button -->
                            <div class="clearfix mt-2">

                                <Poptip trigger="hover" 
                                        class="float-right"
                                        placement="top-end"
                                        word-wrap width="250" 
                                        content="Launch Simulator to have a glimpse of what your customers see when visiting your store">
                                    <Button type="success" size="small" @click.native="launchCustomerUssdSimulator()">Launch Simulator</Button>
                                </Poptip>
                            </div>

                        </Card>

                    </Col>

                    <Col :span="24" class="pr-2 pl-2 pb-2">

                        <Card @click.native="showUssdContentModal = true">
                            
                            <span class="font-weight-bold d-block">Staff Simulator</span>
                            
                            <p class="mt-2">
                                <span class="d-block">
                                    Inform your team to Dial 
                                    <span class="font-weight-bold text-primary">{{ localUssdInterface.team_access_code }}</span> 
                                     to manage your store on their mobile phones. Click <span class="font-weight-bold text-primary">Launch Simulator</span> 
                                     to see how your team view your store.
                                </span>
                            </p>

                            <!-- Launch Simulator button -->
                            <div class="clearfix mt-2">
                                <Poptip trigger="hover" 
                                        class="float-right"
                                        placement="top-end"
                                        word-wrap width="250"
                                        content="Launch Simulator to have a glimpse of what your team members see when visiting your store">
                                    <Button type="success" size="small" @click.native="launchStaffUssdSimulator()">Launch Simulator</Button>
                                </Poptip>
                            </div>

                        </Card>

                    </Col>

                </Row>

            </div>

            <!-- Ussd info goes here -->
            <div v-show="showUssdContentModal" class="ussd-content-container">
                    
                <Poptip trigger="hover" :content="liveModeStatusMsg" word-wrap width="300">
                        
                    <span :class="'ussd-heading' + (localUssdInterface.live_mode ? ' online' : ' offline')">
                        <span>Your store is {{ (localUssdInterface.live_mode ? 'Online' : 'Offline') }}</span>
                    </span>

                </Poptip>

                <Card :bordered="false" class="ussd-content">

                    <div v-show="!isSendingUssdResponse">

                        <!-- Ussd response goes here -->
                        <div>
                            <p v-html="ussdResponse" style="white-space: pre-wrap;"></p>
                        </div>

                        <!-- Ussd reply button -->
                        <el-input 
                            ref="reply_input"
                            type="text" v-model="ussd_reply" size="small" 
                            class="w-100 mt-2" placeholder="Reply..."
                            @keyup.enter.native="handleUssdReply()"
                            @keyup.escape.native="closeUssdSimulator()">
                        </el-input>

                    </div>

                    <!-- Loader -->
                    <Loader v-show="isSendingUssdResponse" :loading="true" type="text" class="text-left mt-2">{{ ussdLoaderText }}</Loader>

                    <!-- Send/Cancel buttons -->
                    <div class="clearfix mt-2">
                                    
                        <Poptip v-show="!isSendingUssdResponse" trigger="hover" content="Press ENTER on keyboard" 
                                class="float-right" placement="bottom-end" word-wrap width="220">
                            <Button type="success" size="large" @click="handleUssdReply()">{{ ussd_reply ? 'Send' : 'Refresh'  }}</Button>
                        </Poptip>
                                    
                        <Poptip trigger="hover" content="Press ESC on keyboard" class="float-right mr-2" 
                                :placement="isSendingUssdResponse? 'bottom-end' : 'bottom'" word-wrap width="200">
                            <Button type="default" size="large" @click="closeUssdSimulator()">Cancel</Button>
                        </Poptip>
                    </div>

                </Card>

                <div class="overlay"></div>

            </div>

            <img src="/images/backgrounds/screensaver-01.jpg" style="width:100%;">

        </div>

    </div>

</template>

<script>

    /*  Loaders   */
    import Loader from './../loaders/Loader.vue'; 

    /*  Buttons  */
    import basicButton from './../buttons/basicButton.vue';

    export default {
        components: { Loader, basicButton },
        props: {
            postURL:{
                type: String,
                default: '/api/ussd/customer'
            },
            ussdInterface:{
                type: Object,
                default: null
            },
            ussdLoaderText:{
                type: String,
                default: 'USSD Code running'
            }
        },
        data(){
            return {
                ussd_text: '',
                ussd_reply: '',
                ussdResponse: '',
                localUssdInterface: null,
                //  phoneNumber: '+26700000000',
                showUssdContentModal: false,
                isSendingUssdResponse: false,
            }
        },
        watch: {
            /*  Keep track of changes on the ussdInterface.  */
            ussdInterface: {

                handler: function (val, oldVal) {

                    /*  Check if the the localProducts has changed  */
                    this.localUssdInterface = val;

                },
                deep: true

            }
        },
        computed: {
            liveModeStatusMsg(){
                if( this.localUssdInterface.live_mode == true ){
                    return 'This means that your Mobile Store is Online and can be accessed by your customers using their mobile phones.';
                }else{
                    return 'This means that your Mobile Store is Offline and can\'t be accessed by your customers. Turn on Live Mode to allow '+
                           'your customers to start paying for your goods/services';
                }
            }  
        },
        methods: {
            showUssdPopup(){
                this.showUssdContentModal = true;
                this.focusOnReplyInput();
            },
            hideUssdPopup(){
                this.showUssdContentModal = false;
            },
            launchCreatorUssdSimulator(){
                this.resetUssdSimulator();
                this.handleUssdReply();
                this.showUssdPopup();
            },
            launchCustomerUssdSimulator(){
                this.resetUssdSimulator();
                this.ussd_reply = '1*'+this.localUssdInterface.code;
                this.handleUssdReply();
                this.showUssdPopup();
            },
            launchStaffUssdSimulator(){
                this.resetUssdSimulator();
                this.ussd_reply = '1*'+this.localUssdInterface.code;
                this.handleUssdReply();
                this.showUssdPopup();
            },
            closeUssdSimulator(){
                this.resetUssdSimulator();
                this.hideUssdPopup();
            },
            focusOnReplyInput(){

                const self = this;

                this.$nextTick(() => {

                    //  Wait for 100 miliseconds
                    setTimeout(()=>{

                        //  Focus on the reply input field
                        self.$refs.reply_input.$refs.input.focus();

                    },100);

                });

            },
            resetUssdSimulator(){
                this.ussd_reply = '';
                this.ussd_text = '';
            },
            handleUssdReply() {
                /** If the the USSD Text (ussd_text) is empty, it means the user has not 
                 *  responded before, therefore this reply will be the first response. If  
                 *  this is infact the first response we will not append the asterix symbol
                 *  '*' which is used when separating multiple responses provided by the user.
                 *  
                 *  If the the USSD Text (ussd_text) is not empty, it means the user has 
                 *  responded before, therefore this reply will not be the first response. 
                 *  If this is infact the case, then we will append the asterix symbol '*'
                 *  which is used to separate our current responses from all other responses
                 *  provided by the user.
                 *  
                 *  Also check if the user has provided a reply using the reply input field.
                 *  If ther user input field is empty '' then the user is not actually sending
                 *  a response, but is only requesting that we refresh the current information
                 *  on screen. This makes sense since we don't actually change the (ussd_text),
                 *  We only resend our previous responses.
                 *   
                 */
                if( this.ussd_text == '' ){
                    this.ussd_text += this.ussd_reply;
                }else{
                    this.ussd_text += ( this.ussd_reply != '' ? '*'+this.ussd_reply : '');
                }

                this.ussd_reply = '';
                this.callUssdEndpoint();
            },
            callUssdEndpoint() {  

                var self = this;

                //  Store data
                let ussdData = {
                    text: this.ussd_text,
                    //  phoneNumber: this.phoneNumber,
                    testMode: true
                };

                //  Start loader
                self.isSendingUssdResponse = true;

                //  Use the api call() function located in resources/js/api.js
                return api.call('post', self.postURL, ussdData)
                    .then(({data}) => {

                        //  Stop loader
                        self.isSendingUssdResponse = false;

                        self.ussdResponse = data.substr(4);

                        /*  If the first 3 characters equal the text "END"  */
                        if( data.substr(3) == 'END' ){

                            /*  Close the simulator  */ 
                            self.closeUssdSimulator()

                        }else{ 

                            self.focusOnReplyInput();

                        }
                        
                    })         
                    .catch(response => { 
                    
                        console.log(response);

                        //  Stop loader
                        self.isSendingUssdResponse = false;     
        
                    });

            }

        },
        created() {
            this.localUssdInterface = this.ussdInterface;
        },

    }

</script>