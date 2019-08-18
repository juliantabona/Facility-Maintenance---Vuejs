<template>

    <Row :gutter="12">

        <Col v-if="isLoadingProduct" span="8" offset="8">
            <!-- Loader -->
            <Loader :loading="true" type="text" class="text-left" theme="white">Loading store</Loader>
        </Col>

        <!-- Show if we have the product -->
        <Col v-if="!isLoadingProduct && localProduct" :span="24">

            <Row :gutter="12">

                <Col span="12">

                    <!-- Primary Image -->
                    <div class="tt-product-single-img">
                        <div v-if="localProduct.primary_image">
                            <!-- Zoom Button -->
                            <button class="tt-btn-zomm tt-top-right"><i class="icon-f-86"></i></button>

                            <!-- Image Display -->
                            <img class="zoom-product" id="product_zoom" 
                                 style="max-height: 400px; margin: auto; display: block;"
                                 :src="(localProduct.primary_image || {}).url"/>
                        </div>
                    </div>

                    <!-- Gallery -->
                    <div class="product-images-carousel">

                        <slick ref="slick" :options="slickOptions">
                            <div v-for="(image, index) in [1,2,3,4]">
                                <img src="images/backgrounds/product-41_2.jpg" width="100%">
                            </div>
                        </slick>
                        
                    </div>
                </Col>

                <Col span="12">
                    <div class="tt-product-single-info mt-2">

                        <!-- General Tags -->
                        <div>
                            <Tag v-if="localProduct.is_featured" color="primary">Featured</Tag>
                            <Tag v-if="localProduct.is_new" color="success">New</Tag>
                            <Tag v-if="localProduct.unit_sale_price" color="warning">Sale</Tag>
                            <Tag v-if="localProduct.has_inventory && !localProduct.stock_quantity" color="error">
                                Out Of Stock
                            </Tag>
                        </div>

                        <!-- Name -->
                        <h1 class="tt-title mt-2">{{ localProduct.name }}</h1>

                        <!-- Price -->
                        <div class="tt-price">
                            
                            <!-- Show sale or normal price -->
                            <span class="new-price">{{ localProduct.store_currency_symbol + (localProduct.unit_sale_price ? localProduct.unit_sale_price : localProduct.unit_price) }}</span>
                            
                            <!-- Show old price if we have a sale -->
                            <span v-if="localProduct.unit_sale_price" 
                                  class="old-price text-secondary" style="font-size: 20px;">
                                  {{ localProduct.store_currency_symbol + localProduct.unit_price }}
                            </span>
                        
                        </div>

                        <!-- Skock -->
                        <div class="tt-add-info">
                            <ul>
                                <li><span>SKU:</span> {{ localProduct.sku }}</li>
                                <li v-if="localProduct.has_inventory">
                                    <span>Availability:</span> {{ localProduct.stock_quantity }} in Stock
                                </li>
                            </ul>
                        </div>

                        <!-- Rating -->
                        <div class="mt-2">
                            <Rate v-if="localProduct.average_rating" allow-half :value="localProduct.average_rating" 
                                  class="tt-rating-stars" />
                            <span v-if="totalReviews" class="font-weight-bold">
                                ({{ totalReviews }} Customer {{ totalReviews == 1 ?  'Review' : 'Reviews' }})
                            </span>
                        </div>

                        <!-- Short description -->
                        <div class="tt-wrapper mt-2">
                            {{ localProduct.description }}
                        </div>

                        <!-- Countdown -->
                        <div class="tt-wrapper">
                            <div class="tt-countdown_box_02">
                                <div class="tt-countdown_inner">
                                    <div class="tt-countdown" data-date="2018-11-01" data-year="Yrs" data-month="Mths" data-week="Wk" data-day="Day" data-hour="Hrs" data-minute="Min" data-second="Sec"><span class="countdown-row"><span class="countdown-section"><span class="countdown-amount">0</span><span class="countdown-period">Day</span></span><span class="countdown-section"><span class="countdown-amount">0</span><span class="countdown-period">Hrs</span></span><span class="countdown-section"><span class="countdown-amount">0</span><span class="countdown-period">Min</span></span><span class="countdown-section"><span class="countdown-amount">0</span><span class="countdown-period">Sec</span></span></span></div>
                                </div>
                            </div>
                        </div>

                        <!-- If we are tracking inventory and we have stock or if we are not tracking inventory -->
                        <div v-if="!localProduct.has_inventory || (localProduct.has_inventory && localProduct.stock_quantity)" 
                             class="tt-wrapper">
                            <div class="tt-row-custom-01">

                                <!-- Quantity Button -->
                                <div class="col-item">
                                     <div class="tt-input-counter style-01">
                                        <span class="minus-btn"></span>
                                        <input type="text" value="1" size="5">
                                        <span class="plus-btn"></span>
                                    </div>
                                </div>

                                <!-- Add To Cart -->
                                <div class="col-item">
                                    <span class="btn btn-primary mt-1 pt-2 pb-2 pl-4 pr-4"><span class="icon icon-check_circle"></span>ADD TO CART</span>
                                </div>

                            </div>
                        </div>

                        <div class="mt-4">
                            <!-- Continue Shopping Button -->
                            <span class="d-inline-block btn btn-link p-0 mr-3"
                                    @click="$router.push({ name: 'store', params: { id: $route.params.storeId }})">
                                <Icon type="md-arrow-back" :size="20" class="mr-1" />
                                <span>CONTINUE SHOPPING</span>
                            </span>

                            <!-- Add To Wishlist Button -->
                            <span class="d-inline-block btn btn-link p-0"><i class="icon-n-072"></i>ADD TO WISH LIST</span>
                        </div>

                        <div class="tt-wrapper">
                            <div class="tt-add-info">
                                <ul>

                                    <!-- Vendor -->
                                    <li>
                                        <span>Vendor:</span> 
                                        <Tag type="border" color="warning">Polo</Tag>
                                    </li>

                                    <!-- Categories -->
                                    <li v-if="(localProduct.categories || {}).length">
                                        <span>Categories:</span>
                                        <Tag v-for="(category, i) in localProduct.categories" :key="i" color="primary">{{ category.name }}</Tag>
                                    </li>

                                    <!-- Tags -->
                                    <li v-if="(localProduct.tags || {}).length">
                                        <span>Tags:</span>
                                        <Tag v-for="(tag, i) in localProduct.tags" :key="i" color="primary">{{ tag.name }}</Tag>
                                    </li>

                                </ul>
                            </div>
                        </div>

                        <Collapse simple class="mt-4">

                            <!-- Detailed Description -->
                            <Panel name="2" class="pt-3 pb-3">
                                ADDITIONAL INFORMATION
                                <div slot="content">
                                    <div class="tt-collapse-content">
                                        <p>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.</p>
                                        <table class="tt-table-03">
                                            <tbody>
                                                <tr>
                                                    <td>Color:</td>
                                                    <td>Blue, Purple, White</td>
                                                </tr>
                                                <tr>
                                                    <td>Size:</td>
                                                    <td>20, 24</td>
                                                </tr>
                                                <tr>
                                                    <td>Material:</td>
                                                    <td>100% Polyester</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </Panel>

                            <!-- Reviews -->
                            <Panel name="3" class="pt-3 pb-3">

                                <!-- Loader -->
                                <Loader v-if="isLoadingReviews" :loading="true" type="text" class="text-left d-inline-block p-0" theme="white">Loading reviews</Loader>

                                <!-- Reviews Heading -->    
                                <span v-if="!isLoadingReviews && localReviews">REVIEWS <span>({{ totalReviews }})</span></span>
                                
                                <!-- Reviews Body -->
                                <div slot="content">
                                    <div class="tt-review-block">
                                        <div v-if="totalReviews" class="tt-row-custom-02">
                                            <div class="col-item">
                                                <span>
                                                    {{ totalReviews }} {{ totalReviews == 1 ?  'REVIEW' : 'REVIEWS' }} FOR THIS PRODUCT
                                                </span>
                                            </div>
                                            <div class="col-item">
                                                <span class="btn btn-link">Write a review</span>
                                            </div>
                                        </div>
                                        <scrollBox v-if="totalReviews" class="border mb-4">
                                            <div class="tt-review-comments pr-3 pl-3" style="max-height: 300px;">
                                                <div v-for="(comment, i) in localReviews" class="tt-item">
                                                    <div class="tt-avatar">
                                                        <img src="images/backgrounds/review-comments-img-01.jpg" :alt="(comment.user || {}).full_name">
                                                    </div>
                                                    <div class="tt-content">
                                                        
                                                        <Rate v-if="comment.rating_value"  :disabled="true"
                                                              :value="comment.rating_value" class="tt-rating-stars" />

                                                        <div class="tt-comments-info">
                                                            <span class="username">by <span>{{ (comment.user || {}).full_name }}</span></span>
                                                            <span class="time">on {{ comment.created_at | moment('DD MMM YYYY') || '___' }}</span>
                                                        </div>
                                                        <p>{{ comment.text }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </scrollBox>

                                        <commentCreator
                                                class="p-4 border" 
                                                :urlParams="{
                                                        commentType: 'review',
                                                        productId: localProduct.id
                                                    }"
                                                requiredTextError="Enter your review"
                                                btnText="SUBMIT REVIEW"
                                                btnClass="w-100 mt-2"
                                                placeholder="Enter your review"
                                                loaderText="Adding review"
                                                fieldType="textarea"
                                                :canRate="true"
                                                @commentSuccess="updateReviews($event)">
                                        </commentCreator>
                                        
                                    </div>
                                </div>

                            </Panel>

                        </Collapse>
                    </div>
                </Col>
            </Row>

        </Col>
    </Row>


