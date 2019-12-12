<style scoped>


</style>

<template>

    <div>

        <!-- Loader -->
        <Loader v-if="isLoadingMessages" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading messages...</Loader>
        
        <!-- No messages message -->
        <Alert v-if="!isLoadingMessages && !localMessages" type="info" :style="{ maxWidth: '250px' }" show-icon>No messages found</Alert>
        
        <!-- Message Filter -->
        <Card v-if="!isLoadingMessages && localMessages" class="mb-3">
            <Row :gutter="20">
                <Col :span="6">
                    <Select v-model="selectedMessageStatuses" filterable multiple placeholder="Search customer...">
                        <Option v-for="customer in localMessages" :value="customer.id" :key="customer.id">
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
        
    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue'; 

    import moment from 'moment';

    export default {
        props: {
            storeId: {
                type: Number,
                default: null
            }
        }, 
        components: { 
            basicButton, Loader
        },
        data(){
            return {
                moment: moment,

                //  Store Info
                localStoreId: this.storeId || this.$route.params.storeId,

                //  Messages Info
                localMessages: null,
                isLoadingMessages: false,

                selectedMessageStatuses: []
            }
        },
        methods: {
            fetchMessages() {

                if( this.localStoreId ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingMessages = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting messages...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/products?storeId='+this.localStoreId)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingMessages = false;

                            //  Message the message data
                            self.localMessages = data.data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingMessages = false;

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