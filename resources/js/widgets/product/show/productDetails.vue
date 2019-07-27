<style scoped>
.el-form-item >>> .el-form-item__label {
  margin: 0 !important;
  padding: 0 !important;
  line-height: 24px !important;
}

.form-label {
  font-size: 14px;
}

.info-highlight-box {
  background: #f5f7fa;
  border-radius: 10px;
  padding: 15px;
}

.dot{
    width:5px;
    height:5px;
    border-radius:50%;
    background: #d1d7e0;
    display:inline-block;
}

</style>

<template>
  <Row>
    <Col :span="24">
      <!-- Loader -->
      <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading...</Loader>

      <div v-if="false">
        <Row :gutter="20" class="mb-1">
          <!-- Product Image -->
          <Col :span="8" :offset="8">
            <imageUploader
              uploadMsg="Upload or change image"
              :allowUpload="editMode"
              :multiple="false"
              :docUrl=" this.localProduct ? '/api/products/'+(this.localProduct || {}).id+'/image' : null"
              :postData="{ 
                    modelId: this.localProduct ? (this.localProduct || {}).id : null,
                    modelType: 'product',
                    location:  'products', 
                    type: 'primary',
                    replaceable: true
                }"
              :thumbnailStyle="{ width:'200px', height:'auto' }"
              @fileBeforeUpload="handleFileAdded('primary_image', $event)"
            ></imageUploader>
          </Col>

          <Col :span="24" class="mb-2">
            <h4 class="ml-1 text-dark">Summary</h4>
          </Col>

          <Col :span="24">
            <Row class="info-highlight-box mb-3">
              <Col :span="12">
                <!-- Title -->
                <p class="text-dark">
                  <strong>Title:</strong>
                  {{ formData.title ? formData.title : '___' }}
                </p>
                <!-- Description -->
                <p class="text-dark">
                  <strong>Description:</strong>
                  {{ formData.description ? formData.description : '___' }}
                </p>
                <!-- Type -->
                <p class="text-dark">
                  <strong>Type:</strong>
                  {{ formData.type ? formData.type : '___' }}
                </p>
              </Col>

              <Col :span="12">
                <!-- >Purchase Price -->
                <p class="text-dark">
                  <strong>Purchase Price:</strong>
                  {{ formData.buy ? formData.buy : '___' }}
                </p>
                <!-- Selling Price -->
                <p class="text-dark">
                  <strong>Selling Price:</strong>
                  {{ formData.sell ? formData.sell : '___' }}
                </p>
              </Col>
            </Row>
          </Col>
        </Row>
      </div>

      <el-form v-if="!isLoading" label-position="top" label-width="100px" :model="formData">

        <Row :gutter="20">
            
            <!-- White overlay when creating/saving product -->
            <Spin size="large" fix v-if="isSaving || isCreating" style="border-radius: 15px">
                <!-- Icon to show as loader  -->
                <clockLoader></clockLoader>
            </Spin>

            <Col :span="24" class="mt-2 mb-2">
                <Card class="clearfix pb-3" :style="{ width: '100%' }">

                    <!-- Create Button -->
                    <basicButton v-if="createMode && (!isSaving && !isCreating)" customClass="float-right" :style="{ position:'relative' }"
                            type="success" size="large" :ripple="false" @click.native="createNewProduct()">
                        Create
                    </basicButton>

                    <!-- Save Button -->
                    <basicButton v-if="!createMode && (!isSaving && !isCreating)" customClass="float-right" :style="{ position:'relative' }"
                            type="success" size="large" :ripple="false" @click.native="saveProduct()">
                        Save
                    </basicButton>

                    <!-- Preview Button -->
                    <basicButton v-if="(!isSaving && !isCreating)" customClass="float-right mr-2" :style="{ position:'relative' }"
                            type="default" size="large" :ripple="false"@click.native="true">
                        Preview
                    </basicButton>

                    <!-- Show on store switch -->
                    <toggleSwitch 
                        v-bind:toggleValue.sync="formData.show_on_store"
                        @update:toggleValue="formData.show_on_store = $event"
                        :ripple="false" :showIcon="true" onIcon="ios-store" offIcon="ios-store" 
                        title="Show on store" onText="Yes" offText="No" poptipMsg="Show this product on the ecommerce store"
                        class="float-left mt-2">
                    </toggleSwitch>
                    <div class="dot float-left" style="margin:17px 2px 0px 10px;"></div>
                    <span class="btn btn-link float-left pl-1" @click="true">
                        <span style="font-size: 12px;" class="inline-block">View Store</span>
                    </span>

                </Card>
            </Col>

            <Col :span="16">
                <Card :style="{ width: '100%' }" class="mb-2">
                    <Row :gutter="20" class="mb-1">
                        
                        <Col :span="24">
                            <!-- Title -->
                            <el-form-item label="Title:" prop="title" class="mb-2">
                                <el-input v-model="formData.title" size="small" style="width:100%" placeholder="Enter product title"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="24">
                            <!-- Description -->
                            <el-form-item label="Description:" prop="description" class="mb-2">
                                <el-input type="textarea" v-model="formData.description" size="small" style="width:100%" placeholder="Enter product description"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <!-- Product Type Selector -->
                            <el-form-item label="Product Type" prop="type" class="mt-2 mb-2">
                                <productTypeSelector
                                    class="mb-2"
                                    :selectedType="(formData || {}).type"
                                    @on-change="formData.type = $event">
                                </productTypeSelector>
                            </el-form-item>
                        </Col>

                    </Row>
                </Card>
                
                <Card :style="{ width: '100%' }" class="mb-2">
                    <Tabs :animated="false" class="pt-3 pb-5">

                        <!-- Pricing Details -->
                        <TabPane label="Pricing">
                            <Row :gutter="20" class="p-2">

                                <Col :span="12">
                                    <!-- Price -->
                                    <el-form-item label="Price" prop="price" class="mb-2">
                                        <el-input
                                            placeholder="0.00"
                                            v-model="formData.unit_price"
                                            size="small"
                                            :style="{ maxWidth:'100%' }">
                                        </el-input>
                                    </el-form-item>
                                </Col>

                                <Col :span="12">
                                    <!-- Sale Price -->
                                    <el-form-item label="Sale price" prop="unit_sale_price" class="mb-2">
                                        <el-input
                                            placeholder="0.00"
                                            v-model="formData.unit_sale_price"
                                            size="small"
                                            :style="{ maxWidth:'100%' }">
                                        </el-input>
                                    </el-form-item>
                                </Col>

                                <Col :span="12">
                                    <!-- Unit Price -->
                                    <el-form-item label="Cost per item" prop="unit_price" class="mb-2">
                                        <el-input
                                            placeholder="0.00"
                                            v-model="formData.cost_per_item"
                                            size="small"
                                            :style="{ maxWidth:'100%' }">
                                        </el-input>
                                    </el-form-item>
                                </Col>
                            </Row>
                        </TabPane>

                        <!-- Inventory Details -->
                        <TabPane label="Inventory">
                            <Row :gutter="20" class="p-2">
                                <Col v-if="!formData.allow_inventory" :span="24" class="mb-2">
                                    <Alert>
                                        <template slot="desc">Use inventory if you have stock and want to keep track of how many items are left.</template>
                                    </Alert>
                                </Col>

                                <Col :span="24" class="mb-0">

                                    <!-- Inventory Checkmark -->
                                    <el-form-item prop="allow_inventory" class="mb-1">
                                        <Checkbox v-model="formData.allow_inventory">This product has inventory</Checkbox>
                                    </el-form-item>

                                </Col>

                                <Col v-if="formData.allow_inventory" :span="24">
                                    <Row :gutter="12">
                                        <Col :span="12">
                                            <!-- Price -->
                                            <el-form-item label="SKU (Stock Keeping Unit)" prop="sku" class="mb-2">
                                                <el-input
                                                    placeholder="e.g BL01"
                                                    v-model="formData.sku"
                                                    size="small"
                                                    :style="{ maxWidth:'100%' }">
                                                </el-input>
                                            </el-form-item>
                                        </Col>

                                        <Col :span="12">
                                            <!-- Price -->
                                            <el-form-item label="Barcode (ISBN, UPC, GTIN, etc.)" prop="barcode" class="mb-2">
                                                <el-input
                                                    placeholder="e.g 07350053850019"
                                                    v-model="formData.barcode"
                                                    size="small"
                                                    :style="{ maxWidth:'100%' }">
                                                </el-input>
                                            </el-form-item>
                                        </Col>

                                        <Col :span="12">

                                            <!-- Quantity-->
                                            <el-form-item label="Quantity" prop="quantity">
                                                <el-input
                                                    type="number" min="1"
                                                    v-model="formData.quantity"
                                                    size="small"
                                                    :style="{ maxWidth:'100%' }">
                                                </el-input>
                                            </el-form-item>

                                        </Col>

                                        <Col :span="12">

                                            <!-- Track Inventory -->
                                            <el-form-item prop="auto_track_inventory">
                                                <Checkbox v-model="formData.auto_track_inventory" class="mt-4 ml-2">Track stock inventory</Checkbox>
                                            </el-form-item>
                                            
                                        </Col>
                                    </Row>
                                </Col>
                            </Row>
                        </TabPane>

                        <!-- Variant Details -->
                        <TabPane label="Variants">
                            <Row :gutter="20" class="p-2">
                                <Col v-if="!formData.allow_variants" :span="24" class="mb-2">
                                    <Alert>
                                        <template slot="desc">Add variants if this product comes in multiple versions, like different sizes or colors.</template>
                                    </Alert>
                                </Col>

                                <Col :span="24" class="mb-0">

                                    <!-- Variant Checkmark -->
                                    <el-form-item prop="allow_variants" class="mb-1">
                                        <Checkbox v-model="formData.allow_variants">This product has variants</Checkbox>
                                    </el-form-item>

                                </Col>

                                <Col v-if="formData.allow_variants" :span="24">
                                    <Row v-for="(variantAttribute, key) in formData.variantAttributes" :gutter="20" :key="key">
                                        
                                        <Col :span="8">

                                            <!-- Option Names -->
                                            <el-form-item :label="key == 0 ? 'Option name' : ''" prop="option_names" class="mb-2">
                                                <el-input
                                                    placeholder="e.g Size"
                                                    v-model="formData.variantAttributes[key].name"
                                                    size="small">
                                                </el-input>
                                            </el-form-item>

                                        </Col>

                                        <Col :span="16">
                                            
                                            <!-- Option Values -->
                                            <el-form-item :label="key == 0 ? 'Option values' : ''" prop="option_values" class="mb-2">
                                                <tagInput 
                                                    style="margin-top:5px;"
                                                    :tags="formData.variantAttributes[key].options" 
                                                    :tagSettings="{
                                                        showSelector: false,
                                                        selectableTags: null,
                                                        saveToDatabase: false,
                                                        inputDirection: 'right'
                                                    }"
                                                    @updated="addVariantAttributeOption(key, $event)">
                                                </tagInput>
                                            </el-form-item>

                                        </Col>

                                    </Row>

                                    <basicButton v-if="hasVariants"
                                            @click.native="addVariantAttribute()"
                                            customClass="mt-3 mb-3" :style="{ width: 'fit-content', position:'relative' }"
                                            type="success" size="small" 
                                            :ripple="false">
                                        + Add Variant
                                    </basicButton>

                                </Col>

                                <Col v-if="formData.allow_variants && hasVariants" :span="24">
                                    <span class="font-weight-bold d-block mb-2">Modify the variants:</span>
                                    <table class="table table-hover mt-3 mb-0 w-100">
                                        <thead style="background-color:#fbfcfd;">
                                            <tr>
                                                <th colspan="4" class="p-2" style=";white-space: nowrap">
                                                    <span>Variant</span>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <span>Price</span>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <span>SKU</span>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <span>Barcode</span>
                                                </th>
                                                <th colspan="1" class="p-2" style="white-space: nowrap">
                                                    <span>Quantity</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(variant, key) in formData.variants">
                                                <th colspan="4" class="p-2" style="white-space: nowrap">
                                                    <span class="d-block mt-1">
                                                        <!-- Track Inventoroption_activey -->
                                                        <el-form-item prop="option_active" class="mb-0">
                                                            <Checkbox v-model="formData.variants[key].checked">{{ variant.name }}</Checkbox>
                                                        </el-form-item>

                                                    </span>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <!-- Price -->
                                                    <el-form-item prop="option_price" class="mb-2">
                                                        <el-input
                                                            placeholder="0.00"
                                                            v-model="formData.variants[key].unit_price"
                                                            size="small"
                                                            :style="{ maxWidth:'100%' }">
                                                        </el-input>
                                                    </el-form-item>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <!-- SKU -->
                                                    <el-form-item prop="option_sku" class="mb-2">
                                                        <el-input
                                                            v-model="formData.variants[key].sku"
                                                            size="small"
                                                            :style="{ maxWidth:'100%' }">
                                                        </el-input>
                                                    </el-form-item>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <!-- Barcode -->
                                                    <el-form-item prop="option_barcode" class="mb-2">
                                                        <el-input
                                                            v-model="formData.variants[key].barcod"
                                                            size="small"
                                                            :style="{ maxWidth:'100%' }">
                                                        </el-input>
                                                    </el-form-item>
                                                </th>
                                                <th colspan="1" class="p-2" style="white-space: nowrap">
                                                    <!-- Quantity -->
                                                    <el-form-item prop="option_quantity" class="mb-2">
                                                        <el-input
                                                            type="number" min="1"
                                                            v-model="formData.variants[key].quantity"
                                                            size="small"
                                                            :style="{ maxWidth:'100%' }">
                                                        </el-input>
                                                    </el-form-item>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </Col>
                            </Row>
                        </TabPane>

                        <!-- Downloadables Details -->
                        <TabPane label="Downloadables">
                            <Row :gutter="20" class="pt-2 pr-2 pl-2 pb-5">
                                <Col v-if="!formData.allow_downloads" :span="24" class="mb-2">
                                    <Alert>
                                        <template slot="desc">Add files if this product has downloadables such as images, audio, documents or artworks.</template>
                                    </Alert>
                                </Col>

                                <Col :span="24" class="mb-0">

                                    <!-- Download Checkmark -->
                                    <el-form-item prop="allow_downloads" class="mb-1">
                                        <Checkbox v-model="formData.allow_downloads">This product has downloadable files</Checkbox>
                                    </el-form-item>

                                </Col>

                                <Col v-if="formData.allow_downloads" :span="24">
                                    <span class="font-weight-bold d-block mb-2">Downloadables:</span>
                                    <table class="table table-hover mt-3 mb-0 w-100">
                                        <thead style="background-color:#fbfcfd;">
                                            <tr>
                                                <th colspan="4" class="p-2" style=";white-space: nowrap">
                                                    <span>Name</span>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <span>Description</span>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <span>File</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(download, key) in formData.downloads">
                                                <th colspan="4" class="p-2" style="white-space: nowrap">
                                                    <!-- Download Name -->
                                                    <el-form-item prop="download_name" class="mb-2">
                                                        <el-input
                                                            v-model="formData.downloads[key].name"
                                                            size="small"
                                                            :style="{ maxWidth:'100%' }">
                                                        </el-input>
                                                    </el-form-item>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <!-- Download Description -->
                                                    <el-form-item prop="download_description" class="mb-2">
                                                        <el-input
                                                            v-model="formData.downloads[key].description"
                                                            size="small"
                                                            :style="{ maxWidth:'100%' }">
                                                        </el-input>
                                                    </el-form-item>
                                                </th>
                                                <th colspan="2" class="p-2" style="white-space: nowrap">
                                                    <!-- Download File -->
                                                    <el-form-item prop="download_file" class="mb-2">
                                                        <imageUploader
                                                            uploadMsg="Upload or change image"
                                                            :allowUpload="editMode"
                                                            :multiple="false"
                                                            :docUrl="(formData.downloads[key].file || {}).id ? '/api/products/'+(this.localProduct || {}).id+'/download/' + (formData.downloads[key].file || {}).id : null"
                                                            :postData="{ 
                                                                    modelId: this.localProduct ? (this.localProduct || {}).id : null,
                                                                    modelType: 'product',
                                                                    location:  'products', 
                                                                    type: 'primary',
                                                                    replaceable: true
                                                                }"
                                                            :thumbnailStyle="{ width:'200px', height:'auto' }"
                                                            @fileBeforeUpload="$set(formData.downloads[key], 'file', $event)">
                                                        </imageUploader>
                                                    </el-form-item>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <basicButton
                                                @click.native="addDownloadOption()"
                                                customClass="mb-3" :style="{ width: 'fit-content', position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="false">
                                            + Add Download
                                    </basicButton>

                                </Col>

                            </Row>
                        </TabPane>

                    </Tabs>
                </Card>
            </Col>

            <Col :span="8">
                <Card :style="{ width: '100%' }">

                    <imageUploader
                        uploadMsg="Upload or change image"
                        :allowUpload="editMode"
                        :multiple="false"
                        :docUrl=" this.localProduct ? '/api/products/'+(this.localProduct || {}).id+'/image' : null"
                        :postData="{ 
                                modelId: this.localProduct ? (this.localProduct || {}).id : null,
                                modelType: 'product',
                                location:  'products', 
                                type: 'primary',
                                replaceable: true
                            }"
                        :thumbnailStyle="{ width:'200px', height:'auto' }"
                        @fileBeforeUpload="handleFileAdded('primary_image', $event)"
                    ></imageUploader>
                </Card>

                <Card class="mt-2 pt-1" :style="{ width: '100%' }">

                    <!-- Vendor Company Selector -->
                    <el-form-item label="Vendor" prop="type" class="mt-2 mb-2">
                        <clientOrVendorSelector
                            :companyId="user.company_id"
                            :selectedCompanyOrIndividual="(formData || {}).vendor"
                            companyOrIndividualType="clients" 
                            :showCompanyDropDownList="true"
                            :showIndividualDropDownList="true"
                            :showSelectedCompanyOrIndividual="true"
                            @updated="formData.vendor = $event">
                        </clientOrVendorSelector> 
                    </el-form-item>

                </Card>

                <Card class="mt-2 pt-1" :style="{ width: '100%' }">

                    <!-- Category Checkmarks -->
                    <el-form-item label="Categories" prop="categories" class="mb-2">
                        <categoryCheckmark 
                            :selectedCategories="formData.categories"
                            :requestData="{ category_type: 'product' }"
                            @updated="formData.categories = $event">
                        </categoryCheckmark>
                    </el-form-item>

                    <!-- Tags Selector -->
                    <el-form-item label="Tags" prop="tags" class="mt-2 mb-2">
                        <tagInput 
                            :tags="formData.tags"
                            :tagSettings="{
                                showSelector: true,
                                selectableTags: 'product',
                                saveToDatabase: true,
                                inputDirection: 'bottom'
                            }"
                            @updated="formData.tags = $event">
                        </tagInput>
                    </el-form-item>

                </Card>
            </Col>
        
        </Row>

      </el-form>
    </Col>
  </Row>
