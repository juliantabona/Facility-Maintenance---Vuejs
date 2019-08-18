<template>

    <div>

        <!-- Loader -->
        <Loader v-if="isLoadingOrders" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading messages...</Loader>
        
        <!-- No messages message -->
        <Alert v-if="!isLoadingOrders && !localOrders" type="info" :style="{ maxWidth: '250px' }" show-icon>No messages found</Alert>
        
        <!-- Message Filter -->
        <Card v-if="!isLoadingOrders && localOrders" class="mb-3">
            <Row :gutter="20">
                <Col :span="6">
                    <Select v-model="selectedMessageStatuses" filterable multiple placeholder="Search customer...">
                        <Option v-for="customer in localOrders" :value="customer.id" :key="customer.id">
                            {{ (customer.billing_info || {}).first_name || (customer.billing_info || {}).name }} {{ (customer.billing_info || {}).last_name }}
                        </Option>
                    </Select>
                </Col>
                <Col :span="6">
                    <Select v-model="selectedMessageStatuses" filterable multiple placeholder="Filter by status">

                        <OptionGroup label="Payment status">
                            <Option v-for="item in ['Pending Payment', 'Verify Payment', 'Failed Payment', 'Paid']" :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                        <OptionGroup label="Refund status">
                            <Option v-for="item in ['Pending Refund', 'Refunded']" :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                        <OptionGroup label="Delivery status">
                            <Option v-for="item in ['Pending Delivery', 'Verify Delivery','Delivered']" :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                        <OptionGroup label="Final status">
                            <Option v-for="item in ['Cancelled', 'Completed']" :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                    </Select>
                </Col>
                <Col :span="4">
                    <DatePicker type="date" placeholder="From"></DatePicker>
                </Col>
                <Col :span="4">
                    <DatePicker type="date" placeholder="To"></DatePicker>
                </Col>
                <Col :span="4">
                    <!-- Add Message Button -->
                    <div class="clearfix">
                        <basicButton @click.native="$router.push({ name:'create-message' })" 
                                    size="large" class="float-right">
                                    <span>+ Add Message</span>
                        </basicButton>
                    </div>
                </Col>
            </Row>
        </Card>
        
        <!-- Meessage List -->
        <Row :gutter="12">
            <Col v-for="(order, index_1) in localOrders" :key="index_1" :span="24">
                <Card class="mb-3">
                    <h5 class="mb-2">Order # {{ order.id }}</h5>

                    <messageChatBox
                        :messages="order.messages"
                        :urlParams="{
                            commentType: 'message',
                            orderId: order.id
                        }"
                        :showAsStaff="true"
                        :showContactList="false"
                        :showMessages="false"
                        :chatBoxStyle="{
                            maxHeight:'250px'
                        }"
                        @sentMessage="localOrders[index_1].messages.push($event)">
                    </messageChatBox>

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

    import messageChatBox from './messageChatBox.vue';

    import moment from 'moment';

    export default {
        props: {
            storeId: {
                type: Number,
                default: null
            }
        }, 
        components: { 
            basicButton, Loader, messageChatBox
        },
        data(){
            return {
                moment: moment,

                //  Store Info
                localStoreId: this.storeId || this.$route.params.storeId,

                //  Messages Info
                localOrders: null,
                isLoadingOrders: false,

                selectedMessageStatuses: []
 
            }
        },
        computed:{
            dynamicColumns(status){ 
                var allowedColumns = [];
            
                //  Customer Details
                allowedColumns.push(
                {
                    width: 150,
                    title: 'Customer',
                    render: (h, params) => {
                        return h('span', (
                            (params.row.user || {}).full_name 
                        ));
                    }
                });

                //  Customer Message
                allowedColumns.push(
                {
                    width: 400,
                    title: 'Message',
                    render: (h, params) => {
                        return h('span', (
                            params.row.text 
                        ));
                    }
                });

                //  Date
                allowedColumns.push(
                {
                    width: 150,
                    title: 'Date',
                    sortable: true,
                    render: (h, params) => {
                        return h('span', this.formatDate(params.row.created_at));
                    }
                });
                
                //  Action
                allowedColumns.push(
                {
                    title: 'Action',
                    align: 'center',
                    render: (h, params) => {
                        return h('div', [
                            h('Button', {
                                props: {
                                    type: 'primary',
                                    size: 'small'
                                },
                                style: {
                                    marginRight: '5px'
                                },
                                on: {
                                    click: () => {
                                        this.$router.push({ name: 'show-order', params: { id: params.row.id } });
                                    }
                                }
                            }, 'View')
                        ]);
                    }
                });

                return allowedColumns;
            }
        },
        methods: {
            
            formatDate(date) {
                return this.moment(date).format('MMM DD YYYY');
            },
            fetchMessages() {

                if( this.localStoreId ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingOrders = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting messages...');
                    
                    var page = (this.$route.query.page) ? this.$route.query.page : 1;

                    var urlParams = {
                            storeId: this.localStoreId,
                            connections: 'messages',
                            page: page
                        }

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/orders', null, urlParams)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingOrders = false;

                            //  Message the message data
                            self.localOrders = data.data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingOrders = false;

                            //  Console log Error Location
                            console.log('dashboard/message/show/main.vue - Error getting messages...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){
            //  Fetch the messages
            this.fetchMessages();
        }
    };
  
</script>