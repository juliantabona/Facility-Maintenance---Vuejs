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

        <!-- Show the current order fulfillment widget -->
        <template v-if="showOrderFulfillmentWidget">

            <!-- Order Fulfillment Widget -->
            <orderFulfillmentWidget :orderUrl="orderUrl"
                @goBack="closeFulfillmentWidget()">
            </orderFulfillmentWidget>

        </template>

        <!-- Show the current order details -->
        <template v-else>

            <!-- Show order as Full Page Layout -->
            <Row :gutter="12">

                <!-- Saving Order Spinner  -->
                <Spin v-if="isSavingOrder" size="large" fix></Spin>

                <Col :span="24">


                    <!-- Go Back Button & Order Navigation Arrows -->
                    <Row class="mb-2">

                        <!-- Order Heading -->
                        <Col :span="20">

                            <!-- Button to go back to orders list -->
                            <Button type="text" @click.native="$emit('goBack')">
                                <Icon type="ios-arrow-back" />
                                <span>Orders</span>
                            </Button>
                            
                        </Col>

                        <!-- Save Changes Button -->
                        <Col :span="4">
                            <div class="clearfix">

                                <!-- Next Order Button -->
                                <Button v-if="canNavigateToNextOrder" :disabled="isLoadingOrder"
                                        type="text" class="float-right" @click.native="goToNextOrder()">
                                    <Icon type="md-arrow-forward" :size="20" />
                                </Button>

                                <!-- Previous Order Button -->
                                <Button v-if="canNavigateToPreviousOrder" :disabled="isLoadingOrder"
                                        type="text" class="float-right" @click.native="goToPreviousOrder()">
                                    <Icon type="md-arrow-back" :size="20" />
                                </Button>

                            </div>
                        </Col>

                    </Row>

                    <template v-if="order && !isLoadingOrder">

                        <!-- Order Heading / Save Button -->
                        <Row class="mb-1">

                            <!-- Order Heading -->
                            <Col :span="24">

                                <!-- Order Number -->
                                <h2 class="d-inline-block font-weight-bold mr-2 pl-4 text-dark">#{{ order.id }}</h2>
                                
                                <!-- Created Date -->
                                <span class="d-inline-block mr-2 text-dark">{{ formatDate(order.created_at.date, true) }}</span>

                                <!-- General Status -->
                                <Poptip :width="350" :wordWrap="true" trigger="hover" placement="top-start"
                                        :content="(order.status.description  || '...')">
                                    <Tag type="dot" class="rounded-status-tag" :color="getStatusColor(order.status.name)">
                                        <span>{{ order.status.name }}</span>
                                    </Tag>
                                </Poptip>

                                <!-- Payment Status -->
                                <Poptip :width="350" :wordWrap="true" trigger="hover" placement="top-start"
                                        :content="(order.payment_status.description  || '...')">
                                    <Tag type="dot" class="rounded-status-tag" :color="getPaymentStatusColor(order.payment_status.name)">
                                        <span>{{ order.payment_status.name }}</span>
                                    </Tag>
                                </Poptip>

                                <!-- Fulfillment Status -->
                                <Poptip :width="350" :wordWrap="true" trigger="hover" placement="top-start"
                                        :content="(order.fulfillment_status.description  || '...')">
                                    <Tag type="dot" class="rounded-status-tag" :color="getFulfillmentStatusColor(order.fulfillment_status.name)">
                                        <span>{{ order.fulfillment_status.name }}</span>
                                    </Tag>
                                </Poptip>
                                
                            </Col>

                        </Row>

                        <!-- Order Action Buttons -->
                        <Row class="border-bottom pb-3 mb-3">
                            
                            <Col :span="24">

                                <!-- Print Button -->
                                <Button type="text" class="text-dark pr-1 pl-1 ml-3" ghost>
                                    <Icon type="ios-print-outline" :size="20" />
                                    <span>Print</span>
                                </Button>

                                <!-- Restock Button -->
                                <Button type="text" class="text-dark pr-1 pl-1" ghost>
                                    <Icon type="ios-cart-outline" :size="20" />
                                    <span>Restock</span>
                                </Button>

                                <!-- Edit Button -->
                                <Button type="text" class="text-dark pr-1 pl-1" ghost>
                                    <Icon type="ios-create-outline" :size="20" />
                                    <span>Edit</span>
                                </Button>

                                <!-- More actions Button -->
                                <Button type="text" class="text-dark p-0" ghost>

                                    <!-- Dropdown -->
                                    <Dropdown trigger="click" placement="bottom-end">
                                        
                                        <!-- Title -->
                                        <span class="pt-2 pl-1 pb-2 pr-1">
                                            <span>More actions</span>
                                            <Icon type="ios-arrow-down"></Icon>
                                        </span>

                                        <!-- Dropdown Options -->
                                        <DropdownMenu slot="list" class="text-left">

                                            <DropdownItem>
                                                <Icon type="ios-browsers-outline" :size="20" />
                                                <span>Duplicate</span>
                                            </DropdownItem>
                                            <DropdownItem>
                                                <Icon type="ios-close" :size="20" />
                                                <span>Cancel</span>
                                            </DropdownItem>

                                            <Divider class="mt-1 mb-1"></Divider>

                                            <DropdownItem>
                                                <Icon type="ios-archive-outline" :size="20" />
                                                <span>Archive</span>
                                            </DropdownItem>

                                            <Divider class="mt-1 mb-1"></Divider>

                                            <DropdownItem>
                                                <Icon type="ios-eye-outline" :size="20" />
                                                <span>View order status page</span>
                                            </DropdownItem>

                                        </DropdownMenu>

                                    </Dropdown>
                                
                                </Button>
                                
                            </Col>

                        </Row>

                        <!-- Order Body -->
                        <Row :gutter="20">

                            <!-- Order Fulfillment / Payment / Timeline -->
                            <Col :span="16">

                                <Card v-if="order.unfulfilled_item_lines.length" dis-hover class="pt-2 pr-2 pl-2 mb-2">

                                    <!-- Number Of Unfulfilled Items -->
                                    <h5 class="text-dark mb-3">
                                        <Icon type="ios-cube-outline" :size="25" color="#fff" class="p-1" style="background: #f90; border-radius: 100%;"/>
                                        <span class="font-weight-bold">Unfulfilled ({{ countItemQuantities(order.unfulfilled_item_lines) }})</span>
                                    </h5>

                                    <List item-layout="vertical">
                                        <ListItem v-for="(item, index) in order.unfulfilled_item_lines" :key="index" class="pt-3 pb-2">
                                            
                                            <Row>

                                                <!-- Item Image -->
                                                <Col :span="2">
                                                    <Badge type="primary" :count="parseInt(item.quantity)">
                                                        <Avatar icon="ios-images-outline" shape="square" size="large" />
                                                    </Badge>
                                                </Col>

                                                <!-- Item Details -->
                                                <Col :span="14">
                                                    <span class="d-block font-weight-bold text-primary">{{ item.name }}</span>
                                                    <span style="font-size: 12px;">
                                                        <span class="font-weight-bold">SKU:</span> {{ item.sku }}
                                                    </span>
                                                </Col>

                                                <!-- Item Price & Quantity -->
                                                <Col :span="4">
                                                    <span>
                                                        {{ formatPrice((item.grand_total / item.quantity), currency) }} x {{ item.quantity }}
                                                    </span>
                                                </Col>

                                                <Col :span="4">
                                                    <span class="text-right d-block">
                                                        {{ formatPrice(item.grand_total, currency) }}
                                                    </span>
                                                </Col>

                                            </Row>

                                        </ListItem>

                                        <ListItem class="clearfix">
                                            
                                            <basicButton @click.native="showOrderFulfillmentWidget = true" size="default"
                                                        type="success" class="float-right">
                                                <Icon type="md-checkbox-outline" :size="20" class="mr-1" />
                                                <span>Mark As Fulfilled</span>
                                            </basicButton>
                                                
                                        </ListItem>

                                    </List>

                                </Card>

                                <Card v-for="(fulfillment, x) in (((order._embedded || {}).fulfillments || {})._embedded || {}).fulfillments" :key="x" dis-hover class="pt-2 pr-2 pl-2 mb-2">
                                    
                                    <!-- Cancelling order fulfillment spinner  -->
                                    <Spin v-if="isCancellingOrderFulfillment == x" size="large" fix></Spin>

                                    <!-- Number Of Fulfillment Items -->
                                    <div>
                                        <h5 class="d-inline-block text-dark">
                                            <Icon type="md-checkmark" :size="25" color="#fff" class="p-1" style="background: #19be6b; border-radius: 100%;"/>
                                            <span class="d-inline-block font-weight-bold mr-1">Fulfilled ({{ countItemQuantities(fulfillment.item_lines) }})</span>
                                        </h5>

                                        <!-- FuLfillment Identification Number -->
                                        <span class="d-inline-block font-weight-bold"># {{ fulfillment.id }}</span>
                                                
                                        <Row v-if="fulfillment.recipient_name || fulfillment.recipient_contact || fulfillment.notes" 
                                             :gutter="12" class="border-bottom mt-2 pb-2">
                                            
                                            <Col v-if="fulfillment.recipient_name" :span="12">

                                                <!-- Recipient Name -->
                                                <span class="mt-2 d-block">
                                                    <span class="font-weight-bold">Recipient: </span> 
                                                    <span>{{ fulfillment.recipient_name }}</span>
                                                </span>

                                            </Col>
                                            
                                            <Col v-if="fulfillment.recipient_contact" :span="12">
                                        
                                                <!-- Recipient Contact -->
                                                <span class="mt-2 d-block">
                                                    <span class="font-weight-bold">Contact: </span> 
                                                    <span>{{ fulfillment.recipient_contact }}</span>
                                                </span>
                                                
                                            </Col>
                                            
                                            <Col v-if="fulfillment.notes" :span="24">

                                                <!-- FuLfillment Additional Notes -->
                                                <span class="mt-2 d-block">
                                                    <span class="font-weight-bold">Notes: </span> 
                                                    <span>{{ fulfillment.notes }}</span>
                                                </span>
                                                        
                                            </Col>

                                        </Row>

                                    </div>

                                    <List item-layout="vertical">
                                        <ListItem v-for="(item, y) in fulfillment.item_lines" :key="y" class="pt-3 pb-2">
                                            
                                            <Row>

                                                <!-- Item Image -->
                                                <Col :span="2">
                                                    <Badge type="primary" :count="parseInt(item.quantity)">
                                                        <Avatar icon="ios-images-outline" shape="square" size="large" />
                                                    </Badge>
                                                </Col>

                                                <!-- Item Details -->
                                                <Col :span="14">
                                                    <span class="d-block font-weight-bold text-primary">{{ item.name }}</span>
                                                    <span style="font-size: 12px;">
                                                        <span class="font-weight-bold">SKU:</span> {{ item.sku }}
                                                    </span>
                                                </Col>

                                                <!-- Item Price & Quantity -->
                                                <Col :span="4">
                                                    <span>
                                                        {{ formatPrice((item.grand_total / item.quantity), currency) }} x {{ item.quantity }}
                                                    </span>
                                                </Col>

                                                <Col :span="4">
                                                    <span class="text-right d-block">
                                                        {{ formatPrice(item.grand_total, currency) }}
                                                    </span>
                                                </Col>

                                            </Row>

                                        </ListItem>

                                        <ListItem class="clearfix">

                                            <!-- More actions Button -->
                                            <Button type="text" class="float-right text-dark p-0" ghost>

                                                <!-- Dropdown -->
                                                <Dropdown trigger="click" placement="bottom-end">
                                                    
                                                    <!-- Title -->
                                                    <span class="p-2">
                                                        <span>More actions</span>
                                                        <Icon type="ios-arrow-down"></Icon>
                                                    </span>

                                                    <!-- Dropdown Options -->
                                                    <DropdownMenu slot="list" class="text-left">

                                                        <DropdownItem>
                                                            <span>Add tracking details</span>
                                                        </DropdownItem>
                                                        <DropdownItem>
                                                            <span>Print packing slip</span>
                                                        </DropdownItem>

                                                        <Divider class="mt-1 mb-1"></Divider>

                                                        <DropdownItem @click.native="cancelFulfillment(fulfillment, x)">
                                                            <span>Cancel fulfillment</span>
                                                        </DropdownItem>

                                                    </DropdownMenu>

                                                </Dropdown>
                                            
                                            </Button>
                                                
                                        </ListItem>

                                    </List>

                                </Card>
                                
                            </Col>

                            <!-- Order Customer / Notes / Tags -->
                            <Col :span="8">

                                Customer
                                <hr>
                                Notes
                                <hr>
                                Tags
                                <hr>

                            </Col>

                        </Row>

                    </template>

                    <Row v-else>

                        <!-- Loading order -->
                        <Col :span="12" :offset="6"> 

                            <!-- Show loader -->
                            <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading order...</Loader>

                        </Col>

                    </Row>

                </Col>

            </Row>

        </template>

    </div>

