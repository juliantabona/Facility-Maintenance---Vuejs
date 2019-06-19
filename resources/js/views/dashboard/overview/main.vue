<style scoped>

    .wallet-tabs >>> .ivu-tabs-nav .ivu-tabs-tab {
        padding: 8px 4px;
    }

    .wallet-tabs >>> .ivu-tabs-nav .ivu-tabs-tab:last-child {
        margin-right: 0;
    }

    .cardPoptips >>> .ivu-poptip-rel {
        display: flow-root;
    }

.slide-enter-active {
   -moz-transition-duration: 0.5s;
   -webkit-transition-duration: 0.5s;
   -o-transition-duration: 0.5s;
   transition-duration: 0.5s;
   -moz-transition-timing-function: ease-in;
   -webkit-transition-timing-function: ease-in;
   -o-transition-timing-function: ease-in;
   transition-timing-function: ease-in;
}

.slide-leave-active {
   -moz-transition-duration: 0.5s;
   -webkit-transition-duration: 0.5s;
   -o-transition-duration: 0.5s;
   transition-duration: 0.5s;
   -moz-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
   -webkit-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
   -o-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
   transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
}

.slide-enter-to, .slide-leave {
   max-height: 100px;
   overflow: hidden;
}

.slide-enter, .slide-leave-to {
   overflow: hidden;
   max-height: 0;
}

  .ivu-badge >>> .ivu-badge-count {
    font-family: inherit !important;
    font-weight: 600 !important;
    min-width: 35px !important;
    height: 20px !important;
    z-index: 1 !important;
  }

    .tool-card >>> .ivu-card-body{
        padding:5px;
    }

    .tool-intro-header{
        padding: 10px 40px;
        border-bottom: 1px dashed #2d8cf0;
        text-shadow: 0px 2px 0px #f5f7f9;
        color: #2d8cf0;
        font-size: 25px;
    }

    .carousel-3d-slide {
        border-radius: 5px;
        border-color: #cccccc;
        background-color: #fff;
        -webkit-box-shadow: 1px 2px 1px #0000001a;
        box-shadow: 1px 2px 1px #0000001a;
    }

    .carousel-3d-slide >>> .image-box{
        background: #ffffff;
        border-radius: 0 0 100px;
    }

    .carousel-3d-slide.current >>> .image-box{
        position:relative;
        background: #2d8cf0;
    }

    .carousel-3d-slide.current >>> .image-box .curvy-strip{
        position: absolute;
        z-index: -1;
        width: 100px;
        height: 100px;
        left: 0;
        bottom: -100px;
        background: #2d8cf0;
    }

    .carousel-3d-slide.current >>> .image-box .curvy-strip:before{
        position: absolute;
        content: "";
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 0px 100px 0px 0px;
        -webkit-transform: rotate(270deg);
        transform: rotate(270deg);
        background: #fff;
    }

    .carousel-3d-slide >>> .image-box .tool-image{
        display: block;
        font-size: 25px;
        width: 80px;
        margin: 0px auto;
        padding: 50px 10px 10px 10px;
    }

    .carousel-3d-slide.current >>> .image-box .tool-image{
        filter: invert(100%) sepia(3%) saturate(1375%) hue-rotate(264deg) brightness(122%) contrast(99%);
    }

    .carousel-3d-slide >>> .tool-btn{
        transition:display 1s ease;
        display:none;
    }

    .carousel-3d-slide.current >>> .tool-btn{
        display:block;
    }

    .carousel-3d-slide >>> .heading{
        font-size: 20px;
    }

    .carousel-3d-slide >>> .body{
        margin: 0 30px;
    }

    .intergration-card >>> .image-box{
        display: block;
        width: 80px;
        margin: 20px auto;
        padding: 15px;
        background: #eeeeee;
        border-radius: 50%;
    }

    .intergration-card >>> .image-box.premium-image-box{
        background: #ff0c91;
    }

    .intergration-card >>> .image-box.subcription-image-box{
        background: #ff6d0c;
    }

    .intergration-card >>> .image-box.mobile-money-image-box{
        background: #19be6b;
    }

    .intergration-card >>> .image-box .tool-image{
        width: 100%;
    }

    .intergration-card >>> .image-box .tool-image{
        filter: invert(100%) sepia(3%) saturate(1375%) hue-rotate(264deg) brightness(122%) contrast(99%);
    }

    .intergration-card >>> .heading{
        font-size: 20px;
    }

    .intergration-card >>> .body{
        margin: 0 10px;
    }

