<template>

    <Row :gutter="20">
        <Col :span="24">
            <h1 class="tt-title-subpages noborder pt-3 pb-4">SHOPPING CART</h1>
            <div class="row">
                <div class="col-sm-12 col-xl-8">
                    <div class="tt-shopcart-table">
                        <table>
                            <tbody>
                                <tr v-for="(product, index) in [[1, 220], [2, 450], [3, 340]]" :key="key">
                                    <td>
                                        <span class="tt-btn-close"></span>
                                    </td>
                                    <td>
                                        <div class="tt-product-img">
                                            <img :src="'images/samples/dress_'+product[0]+'.jpg'" :data-src="'images/samples/dress_'+product[0]+'.jpg'" alt="" class="loaded" data-was-processed="true">
                                        </div>
                                    </td>
                                    <td>
                                        <h2 class="tt-title">
                                            <span>Flared Shift Dress</span>
                                        </h2>
                                        <ul class="tt-list-parameters">
                                            <li>
                                                <div class="tt-price">
                                                    P350
                                                </div>
                                            </li>
                                            <li>
                                                <div class="detach-quantity-mobile"></div>
                                            </li>
                                            <li>
                                                <div class="tt-price subtotal">
                                                    P350
                                                </div>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <div class="tt-price">P350</div>
                                    </td>
                                    <td>
                                        <div class="detach-quantity-desctope">
                                            <div class="tt-input-counter style-01">
                                                <span class="minus-btn"></span>
                                                <input type="text" value="1" size="5">
                                                <span class="plus-btn"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tt-price subtotal">P350</div>
                                    </td>
                                </tr>
                                
                                
                            </tbody>
                        </table>
                        <div class="tt-shopcart-btn">
                            <div class="col-left">
                                <span class="btn btn-link"><i class="icon-e-19"></i>CONTINUE SHOPPING</span>
                            </div>
                            <div class="col-right">
                                <span class="btn btn-link"><i class="icon-h-02"></i>CLEAR SHOPPING CART</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-4">
                    <div class="tt-shopcart-wrapper">
                        
                        <div class="tt-shopcart-box">
                            <h4 class="tt-title">
                                NOTE
                            </h4>
                            <p>Add special instructions for your order...</p>
                            <form class="form-default">
                                <textarea class="form-control" rows="7" style="margin-top: 23px; margin-bottom: 0px; height: 75px;"></textarea>
                            </form>
                        </div>
                        <div class="tt-shopcart-box tt-boredr-large">
                            <table class="tt-shopcart-table01">
                                <tbody>
                                    <tr>
                                        <th>SUBTOTAL</th>
                                        <td>P350</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>GRAND TOTAL</th>
                                        <td>P350</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <span class="btn btn-outline-dark mb-3"><span class="icon icon-check_circle"></span>DOWNLOAD QUOTATION</span>
                            <span class="btn btn-primary mb-3"><span class="icon icon-check_circle"></span>PROCEED TO CHECKOUT</span>
                        </div>
                    </div>
                </div>
            </div>
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
                localProducts: this.products, 
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