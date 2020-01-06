<template>

    <div>

        <!-- Full Page Layout -->
        <Row :gutter="12">

            <!-- Saving Order Spinner  -->
            <Spin v-if="isSavingOrder" size="large" fix></Spin>

            <Col :span="24">

                <!-- Go Back Button -->
                <Row class="mb-3">

                    <Col :span="24">

                        <!-- Button to go back to the order page -->
                        <Button type="text" @click.native="$emit('goBack')">
                            <Icon type="ios-arrow-back" />
                            <span>Go Back</span>
                        </Button>
                        
                    </Col>

                </Row>

                <template v-if="localOrder && orderBeforeChange && !isLoadingOrder">

                    <!-- Order Heading / Save Button -->
                    <Row class="mb-3">

                        <!-- Order Heading -->
                        <Col :span="24">

                            <!-- Order Number -->
                            <h2 class="d-inline-block font-weight-bold mr-2 pl-4 text-dark">Fulfillment </h2>
                            
                        </Col>

                    </Row>

                    <!-- Order Body -->
                    <Row :gutter="20">

                        <!-- Fulfilling Order Spinner  -->
                        <Spin v-if="isFulfillingOrder" size="large" fix></Spin>

                        <!-- Order Fulfillment / Payment / Timeline -->
                        <Col :span="16">

                            <Card dis-hover class="pt-2 pr-2 pl-2 mb-2">

                                <div>

                                    <!-- Main Heading -->
                                    <h5 class="d-block text-dark mb-3">
                                        <span class="font-weight-bold">Order #{{ localOrder.id }} - Mark as fulfilled</span>
                                    </h5>

                                    <!-- Sub Heading -->
                                    <h6 class="text-mute mb-3">
                                        <span class="font-weight-bold">QUANTITY TO FULFILL</span>
                                    </h6>

                                </div>

                                <Alert v-if="numberOfFulfilledItemQuantities == 0" type="warning" class="mb-3">
                                    You need to fulfill at least 1 item.
                                </Alert>


                                <List item-layout="vertical">

                                    <ListItem class="pt-3 pb-2">
                                        
                                        <Row>

                                            <Col :span="20">
                                                <span class="d-block font-weight-bold">Items</span>
                                            </Col>

                                            <Col :span="4">
                                                <span class="d-block font-weight-bold">Quantity</span>
                                            </Col>

                                        </Row>

                                    </ListItem>

                                    <ListItem v-for="(item, index) in localOrder.unfulfilled_item_lines" :key="index" class="pt-3 pb-2">
                                        
                                        <Row>

                                            <!-- Item Image -->
                                            <Col :span="2">
                                                <Avatar icon="ios-images-outline" shape="square" size="large" />
                                            </Col>

                                            <!-- Item Details -->
                                            <Col :span="18">
                                                <span class="d-block font-weight-bold text-primary">{{ item.name }}</span>
                                                <span style="font-size: 12px;">
                                                    <span class="font-weight-bold">SKU:</span> {{ item.sku }}
                                                </span>
                                            </Col>

                                            <Col :span="4">

                                                <!-- Quantity Input Field -->
                                                <InputNumber v-model="item.quantity" 
                                                            size="large" 
                                                            class="w-100" 
                                                            :min="0" :step="1"
                                                            :disabled="isFulfillingOrder"
                                                            :max="orderBeforeChange.unfulfilled_item_lines[index].quantity" 
                                                            :formatter="value => `${value} / ${orderBeforeChange.unfulfilled_item_lines[index].quantity}`"
                                                            :parser="value => value.replace(` / ${orderBeforeChange.unfulfilled_item_lines[index].quantity}`, '')">
                                                </InputNumber>
                                                
                                            </Col>

                                        </Row>

                                    </ListItem>

                                </List>

                            </Card>

                            <Card dis-hover class="pt-2 pr-2 pl-2 mt-2 mb-2">

                                <!-- Receipient Details -->
                                <h6 class="text-mute mb-3">
                                    <span class="font-weight-bold">RECIPIENT DETAILS (OPTIONAL)</span>
                                </h6>
                                                
                                <Row :gutter="12">

                                    <!-- Recipient name -->
                                    <Col :span="12">

                                        <Input v-model="recipient_name" maxlength="100" placeholder="Recepient name" class="w-100 mb-2" />

                                    </Col>

                                    <!-- Recipient contact -->
                                    <Col :span="12">

                                        <Input v-model="recipient_contact" maxlength="100" placeholder="Recepient Phone/Email" class="w-100 mb-2" />

                                    </Col>

                                    <!-- Notes -->
                                    <Col :span="24">

                                        <Input v-model="notes" maxlength="100" show-word-limit type="textarea" placeholder="Add notes or comments..." class="w-100 mb-4" />

                                    </Col>

                                </Row>
                                
                            </Card>
                            
                        </Col>

                        <!-- Order summary -->
                        <Col :span="8">

                            <Card dis-hover class="pt-2 pr-2 pl-2 mb-2">

                                <!-- Summary heading -->
                                <h5 class="d-block text-dark mb-3">
                                    <span class="font-weight-bold">Summary</span>
                                </h5>

                                <!-- Number of items being fulfilled -->         
                                <span>
                                    <span class="mr-1">Fulfilling:</span>
                                    <Badge type="primary" :text="numberOfFulfilledItemQuantities +' of '+ numberOfAlltemQuantities"></Badge>
                                </span>

                                <div class="clearfix border-top pt-2 mt-4">
                                    
                                    <!-- Fulfill Items Button -->  
                                    <basicButton @click.native="fulfillOrder()" size="default"
                                                type="success" class="float-right"
                                                :disabled="isFulfillingOrder || numberOfFulfilledItemQuantities == 0">
                                        <Icon type="md-checkbox-outline" :size="20" class="mr-1" />
                                        <span>Fulfill Items</span>
                                    </basicButton>
                                        
                                </div>

                            </Card>

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

    </div>