</style>

<template>

    <Row :gutter="20">
        
        <Col span="18">
    
            <Card class="tool-card mb-2">
                <Row :gutter="20" class="mr-0 ml-0">
                    <Col :span="10" :offset="7" class="mt-0 mb-0">
                        <h3 class="tool-intro-header text-center mt-2 ml-auto mr-auto">Lets Get Started</h3>
                    </Col>
                    <Col :span="24">

                        <carousel-3d 
                            :controls-visible="true"
                            :autoplay="true" 
                            :autoplay-timeout="5000">
                            <slide :index="0">
                                <div class="image-box">
                                    <div class="curvy-strip"></div>
                                    <img src="/images/assets/icons/paper-and-calculator.svg" class="tool-image">
                                </div>
                                <span class="d-block font-weight-bold mt-2 text-center heading" style=" font-size: 18px; ">Quotations</span>
                                <span data-v-af064eb6="" class="d-block mt-2 text-center body">Create, manage and send quotations via sms/email to customers</span>
                                <basicButton customClass="tool-btn mt-3 ml-auto mr-auto" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="false"
                                                @click.native="$router.push({name:'create-quotation'})">
                                    + Create Quotation
                                </basicButton>
                            </slide>
                            <slide :index="1">
                                <div class="image-box">
                                    <div class="curvy-strip"></div>
                                    <img src="/images/assets/icons/hand-payment.svg" class="tool-image">
                                </div>
                                <span class="d-block font-weight-bold mt-2 text-center heading" style=" font-size: 18px; ">Invoices</span>
                                <span data-v-af064eb6="" class="d-block mt-2 text-center body">Send invoices via sms/email and get paid via mobile money transfers</span>
                                <basicButton customClass="tool-btn mt-3 ml-auto mr-auto" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="false"
                                                @click.native="$router.push({name:'create-quotation'})">
                                    + Create Invoice
                                </basicButton>
                            </slide>
                            <slide :index="2">
                                <div class="image-box">
                                    <div class="curvy-strip"></div>
                                    <img src="/images/assets/icons/calendar.svg" class="tool-image">
                                </div>
                                <span class="d-block font-weight-bold mt-2 text-center heading" style=" font-size: 18px; ">Appointments</span>
                                <span data-v-af064eb6="" class="d-block mt-2 text-center body">Set appointments that will automatically remind customers via sms/email</span>
                                <basicButton customClass="tool-btn mt-3 ml-auto mr-auto" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="false"
                                                @click.native="$router.push({name:'create-quotation'})">
                                    + Create Appointment
                                </basicButton>
                            </slide>
                            <slide :index="3">
                                <div class="image-box">
                                    <div class="curvy-strip"></div>
                                    <img src="/images/assets/icons/shopping-cart.svg" class="tool-image">
                                </div>
                                <span class="d-block font-weight-bold mt-2 text-center heading" style=" font-size: 18px; ">Ecommerce</span>
                                <span data-v-af064eb6="" class="d-block mt-2 text-center body">Add products and get customers shopping and paying for your goods</span>
                                <basicButton customClass="tool-btn mt-3 ml-auto mr-auto" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="false"
                                                @click.native="$router.push({name:'create-quotation'})">
                                    + Create Product
                                </basicButton>
                            </slide>
                            <slide :index="4">  
                                <div class="image-box">
                                    <div class="curvy-strip"></div>
                                    <img src="/images/assets/icons/checklist.svg" class="tool-image">
                                </div>
                                <span class="d-block font-weight-bold mt-2 text-center heading" style=" font-size: 18px; ">Jobcards</span>
                                 <span data-v-af064eb6="" class="d-block mt-2 text-center body">Track jobs and keep customers conveniently informed via sms/email</span>
                                <basicButton customClass="tool-btn mt-3 ml-auto mr-auto" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="false"
                                                @click.native="$router.push({name:'create-quotation'})">
                                    + Create Jobcard
                                </basicButton>
                            </slide>
                        </carousel-3d>
                        
                    </Col>
                </Row>
            </Card>

            
            <Row :gutter="20" class="mt-3 mr-0 ml-0">
                <Col :span="8" class="mt-0 mb-0 pl-0">
                    <Card class="intergration-card">

                        <div class="image-box premium-image-box">
                            <img src="/images/assets/icons/rocket-blast.svg" class="tool-image">
                        </div>
                        <span class="d-block font-weight-bold mt-2 text-center heading" style=" font-size: 18px; ">Go Premium</span>
                        <span data-v-af064eb6="" class="d-block mt-2 text-center body">Get access to unlimited communication, reporting, suggestions to boast sales and customer satisfaction.</span>
                        <basicButton customClass="d-block mt-3 ml-auto mr-auto" :style="{ position:'relative' }"
                                        type="success" size="small" 
                                        :ripple="false"
                                        @click.native="$router.push({name:'create-quotation'})">
                            Get Premium
                        </basicButton>

                    </Card>
                </Col>
                <Col :span="8" class="mt-0 mb-0">
                    <Card class="intergration-card">

                        <div class="image-box subcription-image-box">
                            <img src="/images/assets/icons/message-chat.svg" class="tool-image">
                        </div>
                        <span class="d-block font-weight-bold mt-2 text-center heading" style=" font-size: 18px; ">Mobile Subscriptions</span>
                        <span data-v-af064eb6="" class="d-block mt-2 text-center body">Subcribe to get instant alerts, reports and insigts directly to your phone</span>
                        <basicButton customClass="d-block mt-3 ml-auto mr-auto" :style="{ position:'relative' }"
                                        type="success" size="small" 
                                        :ripple="false"
                                        @click.native="$router.push({name:'create-quotation'})">
                            Subscribe
                        </basicButton>

                    </Card>

                </Col>
                <Col :span="8" class="mt-0 mb-0 pr-0">
                    <Card class="intergration-card">

                        <div class="image-box mobile-money-image-box">
                            <img src="/images/assets/icons/mobile-money.svg" class="tool-image">
                        </div>
                        <span class="d-block font-weight-bold mt-2 text-center heading" style=" font-size: 18px; ">Mobile Money</span>
                        <span data-v-af064eb6="" class="d-block mt-2 text-center body">Connect to Orange Money, MyZaka and Smega to get paid by customers</span>
                        <basicButton customClass="d-block mt-3 ml-auto mr-auto" :style="{ position:'relative' }"
                                        type="success" size="small" 
                                        :ripple="false"
                                        @click.native="$router.push({name:'create-quotation'})">
                            Connect
                        </basicButton>

                    </Card>
                </Col>
            </Row>

        </Col>

        <Col span="6">

            <Poptip word-wrap width="250" trigger="hover" placement="left" class="cardPoptips" 
                    content="Get more SMS's to send quotations, invoices, appointments or jobcard updates to your clients"
                    :style="{ width: '100%' }">
                <Card @click.native="clicked()" class="pb-2 mb-1">
                    <Row :gutter="20">
                        <Col span="18">
                            <span style="
                                min-width: 55px;
                                min-height: 55px;
                                line-height: 52px;
                                margin-right: 0px;
                                display: inline-block;
                                border: 2px solid #2d8cf0;
                                border-radius: 50%;
                                font-size: 25px;
                                text-align: center;
                                color: #2d8cf0;
                                background: #f0faff;
                            ">{{ ((company || {}).sms_credits || {}).count }}</span>
                            <span class="font-weight-bold" style="
                                color: #2d8cf0;
                                border-bottom: 1px solid #2d8cf0;
                                padding: 5px 12px;
                            ">SMS Left</span>
                        </Col>
                        <Col span="6">
                            <!-- Subscribe button -->
                            <basicButton class="float-right mt-3" :style="{ position:'relative' }"
                                        type="success" size="small" 
                                        :ripple="true"
                                        @click.native="clicked()">
                                Get SMS's
                            </basicButton>
                        </Col>
                    </Row>
                </Card>
            </Poptip>

            <Poptip word-wrap width="250" trigger="hover" placement="left" class="cardPoptips" 
                    content="After you subscribe for any service using your phone, you can always view all your subcriptions here"
                    :style="{ width: '100%' }">
                <Card @click.native="clicked()" class="pb-2 mb-1">
                    <Row :gutter="20">
                        <Col span="18">
                            <span style="min-width: 45px;min-height: 45px;line-height: 44px;margin-right: 0px;margin-left: 5px;display: inline-block;border-radius: 50%;border: 1px solid #2d8cf0;text-align: center;color: rgb(45, 140, 240);background: rgb(240, 250, 255);font-size: 20px;">2</span>
                            <span class="font-weight-bold" style="color: rgb(45, 140, 240); padding: 5px;">Subscriptions</span>
                        </Col>
                        <Col span="6">
                            <!-- Subscribe button -->
                            <span @click="showSubcriptions = !showSubcriptions" class="btn btn-link d-inline-block m-0 p-0 mt-3" style="font-size: 12px; float: right;">
                                <Icon type="ios-remove" :size="20" />
                                <span>{{ showSubcriptions ? 'Hide' : 'Show' }}</span>
                            </span>
                        </Col>
                        <Col span="24">
                            <Divider class="d-block mt-2 mb-2" />
                            <Alert v-if="showSubcriptions" type="success" class="clearfix mb-2">
                                <template slot="desc">
                                    <!-- Active Subscriptions -->
                                    <div>
                                        <span class="d-inline-block">1) Daily Report - 75993221</span>
                                        <span class="btn btn-link d-inline-block m-0 p-0" style="font-size: 12px; float: right; ">View</span>
                                    </div>
                                    <div>
                                        <span class="d-inline-block">2) Client Tracker - 75993221</span>
                                        <span class="btn btn-link d-inline-block m-0 p-0" style="font-size: 12px; float: right; ">View</span>
                                    </div>
                                </template>
                            </Alert>
                            <basicButton class="float-right mt-1 mb-0" :style="{ position:'relative' }"
                                        type="success" size="small" 
                                        :ripple="false"
                                        @click.native="clicked()">
                                + Subscribe
                            </basicButton>
                        </Col>
                    </Row>
                </Card>
            </Poptip>

            <Divider orientation="left"><Icon type="ios-phone-portrait" class="icons d-inline-block mr-2" :size="20"></Icon><span>My Active Mobile</span></Divider>
            
            <Poptip word-wrap width="250" trigger="hover" placement="left" class="cardPoptips" 
                    content="This number will be used to receive alerts or updates while you are offline"
                    :style="{ width: '100%' }">
                <Card @click.native="clicked()" class="pb-0 mb-1">
                    <Row :gutter="20">

                        <Col span="24">
                        
                            <Loader v-if="gettingUserMobilePhones" :loading="gettingUserMobilePhones" type="text" class="text-left">Getting mobile phones...</Loader>

                            <div v-else>
                                
                                <div v-for="phone in userMobilePhones">
                                    <span class="d-inline-block">(+{{ phone.calling_code.calling_code }}) {{ phone.number }}</span>
                                    <span class="btn btn-link d-inline-block m-0 p-0" style="font-size: 12px; float: right;">
                                        <Icon type="ios-remove" :size="20" />
                                        <span>Edit</span>
                                    </span>
                                </div>
                            </div>
                        </Col>
                    </Row>
                </Card>
            </Poptip>

            
        </Col>

    </Row>

