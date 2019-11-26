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
        width: 85% !important;
        white-space: nowrap !important;
        display: block;
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

        <!-- Deleting Spinner  -->
        <Spin v-if="isDeletingProduct" size="large" fix></Spin>

        <div slot="title">

            <!-- Product Name  -->
            <span class="product-name font-weight-bold cut-text">
                {{ getProductNumber ? getProductNumber +'. ' : '' }}
                {{ localProduct.name }}
            </span>
            
        </div>

        <div slot="extra">

            <div class="product-toolbox float-right d-block">

                <!-- Show/Hide Product Details Button  -->
                <Icon v-if="showViewDetailsButton" :type="'ios-arrow-'+(showContent ? 'dropdown':'dropup hidable')" 
                      class="product-icon mr-2" size="20" @click="showContent = !showContent"/>

                <!-- Remove Product Button  -->
                <Poptip v-if="showDeleteButton" confirm title="Are you sure you want to remove this product?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemoveProduct(index)"
                        placement="left">
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

                <!-- View Product Variants  -->
                <template v-if="!editVariants">
                    
                    <Badge v-for="(variable, index) in productVariables" :key="index"
                            :text="variable.value" type="info" class="float-right mr-2">
                    </Badge>

                    <Button type="dashed" icon="ios-create-outline" 
                            @click="editVariants = true">
                        Edit Variants
                    </Button>

                </template>

                <!-- Edit Product Variants  -->
                <template v-else>
                        
                    <div v-for="(variable_attribute, index) in getParentVariantAttributes" :key="index" class="mb-2">

                        <Row :gutter="12">

                            <!-- Variant Name  -->
                            <Col :span="12">

                                <span class="font-weight-bold">{{ variable_attribute.name ? variable_attribute.name+': ': '' }}</span>

                            </Col>

                            <!-- Variant Value  -->
                            <Col :span="12">
                            
                                <Select :value="getSelectedVariantAttributeOption(variable_attribute.name)" style="width:200px"
                                        @on-change="updateSelectedVariantAttributeOption(variable_attribute.name, $event)">
                                    <Option v-for="(option, index) in getAvailableVariantAttributeOptions(variable_attribute.name)" 
                                            :value="option" :key="index">
                                        {{ option }}
                                    </Option>
                                </Select>

                            </Col>

                        </Row>

                    </div>

                    <div class="mt-2">

                        <!-- Save Button -->
                        <basicButton 
                            v-if="checkIfProductHasChanged" :disabled="isSavingVariants" class="float-right mr-2" 
                            type="success" size="default" :ripple="!isSavingVariants"
                            @click.native="handleSaveVariants()">
                            <span>Save Changes</span>
                        </basicButton>

                        <!-- Cancel Button -->
                        <Button type="error" size="default" ghost
                                class="float-right mr-2" @click="handleCancel()" >
                            Cancel
                        </Button>
                        
                    </div>

                </template>

            </Col>

        </Row>
        
        <Row v-if="hasReminders">

            <Col :span="24">

                <div class="float-right mb-1 mt-1">

                    <Poptip title="Reminders" trigger="hover" width="300" placement="top-end">

                        <Badge style="margin: 0px 25px 5px 0px;">
                            <Icon type="ios-alert-outline" slot="count" color="#ed4014" size="20" />
                        </Badge>

                        <!-- Reminder Poptip Content  -->
                        <div slot="content">
                            
                            <List size="small">

                                <!-- If its a simple product and does not have a price -->
                                <ListItem v-if="(isSimpleProduct && !hasPrice)">No price provided</ListItem>

                                <!-- If its a variable product and does not have a price on atleast one variation -->
                                <ListItem v-if="(!isSimpleProduct && !hasPriceOnVariations)">No price on variations</ListItem>

                                <!-- If its a simple product and does not have stock -->
                                <ListItem v-if="(isSimpleProduct && !hasStock)">No stock</ListItem>

                                <!-- If its a simple product and it does have stock -->
                                <template v-else>

                                    <!-- If its a simple product and does not have plenty stock -->
                                    <ListItem v-if="(isSimpleProduct && !hasPlentyStock)">Low stock</ListItem>

                                </template>

                                <!-- If its a variable product and does not have stock on atleast one variation -->
                                <ListItem v-if="(!isSimpleProduct && !hasEnoughStockOnAllOnVariations)">Low stock on variations</ListItem>

                            </List>

                        </div>

                    </Poptip>

                </div>

            </Col>

        </Row>


    </Card>

</template>