</template>

<script>
/*  Loaders   */
import Loader from './../../../components/_common/loaders/Loader.vue';
import clockLoader from './../../../components/_common/loaders/clockLoader.vue';

/*  Selectors   */
import productTypeSelector from './../../../components/_common/selectors/productTypeSelector.vue';
import clientOrVendorSelector from "./../../../components/_common/selectors/clientOrVendorSelector.vue"; 

/*  Image Uploader  */
import imageUploader from "./../../../components/_common/uploaders/imageUploader.vue";

/*  Switches  */
import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';

/*  Inputs  */
import tagInput from './../../../components/_common/inputs/tagInput.vue';

/*  Buttons  */
import basicButton from './../../../components/_common/buttons/basicButton.vue';

/*  Checkmarks  */ 
import categoryCheckmark from './../../../components/_common/checkmarks/categoryCheckmark.vue';

import lodash from "lodash";
Event.prototype._ = lodash;

export default {
  props: {
    editMode: {
      type: Boolean,
      default: false
    },
    createMode: {
      type: Boolean,
      default: false
    },
    productId: {
      type: Number,
      default: null
    },
    product: {
      type: Object,
      default: null
    },
    /*
     *  canSaveOrCreate checks if the parent has permitted for the product
     *  to be saved to the databse. If canSaveOrCreate is set to true
     *  we will perform an ajax request to create the new product
     *  using our formData information.
     */
    canSaveOrCreate: {
      type: Boolean,
      default: false
    },
    hideSaveBtn: {
      type: Boolean,
      default: false
    },
    showproductTypeSelector: {
      type: Boolean,
      default: false
    },
    hideSummaryToggle:{
        type: Boolean,
        default: false
    },
    activateSummaryMode:{
        type: Boolean,
        default: false
    }
  },
  components: {
    Loader, clockLoader, imageUploader, productTypeSelector, clientOrVendorSelector, 
    toggleSwitch, tagInput, basicButton, categoryCheckmark
  },
  data() {
    return {

        user: auth.user,
        localProduct: null,
        summaryMode: this.activateSummaryMode,
        isLoading: false,
        ruleForm: {
        //  VALIDATION RULES
        },
        formData: {

            //  General details
            title: "",
            description: "",
            type: "",               //  product/service
            primary_image: null,
            categories: [],
            tags: [],

            //  Supplier details
            vendor: null,

            //  Pricing details
            cost_per_item: 0,
            unit_price: 0,
            unit_sale_price: 0,

            //  Inventory & Tracking details
            sku: "",
            barcode: "",
            quantity: 1,
            allow_inventory: false,
            auto_track_inventory: true,

            //  Variant details
            variants:[],
            variantAttributes: [
                {
                    name: '',
                    options: []
                }
            ],
            allow_variants: false,

            //  Download Details
            downloads: [
                {
                    name: '',
                    description: '',
                    file: null
                }
            ],
            allow_downloads: false,

            //  Ecommerce details
            show_on_store: true
        },
        localEditMode: this.editMode,
        isCreating: false,
        isSaving: false
    };
  },
  watch: {
    //  Watch for changes on the canSaveOrCreate value
    canSaveOrCreate: {
      handler: function(val, oldVal) {
        //  Create a new product if canSaveOrCreate is set to true
        if (this.productId) {
          this.saveProduct();
        } else {
          this.createNewProduct();
        }
      }
    },

    //  Watch for changes on the edit mode value
    editMode: {
      handler: function(val, oldVal) {
        //  Update the edit mode value
        this.localEditMode = val;
      }
    }
  },
  computed: {
        hasVariants(){

            // Check if we have variants
            if((this.formData.variantAttributes || {}).length){
                for(var x=0; x < (this.formData.variantAttributes || {}).length; x++){
                    // If the name and options have been set then this is a valid variant attribute
                    if( this.formData.variantAttributes[x].name && (this.formData.variantAttributes[x].options || {}).length ){
                        return true;
                    }
                }
            }

            return false;
        },
        validVariants(){
            
            var valid = [];

            // Check if we have variants
            if( (this.formData.variantAttributes || {}).length ){
                for(var x=0; x < (this.formData.variantAttributes || {}).length; x++){
                    // If the name and options have been set then this is a valid variant attributes
                    if( this.formData.variantAttributes[x].name && (this.formData.variantAttributes[x].options || {}).length ){
                        valid.push( this.formData.variantAttributes[x] );
                    }
                }
            }

            return valid;
        }
  },
  methods: {
    handleFileAdded(key, fileData){
        
        this.$set(this.formData, key, fileData);

    },
    addVariantAttributeOption(key, options){
        this.formData.variantAttributes[key].options = options;
        this.buildVariantTemplates();
    },
    addVariantAttribute(){
        this.formData.variantAttributes.push({  name: '', options: [] });
        this.buildVariantTemplates();
    },
    buildVariantTemplates(){
        
        var variantsNames = this.getVariantName(0);
        var variantTemplates = [];

        for(var x = 0; x < (variantsNames || {}).length; x++){
            variantTemplates.push({
                name: variantsNames[x],
                unit_price: 0.00,
                unit_sale_price: 0.00,
                sku: variantsNames[x],
                barcode: '',
                quantity: 1,
                checked: true
            });
        }

        this.formData.variants = variantTemplates;

        return variantTemplates;
    },
    getVariantName(level = 0){
        /*  If we have the following variant attributes
                - Size: [XS, S]
                - Color: [blue, red]
                - Material: [cotton, nylon]
            This function return the variant attribute option name in the 
            following format:
                - [XS, blue, cotton], [XS, blue, nylon], [XS, red, cotton], [XS, red, nylon]
                - [S, blue, cotton], [S, blue, nylon], [S, red, cotton], [S, red, nylon]
            
        */



        // Check if we have valid variants e.g sizes, colors, materials respectively
        if( (this.validVariants || {}).length ){
            
            var variations = [];

            //  Foreach valid variant attribute e.g size, color, material
            for(var x = level; x < (this.validVariants || {}).length; x++){

                if( (x == level) ){

                //  Get the variant attribute options e.g)
                //  level = 0 : XS, SM, M, L, XL
                //  level = 1 : Blue, Red
                //  level = 2 : Cottom, Nylon
                var variantOptions = this.validVariants[x].options;

                //  Foreach variant attribute option e.g "XS, SM, M, L, XL" or "Blue, Red" or "Cottom, Nylon"
                for(var y=0; y < (variantOptions || {}).length; y++){
                    console.log('--------------------------------------------------------------');
                    console.log('level == '+ level);
                    console.log('Focus: ' + this.validVariants[x].name);
                    console.log(level == 0 ? variantOptions[y] : ' ---' + variantOptions[y]);

                    //  Get the variant option name .g) XS_blue_cotton or XS_blue_nylon e.t.c
                    //  level = 0 : return XS_Blue_Cotton
                    //  level = 1 : return Blue
                    //  level = 2 : return Cottom


                    var variation = variantOptions[y];
                    
                    //  if we have more variations we can attach to the existing 
                    if( level != ((this.validVariants || {}).length - 1) ){
                        //  Foreach child variation we got e.g) blue, red or cotton, nylon
                        var childVariations = this.getVariantName(level + 1);
                        console.log('childVariations');
                        console.log(childVariations);
                        for( var z = 0; z < childVariations.length; z++){
                            //  To avoid xs_cotton_cotton or xs_nylon_nylon
                            if(variation != childVariations[z]){
                                //  To avoid xs_cotton_nylon or xs_nylon_cotton
                                if(variation != childVariations[z]){
                                    variations.push(variation+'_'+childVariations[z]);
                                }
                            }
                        }
                    }else{
                        //  e.g to produce [blue, red] or [cotton, nylon]
                        variations.push(variation);
                    }

                }
                }

            }

            return variations;
        }
    },
    addDownloadOption(){
        this.formData.downloads.push({
            name: '',
            description: '',
            file: null
        });
    },
    fetchProduct() {
      if (this.productId) {
        //  Start loader
        this.isLoading = true;

        const self = this;

        //  Additional data to eager load along with the product found
        var connections = "";

        //  Use the api call() function located in resources/js/api.js
        api
          .call("get", "/api/products/" + this.productId + connections)
          .then(({ data }) => {
            console.log(data);

            //  Stop loader
            self.isLoading = false;

            self.updateFormData(data);
          })
          .catch(response => {
            console.log(response);

            //  Stop loader
            self.isLoading = false;
          });
      }
    },
    updateFormData(product) {
        /*
        *  Vue.set()
        *  We use Vue.set to set a new object property. This method ensures the
        *  property is created as a reactive property and triggers view updates:
        */

        for (var x = 0; x < _.size(this.formData); x++) {
            var key = Object.keys(this.formData)[x];

            //  product[key] != undefined if the key does not exist e.g) title, description, e.t.c
            if (product[key] != undefined) {
            this.$set(this.formData, key, product[key]);
            }
        }

        //  Store the data as the localProduct
        this.localProduct = product;
    },
    saveProduct() {
        const self = this;

        //  Start loader
        self.isSaving = true;

        console.log('Attempt to save product details...');
            
        var productData = new FormData();

        Object.keys(this.formData).map(key => {
            //  If its an object and also not a file or blob. Then we need to stringify it
            if(typeof self.formData[key] === "object" && !(typeof (self.formData[key] || {}).name == 'string')){
                productData.append(key, JSON.stringify(self.formData[key]) );
            }else{
                productData.append(key, self.formData[key] );
            }
        });

        //  Get categories as ids
        productData.set('categories', JSON.stringify( this.formData.categories.map( category => { return category.id } ) ));

        //  Get tags as ids
        productData.set('tags', JSON.stringify( this.formData.tags.map( tag => { return tag.id } ) ));

        console.log(productData);

        //  Additional data to eager load along with the product
        var connections = '';

        //  Use the api call() function located in resources/js/api.js
        api.call('post', '/api/products/'+this.localProduct.id + connections, productData)
            .then(({data}) => {

                console.log(data);

                //  Stop loader
                self.isSaving = false;
                
                //  Alert creation success
                self.$Message.success("Saved sucessfully!");

                self.$emit("updated:product", data);

            })         
            .catch(response => { 
                console.log('widgets/product/show/main.vue - Error saving product...');
                console.log(response);

                //  Stop loader
                self.isSaving = false;

            });

    },
    createNewProduct() {
        const self = this;

        //  Start loader
        self.isCreating = true;

        console.log('Attempt to create new product...');
            console.log('self.formData');
            console.log(self.formData);
        var productData = new FormData();

        Object.keys(this.formData).map(key => {
            //  If its an object and also not a file or blob. Then we need to stringify it
            if(typeof self.formData[key] === "object" && !(typeof (self.formData[key] || {}).name == 'string')){
                productData.append(key, JSON.stringify(self.formData[key]) );
            }else{
                productData.append(key, self.formData[key] );
            }
        });

        //  Get categories as ids
        productData.set('categories', JSON.stringify( this.formData.categories.map( category => { return category.id } ) ));

        //  Get tags as ids
        productData.set('tags', JSON.stringify( this.formData.tags.map( tag => { return tag.id } ) ));

        console.log(productData);

        //  Additional data to eager load along with the product
        var connections = '';

        //  Use the api call() function located in resources/js/api.js
        api.call('post', '/api/products?'+connections, productData)
            .then(({data}) => {
                
                console.log(data);

                //  Stop loader
                self.isCreating = false;
                
                //  Alert creation success
                self.$Message.success('Created sucessfully!');

                self.$emit("created:product", data);

            })         
            .catch(response => { 
                console.log('widgets/product/show/main.vue - Error creating product...');
                console.log(response);

                //  Stop loader
                self.isCreating = false;

            });
    }
  },
  created() {
    if (this.product) {
      this.updateFormData(this.product);
    } else {
      this.fetchProduct();
    }
  }
};
</script>