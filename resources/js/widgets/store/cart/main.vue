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

                    <Row v-if="!isLoadingCartItems && (localCart.items || []).length" :gutter="12" class="mt-5 mb-3">
                        
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
                                    <tr v-for="(item, index) in (localCart.items || [])" :key="index">
                                        <td>
                                            <div class="tt-product-img">
                                                <img :src="item.primary_image.url" style="max-width:80px;max-height:80px;">
                                            </div>
                                        </td>
                                        <td>
                                            <span>{{ item.name }}</span>
                                        </td>
                                        <td>
                                            click<br>
                                            <div class="tt-input-counter style-01 small mt-2 ml-4 mr-4" style="max-width: 100%;">
                                                <span class="minus-btn" @click="updateItemQuantity(item, 'subtract')"></span>
                                                <input type="text" :value="item.quantity" size="5"
                                                        @input="updateItemQuantity(item, $event.target.value)">
                                                <span class="plus-btn" @click="updateItemQuantity(item, 'add')"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <!-- Total price for one item (discount and taxes calculated) -->
                                                <span>
                                                    {{ item.store_currency_symbol }}
                                                    {{ (item.grand_total / item.quantity) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                {{ item.store_currency_symbol }}
                                                {{ item.grand_total }}
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Remove Item  -->
                                            <Poptip confirm title="Are you sure you want to remove this?"  width="300" class="mr-3"
                                                    ok-text="Yes" cancel-text="No" @on-ok="removeItemFromCart(item)">
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

                    <div v-if="!isLoadingCartItems && !(localCart.items || []).length">
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
                    <span class="text-white mt-2 float-left">{{ (localCart.items || []).length }} {{ (localCart.items || []).length == 1 ? ' Item': ' Items' }}</span>
                    <template v-if="!isLoadingCartItems && (localCart.items || []).length">
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
                    <div v-if="!isLoadingCartItems && (localCart.items || []).length" class="tt-shopcart-wrapper">
                        <scrollBox class="border">
                            <div class="tt-shopcart-box p-3" style="max-height: 300px;">
                                <table class="w-100">
                                    <tbody>
                                        <tr v-for="(item, index) in (localCart.items || [])" :key="index" class="mb-1">
                                            <td>
                                                <div class="tt-product-img">
                                                    <img :src="item.primary_image.url" style="max-width:80px;max-height:80px;">
                                                </div>
                                            </td>
                                            <td style="position:relative;">
                                                <span class="d-block text-dark">{{ item.name }}</span>
                                                <span style="font-size:0.9;" class="d-inline-block">
                                                    {{ item.store_currency_symbol }}
                                                    {{ (item.grand_total / item.quantity) }}
                                                    each
                                                </span>
                                                <span style="font-size:0.9;" class="d-inline-block btn btn-link m-0 p-0 text-left"
                                                      @click="$router.push({ name: 'single-product', params: { storeId: storeId, productId: item.id } })">
                                                      View Details
                                                </span>
                                                <span style="display: inline-block; position: absolute; top: -10px; left: -30px; height: 1.7em; border-radius: 3.235801032000001em; background: #0071ce; color: #fff; padding: .38198205906665em .618046971569839em; text-align: center; font-size: .875rem; line-height: .875rem;">
                                                    {{ item.quantity }}
                                                </span>
                                            </td>
                                            <td class="text-right" style="font-size: 1.5em">

                                                <!-- Grand Total Price  -->
                                                <div class="tt-price font-weight-bold text-dark text-right">
                                                    {{ item.store_currency_symbol }}
                                                    {{ item.grand_total }}
                                                </div>

                                                <!-- Remove Item  -->
                                                <Poptip confirm title="Are you sure you want to remove this?" width="300" placement="left-start"
                                                        ok-text="Yes" cancel-text="No" @on-ok="removeItemFromCart(item)">
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
                                        <td>
                                            {{ localCart.items[0].store_currency_symbol }}
                                            {{ localCart.sub_total }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>GRAND TOTAL</th>
                                        <td>
                                            <span style="font-size: 1.4em;">
                                                {{ localCart.items[0].store_currency_symbol }}
                                                {{ localCart.grand_total }}
                                            </span>
                                        </td>
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

                    <div v-if="!isLoadingCartItems && !(localCart.items || []).length">
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
            items: {
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
                localCart: cartInstance.cart,
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

            addItem: {
                handler: function (val, oldVal) {
                    
                    if(val){
                        //  Add the item to cart
                        this.addToCart(val);
                    }
                }
            },

            removeItem: {
                handler: function (val, oldVal) {
                    
                    if(val){
                        //  Remove the item to cart
                        this.removeItemFromCart(val);
                    }
                }
            },

            updateItem: {
                handler: function (val, oldVal) {
                    
                    if(val.product && val.action){
                        //  Update the item quantity
                        this.updateItemQuantity(val.product, val.action);
                    }
                }
            }

        },
        methods: {
            
            /*  Whe the user wants to add a item to the cart, we will request the
             *  item object to be provided. We will then check if the item exists
             *  in the cart. If it does we will not do anything else, however if it does
             *  not then we can add a new property to the item object. We use the vue 
             *  $set method to ensure that vue can track the changes of the properties
             *  added, making them reactive. The "inside_cart" property will be used to
             *  determine is the item is inside the cart. If set to true then we will
             *  know that the item has been added to the cart. The "quantity" 
             *  property will b used to know how much quantity is required of that
             *  items. So far the item is added to cart only on the frontend,
             *  but not the server-end via cookies. This means that if the user
             *  turns of their computer or closes the browser we will lose their
             *  cart items. To avoid this we must use the "addItemToCartSession()"
             *  method which then pushes the item to b stored via cookies so that
             *  we can remember the cart items no matter what happens
             */
            addToCart(item){
                cartInstance.addItem( item );
            },

            /*  Whe the user wants to remove a item from the cart, we will request the
             *  item object to be provided.  We use the vue $set method to ensure that
             *  vue can track the changes of the properties added, making them reactive.
             *  The "inside_cart" property will be used to remove the item from the
             *  inside the cart. If set to false then we will know that the item has
             *  been removed to the cart.  So far the item is removed from cart only
             *  on the frontend, but not the server-end via cookies. This means that 
             *  if the user turns of their computer or closes the browser we will lose 
             *  their cart changes. To avoid this we must use the "removeItemFromCartSession()"
             *  method which then removes the item stored via cookies so that we can
             *  remember the cart items left no matter what happens
             */
            removeItemFromCart(item){
                cartInstance.removeItem( item );
            }, 

            /*  updateItemQuantity()
             *  This method will update the quantity property of a item provided. 
             *  It requires the item being updated as well as the action to be 
             *  carried out. When the action is set to "add" we will increase the item
             *  quantity by one, but is the action is set to "subtract" we will reduce
             *  the quantity by one. If the action is a number then we will set the 
             *  quantity of the item to that number provided e.g) 20 or 60. So far 
             *  the item quantity is updated only on the frontend, but not the
             *  server-end via cookies. This means that if the user turns of their 
             *  computer or closes the browser we will lose their cart changes. 
             *  To avoid this we must use the "updateItemInCartOnlineSession()" method
             *  which then updates the item stored via cookies so that we can
             *  remember the cart item changes no matter what happens
             */  
            updateItemQuantity(item, action){
                
                cartInstance.updateItemQuantity(item, action);

            },

            /*  existsInCart()
             *  This method will check if a provided item already exists in the
             *  cart or not and returns a true or false result. We check if the 
             *  item exists by first getting all the items in the cart and
             *  returning their id's. We then check if the provided item id
             *  exists in the returned cart id's. If it does appear in the list
             *  then it already exists and we return true. If it does not appear
             *  on the list then it does not already exist and we return false
             */ 
            existsInCart(item){
                var itemIds = this.localCart.items.map( (item) => { return item.id } );
                return itemIds.includes(items.id);
            }
        },
        mounted () {
            //  Listen for global changes on the updating of the cart. 
            //  Updates on the cart should reflect changes on the
            //  localCart as well

            var self = this;

            Event.$on('cartUpdated', function(cart){
                //  Update the local cart
                self.localCart = cart;
            });
        },
        beforeDestroy() {
            //  Stop listening for global changes on the cart.
            Event.$off('cartUpdated');
        }
    };
  
</script>