<script>

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

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
        components: { 
            basicButton
        },
        data(){
            return {
                localProduct: this.product,
                productBeforeChange: null,
                isDeletingProduct: false,
                isSavingVariants: false,
                editVariants: false,
                showContent: false
            }
        },
        computed: {
            getProductNumber(){
                /**
                 *  Returns the product number. We use this as we list the products.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            },
            getParentVariantAttributes(){
                /**
                 *  Get the parent variant attribute names. If this product is a variation,
                 *  then we can get the variant attributes of the parent product that this
                 *  variation belongs to. The variant attributes may be returned as 
                 *  follows:
                 * 
                 *  [
                 *      [ name: 'size', 'values': ['small', 'medium', 'large'],
                 *      [ name: 'color', 'values': ['blue', 'green', 'red'],
                 *      [ name: 'material', 'values': ['cotton', 'nylon'],
                 *      ... e.t.c
                 *  ] 
                 *      
                 */
                return this.localProduct.parent_variant_attributes || [];
            },
            isSimpleProduct(){
                //  A simple product does not support variations
                return (!this.localProduct.allow_variants) ? true : false;
            },
            hasReminders(){
                return ( 
                        //  If its a simple product and does not have a price
                        (this.isSimpleProduct && !this.hasPrice) ||
                        
                        //  If its a variable product and does not have a price on atleast one variation
                        (!this.isSimpleProduct && !this.hasPriceOnVariations) || 

                        //  If its a simple product and does not have stock  
                        (this.isSimpleProduct && !this.hasStock) ||

                        //  If its a simple product and does not have plenty stock  
                        (this.isSimpleProduct && !this.hasPlentyStock) ||
                
                        //  If its a variable product and does not have stock on atleast one variation
                        (!this.isSimpleProduct && !this.hasEnoughStockOnAllOnVariations)
                );
            },
            hasPrice(){
                if( this.localProduct.has_price != null ){

                    return (this.localProduct.has_price === true ? true : false);

                /*  If the has_price is null it means this product has a price but
                 *  its determined by the product variations.
                 */
                }else{

                    return true;

                }
            },
            hasPriceOnVariations(){

                return (this.localProduct.has_prices_on_all_variations === true ? true : false);

            },
            hasStock(){

                //  If the stock status is not null it means this product supports stock take.
                if( this.localProduct.stock_status != null ){

                    //  If the stock status type is not set to "out_of_stock" then we still have stock
                    return ((this.localProduct.stock_status || {}).type !== 'out_of_stock') ? true : false;

                /*  If the stock status is null it means this product does not take stock.
                 *  This means we always have stock for this product
                 */
                }else{

                    return true;

                }
            },
            hasPlentyStock(){

                //  If the stock status is not null it means this product supports stock take.
                if( this.localProduct.stock_status != null ){

                    //  If the stock status type is set to "in_stock" then we still have plenty stock
                    return ((this.localProduct.stock_status || {}).type === 'in_stock') ? true : false;

                /*  If the stock status is null it means this product does not take stock.
                 *  This means we always have stock for this product
                 */
                }else{

                    return true;

                }
            },
            hasEnoughStockOnAllOnVariations(){
                return (this.localProduct.has_enough_stock_on_all_variations === true ? true : false);
            },
            productStockDescription(){
                return this.localProduct.stock_status.description;
            },
            productCurrency(){
                return (this.localProduct.currency.symbol || this.localProduct.currency.code);
            },
            productDescription(){
                return this.localProduct.description;
            },
            productRegularPrice(){
                return this.productCurrency + this.localProduct.unit_regular_price;
            },
            productSalesPrice(){
                return this.localProduct.on_sale ? (this.productCurrency + this.localProduct.unit_sale_price) : '(N/A)';
            },
            productDiscountTotal(){
                return  this.productCurrency + this.localProduct.discount_total;
            },
            productTaxTotal(){
                return this.productCurrency + this.localProduct.tax_total;
            },
            productStockQuantity(){
                return (this.localProduct.allow_stock_management ? this.localProduct.stock_quantity : '(N/A)');
            },
            productVariables(){
                return this.localProduct.variables;
            },
            checkIfProductHasChanged(){
                var now = _.cloneDeep(this.localProduct);
                var before = (this.productBeforeChange);
                var isNotEqual = !_.isEqual(now, before);

                return isNotEqual;
            }

        },
        methods: {
            getAvailableVariantAttributeNames(){
                /**
                 *  Get the avaialable variant attribute names. If we have the
                 *  following variant attribute:
                 * 
                 *  [
                 *      [ name: 'size', 'values': ['small', 'medium', 'large'],
                 *      [ name: 'color', 'values': ['blue', 'green', 'red'],
                 *      [ name: 'material', 'values': ['cotton', 'nylon'],
                 *      ... e.t.c
                 *  ] 
                 * 
                 *  We would like to only return the name values e.g
                 *  
                 *  ['size', 'color', 'material', ... e.t.c]
                 *      
                 */
                return this.getParentVariantAttributes.map( (variant_attribute) => {
                    return variant_attribute.name
                });
            },
            getAvailableVariantAttributeOptions(variant_attribute_name) {
                if( variant_attribute_name ){

                    /**
                     *  Get the parent variant attributes e.g
                     *  [
                     *      [ name: 'size', 'values': ['small', 'medium', 'large'],
                     *      [ name: 'color', 'values': ['blue', 'green', 'red'],
                     *      [ name: 'material', 'values': ['cotton', 'nylon'],
                     *      ... e.t.c
                     *  ] 
                     * 
                     *  Once collected map through each one e.g
                     *  [ name: 'size', 'values': ['small', 'medium', 'large']
                     * 
                     *  Then check if the name matches the variant_attribute_name provided.
                     *  If yes then return the options of that variant attribute name e.g
                     *  ['small', 'medium', 'large'].
                     * 
                     *  Since we are using the map function, if the variant_attribute_name 
                     *  does not match the current variant_attribute name then it will 
                     *  return null by default. The results may be returned as follows:
                     *  
                     *  results = [
                     *      null,
                     *      ['small', 'medium', 'large'],
                     *      null,
                     *      ... e.t.c
                     *  ] 
                     */
                    var results = this.getParentVariantAttributes.map( (variant_attribute) => {
                        if(variant_attribute_name == variant_attribute.name){
                            return variant_attribute.values
                        }
                    });

                    /**
                     *  We need to only return the values that are not null
                     */
                    for(var x = 0; x < results.length; x++){

                        //  If not equal to null
                        if( results[x] != null ){

                            //  Returns a non null value e.g ['small', 'medium', 'large']
                            return results[x];

                        }

                    }
                    
                }
            },
            getSelectedVariantAttributeOption(variant_attribute_name) {
                if( variant_attribute_name ){

                    /**
                     *  Get the current product variables e.g
                     *  [
                     *      [ name: 'size', 'value': 'small'],
                     *      [ name: 'color', 'value': 'blue'],
                     *      [ name: 'material', 'value': 'cotton'],
                     *      ... e.t.c
                     *  ] 
                     * 
                     *  Once collected map through each one e.g
                     *  [ name: 'size', 'value': 'small']
                     * 
                     *  Then check if the name matches the variant_attribute_name provided.
                     *  If yes then return the value of that variable e.g
                     *  'small', 'blue', 'cotton', ... e.t.c
                     * 
                     *  Since we are using the map function, if the variant_attribute_name 
                     *  does not match the current variable name then it will return null 
                     *  by default. The results may be returned as follows:
                     *  
                     *  results = [
                     *      null,
                     *      'small',
                     *      null,
                     *      ... e.t.c
                     *  ] 
                     */
                    var results = this.localProduct.variables.map( (variable) => {
                        if(variant_attribute_name == variable.name){
                            return variable.value
                        }
                    });

                    /**
                     *  We need to only return the values that are not null
                     */
                    for(var x = 0; x < results.length; x++){

                        //  If not equal to null
                        if( results[x] != null ){

                            //  Returns a non null value e.g 'small' or 'blue' or 'cotton' ... e.t.c
                            return results[x];

                        }

                    }
                    
                }
            },
            updateSelectedVariantAttributeOption(variant_attribute_name, newOption){

                /** Get all the product variables and find the index value of the product 
                 *  variable with a name value that matches the variant_attribute_name. 
                 *  Once the index is found use the $set method to update the product 
                 *  variable value of that index position
                 */
                console.log('variant_attribute_name: '+variant_attribute_name);
                console.log('newOption: '+newOption);
                console.log('Variables');
                console.log(this.localProduct.variables);

                var index = this.localProduct.variables.findIndex(
                        variable => variable.name == variant_attribute_name
                    );

                this.$set(this.localProduct.variables[index], 'value', newOption);
            },
            storeOriginalProductData(){

                //  Store the original product data
                this.productBeforeChange = _.cloneDeep(this.localProduct);

            },
            handleCancel(){

                //  Stop editing the product variants
                this.editVariants = false;

                //  Undo any changes made to the product while editing
                this.undoProductChanges();

            },
            handleSaveVariants(){

                //  Hold constant reference to the vue instance
                const self = this;

                //  Product data to update
                let variableData = self.localProduct.variables.map( (variable) => {
                    return {name: variable.name, value: variable.value}
                });

                if( (variableData || []).length ){

                    //  Console log to acknowledge the start of api process
                    console.log('Attempt to save product variables using the following...');  
                    console.log(variableData);

                    //  Start loader
                    self.isSavingVariants = true;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', this.localProduct._links['oq:variables'].href, variableData)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isSavingVariants = false;

                            //  Update the local product
                            self.localProduct = data;

                            //  Store the updated local product as the  original product
                            self.storeOriginalProductData();

                            //  Close the editor
                            self.handleCancel();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isSavingVariants = false;

                            //  Console log Error Location
                            console.log('Error saving product variables...');

                            //  Log the responce
                            console.log(response);    
                        });

                }
            },
            undoProductChanges(){
                
                this.localProduct = _.cloneDeep(this.productBeforeChange);

            },
            handleRemoveProduct(index) {

                let url = ((this.localProduct._links || {}).self || {}).href;

                if( url ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isDeletingProduct = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start deleting the product...');

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('delete', url )
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isDeletingProduct = false;

                            self.$emit('removeProduct', index);

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isDeletingProduct = false;

                            //  Console log Error Location
                            console.log('resources/js/widgets/store/overview/mobileStoreProductWidget.vue - Error getting products...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){

            //  Store the original product data before editing
            this.storeOriginalProductData();

        }
    }

</script>