<template>
    <div class="container container-fluid-mobile mt-3">
        <div class="row">
            <div class="col-6 hidden-xs">
                <div class="tt-product-single-img">
                    <div>
                        <button class="tt-btn-zomm tt-top-right"><i class="icon-f-86"></i></button>
                        <img class="zoom-product" id="zoom_05" src='images/backgrounds/product-41_2.jpg' data-zoom-image="images/backgrounds/product-41_2.jpg" alt=""/>
                    </div>
                </div>
                <div class="product-images-carousel">

                    <slick ref="slick" :options="slickOptions">
                        <div v-for="(image, index) in [1,2,3,4]">
                            <a href="#">
                                <img src="images/backgrounds/product-41_2.jpg" width="100%">
                            </a>
                        </div>
                    </slick>
                    
                </div>
            </div>
            <div class="col-6">
                <div class="tt-product-single-info mt-2">
                    <div>
                        <Tag color="primary">Featured</Tag>
                        <Tag color="success">New</Tag>
                        <Tag color="error">Sale</Tag>
                        <Tag color="warning">Out Of Stock</Tag>
                    </div>
                    <h1 class="tt-title mt-2">Fleece Hoodie</h1>
                    <div class="tt-price">
                        <span class="new-price">P350</span>
                        <span class="old-price"></span>
                    </div>
                    <div class="tt-add-info">
                        <ul>
                            <li><span>SKU:</span> 001</li>
                            <li><span>Availability:</span> 40 in Stock</li>
                        </ul>
                    </div>
                    <div class="tt-review">
                        <div class="tt-rating">
                            <i class="icon-star"></i>
                            <i class="icon-star"></i>
                            <i class="icon-star"></i>
                            <i class="icon-star-half"></i>
                            <i class="icon-star-empty"></i>
                        </div>
                        <a href="#">(1 Customer Review)</a>
                    </div>
                    <div class="tt-wrapper mt-2">
                        Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.
                    </div>
                    <div class="tt-wrapper">
                        <div class="tt-countdown_box_02">
                            <div class="tt-countdown_inner">
                                <div class="tt-countdown" data-date="2018-11-01" data-year="Yrs" data-month="Mths" data-week="Wk" data-day="Day" data-hour="Hrs" data-minute="Min" data-second="Sec"><span class="countdown-row"><span class="countdown-section"><span class="countdown-amount">0</span><span class="countdown-period">Day</span></span><span class="countdown-section"><span class="countdown-amount">0</span><span class="countdown-period">Hrs</span></span><span class="countdown-section"><span class="countdown-amount">0</span><span class="countdown-period">Min</span></span><span class="countdown-section"><span class="countdown-amount">0</span><span class="countdown-period">Sec</span></span></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="tt-wrapper">
                        <div class="tt-row-custom-01">
                            <div class="col-item">
                                <div class="tt-input-counter style-01">
                                    <span class="minus-btn"></span>
                                    <input type="text" value="1" size="5">
                                    <span class="plus-btn"></span>
                                </div>
                            </div>
                            <div class="col-item">
                                <a href="#" class="btn btn-lg"><i class="icon-f-39"></i>ADD TO CART</a>
                            </div>
                        </div>
                    </div>
                    <div class="tt-wrapper">
                        <ul class="tt-list-btn">
                            <li><a class="btn_link" href="#"><i class="icon-n-072"></i>ADD TO WISH LIST</a></li>
                        </ul>
                    </div>
                    <div class="tt-wrapper">
                        <div class="tt-add-info">
                            <ul>
                                <li>
                                    <span>Vendor:</span> 
                                    <Tag type="border" color="warning">Polo</Tag>
                                </li>
                                <li>
                                    <span>Category:</span>
                                    <Tag type="border" color="success">T-Shirts</Tag>
                                </li>
                                <li>
                                    <span>Tags:</span>
                                    <Tag type="border" color="primary">Ladies</Tag>, 
                                    <Tag type="border" color="primary">T-Shirt</Tag>, 
                                    <Tag type="border" color="primary">Top</Tag>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <Collapse simple class="mt-4">
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
                        <Panel name="3" class="pt-3 pb-3">
                            REVIEWS (3)
                            <div slot="content">
                                <div class="tt-review-block">
                                    <div class="tt-row-custom-02">
                                        <div class="col-item">
                                            <h2 class="tt-title">
                                                1 REVIEW FOR THIS PRODUCT
                                            </h2>
                                        </div>
                                        <div class="col-item">
                                            <a href="#">Write a review</a>
                                        </div>
                                    </div>
                                    <div class="tt-review-comments">
                                        <div class="tt-item">
                                            <div class="tt-avatar">
                                                <a href="#"><img src="images/backgrounds/review-comments-img-01.jpg" alt=""></a>
                                            </div>
                                            <div class="tt-content">
                                                <div class="tt-rating">
                                                    <i class="icon-star"></i>
                                                    <i class="icon-star"></i>
                                                    <i class="icon-star"></i>
                                                    <i class="icon-star-half"></i>
                                                    <i class="icon-star-empty"></i>
                                                </div>
                                                <div class="tt-comments-info">
                                                    <span class="username">by <span>ADRIAN</span></span>
                                                    <span class="time">on January 14, 2017</span>
                                                </div>
                                                <div class="tt-comments-title">Very Good!</div>
                                                <p>
                                                    Ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim.
                                                </p>

                                            </div>
                                        </div>
                                    </div>
                                    <form class="form-default">
                                        <div class="form-group">
                                            <label for="inputName" class="control-label">YOUR NAME *</label>
                                            <input type="email" class="form-control" id="inputName" placeholder="Enter your name">
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail" class="control-label">COUPONE E-MAIL *</label>
                                            <input type="password" class="form-control" id="inputEmail" placeholder="Enter your e-mail">
                                        </div>
                                        <div class="form-group">
                                            <label for="textarea" class="control-label">YOUR REVIEW *</label>
                                            <textarea class="form-control" id="textarea" placeholder="Enter your review" rows="3"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn">SUBMIT</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </Panel>
                    </Collapse>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    
    import Slick from 'vue-slick';

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
            Slick, basicButton, toggleSwitch, Loader
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
                localProducts: this.products,
                
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
            products: {
                handler: function (val, oldVal) {
                    this.localProducts = val;
                },
                deep: true
            }
        },
        methods: {
            
        },
        mounted(){
            $("#zoom_05").elevateZoom({
                zoomType: "inner",
                cursor: "crosshair"
            });
        }
    };
  
</script>