<style scoped>

    .wallet-tabs >>> .ivu-tabs-nav .ivu-tabs-tab {
        padding: 8px 4px;
    }

    .wallet-tabs >>> .ivu-tabs-nav .ivu-tabs-tab:last-child {
        margin-right: 0;
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

.tool-image{
    display: block;
    font-size: 25px;
    width: 65px;
    margin: -40px auto 0;
    padding: 10px;
    background: #fff;
}

  .ivu-badge >>> .ivu-badge-count {
    font-family: inherit !important;
    font-weight: 600 !important;
    min-width: 35px !important;
    height: 20px !important;
    z-index: 1 !important;
  }

</style>

<template>

    <Row :gutter="20">
        
        <Col span="18">
    
            <Card @click.native="clicked()" class="pb-4 mb-2">
                <Row :gutter="20">
                    <Col span="24" class="mb-3">
                        <Divider orientation="left">
                            <Icon type="ios-cash-outline" class="d-inline-block mr-2" :size="24"></Icon>
                            <span class="font-weight-bold">Accounts & Billing</span>
                        </Divider>
                    </Col>
                    <Col span="8">
                        <Card class="pb-0 mb-1"
                              @mouseenter.native="show_create_quotation_btn = true"
                              @mouseleave.native="show_create_quotation_btn = false">
                            <Badge :show-zero="true" :count="202" type="primary" style="float: right;"></Badge>
                            <img src="/images/assets/icons/paper-and-calculator.png" class="tool-image">
                            <span class="d-block font-weight-bold mt-2 text-center" style=" font-size: 18px; ">Quotations</span>
                            <!-- Subscribe button -->
                            <transition name="slide">
                                <basicButton v-if="show_create_quotation_btn" 
                                            customClass="w-100 mt-3" :style="{ position:'relative' }"
                                            type="success" size="small" 
                                            :ripple="false"
                                            @click.native="$router.push({name:'create-quotation'})">
                                    + Create Quotation
                                </basicButton>
                            </transition>
                        </Card>
                    </Col>
                    <Col span="8">
                        <Card class="pb-0 mb-1"
                              @mouseenter.native="show_create_invoice_btn = true"
                              @mouseleave.native="show_create_invoice_btn = false">
                            <Badge :show-zero="true" :count="0" type="primary" style="float: right;"></Badge>
                            <img src="/images/assets/icons/hand-payment.png" class="tool-image">
                            <span class="d-block font-weight-bold mt-2 text-center" style=" font-size: 18px; ">Invoices</span>
                            <!-- Subscribe button -->
                            <transition name="slide">
                                <basicButton v-if="show_create_invoice_btn" 
                                    customClass="w-100 mt-3" :style="{ position:'relative' }"
                                        type="success" size="small" 
                                        :ripple="false"
                                        @click.native="$router.push({name:'create-invoice'})">
                                    + Create Invoice
                                </basicButton>
                            </transition>
                        </Card>
                    </Col>
                    <Col span="8">
                        <Card class="pb-0 mb-1"
                              @mouseenter.native="show_create_receipt_btn = true"
                              @mouseleave.native="show_create_receipt_btn = false">
                            <Badge :show-zero="true" :count="0" type="primary" style="float: right;"></Badge>
                            <img src="/images/assets/icons/cash-receipt.png" class="tool-image">
                            <span class="d-block font-weight-bold mt-2 text-center" style=" font-size: 18px; ">Receipts</span>
                            <!-- Subscribe button -->
                            <transition name="slide">
                                <basicButton v-if="show_create_receipt_btn" 
                                        customClass="w-100 mt-3" :style="{ position:'relative' }"
                                        type="success" size="small" 
                                        :ripple="false"
                                        @click.native="$router.push({name:'create-receipt'})">
                                    + Create Receipt
                                </basicButton>
                            </transition>
                        </Card>
                    </Col>
                </Row>
            </Card>

            <Card @click.native="clicked()" class="pb-0 mb-2">
                <Row :gutter="20">
                    <Col span="24" class="mb-3">
                        <Divider orientation="left">
                            <Icon type="ios-time-outline" class="d-inline-block mr-2" :size="24"></Icon>
                            <span class="font-weight-bold">Appointment Management</span>
                        </Divider>
                    </Col>
                    <Col span="8">
                        <Card class="pb-0 mb-1"
                                @mouseenter.native="show_create_appointment_btn = true"
                                @mouseleave.native="show_create_appointment_btn = false">
                            <Badge :show-zero="true" :count="0" type="primary" style="float: right;"></Badge>
                            <img src="/images/assets/icons/calendar.png" class="tool-image">
                            <span class="d-block font-weight-bold mt-2 text-center" style=" font-size: 18px; ">Appointments</span>
                            <!-- Subscribe button -->
                            <transition name="slide">
                                <basicButton v-if="show_create_appointment_btn" 
                                            customClass="w-100 mt-3" :style="{ position:'relative' }"
                                            type="success" size="small" 
                                            :ripple="false"
                                            @click.native="$router.push({name:'create-appointment'})">
                                    + Create Appointment
                                </basicButton>
                            </transition>
                        </Card>
                    </Col>
                    <Col span="8">
                    
                    </Col>
                    <Col span="8">
                    
                    </Col>
                </Row>
            </Card>

            <Card @click.native="clicked()" class="pb-0 mb-2">
                <Row :gutter="20">
                    <Col span="24" class="mb-3">
                        <Divider orientation="left">
                            <Icon type="ios-copy-outline" class="icon-wallet icons d-inline-block mr-2" :size="24"></Icon>
                            <span class="font-weight-bold">Jobcard Management</span>
                        </Divider>
                    </Col>
                    <Col span="8">
                        <Card class="pb-0 mb-1"
                                @mouseenter.native="show_create_jobcard_btn = true"
                                @mouseleave.native="show_create_jobcard_btn = false">
                            <Badge :show-zero="true" :count="0" type="primary" style="float: right;"></Badge>
                            <img src="/images/assets/icons/checklist.png" class="tool-image">
                            <span class="d-block font-weight-bold mt-2 text-center" style=" font-size: 18px; ">Jobcards</span>
                            <!-- Subscribe button -->
                            <transition name="slide">
                                <basicButton v-if="show_create_jobcard_btn" 
                                            customClass="w-100 mt-3" :style="{ position:'relative' }"
                                            type="success" size="small" 
                                            :ripple="false"
                                            @click.native="$router.push({name:'create-jobcard'})">
                                    + Create Jobcard
                                </basicButton>
                            </transition>
                        </Card>
                    </Col>
                    <Col span="8">
                    
                    </Col>
                    <Col span="8">
                    
                    </Col>
                </Row>
            </Card>
            
            <Card @click.native="clicked()" class="pb-5 mb-2">
                <Row :gutter="20">
                    <Col span="18">
                        <p class="font-weight-bold">-- SMS/Email Activity Chart --</p>
                    </Col>
                </Row>
            </Card>

        </Col>

        <Col span="6">

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
                        ">37</span>
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
            
            <Divider orientation="left"><i class="icon-wallet icons d-inline-block mr-2" :style="{ fontSize:'20px' }"></i><span>Mobile Money</span></Divider>
            
            <Tabs class="wallet-tabs">
                <TabPane :label="orangeWalletlabel">

                    <Card @click.native="clicked()" class="pb-0 mb-1">
                        <Row :gutter="20">
                            <Col :span="24">

                                <Alert type="success" class="mb-0">
                                    <img src="/images/samples/orange_money_logo.png" style="display: inline-block !important;font-size: 25px;color: rgb(255, 255, 255);background: rgb(255, 102, 0);border-radius: 50%;padding: 2px;width: 45px;height: 45px;margin-top: -10px;">
                                    <div style="display: inline-block;">
                                        <h4 style="font-size: 18px;margin: 10px 0 0 5px;">Orange Money</h4>
                                    </div>
                                    <template slot="desc">
                                        <Divider class="mt-2 mb-2" />
                                        <p><b>Name:</b> Julian B Tabona</p>
                                        <p><b>Number:</b> (+267) 75993221</p>
                                        <p><b>Status:</b> Active</p>
                                    </template>
                                </Alert>

                                <span class="btn btn-link" @click="isOpenChangeMobileMoneyAccountModal = !isOpenChangeMobileMoneyAccountModal">
                                    <Icon type="ios-repeat" :size="20" class="mr-1"></Icon>
                                    <span>Change Account</span>
                                </span>

                            </Col>
                        </Row>
                    </Card>

                </TabPane>
                <TabPane :label="mascomWalletlabel">
                    
                    <Card @click.native="clicked()" class="pb-0 mb-1">
                        <Row :gutter="20">
                            <Col :span="24">

                                <Alert type="success" class="clearfix mb-0">
                                    <img src="/images/samples/myzaka_logo.png" style="display: inline-block !important;font-size: 25px;color: rgb(255, 255, 255);background: rgb(255, 102, 0);border-radius: 50%;padding: 2px;width: 45px;height: 45px;margin-top: -10px;">
                                    <div style="display: inline-block;">
                                        <h4 style="font-size: 18px;margin: 10px 0 0 5px;">MyZaka</h4>
                                    </div>
                                    <template slot="desc">
                                        <Divider class="mt-2 mb-2" />
                                        <!-- Connect To Myzaka button -->
                                        <basicButton class="float-right" :style="{ position:'relative' }"
                                                    type="success" size="small" 
                                                    :ripple="false"
                                                    @click.native="clicked()">
                                            <Icon type="ios-add" :size="20" class="mr-1"></Icon>
                                            <span>Connect To Myzaka</span>
                                        </basicButton>
                                    </template>
                                </Alert>

                            </Col>
                        </Row>
                    </Card>

                </TabPane>
                <TabPane :label="btcWalletlabel">
                    
                    <Card @click.native="clicked()" class="pb-0 mb-1">
                        <Row :gutter="20">
                            <Col :span="24">

                                <Alert type="success" class="clearfix mb-0">
                                    <img src="/images/samples/btc_logo.jpg" style="display: inline-block !important;font-size: 25px;color: rgb(255, 255, 255);background: rgb(34, 130, 11);border-radius: 50%;padding: 2px;width: 45px;height: 45px;margin-top: -10px;">
                                    <div style="display: inline-block;">
                                        <h4 style="font-size: 18px;margin: 10px 0 0 5px;">SMEGA</h4>
                                    </div>
                                    <template slot="desc">
                                        <Divider class="mt-2 mb-2" />
                                        <!-- Connect To SMEGA button -->
                                        <basicButton class="float-right" :style="{ position:'relative' }"
                                                    type="success" size="small" 
                                                    :ripple="false"
                                                    @click.native="clicked()">
                                            <Icon type="ios-add" :size="20" class="mr-1"></Icon>
                                            <span>Connect To SMEGA</span>
                                        </basicButton>
                                    </template>
                                </Alert>

                            </Col>
                        </Row>
                    </Card>

                </TabPane>
            </Tabs>
            
        </Col>

    </Row>

</template>

<script>

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    export default {
        components: { basicButton },
        data(){
            return {
                showSubcriptions: false,
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
            }
        }
    };
</script>