<style scoped>

    .business-tool-card:hover{
        cursor:pointer;
        box-shadow: 0px 5px 10px #b9b9b9;
    }

    .business-tool-card >>> .image-box{
        display: block;
        width: 70px;
        margin: 10px;
        padding: 12px;
        border: 1px solid #0a4d94;
        background: #eeeeee;
        border-radius: 50%;
    }

    .business-tool-card >>> .image-box.purple-bg{
        background: #d817da;
        border: 1px solid #88008a;
    }

    .business-tool-card >>> .image-box.pink-bg{
        background: #f9247b;
        border: 1px solid #b5044c;
    }

    .business-tool-card >>> .image-box.green-bg{
        background: #3ec109;
        border: 1px solid #2c8c05;
    }

    .business-tool-card >>> .image-box.orange-bg{
        background: #e49a0e;
        border: 1px solid #946100;
    }

    .business-tool-card >>> .image-box.light-blue-bg{
        background: #07b7c1;
        border: 1px solid #00828a;
    }

    .business-tool-card >>> .image-box.dark-purple-bg{
        background: #9d4bf5;
        border: 1px solid #6417b7;
    }

    .business-tool-card >>> .image-box .tool-image{
        width: 100%;
    }

    .business-tool-card >>> .image-box .tool-image{
        filter: invert(100%) sepia(3%) saturate(1375%) hue-rotate(264deg) brightness(122%) contrast(99%);
    }

    .business-tool-card >>> .heading{
        font-size: 20px;
    }

    .business-tool-card >>> .body{
        margin: 0 10px;
    }

</style>

<template>
    <Row :gutter="20">

        <!-- Show when we don't have products -->
        <Col :span="20" :offset="2">
            <div class="pb-3 border-bottom">
                <h2>Business Tools</h2>
            </div>

            <Row :gutter="20">

                <Col :span="24" class="mt-0 mb-0 pl-0">
                    
                    <h6 class="pt-5 pb-3 pl-3 mt-2 ml-3 mb-3 border-bottom">
                        <Icon type="ios-cash-outline" :size="20" class="inline-block" />
                        <span class="font-weight-bold inline-block">Billing Tools</span>
                    </h6>
                </Col>
                <Col :span="8" class="mt-0 mb-0 pl-0">
                    <Card class="business-tool-card">
                        <div class="image-box purple-bg d-block float-left">
                            <img src="/images/assets/icons/paper-and-calculator.svg" class="tool-image">
                        </div>
                        <div>
                            <span class="d-block font-weight-bold mt-2 heading" style=" font-size: 18px; ">Quotations</span>
                            <span class="d-block mt-2 body" style="font-size:12px;">Create, manage and send quotations via sms/email to customers</span>
                        </div>
                    </Card>
                </Col>
                <Col :span="8" class="mt-0 mb-0">
                    <Card class="business-tool-card">
                        <div class="image-box pink-bg d-block float-left">
                            <img src="/images/assets/icons/hand-payment.svg" class="tool-image">
                        </div>
                        <div>
                            <span class="d-block font-weight-bold mt-2 heading" style=" font-size: 18px; ">Invoices</span>
                            <span class="d-block mt-2 body" style="font-size:12px;">Send invoices via sms/email and get paid via mobile money transfers</span>
                        </div>
                    </Card>

                </Col>
                <Col :span="8" class="mt-0 mb-0 pr-0">
                    <Card class="business-tool-card">
                        <div class="image-box green-bg d-block float-left">
                            <img src="/images/assets/icons/cash-receipt.svg" class="tool-image">
                        </div>
                        <div>
                            <span class="d-block font-weight-bold mt-2 heading" style=" font-size: 18px; ">Receipts</span>
                            <span class="d-block mt-2 body" style="font-size:12px;">Track sales and send payment confirmations via sms/email to customers</span>
                        </div>
                    </Card>
                </Col>
            </Row>
            <Row :gutter="20">
                <Col :span="24" class="mt-0 mb-0 pl-0">
                    
                    <h6 class="pt-3 pb-3 pl-3 mt-2 ml-3 mb-3 border-bottom">
                        <Icon type="ios-bulb-outline" :size="20" class="inline-block" />
                        <span class="font-weight-bold inline-block">Other Tools</span>
                    </h6>
                </Col>
                <Col :span="8" class="mt-0 mb-0 pl-0">
                    <Card class="business-tool-card">
                        <div class="image-box orange-bg d-block float-left">
                            <img src="/images/assets/icons/shopping-cart.svg" class="tool-image">
                        </div>
                        <div>
                            <span class="d-block font-weight-bold mt-2 heading" style=" font-size: 18px; ">Ecommerce</span>
                            <span class="d-block mt-2 body" style="font-size:12px;">Manage your store and get customers shopping and paying for your goods</span>
                        </div>
                    </Card>
                </Col>
                <Col :span="8" class="mt-0 mb-0">
                    <Card class="business-tool-card">
                        <div class="image-box light-blue-bg d-block float-left">
                            <img src="/images/assets/icons/calendar.svg" class="tool-image">
                        </div>
                        <div>
                            <span class="d-block font-weight-bold mt-2 heading" style=" font-size: 18px; ">Appointments</span>
                            <span class="d-block mt-2 body" style="font-size:12px;">Set appointments that will automatically remind customers via sms/email</span>
                        </div>
                    </Card>

                </Col>
                <Col :span="8" class="mt-0 mb-0 pr-0">
                    <Card class="business-tool-card">
                        <div class="image-box dark-purple-bg d-block float-left">
                            <img src="/images/assets/icons/checklist.svg" class="tool-image">
                        </div>
                        <div>
                            <span class="d-block font-weight-bold mt-2 heading" style=" font-size: 18px; ">Jobcards</span>
                            <span class="d-block mt-2 body" style="font-size:12px;">Track jobs and keep customers conveniently informed via sms/email</span>
                        </div>
                    </Card>
                </Col>
            </Row>
        </Col>

    </Row>
</template>
<script type="text/javascript">

    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    export default {
        components: { basicButton },
        data(){
            return {
                
            }
        }
    }
</script>