</template>

<script>
    
    import Slick from 'vue-slick';

        /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    /*  scrollBox  */
    import scrollBox from './../../../components/_common/scrollBox/scrollBox.vue'; 
    
    /*  scrollBox  */
    import commentCreator from './../../../components/_common/forms/comment/comment.vue'; 

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            Slick, basicButton, toggleSwitch, Loader, scrollBox, commentCreator
        },
        props: {
            product: {
                type: Object,
                default: null
            }
        },
        data(){
            return {

                user: auth.user,
                localProduct: this.product,
                isLoadingProduct: false,

                localReviews: [],
                totalReviews: 0,
                isLoadingReviews: false,
                
                //  Slick slider options
                slickOptions: {
                    dots: true,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                },   
            }
        },
        watch: {
            product: {
                handler: function (val, oldVal) {
                    this.localProduct = val;
                },
                deep: true
            }
        },
        methods: {
            updateReviews(review){
                //  Add rview to top of existing reviews
                this.localReviews.unshift(review);

                //  Increase the number of total reviews by one
                this.totalReviews = ( parseInt(this.totalReviews) + 1 );
            },
            fetchProduct() {

                var productId = (this.$route.params.productId);

                if(productId){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingProduct = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting localProduct...');

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', '/api/products/'+productId)
                            .then(({data}) => {
                                
                                //  Console log the data returned
                                console.log(data);

                                //  Stop loader
                                self.isLoadingProduct = false;

                                //  Store the product data
                                self.localProduct = data;

                                self.setupImageZoom();

                            })         
                            .catch(response => { 

                                //  Stop loader
                                self.isLoadingProduct = false;

                                //  Console log Error Location
                                console.log('dashboard/product/show/main.vue - Error getting localProduct...');

                                //  Log the responce
                                console.log(response);    
                            });
                }
            },
            fetchReviews() {

                var productId = (this.$route.params.productId);

                if(productId){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingReviews = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting comments...');

                    var page = (this.$route.query.page) ? this.$route.query.page : 1;

                    var urlParams = {
                            productId: productId,
                            commentType: 'review',
                            page: page
                        }

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/comments', null, urlParams)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingReviews = false;

                            //  Store the product data
                            self.localReviews = (data || {}).data;

                            self.totalReviews = (data || {}).total || 0;

                            self.setupImageZoom();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingReviews = false;

                            //  Console log Error Location
                            console.log('dashboard/product/show/main.vue - Error getting comments...');

                            //  Log the responce
                            console.log(response);    
                        });
                }
            },
            setupImageZoom(){

                setTimeout(()=>{
                    $("#product_zoom").elevateZoom({
                        zoomType: "inner",
                        cursor: "crosshair"
                    });
                },1000);
                
            }
        },
        created(){
            if( !this.product ){
                
                var self = this;

                //  Fetch the store
                this.fetchProduct().then( data => {
                    self.fetchReviews();
                });

            }else{

                this.setupImageZoom();

            }
        }
    };
  
</script>