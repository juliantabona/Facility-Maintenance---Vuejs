<template>

    <!-- Product View/Editor -->
    <Row :gutter="20" id="product-summary"  key="product_template" class="animated mb-5">
        <Col :xs="24" :sm="18" :md="18" :lg="18" class="mb-2">
            <Row :gutter="20">

                <!-- Loader -->
                <Loader v-if="isLoadingProducts" :loading="true" type="text" class="text-left" theme="white">Loading products...</Loader>

                <Col v-else v-for="(product, index) in products" :key="index" :xs="24" :sm="12" :md="8" :lg="6" class="mb-2">
                    
                    <!-- Single Product -->
                    <div class="tt-product thumbprod-center hovered" style="height: 420px;">
                        <div class="tt-image-box">
                            
                            <!-- Quick View Button -->
                            <Poptip trigger="hover" content="Quick View" placement="left" class="tt-btn-quickview">
                                <span></span>
                            </Poptip>
                            
                            <!-- Add to Wishlist Button -->
                            <Poptip trigger="hover" content="Add to Wishlist" placement="left" class="tt-btn-wishlist">
                                <span></span>
                            </Poptip>

                            <!-- Add to Compare Button -->
                            <Poptip trigger="hover" content="Add to Compare" placement="left" class="tt-btn-compare">
                                <span></span>
                            </Poptip>
                            <span class="d-block">
                                <!-- Product Image -->
                                <span class="tt-img d-block" style="max-height: 250px; overflow: hidden;">
                                    <img :src="product.primary_image.url" alt="" class="d-block">
                                </span>
                                <!-- Product Image Roll Over  -->
                                <span class="tt-img-roll-over d-block" style="max-height: 250px; overflow: hidden;">
                                    <img :src="product.primary_image.url" alt="" class="d-block">
                                </span>
                                <!-- Product Tag -->
                                <span class="tt-label-location">
                                    <span class="tt-label-new">New</span>
                                </span>
                            </span>

                            <!-- Product Countdown -->
                            <div v-if="false" class="tt-countdown_box" style="bottom: 0px;">
                                <div class="tt-countdown_inner">
                                    <div class="tt-countdown" data-date="2018-11-01" data-year="Yrs" data-month="Mths" data-week="Wk" data-day="Day" data-hour="Hrs" data-minute="Min" data-second="Sec">
                                        <span class="countdown-row">
                                            <span class="countdown-section">
                                                <span class="countdown-amount">0</span>
                                                <span class="countdown-period">Day</span>
                                            </span>
                                            <span class="countdown-section">
                                                <span class="countdown-amount">0</span>
                                                <span class="countdown-period">Hrs</span>
                                            </span>
                                            <span class="countdown-section">
                                                <span class="countdown-amount">0</span>
                                                <span class="countdown-period">Min</span>
                                            </span>
                                            <span class="countdown-section">
                                                <span class="countdown-amount">0</span>
                                                <span class="countdown-period">Sec</span>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Product Description -->
                        <div class="tt-description" style="top: 0px;">
                            <div class="tt-row">
                                
                                <ul class="tt-add-info">
                                    <!-- Product Category -->
                                    <li><span class="btn btn-link p-0 m-0">Dresses</span></li>
                                </ul>

                                <!-- Product Rating -->
                                <Rate v-if="false" allow-half :value="2.5" class="tt-rating-stars" />
                            </div>

                            <!-- Product Name -->
                            <h2 class="tt-title">
                                <span class="btn btn-link pt-0 m-0" style="white-space: initial;">{{ product.title }}</span>
                            </h2>

                            <!-- Product Price -->
                            <div class="tt-price mt-2">P{{ product.unit_price }}</div>

                            <!-- Product Color Swatch -->
                            <div v-if="false" class="tt-option-block">
                                <ul class="tt-options-swatch">
                                    <li><span class="options-color tt-color-bg-01"></span></li>
                                    <li><span class="options-color tt-color-bg-02"></span></li>
                                </ul>
                            </div>

                            <div v-if="existsInCart(product)" class="tt-input-counter style-01 small mt-2 ml-3 mr-3" style="max-width:100%;">
                                <span class="minus-btn" @click="updateItemQuantity(product, 'subtract')"></span>
                                <input type="text" :value="product.quantity" size="5"
                                        @input="updateItemQuantity(product, $event.target.value)">
                                <span class="plus-btn" @click="updateItemQuantity(product, 'add')"></span>
                            </div>
                            
                            <div v-if="!existsInCart(product)" class="tt-product-inside-hover" style="opacity: 0;">
                                <div class="tt-row-btn">
                                    <!-- Add To Cart Button -->
                                    <span class="tt-btn-addtocart thumbprod-button-bg btn btn-primary"
                                          @click="addToCart(product)">
                                          ADD TO CART
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </Col>

            </Row>
        </Col>
        <Col :xs="24" :sm="6" :md="6" :lg="6" class="mb-2">
            
            <cartWidget 
                :products="products"
                :showParentIsLoading="isLoadingProducts"
                cartType="widget-cart"
                :addItem="addItem"
                :removeItem="removeItem"
                :updateItem="updateItem"
                @updatedProducts="products = $event"
                @updatedCartItems="cartItems = $event">
            </cartWidget>

        </Col>
    </Row>

</template>

<script>

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /*  Cart Widget  */
    import cartWidget from './../cart/main.vue';

    export default {
        components: { Loader, cartWidget },
        data(){
            return {
                products: null,
                isLoadingProducts: false,

                //  Cart parameters
                addItem: null,
                removeItem: null,
                updateItem: null

            }
        },
        watch: {
            //  Watch for changes on the product id
            '$route.query.page': function (id) {
                
                // react to route changes by fetching the associated product...
                this.fetchProducts();

            }
        },
        methods: {
            addToCart(product){
                this.addItem = product;
            },
            removeItemFromCart(product){
                this.removeItem = product;
            },            
            updateItemQuantity(product, action){
                this.updateItem = { product: product, action: action };
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
                var itemIds = this.products.map( (item) => { 
                        if(item.inside_cart){
                            return item.id
                        } 
                    });
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
                self.isLoadingProducts = true;

                //  Console log to acknowledge the start of api process
                console.log('Start getting store products...');

                //  Get the store id and the pagination page number
                var storeId = (this.$route.params.id);
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
                        self.isLoadingProducts = false;

                        //  Store the products
                        self.products = (data || {}).data;Z

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingProducts = false;

                        //  Console log Error Location
                        console.log('dashboard/product/show/main.vue - Error getting product details...');

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){

            //  Fetch the store products
            this.fetchProducts();

        }
    };
  
</script>