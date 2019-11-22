<style scoped>

    .product-name {
        color: #515a6e;
    }

    .product-desc {
        color: #808695;
        font-weight: 100;
    }

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important; 
        width: 90% !important; 
        white-space: nowrap !important;
    }

    .unbolded >>> .field-label{
        font-weight:100 !important;
    }

    /*  Product Toolbox */

    .single-product >>> .product-toolbox{
        margin: -2px 0 0 0;
    }

    .single-product:hover >>> .product-toolbox .hidable{
        opacity:1;
    }

    .single-product >>> .product-toolbox .hidable{
        opacity:0;
    }

    .single-product >>> .product-toolbox .product-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .single-product >>> .product-toolbox .product-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .single-product >>> .ivu-card-body{
        padding:0 !important;
    }

    .product-details{
        padding:16px;
    }

</style>

<template>

    <Card v-if="product" :class="'box-card single-product mb-2'+(!showContent ? ' hidden-content':'')">

        <div slot="title">

            <!-- Product Name  -->
            <span class="product-name font-weight-bold">
                {{ productNumber ? productNumber +'. ' : '' }}
                {{ product.name }}
            </span>
        </div>

        <div slot="extra">

            <div class="product-toolbox float-right d-block">

                <!-- Show/Hide Product Details Button  -->
                <Icon v-if="showViewDetailsButton" :type="'ios-arrow-'+(showContent ? 'dropdown':'dropup hidable')" 
                      class="product-icon mr-2" size="20" @click="showContent = !showContent"/>

                <!-- Remove Product Button  -->
                <Poptip v-if="showDeleteButton" confirm title="Are you sure you want to remove this product?" ok-text="Yes" cancel-text="No" width="300"
                    @on-ok="$emit('removeProduct')">
                    <Icon type="ios-trash-outline" class="product-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Edit Product Button  -->
                <Icon v-if="showEditButton" type="ios-create-outline" class="product-icon hidable" size="20" @click.native="$emit('editProduct', product)" />

                <!-- Move Product Button  -->
                <Icon v-if="showDragButton" type="ios-move" class="product-icon product-dragger-handle hidable mr-2" size="20" />
                
            </div>
        </div>    

        <Row v-if="showContent" class="product-details">

            <Col :span="12">

                <span class="d-block">
                    <span class="font-weight-bold">Regular Price:</span>
                    <span>{{ productRegularPrice }}</span>
                </span>
                <span class="d-block">
                    <span class="font-weight-bold">Discount:</span>
                    <span>{{ productDiscountTotal }}</span>
                </span>           
                <span class="d-block">
                    <span class="font-weight-bold">Stock:</span>
                    <span>{{ productStockQuantity }}</span>
                </span>

            </Col>

            <Col :span="12">
            
                <span class="d-block">
                    <span class="font-weight-bold">Sale Price:</span>
                    <span>{{ productSalesPrice }}</span>
                </span>
                <span class="d-block">
                    <span class="font-weight-bold">Tax:</span>
                    <span>{{ productTaxTotal }}</span>
                </span>   

            </Col>

            <Col :span="24">
            
                <span class="d-block mt-2">
                    <span class="font-weight-bold d-block">Description:</span>
                    <span>{{ productDescription }}</span>
                </span>
            </Col>

        </Row>

        <Row v-if="showFooter">

            <Col :span="24" class="p-2 clearfix">

                <!-- Product Variants  -->
                <Badge v-for="(variable, index) in productVariables" :key="index"
                        :text="variable.value" type="info" class="float-right mr-2">
                </Badge>

            </Col>

        </Row>


    </Card>

</template>

<script>

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            product: {
                type: Object,
                default:() => {}
            },
            showDragButton: {
                type: Boolean,
                default:true
            },
            showEditButton: {
                type: Boolean,
                default:true
            },
            showDeleteButton: {
                type: Boolean,
                default:true
            },
            showViewDetailsButton: {
                type: Boolean,
                default:true
            },
            showFooter:{
                type: Boolean,
                default:true
            }
        },
        data(){
            return {
                showContent: false
            }
        },
        computed: {
            productNumber(){
                return (this.index != null ? this.index + 1 : '');
            },
            productCurrency(){
                return (this.product.currency.symbol || this.product.currency.code);
            },
            productDescription(){
                return this.product.description;
            },
            productRegularPrice(){
                return this.productCurrency + this.product.unit_regular_price;
            },
            productSalesPrice(){
                return this.product.on_sale ? (this.productCurrency + this.product.unit_sale_price) : '(N/A)';
            },
            productDiscountTotal(){
                return  this.productCurrency + this.product.discount_total;
            },
            productTaxTotal(){
                return this.productCurrency + this.product.tax_total;
            },
            productStockQuantity(){
                return (this.product.allow_stock_management ? this.product.stock_quantity : '(N/A)');
            },
            productVariables(){
                return this.product.variables;
            }

        },
    }

</script>