</template>

<script>

    /*  Buttons  */
    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../../components/_common/loaders/Loader.vue';

    export default {
        props:{
            orderUrl: {
                type: String,
                default: null
            }
        },
        components: { 
            basicButton, Loader
        },
        data(){
            return {
                localOrderUrl: this.orderUrl,
                isFulfillingOrder: false,
                orderBeforeChange: null,
                recipient_contact: null,
                isLoadingOrder: false,
                isSavingOrder: false,
                recipient_name: null,
                localOrder: null,
                notes: null
            }
        },
        computed: {
            currency(){

                return this.localOrder.currency.symbol || this.localOrder.currency.code;

            },
            numberOfAlltemQuantities(){
                
                var total = 0;

                if( ((this.orderBeforeChange || {}).unfulfilled_item_lines || {}).length ){

                    for(var x=0; x < this.orderBeforeChange.unfulfilled_item_lines.length; x++){
                        
                        total += parseInt(this.orderBeforeChange.unfulfilled_item_lines[x].quantity);

                    }  

                } 

                return total;

            },
            numberOfFulfilledItemQuantities(){
                
                var total = 0;

                if( ((this.localOrder || {}).unfulfilled_item_lines || {}).length ){

                    for(var x=0; x < this.localOrder.unfulfilled_item_lines.length; x++){
                        
                        total += parseInt(this.localOrder.unfulfilled_item_lines[x].quantity);

                    }   

                }

                return total;

            },
            checkIfOrderHasChanged(){
                var now = _.cloneDeep(this.localOrder);
                var before = (this.orderBeforeChange);
                var isNotEqual = !_.isEqual(now, before);

                return isNotEqual;
            }
        },
        methods: {
            fetchOrder() {

                if(this.localOrderUrl){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingOrder = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting order...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.localOrderUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingOrder = false;

                            //  Order the order data
                            self.localOrder = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingOrder = false;

                            //  Console log Error Location
                            console.log('dashboard/order/show/main.vue - Error getting order...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            
            fulfillOrder() {

                var postURL = ((((this.localOrder || {})._links || [])['oq:fulfillment'] ) || {}).href;

                if( postURL ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isFulfillingOrder = true;

                    var fulfillmentData = {

                        notes: this.notes,
                        recipient_name: this.recipient_name,
                        recipient_contact: this.recipient_contact,
                        item_lines: this.localOrder.unfulfilled_item_lines

                    };

                    //  Console log to acknowledge the start of api process
                    console.log('Start order fulfillment...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('post', postURL, fulfillmentData)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isFulfillingOrder = false;

                            //  Go back to the order
                            self.$emit('goBack');

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isFulfillingOrder = false;

                            //  Console log Error Location
                            console.log('dashboard/order/show/main.vue - Error fulfilling order...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            storeOriginalOrderData(){

                //  Store the original order data
                this.orderBeforeChange = _.cloneDeep(this.localOrder);

            }
        },
        created(){

            const self = this;

            this.fetchOrder().then( (data) =>{ 

                //  Store the original order data before editing
                self.storeOriginalOrderData();

            });

        }
    };
  
</script>