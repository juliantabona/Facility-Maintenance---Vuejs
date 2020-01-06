<template>

    <div>

        <!-- If we want to show the current order fulfillment/payment widget -->
        <template v-if="showOrderFulfillmentWidget || showOrderPaymentWidget">

            <!-- If we want to show the current order fulfillment widget -->
            <template v-if="showOrderFulfillmentWidget">

                <!-- Order Fulfillment Widget -->
                <orderFulfillmentWidget :orderUrl="orderUrl"
                    @goBack="closeFulfillmentWidget()">
                </orderFulfillmentWidget>

            </template>

            <!-- If we want to show the current order payment -->
            <template v-if="showOrderPaymentWidget">

                <!-- Order Payment Widget -->
                <orderPaymentWidget :orderUrl="orderUrl"
                    @goBack="closePaymentWidget()">
                </orderPaymentWidget>

            </template>

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
                            <Button type="text" @click.native="goBack()">
                                <Icon type="ios-arrow-back" />
                                <span>Orders</span>
                            </Button>
                            
                        </Col>

                        <template v-if="localOrder && !isSavingOrder && !isLoadingOrder">

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

                        </template>

                    </Row>

                    <!-- Loading order -->
                    <Row v-if="isLoadingOrder">

                        <Col :span="12" :offset="6"> 

                            <!-- Show loader -->
                            <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading order...</Loader>

                        </Col>

                    </Row>

                    <template v-if="localOrder && !isLoadingOrder">

                        <!-- Order Heading / Save Button -->
                        <Row class="mb-1">

                            <!-- Order Heading -->
                            <Col :span="24">

                                <!-- Order Number -->
                                <h2 class="d-inline-block font-weight-bold mr-2 pl-4 text-dark">#{{ localOrder.id }}</h2>
                                
                                <!-- Created Date -->
                                <span class="d-inline-block mr-2 text-dark">{{ formatDate(localOrder.created_at.date, true) }}</span>

                                <!-- order Status -->
                                <orderStatusBadge :status="localOrder.status"></orderStatusBadge>

                                <!-- Payment Status -->
                                <orderPaymentStatusBadge :status="localOrder.payment_status"></orderPaymentStatusBadge>

                                <!-- Fulfillment Status -->
                                <orderFulfillmentStatusBadge :status="localOrder.fulfillment_status"></orderFulfillmentStatusBadge>
                                                
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
                        <Row :gutter="20" class="mb-2">

                            <!-- Order Fulfillment / Payment / Timeline -->
                            <Col :span="16">

                                <!-- Unfulfilled Items -->
                                <Card v-if="localOrder.unfulfilled_item_lines.length" dis-hover class="pt-2 pr-2 pl-2 mb-2">

                                    <!-- Number Of Unfulfilled Items -->
                                    <h5 class="text-dark mb-3">
                                        <Icon type="ios-cube-outline" :size="25" color="#fff" class="p-1" style="background: #f90; border-radius: 100%;"/>
                                        <span class="font-weight-bold d-inline-block">Unfulfilled ({{ countItemQuantities(localOrder.unfulfilled_item_lines) }})</span>
                                    </h5>

                                    <List item-layout="vertical">
                                        <ListItem v-for="(item, index) in localOrder.unfulfilled_item_lines" :key="index" class="pt-3 pb-2">
                                            
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
                                                        <span class="d-block">SKU:</span> {{ item.sku }}
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

                                <!-- Fulfilled Items -->
                                <Card v-for="(fulfillment, x) in (((localOrder._embedded || {}).fulfillments || {})._embedded || {}).fulfillments" :key="x" dis-hover class="pt-2 pr-2 pl-2 mb-2">
                                    
                                    <!-- Cancelling order fulfillment spinner  -->
                                    <Spin v-if="isCancellingOrderFulfillment == x" size="large" fix></Spin>

                                    <!-- Number Of Fulfillment Items -->
                                    <div>
                                        
                                        <Row>

                                            <Col :span="24" class="clearfix">

                                                <h5 class="d-inline-block text-dark">
                                                    <Icon type="md-checkmark" :size="25" color="#fff" class="p-1" style="background: #19be6b; border-radius: 100%;"/>
                                                    <span class="d-inline-block font-weight-bold mr-1">Fulfilled ({{ countItemQuantities(fulfillment.item_lines) }})</span>
                                                </h5>

                                                <!-- FuLfillment Identification Number -->
                                                <span class="d-inline-block font-weight-bold"># {{ fulfillment.id }}</span>

                                                <div class="float-right">

                                                    <!-- Clock Icon -->
                                                    <Icon type="ios-time-outline" :size="20" />

                                                    <!-- Created Date -->
                                                    <span class="d-inline-block mr-2 text-dark">{{ formatDate(fulfillment.created_at.date, true) }}</span>

                                                </div>

                                            </Col> 

                                        </Row>   

                                        <Row v-if="fulfillment.recipient_name || fulfillment.recipient_contact || fulfillment.notes" 
                                             :gutter="12" class="border-bottom mt-2 pb-2">
                                            
                                            <Col v-if="fulfillment.recipient_name" :span="12">

                                                <!-- Recipient Name -->
                                                <span class="mt-2 d-block">
                                                    <span class="d-block">Recipient: </span> 
                                                    <span>{{ fulfillment.recipient_name }}</span>
                                                </span>

                                            </Col>
                                            
                                            <Col v-if="fulfillment.recipient_contact" :span="12">
                                        
                                                <!-- Recipient Contact -->
                                                <span class="mt-2 d-block">
                                                    <span class="d-block">Contact: </span> 
                                                    <span>{{ fulfillment.recipient_contact }}</span>
                                                </span>
                                                
                                            </Col>
                                            
                                            <Col v-if="fulfillment.notes" :span="24">

                                                <!-- FuLfillment Additional Notes -->
                                                <span class="mt-2 d-block">
                                                    <span class="d-block">Notes: </span> 
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
                                                        <span class="d-block">SKU:</span> {{ item.sku }}
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

                                <!-- Payment -->
                                <Card dis-hover class="pt-2 pr-2 pl-2 mb-2">

                                    <!-- Heading -->
                                    <h5 class="text-dark border-bottom pb-3">
                                        <Icon type="ios-cash-outline" :size="25" color="#fff" class="p-1" 
                                              :style="'background:#' +(localOrder.payment_status.name == 'Paid' ? '19be6b': 'f90')+';border-radius: 100%;'"/>
                                        <span class="font-weight-bold d-inline-block">Payment</span>
                                    </h5>

                                    <List item-layout="vertical">
                                            
                                        <!-- Grand Total -->                                        
                                        <ListItem class="pt-3 pb-2">

                                            <!-- Subtotal -->   
                                            <Row>

                                                <!-- Sub heading -->
                                                <Col :span="20">
                                                    <span>Subtotal</span>
                                                </Col>

                                                <!-- Amount -->
                                                <Col :span="4">
                                                    <span class="d-block text-right">{{ formatPrice(localOrder.sub_total, currency) }}</span>
                                                </Col>

                                            </Row>
                                            
                                            <!-- Taxes -->   
                                            <Row>

                                                <!-- Sub heading -->
                                                <Col :span="20">
                                                    <span>Taxes</span>
                                                </Col>

                                                <!-- Amount -->
                                                <Col :span="4">
                                                    <span class="d-block text-right">{{ formatPrice(localOrder.grand_tax_total, currency) }}</span>
                                                </Col>

                                            </Row>
                                            
                                            <!-- Discounts -->   
                                            <Row>

                                                <!-- Sub heading -->
                                                <Col :span="20">
                                                    <span>Discount</span>
                                                </Col>

                                                <!-- Amount -->
                                                <Col :span="4">
                                                    <span class="d-block text-right">{{ formatPrice(localOrder.grand_discount_total, currency) }}</span>
                                                </Col>

                                            </Row>
                                            
                                            <!-- Grand Total -->    
                                            <Row class="mt-2">

                                                <!-- Sub heading -->
                                                <Col :span="20">
                                                    <span class="d-block font-weight-bold">Grand Total</span>
                                                </Col>

                                                <!-- Amount -->
                                                <Col :span="4">
                                                    <span class="d-block font-weight-bold text-right">{{ formatPrice(localOrder.grand_total, currency) }}</span>
                                                </Col>

                                            </Row>

                                        </ListItem>  

                                        <!-- Payment by customer -->                                    
                                        <ListItem class="pt-2 pb-2">
                                            
                                            <Row>

                                                <!-- Sub heading -->
                                                <Col :span="20">
                                                    <span class="d-block font-weight-bold">Paid by customer</span>
                                                </Col>

                                                <!-- Amount -->
                                                <Col :span="4">
                                                    <span class="d-block font-weight-bold text-right">{{ formatPrice(localOrder.transaction_total, currency) }}</span>
                                                </Col>

                                            </Row>
                                            
                                        
                                        </ListItem>

                                        <!-- Remaining Balance -->                                    
                                        <ListItem v-if="localOrder.transaction_total != 0 && localOrder.outstanding_balance != 0" class="pt-2 pb-2">
                                            
                                            <Row>

                                                <!-- Sub heading -->
                                                <Col :span="20">
                                                    <span class="d-block font-weight-bold">Remaining Balance</span>
                                                </Col>

                                                <!-- Amount -->
                                                <Col :span="4">
                                                    <span class="d-block font-weight-bold text-right">{{ formatPrice(localOrder.outstanding_balance, currency) }}</span>
                                                </Col>

                                            </Row>
                                            
                                        
                                        </ListItem>

                                        <ListItem class="clearfix">
                                            
                                            <basicButton 
                                                v-if="localOrder.outstanding_balance != 0"
                                                @click.native="showOrderPaymentWidget = true"
                                                size="default" type="success" class="float-right">
                                                <Icon type="md-checkbox-outline" :size="20" class="mr-1" />
                                                <span>Mark As Paid</span>
                                            </basicButton>
                                                
                                        </ListItem>

                                    </List>

                                </Card>
                                
                            </Col>

                            <!-- Order Customer / Notes / Tags -->
                            <Col :span="8">


                                <!-- Customer Card -->
                                <Card dis-hover class="pt-2 pr-2 pl-2 mb-2">

                                    <List item-layout="vertical">
                                            
                                        <!-- Customer Details -->                                        
                                        <ListItem class="pt-3 pb-2">
  
                                            <Row>

                                                <Col :span="24">

                                                    <!-- Heading -->
                                                    <h5 class="text-dark border-bottom pb-2 mb-2 clearfix">
                                                        <span class="font-weight-bold d-block float-left mt-2">Customer</span>
                                                        <initialsAvatar :text="customer.name" class="float-right"></initialsAvatar>
                                                    </h5>

                                                    <!-- Customer Name -->
                                                    <Button type="text" ghost class="p-0 d-block" style="color:#2d8cf0;">
                                                        {{ customer.name }}
                                                    </Button>
                                                    
                                                    <!-- Number Of Orders -->
                                                    <Button type="text" ghost class="p-0 d-block" style="color:#2d8cf0;">
                                                        {{ customerOrderTotal == 1 ? customerOrderTotal + ' Order' : customerOrderTotal + ' Orders' }}
                                                    </Button>

                                                </Col>

                                            </Row>

                                        </ListItem>  
                                            
                                        <!-- Contact Details -->                                       
                                        <ListItem class="pt-3 pb-2">
  
                                            <Row>

                                                <Col :span="24">

                                                    <!-- Heading -->
                                                    <h6 class="text-dark pb-3 font-weight-bold">CONTACT INFORMATION</h6>

                                                    <!-- Customer Phone -->
                                                    <span class="d-block">
                                                        {{ ((customer || {}).default_mobile || {}).full_number ? 'Phone: ' + customer.default_mobile.full_number: 'No phone number' }}
                                                    </span>

                                                    <!-- Customer Email -->
                                                    <span class="d-block">
                                                        {{ ((customer || {}).default_email || {}).email ? 'Email: ' + customer.default_email.email: 'No email' }}
                                                    </span>

                                                </Col>

                                            </Row>
  
                                            <Row class="mt-4">

                                                <Col :span="24"> 

                                                    <Collapse simple value="1">

                                                        <!-- Delivery Details -->
                                                        <Panel name="1">

                                                            <!-- Heading -->
                                                            <span class="text-dark font-weight-bold">Delivery Address</span>

                                                            <p slot="content" class="border-left pl-4 ml-2">

                                                                <template v-if="(deliveryAddress || {}).default_address">

                                                                    <!-- Delivery Name -->
                                                                    <span class="d-block">{{ deliveryAddress.name }}</span>

                                                                    <!-- Delivery Address 1 -->
                                                                    <span class="d-block">{{ deliveryAddress.default_address.address_1 }}</span>

                                                                    <!-- Delivery Address 2 -->
                                                                    <span class="d-block">{{ deliveryAddress.default_address.address_2 }}</span>

                                                                    <!-- Delivery City -->
                                                                    <span class="d-block">
                                                                        {{ deliveryAddress.default_address.postal_or_zipcode ? deliveryAddress.default_address.postal_or_zipcode +' ' : '' }}
                                                                        {{ deliveryAddress.default_address.city }}
                                                                    </span>

                                                                    <!-- Delivery Province -->
                                                                    <span class="d-block">{{ deliveryAddress.default_address.province }}</span>

                                                                    <!-- Delivery Country -->
                                                                    <span class="d-block">{{ deliveryAddress.default_address.country }}</span>

                                                                    <!-- Delivery Phone -->
                                                                    <span class="d-block">{{ (deliveryAddress.default_mobile).full_number }}</span>

                                                                </template>

                                                                <template v-else>
                                                                    <span class="text-muted d-block">No delivery address</span>
                                                                </template>
                                                                
                                                            </p>

                                                        </Panel>

                                                        <!-- Billing Details -->
                                                        <Panel name="2">

                                                            <!-- Heading -->
                                                            <span class="text-dark font-weight-bold">Billing Address</span>

                                                            <p slot="content" class="border-left pl-4 ml-2">

                                                                <template v-if="(billingAddress || {}).default_address">

                                                                    <!-- Billing Name -->
                                                                    <span class="d-block">{{ billingAddress.name }}</span>

                                                                    <!-- Billing Address 1 -->
                                                                    <span class="d-block">{{ billingAddress.default_address.address_1 }}</span>

                                                                    <!-- Billing Address 2 -->
                                                                    <span class="d-block">{{ billingAddress.default_address.address_2 }}</span>

                                                                    <!-- Billing City -->
                                                                    <span class="d-block">
                                                                        {{ billingAddress.default_address.postal_or_zipcode ? billingAddress.default_address.postal_or_zipcode +' ' : '' }}
                                                                        {{ billingAddress.default_address.city }}
                                                                    </span>

                                                                    <!-- Billing Province -->
                                                                    <span class="d-block">{{ billingAddress.default_address.province }}</span>

                                                                    <!-- Billing Country -->
                                                                    <span class="d-block">{{ billingAddress.default_address.country }}</span>

                                                                    <!-- Billing Phone -->
                                                                    <span class="d-block">{{ (billingAddress.default_mobile).full_number }}</span>

                                                                </template>

                                                                <template v-else>
                                                                    <span class="text-muted d-block">No billing address</span>
                                                                </template>
                                                                
                                                            </p>

                                                        </Panel>

                                                    </Collapse>

                                                </Col>

                                            </Row>

                                        </ListItem> 

                                    </List>

                                </Card>

                            </Col>

                        </Row>

                        <!-- Invoice Table List With Search Input, Filters & Sort Functionality -->
                        <Row v-if="invoicesUrl && hasInvoices" class="mb-2">

                            <Col :span="24">

                                <!-- Invoice Table Widget -->
                                <invoiceTableWidget 
                                    title="Invoices"
                                    :invoicesUrl="invoicesUrl"
                                    :showFilterAndSortingOptions="hasManyInvoices"
                                    :showPaginationOptions="hasManyInvoices">
                                </invoiceTableWidget>

                            </Col>

                        </Row>

                        <!-- Transaction Table List With Search Input, Filters & Sort Functionality -->
                        <Row v-if="transactionsUrl && hasTransactions" class="mb-2">

                            <Col :span="24">

                                <!-- Transaction Table Widget -->
                                <transactionTableWidget 
                                        title="Transactions"
                                        :transactionsUrl="transactionsUrl" 
                                        :showFilterAndSortingOptions="hasManyTransactions"
                                        :showPaginationOptions="hasManyTransactions">
                                </transactionTableWidget>

                            </Col>

                        </Row>                        

                    </template>

                </Col>

            </Row>

        </template>
        
        <!-- 
            MODAL TO MARK ORDER AS PAID
        -->
        <markOrderAsPaidModal
            v-if="isOpenPaymentModal" 
            @visibility="isOpenPaymentModal = $event"
            @selected="true">
        </markOrderAsPaidModal>

    </div>

