<template>

    <!-- Product View/Editor -->
    <Row :gutter="20" id="product-summary"  key="product_template" class="animated mb-5">
        <Col :xs="24" :sm="6" :md="5" :lg="5" class="mb-2">
            
            <Card>
                <div class="mb-4">
                    <img src="/images/samples/dress_me_logo.png" style="width: 100%;" class="mb-4">
                    <h4 class="tt-title mb-2">About</h4>
                    <span class="d-block">
                        <span class="d-block-inline">Welcome to the Dress-me online clothing store for all your fabulous dresses</span>
                    </span>
                </div>
                <div>
                    <h4 class="tt-title mb-2">Contact</h4>
                    <span class="d-block">
                        <span class="d-block-inline font-weight-bold mr-1 mb-1">Phone:</span>
                        <span class="d-block-inline">(+267) 3990867</span>
                    </span>
                    <span class="d-block">
                        <span class="d-block-inline font-weight-bold mr-1 mb-1">Email:</span>
                        <span class="d-block-inline">enquiries@dressme.co.bw</span>
                    </span>
                    <span class="d-block">
                        <span class="d-block-inline font-weight-bold mr-1 mb-1">Website:</span>
                        <span class="d-block-inline">www.dressme.co.bw</span>
                    </span>
                </div>
            </Card>

        </Col>
        <Col :xs="24" :sm="18" :md="19" :lg="19" class="mb-2">
            <Row :gutter="20">

                <Col v-for="(product, index) in [[1, 220], [2, 450], [3, 340], [4, 225], [5, 520], [6, 360], [7,420], [8,260], [9,280], [10,450], [11,385], [12,350]]" :key="index" :xs="24" :sm="12" :md="8" :lg="6" class="mb-2">
                    <div class="tt-product thumbprod-center hovered" style="height: 487px;">
                        <div class="tt-image-box">
                            <Poptip trigger="hover" content="Quick View" placement="left" class="tt-btn-quickview">
                                <a href="#"></a>
                            </Poptip>
                            <Poptip trigger="hover" content="Add to Wishlist" placement="left" class="tt-btn-wishlist">
                                <a href="#"></a>
                            </Poptip>
                            <Poptip trigger="hover" content="Add to Compare" placement="left" class="tt-btn-compare">
                                <a href="#"></a>
                            </Poptip>
                            <a href="product.html" tabindex="0">
                                <span class="tt-img"><img :src="'images/samples/dress_'+product[0]+'.jpg'" alt="" class="loading" data-was-processed="true"></span>
                                <span class="tt-img-roll-over"><img :src="'images/samples/dress_'+product[0]+'.jpg'" alt="" class="loading" data-was-processed="true"></span>
                                <span class="tt-label-location">
                                    <span class="tt-label-new">New</span>
                                </span>
                            </a>
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
                        <div class="tt-description" style="top: 0px;">
                            <div class="tt-row">
                                <ul class="tt-add-info">
                                    <li><a href="#" tabindex="0">Dresses</a></li>
                                </ul>
                                <Rate allow-half :value="2.5" class="tt-rating-stars" />
                            </div>
                            <h2 class="tt-title"><a href="product.html" tabindex="0">Flared Shift Dress</a></h2>
                            <div class="tt-price mt-2">P{{ product[1] }}</div>
                            <div class="tt-option-block">
                                <ul class="tt-options-swatch">
                                    <li><a class="options-color tt-color-bg-01" href="#" tabindex="0"></a></li>
                                    <li><a class="options-color tt-color-bg-02" href="#" tabindex="0"></a></li>
                                </ul>
                            </div>
                            <div class="tt-product-inside-hover" style="opacity: 0;">
                                <div class="tt-row-btn">
                                    <a href="#" class="tt-btn-addtocart thumbprod-button-bg" data-toggle="modal" data-target="#modalAddToCartProduct" tabindex="0">ADD TO CART</a>
                                </div>
                                <div class="tt-row-btn">
                                    <a href="#" class="tt-btn-quickview" data-toggle="modal" data-target="#ModalquickView" tabindex="0"></a>
                                    <a href="#" class="tt-btn-wishlist" tabindex="0"></a>
                                    <a href="#" class="tt-btn-compare" tabindex="0"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </Col>

            </Row>
        </Col>
    </Row>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            basicButton, toggleSwitch, Loader
        },
        props: {
            products: {
                type: Array,
                default: function(){
                    return [];
                }
            }
        },
        data(){
            return {

                user: auth.user,
                localProducts: this.products
                
            }
        },
        watch: {
            products: {
                handler: function (val, oldVal) {
                    this.localProducts = val;
                },
                deep: true
            }
        },
        methods: {
            
        }
    };
  
</script>