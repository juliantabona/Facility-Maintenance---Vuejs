<style scoped>


    .cart-table >>> th,
    .cart-table >>> td {
        border: 1px #dee2e6 solid;
        padding: 10px;
    }

</style>

<template>

    <!-- Cart Side Widget -->
    <Row :gutter="24">

        <Col v-if="['table-cart', 'both'].includes(cartType)" :xs="24" :sm="cartType == 'both' ? 18 : 24">

            <div class="tt-shopcart-table">

                <!-- Loader -->
                <Loader v-if="(showParentIsLoading || isLoadingCartItems)" :loading="true" type="text" class="text-left mt-5 mb-5 ml-3" theme="white">Loading cart...</Loader>

                <template v-if="!showParentIsLoading">

                    <Row v-if="!isLoadingCartItems && cartItems.length" :gutter="12" class="mt-5 mb-3">
                        
                        <Col :span="24" class="clearfix mb-3">

                            <h1 class="float-left">SHOPPING CART</h1>

                            <router-link v-if="!hideCheckoutBtn" class="float-right mr-1" :to="{ name: 'checkout', params: { id: $route.params.id }}">
                                <span class="btn btn-primary"><span class="icon icon-check_circle"></span>PROCEED TO CHECKOUT</span>
                            </router-link>

                            <router-link v-if="!hideDownloadBtn" class="float-right mr-1" :to="{ name: 'checkout', params: { id: $route.params.id }}">
                                <span class="btn btn-outline-dark"><span class="icon icon-check_circle"></span>DOWNLOAD QUOTATION</span>
                            </router-link>
                            
                        </Col>

                        <Col :span="24">
                            <table class="cart-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-dark font-weight-bold text-center">Name</th>
                                        <th class="text-dark font-weight-bold text-center">Qty</th>
                                        <th class="text-dark font-weight-bold text-center">Price</th>
                                        <th class="text-dark font-weight-bold text-center">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in cartItems" :key="index">
                                        <td>
                                            <div class="tt-product-img">
                                                <img :src="product.primary_image.url" style="max-width:80px;max-height:80px;">
                                            </div>
                                        </td>
                                        <td>
                                            <span>{{ product.name }}</span>
                                        </td>
                                        <td>
                                            <div class="tt-input-counter style-01 small mt-2 ml-4 mr-4" style="max-width: 100%;">
                                                <span class="minus-btn" @click="updateItemQuantity(product, 'subtract')"></span>
                                                <input type="text" :value="product.quantity" size="5"
                                                        @input="updateItemQuantity(product, $event.target.value)">
                                                <span class="plus-btn" @click="updateItemQuantity(product, 'add')"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <!-- Show sale or normal price -->
                                                <span>
                                                    {{ product.store_currency_symbol + (product.unit_sale_price ? product.unit_sale_price : product.unit_price) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">P{{ getProductTotalPrice(product) }}</div>
                                        </td>
                                        <td>
                                            <!-- Remove Item  -->
                                            <Poptip confirm title="Are you sure you want to remove this?"  width="300" class="mr-3"
                                                    ok-text="Yes" cancel-text="No" @on-ok="removeItemFromCart(product)">
                                                <Icon type="md-trash" :size="20" />
                                            </Poptip>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </Col>

                        <Col :span="24">
                            <div class="tt-shopcart-btn">
                                <div class="col-left">
                                    <router-link :to="{ name: 'store', params: { id: $route.params.id }}">
                                        <span class="btn btn-link">
                                            <Icon type="md-arrow-back" :size="20" class="mr-1" />
                                            <span>CONTINUE SHOPPING</span>
                                        </span>
                                    </router-link>
                                </div>
                                <div class="col-right">
                                    <span class="btn btn-link">
                                        <Icon type="ios-repeat" :size="20" class="mr-1" />
                                        <span>CLEAR SHOPPING CART</span>
                                    </span>
                                </div>
                            </div>
                        </Col>

                    </Row>

                    <div v-if="!isLoadingCartItems && !cartItems.length">
                        <div style="width:100px;margin:80px auto 0;">
                            <img src="/images/assets/icons/cart.svg" style="filter: invert(87%) sepia(1%) saturate(670%) hue-rotate(54deg) brightness(99%) contrast(85%);">
                        </div>
                        <span class="d-block w-100 text-center mt-4">Your Cart Is Empty</span>
                    </div>

                </template>

            </div>

        </Col>

        <Col v-if="['widget-cart', 'both'].includes(cartType)" :xs="24" :sm="cartType == 'both' ? 6 : 24">

            <div class="cart-overview-widget">

                <div class="clearfix cart-overview-header"> 
                    <span class="text-white mt-2 float-left">{{ cartItems.length }} {{ cartItems.length == 1 ? ' Item': ' Items' }}</span>
                    <template v-if="!isLoadingCartItems && cartItems.length">
                        <router-link v-if="!hideCheckoutBtn" class="float-right" :to="{ name: 'checkout', params: { id: $route.params.id }}">
                            <span class="btn btn-link text-white">Checkout</span>
                        </router-link>
                        <Divider v-if="!hideCheckoutBtn" type="vertical" class="float-right" style="margin-top: 13px;" />
                        <router-link class="float-right" :to="{ name: 'cart', params: { id: $route.params.id }}">
                            <span class="btn btn-link text-white">View Cart</span>
                        </router-link>
                    </template>
                </div>

                <!-- Loader -->
                <Loader v-if="(showParentIsLoading || isLoadingCartItems)" :loading="true" type="text" class="text-left mt-5 mb-5 ml-3" theme="white">Loading cart...</Loader>

                <template v-if="!showParentIsLoading">
                    <div v-if="!isLoadingCartItems && cartItems.length" class="tt-shopcart-wrapper">
                        <scrollBox class="border">
                            <div class="tt-shopcart-box p-3" style="max-height: 300px;">
                                <table class="w-100">
                                    <tbody>
                                        <tr v-for="(product, index) in cartItems" :key="index" class="mb-1">
                                            <td>
                                                <div class="tt-product-img">
                                                    <img :src="product.primary_image.url" style="max-width:80px;max-height:80px;">
                                                </div>
                                            </td>
                                            <td style="position:relative;">
                                                <span class="d-block text-dark">{{ product.name }}</span>
                                                <span style="font-size:0.9;" class="d-inline-block">
                                                    {{ product.store_currency_symbol + (product.unit_sale_price ? product.unit_sale_price : product.unit_price) }}
                                                    each
                                                </span>
                                                <span style="font-size:0.9;" class="d-inline-block btn btn-link m-0 p-0 text-left"
                                                      @click="$router.push({ name: 'single-product', params: { storeId: storeId, productId: product.id } })">
                                                      View Details
                                                </span>
                                                <span style="display: inline-block; position: absolute; top: -10px; left: -30px; height: 1.7em; border-radius: 3.235801032000001em; background: #0071ce; color: #fff; padding: .38198205906665em .618046971569839em; text-align: center; font-size: .875rem; line-height: .875rem;">
                                                    {{ product.quantity }}
                                                </span>
                                            </td>
                                            <td class="text-right" style="font-size: 1.5em">

                                                <!-- Grand Total Price  -->
                                                <div class="tt-price font-weight-bold text-dark text-right">
                                                    {{ product.store_currency_symbol + getProductTotalPrice(product) }}</div>

                                                <!-- Remove Item  -->
                                                <Poptip confirm title="Are you sure you want to remove this?" width="300" placement="left-start"
                                                        ok-text="Yes" cancel-text="No" @on-ok="removeItemFromCart(product)">
                                                    <Icon type="md-trash"/>
                                                </Poptip>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                        </scrollBox>
                        <div class="tt-shopcart-box tt-boredr-large mt-2">
                            <table class="tt-shopcart-table01">
                                <tbody>
                                    <tr>
                                        <th>SUBTOTAL</th>
                                        <td>P{{ cartGrandTotal }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>GRAND TOTAL</th>
                                        <td><span style="font-size: 1.4em;">P{{ cartGrandTotal }}</span></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <router-link v-if="!hideDownloadBtn" class="d-block mb-3" :to="{ name: 'checkout', params: { id: $route.params.id }}">
                                <span class="btn btn-outline-dark"><span class="icon icon-check_circle"></span>DOWNLOAD QUOTATION</span>
                            </router-link>

                            <router-link v-if="!hideCheckoutBtn" class="d-block mb-3" :to="{ name: 'checkout', params: { id: $route.params.id }}">
                                <span class="btn btn-primary mb-3"><span class="icon icon-check_circle"></span>PROCEED TO CHECKOUT</span>
                            </router-link>
                            
                        </div>
                    </div>

                    <div v-if="!isLoadingCartItems && !cartItems.length">
                        <div style="width:100px;margin:80px auto 0;">
                            <img src="/images/assets/icons/cart.svg" style="filter: invert(87%) sepia(1%) saturate(670%) hue-rotate(54deg) brightness(99%) contrast(85%);">
                        </div>
                        <span class="d-block w-100 text-center mt-4">Your Cart Is Empty</span>
                    </div>
                </template>
            </div>

        </Col>

    </Row>
</template>

<script>

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    /*  scrollBox  */
    import scrollBox from './../../../components/_common/scrollBox/scrollBox.vue'; 

    export default {
        props: {
            showParentIsLoading: {
                type: Boolean,
                default: false
            },
            products: {
                type: Array,
                default: function(){ return [] }
            },
            addItem: {
                type: Object,
                default: null
            },
            removeItem: {
                type: Object,
                default: null
            },
            updateItem: {
                type: Object,
                default: null
            },
            cartType: {
                type: String,
                default: 'table-cart'    //  cart-table, widget-cart, both, 
            },
            hideCheckoutBtn: {
                type: Boolean,
                default: false   
            },
            hideDownloadBtn: {
                type: Boolean,
                default: false   
            }
        },
        components: { 
            Loader, scrollBox
        },
        data(){
            return {
                cartItems: [],
                localProducts: [],

                storeId: (this.$route.params || {}).storeId,

                //  Cart loading states
                isLoadingCartItems: false,
                isAddingItemToCart: false,
                isDeletingItemFromCart: false,
                isUpdatingItemInCart: false,
            }
        },
        watch: {
            //  Watch for changes on the page
            '$route.query.storeId': function (storeId) {
                
                this.storeId = storeId;

            },
            /*  The products watch checks all the changes experienced by the products
             *  that are currently listed on the shop page. When a value of any product
             *  changes e.g) if the item is set to be added to cart or has its quantity
             *  changed, then this dynamic watcher will be fired because of those
             *  changes. We can use this as an opportunity to check if any item
             *  has been listed to be added to cart. We can then add that item
             *  to the cart as required.
             */
            products: {
                handler: function (currProducts, oldProducts) {
                    
                    this.localProducts = currProducts;

                    //  Check for products that should be added to cart
                    for(var x=0; x < (currProducts || []).length; x++){
                        
                        var availableCartItemIds = this.cartItems.map( (item) => { return item.id });

                        //  If the current product is set to be inside the cart but is not inside the cart
                        if( (currProducts[x] || {}).inside_cart && !availableCartItemIds.includes(currProducts[x].id)){
                            //  Then add the item to the cart
                            this.cartItems.push(currProducts[x]);
                        }   
                    }

                    //  Check for products that should not be inside the cart
                    for(var x=0; x < (this.cartItems || []).length; x++){

                        //  If the product should not be inside the cart
                        if( !(this.cartItems[x] || {}).inside_cart){
                            //  Then remove it from the cart
                            this.cartItems.splice(x, 1);
                        }   

                    }

                },
                deep: true
            },

            showParentIsLoading: {
                handler: function (val, oldVal) {
                    
                    //  If the shop products are done loading
                    if( val === false){
                        this.fetchItemsFromCartSession();
                    }
                }
            },

            addItem: {
                handler: function (val, oldVal) {
                    
                    if(val){
                        //  Add the product to cart
                        this.addToCart(val);
                    }
                }
            },

            removeItem: {
                handler: function (val, oldVal) {
                    
                    if(val){
                        //  Remove the product to cart
                        this.removeItemFromCart(val);
                    }
                }
            },

            updateItem: {
                handler: function (val, oldVal) {
                    
                    if(val.product && val.action){
                        //  Update the product quantity
                        this.updateItemQuantity(val.product, val.action);
                    }
                }
            }

        },
        computed:{
            cartGrandTotal(){
                var itemPrices = this.cartItems.map( (item) => { 
                        return item.unit_sale_price ? (item.unit_sale_price *  item.quantity) : (item.unit_price *  item.quantity)
                    } );

                var itemTotals = itemPrices.reduce((a, b) => a + b, 0);
                return itemTotals;
            }
        },
        methods: {
            getProductTotalPrice(product){
                return product.unit_sale_price ? (product.unit_sale_price *  product.quantity) : (product.unit_price *  product.quantity)
            },
            
            /*  Whe the user wants to add a product to the cart, we will request the
             *  product object to be provided. We will then check if the product exists
             *  in the cart. If it does we will not do anything else, however if it does
             *  not then we can add a new property to the product object. We use the vue 
             *  $set method to ensure that vue can track the changes of the properties
             *  added, making them reactive. The "inside_cart" property will be used to
             *  determine is the product is inside the cart. If set to true then we will
             *  know that the product has been added to the cart. The "quantity" 
             *  property will b used to know how much quantity is required of that
             *  product. So far the product is added to cart only on the frontend,
             *  but not the server-end via cookies. This means that if the user
             *  turns of their computer or closes the browser we will lose their
             *  cart products. To avoid this we must use the "addProductToCartSession()"
             *  method which then pushes the product to b stored via cookies so that
             *  we can remember the cart products no matter what happens
             */
            addToCart(product){
                
                //  Only add the product if it does not exist in the cart
                if(!this.existsInCart(product)){  
                    
                    //  Add the item to cart 
                    this.$set(product, 'inside_cart', true);
                    this.$set(product, 'quantity', 1);

                    //  Add the product to the cart cookie
                    this.addProductToCartSession(product);
                }

            },

            /*  addProductToCartSession()
             *  This method will push the product we added to cart to be stored in a
             *  cookie session so that we can remember the product being in the cart
             *  even if the user closed their browser or switched off their computer
             *  The api call sends the product id, name, unit_sale_price, and unit_price to
             *  be stored in the cookie. The response wil be the actual product 
             *  as stored in the cookie session. It will be returned with a rowId
             *  which is a unique alphanumeric value that identifies the product in
             *  the cart. We can later use the rowId to find the product in the cart
             *  session and modify/update or delete it entirely. 
             */
            addProductToCartSession(product) {
                
                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isAddingItemToCart = true;

                //  Console log to acknowledge the start of api process
                console.log('Start adding product to cart...');

                var cartData = {
                        id: product.id,
                        name: product.name,
                        quantity: product.quantity,
                        unit_sale_price: product.unit_sale_price,
                        unit_price: product.unit_price,
                    }

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/cart', cartData)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Update the product with the cart row id
                        self.$set(product, 'cart_row_id', data.rowId);

                        //  Stop loader
                        self.isAddingItemToCart = false;
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isAddingItemToCart = false;

                        //  Console log Error Location
                        console.log('dashboard/product/show/main.vue - Error adding product to cart...');

                        //  Log the responce
                        console.log(response);    
                    });
            },

            /*  Whe the user wants to remove a product from the cart, we will request the
             *  product object to be provided.  We use the vue $set method to ensure that
             *  vue can track the changes of the properties added, making them reactive.
             *  The "inside_cart" property will be used to remove the product from the
             *  inside the cart. If set to false then we will know that the product has
             *  been removed to the cart.  So far the product is removed from cart only
             *  on the frontend, but not the server-end via cookies. This means that 
             *  if the user turns of their computer or closes the browser we will lose 
             *  their cart changes. To avoid this we must use the "removeProductFromCartSession()"
             *  method which then removes the product stored via cookies so that we can
             *  remember the cart products left no matter what happens
             */
            removeItemFromCart(product){
                //  Remove the item to cart 
                this.$set(product, 'inside_cart', false);

                //  Remove the cart product from cookie
                this.removeProductFromCartSession(product);
            },       

            /*  removeProductFromCartSession()
             *  This method will remove the product we added to cart and stored in a
             *  cookie session so that we can forget about its existence in the cart
             *  even if the user closed their browser or switched off their computer
             *  The api call sends the required the product rowId which is stored in
             *  the product "cart_row_id" property which we added using the method 
             *  "addProductToCartSession()". The rowId is required to identify the 
             *  product in the cart session so that we can remove it entirely 
             *  without affecting the other products. The response will return no
             *  data but will show as successful with a 200 status. 
             */
            removeProductFromCartSession(product) {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isDeletingItemFromCart = true;

                //  Console log to acknowledge the start of api process
                console.log('Start deleting cart product...');

                //  Use the api call() function located in resources/js/api.js
                api.call('delete', '/api/cart/'+product.cart_row_id)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isDeletingItemFromCart = false;
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isDeletingItemFromCart = false;

                        //  Console log Error Location
                        console.log('dashboard/product/show/main.vue - Error deleting cart product...');

                        //  Log the responce
                        console.log(response);    
                    });
            },

            /*  updateItemQuantity()
             *  This method will update the quantity property of a product provided. 
             *  It requires the product being updated as well as the action to be 
             *  carried out. When the action is set to "add" we will increase the item
             *  quantity by one, but is the action is set to "subtract" we will reduce
             *  the quantity by one. If the action is a number then we will set the 
             *  quantity of the product to that number provided e.g) 20 or 60. So far 
             *  the product quantity is updated only on the frontend, but not the
             *  server-end via cookies. This means that if the user turns of their 
             *  computer or closes the browser we will lose their cart changes. 
             *  To avoid this we must use the "updateItemInCartOnlineSession()" method
             *  which then updates the product stored via cookies so that we can
             *  remember the cart product changes no matter what happens
             */  
            updateItemQuantity(product, action){
                
                var currentQuantity = parseInt(product.quantity);

                //  If the action is to increase the quantity of the item
                if(action == 'add'){
                    //  Increase quantity
                    this.$set(product, 'quantity', (currentQuantity + 1));

                //  If the action is to reduce the quantity of the item
                }else if(action == 'subtract'){
                    
                    if( (currentQuantity - 1) != 0 ){
                        this.$set(product, 'quantity', (currentQuantity - 1));
                    }

                }else{

                    this.$set(product, 'quantity', parseInt(action));

                }

                //  Update the cart product cookie
                this.updateItemInCartOnlineSession(product);

            },

            /*  existsInCart()
             *  This method will check if a provided product already exists in the
             *  cart or not and returns a true or false result. We check if the 
             *  product exists by first getting all the products in the cart and
             *  returning their id's. We then check if the provided product id
             *  exists in the returned cart id's. If it does appear in the list
             *  then it already exists and we return true. If it does not appear
             *  on the list then it does not already exist and we return false
             */ 
            existsInCart(product){
                var itemIds = this.cartItems.map( (item) => { return item.id } );
                return itemIds.includes(product.id);
            },

            /*  fetchProducts()
             *  This method will fetch all the products related to the current store.
             *  It requires the store id to fetch the products as well as two optional
             *  parameters being the "per_page" which sets how many items can be returned
             *  at a time and the "page" which sets which page we are showing from the 
             *  pagination list.
             */ 
            fetchProducts() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.showParentIsLoading = true;

                //  Console log to acknowledge the start of api process
                console.log('Start getting store products...');

                //  Get the store id and the pagination page number
                var storeId = (this.$route.params.storeId);
                var per_page = (this.$route.query.per_page) ? this.$route.query.per_page : 1;
                var page = (this.$route.query.page) ? this.$route.query.page : 1;

                var urlParams = {
                        // Store Id 
                        storeId: storeId,
                        //  Number of items to return per page
                        perPage: per_page,
                        //  The page number of the paginated items
                        page: page,
                    }

                //  Use the api call() function located in resources/js/api.js
                return api.call('get', '/api/products', null, urlParams)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.showParentIsLoading = false;

                        //  Store the products
                        self.products = (data || {}).data;
                        
                        for(var x=0; x < (self.products || []).length; x++){

                            //  Add a new property to track if this product has been added to cart
                            this.$set(this.products[x], 'inside_cart', false);

                            //  Add a new property to track how many of this product has been added to cart
                            this.$set(this.products[x], 'quantity', 0);
                            
                        }

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.showParentIsLoading = false;

                        //  Console log Error Location
                        console.log('dashboard/product/show/main.vue - Error getting product details...');

                        //  Log the responce
                        console.log(response);    
                    });
            },

            /*  fetchItemsFromCartSession()
             *  This method will fetch all the products currently stored in the cart session.
             *  When the items are returned we will carry out two primary actions. The first
             *  action will be to update the current listed products on the shop. Remember 
             *  that the listed products will still have old data e.g incorrect quantity,
             *  and will need to be updated. This action will update the products "cart_row_id",
             *  "quantity", and the "inside_cart" properties as required. The second action
             *  will be to add the cart items we returned to the vue cart so that they are shown
             *  to the user
             */ 
            fetchItemsFromCartSession() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoadingCartItems = true;

                //  Console log to acknowledge the start of api process
                console.log('Start getting cart...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/cart')
                    .then(({data}) => {
                        
                        //  Stop loader
                        self.isLoadingCartItems = false;

                        //  Console log the data returned
                        console.log(data);
                        
                        /*  Update the existing products with the ones added to cart
                         *  This is so that we can update the products row id (cart_row_id)
                         *  which is very important when updating the quantity values in other
                         *  requests. We also need to update the quantity (quantity) so that
                         *  we remind the product of its quantity. Last we need to set the (inside_cart)
                         *  property that helps us know that this item is actually inside the cart
                         *  and should be treated as such
                         */
                        //  Show that it is added to the cart
                        for(var x=0; x < data.length; x++){
                            for(var y=0; y < self.localProducts.length; y++){
                                if( data[x].id == self.localProducts[y].id ){
                                    self.$set(self.localProducts, y, data[x]);
                                }
                            }
                        }

                        //  Notify parent on the updated products
                        self.$emit('updatedProducts', self.localProducts);

                        /*  After updating the products we need to now add the fetched cart items to
                         *  the current cart to show all the items
                         */
                        self.cartItems = data;

                        //  Notify parent on the updated cart items
                        self.$emit('updatedCartItems', self.localProducts);

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingCartItems = false;

                        //  Console log Error Location
                        console.log('dashboard/product/show/main.vue - Error getting cart...');

                        //  Log the responce
                        console.log(response);    
                    });
            },

            /*  updateItemInCartOnlineSession()
             *  This method will update the product stored in the cart cookie session
             *  so that we can remember those changes even if the user closed their 
             *  browser or switched off their computer. The api call sends the product
             *  name, quantity, unit_sale_price, and unit_price to be stored in the cookie.  
             */
            updateItemInCartOnlineSession(product) {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isUpdatingItemInCart = true;

                //  Console log to acknowledge the start of api process
                console.log('Start updating cart product...');

                var cartData = {
                        name: product.name,
                        quantity: product.quantity,
                        unit_sale_price: product.unit_sale_price,
                        unit_price: product.unit_price,
                    }

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/cart/'+product.cart_row_id, cartData)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isUpdatingItemInCart = false;
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isUpdatingItemInCart = false;

                        //  Console log Error Location
                        console.log('dashboard/product/show/main.vue - Error updating cart product...');

                        //  Log the responce
                        console.log(response);    
                    });
            },
        },
        created(){

            //  If the parent is done loading products
            if( !this.showParentIsLoading){
                //  Fetch the cart
                this.fetchItemsFromCartSession();
            }

        }
    };
  
</script>