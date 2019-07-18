<style scoped>

    .column-equal-height {
        display: flex;
    }

    .full-height{
        height: 100%;
    }

</style>

<template>
    <div>
        <Row :gutter="20">
            <Col :span="24" class="mb-2">
                <Card>
                    <span>
                        <span>Mark As: </span>
                        <Select :value="['Pending Payment']" filterable multiple  style="max-width:200px;">

                            <OptionGroup label="Payments">
                                <Option v-for="item in ['Pending Payment', 'Failed Payment', 'Paid']" :value="item" :key="item" :disabled="item == 'Failed Payment'">{{ item }}</Option>
                            </OptionGroup>

                            <OptionGroup label="Refunds">
                                <Option v-for="item in ['Pending Refund', 'Refunded']" :value="item" :key="item">{{ item }}</Option>
                            </OptionGroup>

                            <OptionGroup label="Deliveries">
                                <Option v-for="item in ['Pending Delivery', 'Delivery']" :value="item" :key="item">{{ item }}</Option>
                            </OptionGroup>

                            <OptionGroup label="Final">
                                <Option v-for="item in ['Cancelled', 'Completed']" :value="item" :key="item">{{ item }}</Option>
                            </OptionGroup>

                        </Select>
                    </span>
                    <Divider type="vertical" />
                    <Dropdown>
                        <Button type="default">
                            <Icon type="ios-mail-outline" :size="20" />
                            <span>Send Email/Sms <Icon type="ios-arrow-down" :size="20" /></span>
                        </Button>
                        <DropdownMenu slot="list">
                            <DropdownItem>Order Invoice</DropdownItem>
                            <DropdownItem>Order Payment Receipt</DropdownItem>
                            <DropdownItem>Bank Tranfer Details</DropdownItem>
                        </DropdownMenu>
                    </Dropdown>

                </Card>
            </Col>
        </Row>
        <Row :gutter="20" class="column-equal-height">
            <Col :span="8">
                <Card class="full-height">
                    <h4 class="text-primary mb-3">Customer</h4>
                    <span class="d-block"><span class="font-weight-bold">Name: </span>Julian Tabona</span>
                    <span class="d-block"><span class="font-weight-bold">Phone: </span> (+267) 75993221</span>
                    <span class="d-block"><span class="font-weight-bold">Email: </span> brandontabona@gmail.com</span>
                    
                    <span class="d-block pt-2 mt-2 border-top"><span class="font-weight-bold">Address: </span> Plot 3465, Cleaveland Street</span>
                    <span class="d-block"><span class="font-weight-bold">Country: </span> Botswana</span>
                    <span class="d-block"><span class="font-weight-bold">City: </span> Gaborone</span>
                </Card>
            </Col>

            <Col :span="8">
                <Card class="full-height">
                    <h4 class="text-primary mb-3">Order</h4>
                    <span class="d-block"><span class="font-weight-bold">Total Cost: </span> P4,500.00</span>
                    <span class="d-block"><span class="font-weight-bold">Purchased: </span>3 items</span>
                    <span class="d-block pt-2 mt-2 border-top"><span class="font-weight-bold">Amount Paid: </span>P4,500.00</span>
                    <span class="d-block"><span class="font-weight-bold">Amount Due: </span>P0.00</span>
                    <span class="d-block pt-2 mt-2 border-top"><span class="font-weight-bold">Refunds: </span>P500.00</span>
                </Card>
            </Col>

            <Col :span="8">

                <Card class="full-height">
                    <h4 class="text-primary mb-3">Status</h4>
                    <scrollBox class="border">
                        <Steps :current="2" direction="vertical" class="p-2" style="max-height: 190px;">
                            <Step title="Open" content="Received on Jun 12 2018"></Step>
                            <Step title="Paid" content="Paid on Jun 14 2018"></Step>
                            <Step title="Pending Delivery" content="Currently waiting delivery"></Step>
                            <Step title="Completed" content="Completed on Jun 16 2018"></Step>
                        </Steps>
                    </scrollBox>
                </Card>
            </Col>
        </Row>
    </div>
</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /*  scrollBox  */
    import scrollBox from './../../../components/_common/scrollBox/scrollBox.vue';

    export default {
        components: { 
            basicButton, Loader, scrollBox
        },
        data(){
            return {

            }
        },
        methods: {

        },
        created(){

        }
    };
  
</script>