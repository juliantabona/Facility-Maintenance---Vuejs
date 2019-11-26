<style scoped>
    
    .el-form-item{
        margin-bottom: 0px;
    }

    .switch >>> .el-form-item__label {
        line-height: 2.9em !important;
    }

    .el-form-item >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }

    .product-form >>> .ivu-poptip, .product-form >>> .ivu-poptip-rel{
        width:100% !important;
    }

</style>
<template>

    <div>

        <!-- Form -->
        <el-form class="product-form pb-5 mb-5" :model="formData" :rules="formDataRules" ref="formData">

            <Row :gutter="12">
                
                <Col :span="12" :offset="12">
                    
                    <!-- Loader -->
                    <Loader v-if="isLoading" :loading="true" type="text" class="text-left mt-2 mb-2">
                        {{ product ? 'Updating...' : 'Creating...' }}
                    </Loader>

                    <div v-if="!isLoading" class="clearfix">

                        <!-- Save Button -->
                        <basicButton 
                            v-if="updateUrl"
                            class="float-right"
                            :customClass="btnClass" type="success" size="large" 
                            :disabled="!formHasChanged" :ripple="formHasChanged"
                            @click.native="handleCreateOrUpdate()">
                            <span>Save Changes</span>
                        </basicButton>

                        <!-- Save Button -->
                        <basicButton 
                            v-if="createUrl"
                            class="float-right"
                            :customClass="btnClass" type="success" size="large" 
                            :disabled="!formHasChanged" :ripple="formHasChanged"
                            @click.native="handleCreateOrUpdate()">
                            <span>Create Product</span>
                        </basicButton>

                    </div>
                </Col>
            </Row>

            <div style="position:relative;">

                <!-- Updating Spinner  -->
                <Spin v-if="isLoading" size="large" fix></Spin>

                Post URL: {{ createUrl || updateUrl }}

                <Row :gutter="12" class="mt-2">
                    <Col :span="12">

                        <!-- Available On Web Store -->
                        <Poptip trigger="hover" width="380" placement="top-start" word-wrap 
                                content="Make this product available on the online website store?">
                            <el-form-item label="Show On Web Store" prop="show_on_store" class="switch mb-2" :error="customErrors.show_on_store">
                                <i-switch 
                                    true-color="#13ce66" 
                                    false-color="#ff4949" 
                                    class="ml-1" size="large"
                                    :disabled="isLoading"
                                    :value="formData.show_on_store" 
                                    @on-change="formData.show_on_store = $event">
                                    <span slot="open">Yes</span>
                                    <span slot="close">No</span>
                                </i-switch>
                            </el-form-item>
                        </Poptip>
                            
                    </Col>

                </Row>

                <Row :gutter="12">
                    <Col :span="24">

                        <!-- Name -->
                        <el-form-item label="Name" prop="name" class="mb-2" :error="customErrors.name">
                            <Poptip trigger="focus" width="350" placement="top" word-wrap 
                                    content="Give your product/service a name e.g Microwave">
                                <el-input type="text" v-model="formData.name" size="small" style="width:100%" 
                                          maxlength="100" show-word-limit placeholder="Enter product name">
                                </el-input>
                            </Poptip>
                        </el-form-item>
                            
                    </Col>
                </Row>

                <Row :gutter="12">
                    <Col :span="24">

                        <!-- Description -->
                        <el-form-item label="Description" prop="description" class="mb-2" :error="customErrors.description">
                            <Poptip trigger="focus" width="350" placement="top" word-wrap 
                                    content="Give more details about your product/service e.g dimensions, materials, features and functions, e.t.c">
                                <el-input type="textarea" v-model="formData.description" size="small" style="width:100%" 
                                          maxlength="300" show-word-limit placeholder="Enter product description" :rows="3">
                                </el-input>
                            </Poptip>
                        </el-form-item>

                    </Col>
                </Row>

                <!-- Type -->
                <el-form-item label="Type" prop="type" class="mb-2" :error="customErrors.type">
                    <el-input type="text" v-model="formData.type" size="small" style="width:100%" placeholder="Select product type"></el-input>
                </el-form-item>

                <!-- Variable Management -->
                <Row :gutter="12">
                    
                    <Col :span="24">

                        <!-- Allow Variants -->
                        <el-form-item label="Allow Variables" prop="allow_variants" class="switch mb-2" :error="customErrors.allow_variants">
                            
                            <Poptip trigger="hover" width="380" placement="top-start" word-wrap>
                                
                                <template slot="content">
                                    <span class="d-block">Add variables if this product/service comes in multiple versions e.g) different sizes, materials or colors. You can change the price and information for each variable</span>
                                
                                    <span v-if="(createUrl ? true : false)"
                                            style="margin-top: -15px;"
                                            class="border-top d-block pt-2">
                                        <span class="font-weight-bold">Note: Only available when editing this product after you create it.</span>
                                    </span>
                                </template>

                                <i-switch 
                                    true-color="#13ce66" 
                                    false-color="#ff4949" 
                                    class="ml-1" size="large"
                                    :disabled="(createUrl ? true : false) || isLoading"
                                    :value="formData.allow_variants" 
                                    @on-change="formData.allow_variants = $event"
                                    :before-change="handleAllowVariantsBeforeChange">
                                    <span slot="open">Yes</span>
                                    <span slot="close">No</span>
                                </i-switch>
                            </Poptip>
                        </el-form-item>

                    </Col>

                    <Col :span="24" v-if="formData.allow_variants">

                        <!-- Variant Attributes -->
                        <Row>

                            <Col :span="24">

                                <Row v-for="(attribute, index) in formData.variant_attributes" :gutter="20" :key="index">
                                    
                                    <Col :span="6">

                                        <!-- Option Names -->
                                        <el-form-item :label="index == 0 ? 'Option name' : ''" prop="option_names" class="mb-2">
                                            <Input
                                                placeholder="e.g Sizes"
                                                v-model="formData.variant_attributes[index].name"
                                                size="small">
                                            </Input>
                                        </el-form-item>

                                    </Col>

                                    <Col :span="16">
                                        
                                        <!-- Option Values -->
                                        <el-form-item :label="index == 0 ? 'Option values' : ''" prop="option_values" class="mb-2">
                                            <tagInput 
                                                style="margin-top:5px;"
                                                :tags="attribute.values" 
                                                :tagSettings="{
                                                    showSelector: false,
                                                    selectableTags: null,
                                                    saveToDatabase: false,
                                                    inputDirection: 'right'
                                                }"
                                                @updated="updateVariantAttributeOptions(index, $event)">
                                            </tagInput>
                                        </el-form-item>

                                    </Col>

                                    <Col :span="2" v-if="(formData.variant_attributes || {}).length > 1">

                                        <!-- Remove Variant Button  -->
                                        <Poptip confirm 
                                                title="Are you sure you want to remove this variant? After removing the variant we will delete all the current variations and create new ones."
                                                ok-text="Yes" cancel-text="No" width="300" placement="left"
                                                @on-ok="handleRemoveVariantAttribute(index)">
                                            <Icon type="ios-trash-outline" class="mr-2 mt-4" size="20"/>
                                        </Poptip>

                                    </Col>


                                </Row>

                                <Row :gutter="12">
                                    
                                    <Col v-if="hasVariants" :span="8">

                                        <!-- Generate Variations Button  -->
                                        <Poptip confirm 
                                                title="Create new variations?"
                                                ok-text="Yes" cancel-text="No" width="300" placement="top-start"
                                                @on-ok="generateVariations(index)">

                                            <basicButton 
                                                :disabled="isCreatingVariations"
                                                type="success" size="small" 
                                                customClass="mt-3 mb-3"
                                                :ripple="false">
                                                Create Variations
                                            </basicButton>
                                        </Poptip>

                                    </Col>
                                    
                                    <Col :span="hasVariants ? 16 : 24" class="clearfix">
                                        <basicButton 
                                            customClass="mt-3 mb-3" :style="{ width: 'fit-content', position:'relative' }"
                                            @click.native="addVariantAttribute()"
                                            :disabled="isCreatingVariations"
                                            type="default" size="small" 
                                            class="float-right"
                                            :ripple="false">
                                            + Add Another Variant
                                        </basicButton>
                                    </Col>
                                </Row>

                            </Col>

                            <Col :span="24">

                                <hr class="mt-2 pt-4" />

                                <!-- Loader -->
                                <Loader v-if="isLoadingVariations" :loading="true" type="text" class="text-left mt-2 mb-2">Loading variations...</Loader>

                                <!-- Loader -->
                                <Loader v-if="isCreatingVariations" :loading="true" type="text" class="text-left mt-2 mb-2">Creating variations...</Loader>

                                <!-- Single Product Variation  -->
                                <singleProductVariation v-else v-for="(product, index) in variations" :key="index"   
                                    :index="index"
                                    :product="product"
                                    :showFooter="true"
                                    :showDragButton="false"
                                    :showDeleteButton="false"
                                     @editProduct="handleEditProductVariation($event, index)">
                                </singleProductVariation>

                            </Col>

                        </Row>

                    </Col>

                </Row>

                <template v-if="!formData.allow_variants">

                    <!-- Pricing -->
                    <Row :gutter="12">
                        <Col :span="8">

                            <!-- Regular Price -->
                            <el-form-item label="Regular Price" prop="unit_regular_price" class="mb-2" :error="customErrors.unit_regular_price">
                                <Poptip trigger="click" width="350" placement="top-start" word-wrap 
                                        content="What is your product/service usual price?">
                                    <InputNumber v-model="formData.unit_regular_price" size="small" style="width:100%" 
                                                placeholder="100">
                                    </InputNumber>
                                </Poptip>
                            </el-form-item>

                        </Col>

                        <Col :span="8">

                            <!-- Sale Price -->
                            <el-form-item label="Sale Price" prop="unit_sale_price" class="mb-2" :error="customErrors.unit_sale_price">
                                <Poptip trigger="click" width="380" placement="top" word-wrap 
                                        content="Is your product/service on sale? Add your sale price">
                                    <InputNumber v-model="formData.unit_sale_price" size="small" style="width:100%" 
                                                :disabled="!formData.unit_regular_price" placeholder="80"
                                                :max="maximumSalePrice" :min="0" :step="1">
                                    </InputNumber>
                                </Poptip>
                            </el-form-item>

                        </Col>

                        <Col :span="8">

                            <!-- Cost Per Item -->
                            <el-form-item label="Cost Per Item" prop="cost_per_item" class="mb-2" :error="customErrors.cost_per_item">
                                <Poptip trigger="click" width="350" placement="top-end" word-wrap 
                                        content="How much does this product/service cost you?">
                                    <InputNumber v-model="formData.cost_per_item" size="small" style="width:100%" 
                                                :disabled="!formData.unit_regular_price" placeholder="50"
                                                :max="maximumSalePrice" :min="0" :step="1">>
                                    </InputNumber>
                                </Poptip>
                            </el-form-item>

                        </Col>

                    </Row>

                    <!-- Stock Management -->
                    <Row :gutter="12">

                        <Col :span="12">

                            <!-- Allow Stock Management -->
                            <el-form-item label="Allow Stock Management" prop="allow_stock_management" class="switch mb-2" :error="customErrors.allow_stock_management">
                                <Poptip trigger="hover" width="380" placement="top-start" word-wrap 
                                        content="Does your product/service have stock or limited items?">
                                    <i-switch 
                                        true-color="#13ce66" 
                                        false-color="#ff4949" 
                                        class="ml-1" size="large"
                                        :disabled="isLoading"
                                        :value="formData.allow_stock_management" 
                                        :before-change="handleBeforeAllowStockManagementChange"
                                        @on-change="formData.allow_stock_management = $event">
                                        <span slot="open">Yes</span>
                                        <span slot="close">No</span>
                                    </i-switch>
                                </Poptip>
                            </el-form-item>

                        </Col>

                        <template v-if="formData.allow_stock_management">
                            
                            <Col :span="12">

                                <!-- Auto Manage Stock -->
                                <el-form-item label="Auto Manage Stock" prop="auto_manage_stock" class="switch mb-2" :error="customErrors.auto_manage_stock">
                                    <Poptip trigger="focus" width="350" placement="top-end" word-wrap 
                                            content="Allow the system to automatically update the number of stock remaining each time customers buy this product/service?">
                                        <i-switch 
                                            true-color="#13ce66" 
                                            false-color="#ff4949" 
                                            class="ml-1" size="large"
                                            :disabled="isLoading"
                                            :value="formData.auto_manage_stock" 
                                            :before-change="handleBeforeAutoManageStockChange"
                                            @on-change="formData.auto_manage_stock = $event">
                                            <span slot="open">Yes</span>
                                            <span slot="close">No</span>
                                        </i-switch>
                                    </Poptip>
                                </el-form-item>

                            </Col>

                            <Col :span="24">

                                <!-- Stock Quantity -->
                                <el-form-item label="Stock Quantity" prop="stock_quantity" class="mb-2" :error="customErrors.stock_quantity">
                                    <Poptip trigger="focus" width="350" placement="top" word-wrap 
                                            content="How much stock (Quantity) of this product/service do you have?">  
                                        <el-input type="text" v-model="formData.stock_quantity" size="small" style="width:100%" placeholder="Enter stock quantity"></el-input>
                                    </Poptip>
                                </el-form-item>

                            </Col>

                            <Col :span="12">

                                <!-- SKU -->
                                <el-form-item label="SKU (Stock Keeping Unit)" prop="sku" class="mb-2" :error="customErrors.sku">
                                    <Poptip trigger="focus" width="380" placement="top-start" word-wrap 
                                            content="Assign a unique number to this product to identify it for inventory management">
                                        <el-input type="text" v-model="formData.sku" size="small" style="width:100%" placeholder="Enter unique code"></el-input>
                                    </Poptip>
                                </el-form-item>

                            </Col>
                            
                            <Col :span="12">

                                <!-- Barcode -->
                                <el-form-item label="Barcode" prop="barcode" class="mb-2" :error="customErrors.barcode">
                                    <Poptip trigger="focus" width="380" placement="top-end" word-wrap 
                                            content="Assign a unique barcode to this product to identify it for inventory management">
                                    <el-input type="text" v-model="formData.barcode" size="small" style="width:100%" placeholder="Enter unique barcode"></el-input>
                                    </Poptip>
                                </el-form-item>

                            </Col>

                        </template>

                    </Row>

                    <!-- Mark As Featured / New -->
                    <Row :gutter="12">

                         <!-- Mark As Featured -->
                        <Col :span="12">

                            <el-form-item label="Mark As Featured" prop="is_featured" class="switch mb-2" :error="customErrors.is_featured">
                                <Poptip trigger="hover" width="350" placement="top-start" word-wrap 
                                        content="Is this product/service featured? E.g marked as best value, best deal, on sale, e.t.c">
                                    <i-switch 
                                        true-color="#13ce66" 
                                        false-color="#ff4949" 
                                        class="ml-1" size="large"
                                        :disabled="isLoading"
                                        :value="formData.is_featured" 
                                        @on-change="formData.is_featured = $event">
                                        <span slot="open">Yes</span>
                                        <span slot="close">No</span>
                                    </i-switch>
                                </Poptip>
                            </el-form-item>

                        </Col>

                        <!-- Mark As New/Featured -->
                        <Col :span="12">

                            <el-form-item label="Mark As New" prop="is_new" class="switch mb-2" :error="customErrors.is_new">
                                <Poptip trigger="hover" width="350" placement="top-end" word-wrap 
                                        content="Is this a new product/service?">
                                    <i-switch 
                                        true-color="#13ce66" 
                                        false-color="#ff4949" 
                                        class="ml-1" size="large"
                                        :disabled="isLoading"
                                        :value="formData.is_new" 
                                        @on-change="formData.is_new = $event">
                                        <span slot="open">Yes</span>
                                        <span slot="close">No</span>
                                    </i-switch>
                                </Poptip>
                            </el-form-item>

                        </Col>
                        
                    </Row>

                    <!-- Allow Downloads -->
                    <Row :gutter="12">

                        <Col :span="12">

                            <el-form-item label="Allow Downloads" prop="allow_downloads" class="switch mb-2" :error="customErrors.allow_downloads">
                                <Poptip trigger="hover" width="350" placement="top-start" word-wrap 
                                        content="Does this product/service have any downloads? E.g documents, images, audio, video, e.t.c">
                                    <i-switch 
                                        true-color="#13ce66" 
                                        false-color="#ff4949" 
                                        class="ml-1" size="large"
                                        :disabled="isLoading"
                                        :value="formData.allow_downloads" 
                                        @on-change="formData.allow_downloads = $event">
                                        <span slot="open">Yes</span>
                                        <span slot="close">No</span>
                                    </i-switch>
                                </Poptip>
                            </el-form-item>

                        </Col>

                    </Row>

                </template>

            </div>

        </el-form>

        <!-- 
            DRAWER TO EDIT A PRODUCT VARIATION
            - This is a global component
        -->
        <editOrCreateProductDrawer 
            :mask="false"
            title="Edit Product Variation"
            :product="storedProductVariation"
            :updateUrl="productVariationUpdateUrl"
            :showDrawer="showEditProductVariationDrawer"
            @visibility="showEditProductVariationDrawer = $event"
            @updateSuccess="handleProductVariationUpdate($event)">
        </editOrCreateProductDrawer>
    </div>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../loaders/Loader.vue'; 

    /*  Inputs  */
    import tagInput from './../../inputs/tagInput.vue';

    /*  Buttons  */
    import basicButton from './../../buttons/basicButton.vue';

    /*  Single Variation Product Widget  */
    import singleProductVariation from './../../../../widgets/store/overview/mobileStoreProductWidget.vue';

    const formHandle = require('./main.js').default;

    export default {
        components: { Loader, tagInput, basicButton, singleProductVariation },
        props: {

            /*  The store  */
            store: {
                type: Object,
                default: null
            },

            /*  The ussd interface of the store  */
            ussdInterface: {
                type: Object,
                default: null
            },

            /*  The url to make a Post Request to create a product  */
            createUrl: {
                type: String,
                default: null
            },

            /*  The url to make a Post Request to update a product  */
            updateUrl: {
                type: String,
                default: null
            },

            /*  The product to update  */
            product: {
                type: Object,
                default: null
            },

            btnClass:{
                type: String,
                default: 'pr-2 pl-2'
            }
        },
        data(){
            return {
                variations: [],
                isLoading: false,
                formHasChanged: false,
                formDataBeforeChange: null,
                isLoadingVariations: false,
                isCreatingVariations: false,
                storedProductVariation: null,
                productVariationUpdateUrl: null,
                variantAttributesBeforeChange: null,
                showEditProductVariationDrawer: false,
                formData: formHandle.getFormFields(),
                formDataRules: formHandle.getFormRules(),
                customErrors: formHandle.getCustomErrorFields(),
                defaultVariantAttributes: [
                    {
                        name: 'Sizes',
                        values: ['Small', 'Medium', 'Large']
                    }
                ]
            }
        },

        watch: {
            /*  Keep track of changes on the formData.  */
            formData: {

                handler: function (val, oldVal) {

                    /*  Check if the the form data has changed  */
                    this.formHasChanged = this.checkIfFormHasChanged(val);

                },
                deep: true

            },
            
            /*  Keep track of changes on the product.  */
            product: {

                handler: function (val, oldVal) {

                    /*  Update the localShowDrawer  */
                    this.updateFormFieldValues(val);
                    
                    //  Store the original form data before editing
                    this.storeOriginalFormData();

                    /*  Check if the the form data has changed  */
                    this.formHasChanged = this.checkIfFormHasChanged();
                    
                },
                deep: true

            }
        },
        computed:{
            maximumSalePrice(){
                var regular_price = this.formData.unit_regular_price || 0;
                var less_than_regular_price = regular_price - 1;

                return (less_than_regular_price > 0) ? less_than_regular_price : 0;
            },
            hasVariants(){

                // Check if we have variant attributes
                if((this.formData.variant_attributes || {}).length){

                    for(var x=0; x < (this.formData.variant_attributes || {}).length; x++){

                        //  Get the current variant key e.g size, color, material, e.t.c
                        let attribute_name = this.formData.variant_attributes[x].name;
                        
                        //  Get the current variant value e.g ["SM", "M", "L"], ["Blue", "Red"] or ["Cotton", "Nylon"]
                        let attribute_values = this.formData.variant_attributes[x].values;
                        
                        // If the name or options have not been set then this is not valid variant attribute
                        if( !attribute_name || !attribute_values.length ){

                            return false;

                        }
                    }
                }

                return true;
            }
        },
        methods: {
            handleCreateOrUpdate(){

                /*  Call the initiateCreateOrUpdate() function from the formHandle in order to make 
                 *  an update request. We must pass "this" current vue instance in order to access
                 *  the vue data properties. The initiateCreateOrUpdate() function will handle all
                 *  validation of the form and will return the updated information after a
                 *  successful update request. We can use the then() hook to determine what 
                 *  to do next. In this case we update the parent vue component and pass the
                 *  updated information.
                */
                var self = this;
                var response = formHandle.initiateCreateOrUpdate(this);
                
                //  If we have a response
                if(response){
                    
                    //  Hook into the response
                    response.then( data => {

                        //  If not false
                        if( data !== false ){

                            if(self.createUrl){

                                //  Notify the parent on create success and pass the data
                                self.$emit('createSuccess', data);

                            }else if(self.updateUrl){
                                
                                //  Notify the parent on update succcess and pass the data
                                self.$emit('updateSuccess', data);

                            }

                        }
                    });
                }

            },
            updateFormFieldValues(currentFormData = this.product)
            {
                //  Get the form fields
                var formFields = formHandle.getFormFields();

                //  Foreach form field
                for(var x = 0; x < _.size(formFields); x++){
                    
                    //  Get the current field key e.g name, description, e.t.c
                    var key = Object.keys(formFields)[x];

                    /*
                    *  Vue.set()
                    *  We use Vue.set to set a new object property. This method ensures the  
                    *  property is created as a reactive property and triggers view updates:
                    */

                    //  If we have form data, then overide the form field values
                    if(currentFormData){

                        //  Update the form data fields using the current form data
                        this.$set(this.formData, key, currentFormData[key]);

                    }
                }

            },
            handleBeforeAllowStockManagementChange(){

            },
            handleBeforeAutoManageStockChange(){

            },
            handleEditProductVariation(product){

                //  Use the product variation url for the Post Request on update
                this.productVariationUpdateUrl = product['_links'].self.href;

                //  Store the current product for editting
                this.storedProductVariation = product;

                //  Open the edit product variation drawer
                this.openEditProductVariationDrawer();

            },
            handleProductVariationUpdate(updatedProduct){

                //  Check if the product data has already been changed
                var isAlreadyChanged = this.formHasChanged;
                
                /*  Get all the product variations and find the index value of the 
                 *  product variation that matches the updated product id. Once the 
                 *  index is found use the $set method to update the product of that 
                 *  index position
                 */

                this.$set(this.variations, this.variations.findIndex(
                    product => product.id == updatedProduct.id
                ), updatedProduct);

                /*  If the current product data is not changed then keep the state as unchanged
                 *  even after updaing this product variation. If its already changed it means
                 *  the user had already done something to the product data and needs to save
                 *  those changes e.g the user was editting the current product name,
                 *  description, pricing, stock, e.t.c and did not save the current 
                 *  product updates.
                 */
                if( !isAlreadyChanged ){
                    
                    //  Store the original form data before editing
                    this.storeOriginalFormData();

                    /*  Check if the the form data has changed  */
                    this.formHasChanged = this.checkIfFormHasChanged();
                    
                }

            },
            openEditProductVariationDrawer(){
                this.showEditProductVariationDrawer = true;
            },
            addVariantAttribute(){
                this.formData.variant_attributes.push({  name: 'Color', values: ['Blue', 'Red'] });
            },
            updateVariantAttributeOptions(key, options){
                this.formData.variant_attributes[key].values = options;
            },
            checkIfFormHasChanged(formData){
                
                var currentForm = _.cloneDeep(formData || this.formData);
                var isNotEqual = !_.isEqual(currentForm, this.formDataBeforeChange);

                return isNotEqual;
            },
            storeOriginalFormData(){
                //  Store the original form data
                this.formDataBeforeChange = _.cloneDeep(this.formData);
            },
            checkIfVariantAttributesHasChanged(){
                
                var currentForm = _.cloneDeep(this.formData.variant_attributes);
                var isNotEqual = !_.isEqual(currentForm, this.variantAttributesBeforeChange);

                return isNotEqual;
            },
            storeOriginalVariantAttributesData(){

                //  Store the original product variant attributes
                this.variantAttributesBeforeChange = _.cloneDeep(this.formData.variant_attributes);

            },
            handleAllowVariantsBeforeChange () {
                return new Promise((resolve) => {
                    
                    //  If the switch was on but is being turned off
                    if(this.formData.allow_variants){
                        
                        //  Restore the variant attributes to their original state
                        this.formData.variant_attributes = this.variantAttributesBeforeChange;


                    //  If the switch was off but is being turned on
                    }else{

                        //  Fetch the product variations
                        this.fetchVariations();

                        //  Store the original product variant attributes
                        this.storeOriginalVariantAttributesData();
                        
                        //  If the product does not already have variant attributes
                        if( !(this.variantAttributesBeforeChange || []).length ){

                            //  Add the default variable attributes
                            this.formData.variant_attributes = _.cloneDeep(this.defaultVariantAttributes);

                        }

                    }

                    //  This concludes our promise
                    resolve();

                });
            },
            fetchVariations() {

                if( this.product ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingVariations = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting variations...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', this.product._links['oq:variations'].href)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingVariations = false;

                            //  Store the product variations data
                            self.variations = ((data || {})._embedded || {}).products || [];

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingVariations = false;

                            //  Console log Error Location
                            console.log('Error getting variations...');

                            //  Log the responce
                            console.log(response);    
                        });

                }
            },

            handleRemoveVariantAttribute(index) {

                //  If we have more that one variant attribute
                if( this.formData.variant_attributes.length > 1 ){

                    //  Remove the variant attribute
                    this.formData.variant_attributes.splice(index, 1);

                    /** Update the product details. This is so that we can actually save the current
                     *  variant attributes of the product. 
                     */
                    self.handleCreateOrUpdate();

                    /** Re-fetch the product variations so that they can pick up the changes of the
                     *  parent variant attributes. 
                     */
                    self.fetchVariations();

                }else{

                    this.$Notice.warning({
                        
                        title: 'You must have atleast one variant'

                    });

                }

            },
            generateVariations() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Product data to update
                let updateData = self.formData.variant_attributes;

                if( (updateData || []).length ){

                    //  Console log to acknowledge the start of api process
                    console.log('Attempt to save product variations using the following...');  
                    console.log(updateData);

                    //  Start loader
                    self.isCreatingVariations = true;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', this.product._links['oq:variations'].href, updateData)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isCreatingVariations = false;

                            //  Store the product variations data
                            self.variations = ((data || {})._embedded || {}).products || [];

                            /*  Update the rest of the product details. This is because we want a fresh instance 
                             *  of this product with the updated attributes. Remember that since we updated
                             *  the variations this will affect specific attributes on the product iteself. 
                             *  We therefore need a fresh version to pick up those changed attributes.
                             */
                            self.handleCreateOrUpdate();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isCreatingVariations = false;

                            //  Console log Error Location
                            console.log('Error saving variations...');

                            //  Log the responce
                            console.log(response);    
                        });

                }
            }
        },
        created(){
            
            //  Update the form fields with the current Ussd Interface field values
            this.updateFormFieldValues();

            //  If the switch is already turned on
            if(this.formData.allow_variants){
                
                //  Fetch the product variations
                this.fetchVariations();

            }

            //  Store the original form data before editing
            this.storeOriginalFormData();

        }
    }

</script>