</template>

<script>

    /*  Modals  */
    import markOrderAsPaidModal from './../../../components/_common/modals/markOrderAsPaidModal.vue';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /*  Avatars  */
    import initialsAvatar from './../../../components/_common/avatars/initialsAvatar.vue';  
    
    /*  Status Badges  */
    import orderStatusBadge from './../../../components/_common/statuses/orderStatusBadge.vue';  
    import orderPaymentStatusBadge from './../../../components/_common/statuses/orderPaymentStatusBadge.vue';  
    import orderFulfillmentStatusBadge from './../../../components/_common/statuses/orderFulfillmentStatusBadge.vue'; 

    /*  External Widgets  */
    import invoiceTableWidget from './../../invoice/list/table.vue';
    import transactionTableWidget from './../../transaction/list/table.vue';

    /*  Local Widgets  */
    import orderPaymentWidget from './payment/main.vue';
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
            markOrderAsPaidModal, basicButton, Loader, initialsAvatar, orderStatusBadge, orderPaymentStatusBadge,
            orderFulfillmentStatusBadge, invoiceTableWidget, transactionTableWidget, 
            orderPaymentWidget, orderFulfillmentWidget
        },
        data(){
            return {
                moment: moment,
                showOrderPaymentWidget: false,
                showOrderFulfillmentWidget: false,
                isCancellingOrderFulfillment: null,
                localOrderUrl: this.orderUrl,
                isOpenPaymentModal: false,
                isLoadingOrder: false,
                isSavingOrder: false,
                localOrder: null
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
            //  Get the order customer
            customer(){

                return ((this.localOrder || {})._embedded || {}).customer;

            },
            //  Get the customer's total number of orders
            customerOrderTotal(){

                return (((this.customer || {})._links || [])['oq:orders'] || {}).total;

            },
            //  Get the bulling address
            billingAddress(){

                return (this.localOrder || {}).billing_info;

            },
            //  Get the delivery address
            deliveryAddress(){

                return (this.localOrder || {}).shipping_info;

            },
            //  Get the order reference
            reference(){

                return ((this.localOrder || {})._embedded || {}).reference;

            },
            //  Get the current order invoices url
            invoicesUrl(){

                return (((this.localOrder || {})._links || [])['oq:invoices'] || {}).href;

            },
            //  Get the current order transactions url
            transactionsUrl(){

                return (((this.localOrder || {})._links || [])['oq:transactions'] || {}).href;

            },
            //  Check if the current order has invoices
            hasInvoices(){

                return (((this.localOrder || {})._links || [])['oq:invoices'] || {}).total ? true: false;

            },
            //  Check if the order has many invoices
            hasManyInvoices(){

                return (((this.localOrder || {})._links || [])['oq:invoices'] || {}).total > 10 ? true: false;

            },
            //  Check if the current order has transactions
            hasTransactions(){

                return (((this.localOrder || {})._links || [])['oq:transactions'] || {}).total ? true: false;

            },
            //  Check if the order has many transactions
            hasManyTransactions(){

                return (((this.localOrder || {})._links || [])['oq:transactions'] || {}).total > 10 ? true: false;

            },
            //  Check if we can navigate to the next order
            currency(){

                return this.localOrder.currency.symbol || this.localOrder.currency.code;

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
            goBack(){
                
                this.isSavingOrder = true;

                /** Wait 1/10 of a second then notify the parent. This is because when we go back to the parent 
                 *  component that holds the list of many other orders, the operation is so heavy that it makes 
                 *  the loader to lag and sometimes not show up instead of showing up immediately. By using the 
                 *  setTimeout we are saying that we want to show the loader first then after its displayed
                 *  (after 1/10 of a second) then perform the heavy logic of going back.
                 */

                const self = this;

                setTimeout(()=>{

                    self.$emit('goBack');

                }, 100);

            },
            closePaymentWidget(){

                //  Close the current order payment widget
                this.showOrderPaymentWidget = false;

                //  Refetch the order
                this.fetchOrder();

            },
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
                        if( this.localOrderUrl == (((order || {})._links || {}).self || {}).href ){

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

                return null;
            },
            goToNextOrder(){
                
                let index = this.getCurrentOrderIndex();

                //  If we have the current order index
                if( index != null ){

                    //  Check if the next order exists
                    if( this.orders[index + 1] != undefined ){

                        //  Get the next order
                        let current_order = this.orders[index + 1];

                        //  Get the next order href
                        let activeOrderUrl = (((current_order || {})._links || {}).self || {}).href

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
                        let current_order = this.orders[index - 1];

                        //  Get the next order href
                        let activeOrderUrl = (((current_order || {})._links || {}).self || {}).href

                        this.$emit('changeOrder', activeOrderUrl);

                    }

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
                return (symbol ? symbol : '') + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            cancelFulfillment(fulfillment, index) {

                var postURL = (((fulfillment || {})._links || {}).self || {}).href;

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
                            self.localOrder._embedded.fulfillments._embedded.fulfillments.splice(index, 1);

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

                    //  If we already have the order
                    if( self.localOrder ){

                        //  Start saving loader
                        self.isSavingOrder = true;

                    }else{

                        //  Start loading loader
                        self.isLoadingOrder = true;

                    }

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting order...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.localOrderUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loaders
                            self.isLoadingOrder = false;
                            self.isSavingOrder = false;

                            //  Order the order data
                            self.localOrder = data;

                        })         
                        .catch(response => { 

                            //  Stop loaders
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