</template>

<script>

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /*  Loaders  */
    import orderFulfillmentWidget from './fulfillment/main.vue';

    import moment from 'moment';

    export default {
        props:{
            orderUrl: {
                type: String,
                default: null
            },
            orders: {
                type: Array,
                default: function(){
                    return []
                }
            }
        },
        components: { 
            basicButton, Loader, orderFulfillmentWidget
        },
        data(){
            return {
                moment: moment,
                showOrderFulfillmentWidget: false,
                isCancellingOrderFulfillment: null,
                localOrderUrl: this.orderUrl,
                isLoadingOrder: false,
                isSavingOrder: false,
                order: null
            }
        },
        watch: {

            //  Watch for changes on the orderUrl
            orderUrl: {
                handler: function (val, oldVal) {

                    //  If the updated order url is not the same as the current local order url
                    if( this.localOrderUrl != val ){

                        //  Update the local order url value
                        this.localOrderUrl = val;

                        //  Fetch the order
                        this.fetchOrder();

                    }

                },
                deep: true
            }
        },
        computed: {
            //  Check if we can navigate to the next order
            currency(){

                return this.order.currency.symbol || this.order.currency.code;

            },
            //  Check if we can navigate to the next order
            canNavigateToNextOrder(){
                
                let index = this.getCurrentOrderIndex();

                //  If we have the current order index
                if( index != null ){

                    //  Check if the next order exists
                    if( this.orders[index + 1] != undefined ){

                        return true;

                    }

                }

                return false;
            },
            //  Check if we can navigate to the previous order
            canNavigateToPreviousOrder(){
                
                let index = this.getCurrentOrderIndex();

                //  If we have the current order index
                if( index != null ){

                    //  Check if the previous order exists
                    if( this.orders[index - 1] != undefined ){

                        return true;

                    }

                }

                return false;
            }
        },
        methods: {
            closeFulfillmentWidget(){

                //  Close the current order fulfillment widget
                this.showOrderFulfillmentWidget = false;

                //  Refetch the order
                this.fetchOrder();
            },
            countItemQuantities( line_items = [] ){
                
                var total = 0;

                for(var x=0; x < line_items.length; x++){
                    
                    total += parseInt(line_items[x].quantity);

                }   

                return total;

            },
            getCurrentOrderIndex(){
                /**
                 *  First we need to check if we have orders so that we can be able to check the index of our order. 
                 *  If we don't have any orders there is no need to continue with the rest of the logic. Once we have 
                 *  confirmed that we have orders, we need to iterate through each order so that we can identify the 
                 *  order with an href that matches our current order href. Once we have located that order, we need 
                 *  to get its index value so that we know exactly where it is located in the orders list. 
                 * 
                 *  Since we are using the map function, the results will contain null values for checks that failed 
                 *  to match the href value e.g:
                 * 
                 *  let results = [null, null , 2, null, null]
                 * 
                 *  As you can see above, the null values are returned for scenerios where the order being checked does 
                 *  not match the check. The "2" is the index of the order that matched our check successfully. We need
                 *  to make sure that we get rid of all the null values and only return the non-null value.
                 */

                //  Check if we have orders
                if( this.orders.length ){

                    //  Foreach order
                    let results = this.orders.map( (order, index) => {

                        //  Check if the current order matches the given order url
                        if(this.orderUrl == ((order._links || {}).self || {}).href){

                            //  If it does return the index value
                            return index;

                        }
                        
                        //  If it does not the map function will return null by default

                    });

                    //  Set the index to null by default
                    let index = null;

                    /**
                     *  We need to only get the non-null value
                     */
                    for(var x = 0; x < results.length; x++){

                        //  If not equal to null
                        if( results[x] != null ){

                            //  Set index to the non null value e.g 0, 1, 2, e.t.c
                            index = results[x];

                        }

                    }

                    return index;

                }
            },
            goToNextOrder(){
                
                let index = this.getCurrentOrderIndex();

                //  If we have the current order index
                if( index != null ){

                    //  Check if the next order exists
                    if( this.orders[index + 1] != undefined ){

                        //  Get the next order
                        let order = this.orders[index + 1];

                        //  Get the next order href
                        let activeOrderUrl = ((order._links || {}).self || {}).href

                        this.$emit('changeOrder', activeOrderUrl);

                    }

                }
            },
            goToPreviousOrder(){
                
                let index = this.getCurrentOrderIndex();

                //  If we have the current order index
                if( index != null ){

                    //  Check if the previous order exists
                    if( this.orders[index - 1] != undefined ){

                        //  Get the next order
                        let order = this.orders[index - 1];

                        //  Get the next order href
                        let activeOrderUrl = ((order._links || {}).self || {}).href

                        this.$emit('changeOrder', activeOrderUrl);

                    }

                }
            },
            getStatusColor(status){
                if (['Draft'].includes(status)) {
                    return 'default';
                }else if(['Cancelled'].includes(status)) {
                    return 'error';
                }else if(['Open', 'Archieved'].includes(status)) {
                    return 'success';
                }else{
                    return '';
                }
            },
            getPaymentStatusColor(status){
                if (['Unpaid'].includes(status)) {
                    return 'default';
                }else if(['Pending', 'Partially paid'].includes(status)) {
                    return 'warning';
                }else if(['Failed Payment'].includes(status)) {
                    return 'error';
                }else if(['Authorized', 'Refunded', 'Partially Refunded', 'Paid', 'Voided'].includes(status)) {
                    return 'success';
                }else{
                    return '';
                }
            },
            getFulfillmentStatusColor(status){
                if (['Unfulfilled'].includes(status)) {
                    return 'default';
                }else if(['Partially Fulfilled'].includes(status)) {
                    return 'warning';
                }else if(['Fulfilled'].includes(status)) {
                    return 'success';
                }else{
                    return '';
                }
            },
            formatDate(date, withTime = false) {
                if( withTime ){

                    return this.moment(date).format('MMM DD YYYY @H:mmA');

                }else{

                    return this.moment(date).format('MMM DD YYYY');

                }
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            cancelFulfillment(fulfillment, index) {

                var postURL = ((fulfillment._links || {}).self || {}).href;

                if( postURL ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    this.isCancellingOrderFulfillment = index;

                    //  Console log to acknowledge the start of api process
                    console.log('Start cancelling the order fulfillment...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('delete', postURL)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isCancellingOrderFulfillment = null;

                            //  Remove the cancelled fulfillment
                            self.order._embedded.fulfillments._embedded.fulfillments.splice(index, 1);

                            //  Refetch the order 
                            self.fetchOrder();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isCancellingOrderFulfillment = null;

                            //  Console log Error Location
                            console.log('dashboard/order/show/main.vue - Error cancelling the order fulfillment...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            fetchOrder() {

                if(this.localOrderUrl){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  If we don't already have the order
                    if( this.localOrder != null ){

                        //  Start loader
                        self.isLoadingOrder = true;

                    }else{

                        self.isSavingOrder = true;

                    }

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting order...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.localOrderUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingOrder = false;
                            self.isSavingOrder = false;

                            //  Order the order data
                            self.order = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingOrder = false;
                            self.isSavingOrder = false;

                            //  Console log Error Location
                            console.log('dashboard/order/show/main.vue - Error getting order...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){

            this.fetchOrder();

        }
    };
  
</script>