</template>

<script>

    import { Carousel3d, Slide } from 'vue-carousel-3d';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';
    import Loader from './../../../components/_common/loaders/Loader.vue';

    export default {
        components: { basicButton, Loader, Carousel3d, Slide },
        data(){
            return {
                user: auth.user,
                company: null,
                showSubcriptions: false,
                userMobilePhones: [],
                gettingUserMobilePhones: false,
                orangeWalletlabel: (h) => {
                    return h('div', [
                        h('Icon', {
                            props: {
                                type: 'ios-checkmark',
                                size: 17
                            },
                            class:'mr-1',
                            style: { width:'18px !important',height:'18px !important',borderRadius:'50%',background:'#19be6b',color:'#fff' }
                        }),
                        h('span', 'Orange')
                    ])
                },
                mascomWalletlabel: (h) => {
                    return h('div', [
                        h('Icon', {
                            props: {
                                type: 'ios-add',
                                size: 17
                            },
                            class:'mr-1',
                            style: { width:'20px !important',height:'20px !important',borderRadius:'50%',border:'1px solid rgb(210, 210, 210)',color:'rgb(152, 152, 153)' }
                        }),
                        h('span', 'Mascom')
                    ])
                },
                btcWalletlabel: (h) => {
                    return h('div', [
                        h('Icon', {
                            props: {
                                type: 'ios-add',
                                size: 17
                            },
                            class:'mr-1',
                            style: { width:'20px !important',height:'20px !important',borderRadius:'50%',border:'1px solid rgb(210, 210, 210)',color:'rgb(152, 152, 153)' }
                        }),
                        h('span', 'BTC')
                    ])
                },
                show_create_quotation_btn: false,
                show_create_invoice_btn: false,
                show_create_receipt_btn: false,
                show_create_appointment_btn: false,
                show_create_jobcard_btn:false
            }
        },
        methods: {
            clicked(){
                //  Do something
            },
            fetchCompany() {

                //  If we have the route id set
                if( this.user.company_id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    //  Additional data to eager load along with the company found
                    var connections = '?connections=smsCredits';

                    console.log('Start getting company details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.user.company_id+connections)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            //  Store the company data
                            self.company = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoading = false;

                            //  Error Location
                            console.log('dashboard/company/show/main.vue - Error getting company details...');

                            //  Log the responce
                            console.log(response);    
                        });

                }
            },
            getUserMobilePhones() {
                const self = this;

                //  Start loader
                self.gettingUserMobilePhones = true;

                console.log('Start getting the users existing phones...');

                //  Model id e.g 1,2,3, e.t.c
                var modelId = (this.user || {}).id;

                //  Model type e.g user/company
                var modelType = 'user';

                //  Additional data to eager load along with each mobile phone found
                var connections = '&connections=';

                //  Settings to prevent pagination
                var pagination = (connections ? '&': '') + 'paginate=0';

                if(modelId && modelType){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/phones?modelId='+modelId+'&modelType='+modelType+connections+pagination)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.gettingUserMobilePhones = false;

                            //  Update local phones phones
                            self.userMobilePhones = data;
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.gettingUserMobilePhones = false;

                            console.log('updateProfileAfterSignUpModal.vue - Error getting the users existing phones...');
                            console.log(response);    
                        });

                }
            },
            renderComponent: function(){
                //  Re-render the component
                this.renderKey++;
            }
        },
        created(){
            //  Fetch the company
            this.fetchCompany();

            //  Get the users mobile phones
            this.getUserMobilePhones();
        }
    };
</script>