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
</style>

<template>
  <Row>
    <Col :span="24">
      <!-- Loader -->
      <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading...</Loader>

      <div v-if="!isLoading && !localEditMode">
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
                <!-- Name -->
                <p class="text-dark">
                  <strong>Name:</strong>
                  {{ formData.name ? formData.name : '___' }}
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

      <el-form
        v-if="!isLoading && localEditMode"
        label-position="top"
        label-width="100px"
        :model="formData"
      >
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
                    type: 'product',
                    replaceable: true
                }"
              :thumbnailStyle="{ width:'200px', height:'auto' }"
              @fileBeforeUpload="handleFileAdded('primary_image', $event)"
            ></imageUploader>
          </Col>

          <Col :span="24">
            <!-- Name -->
            <el-form-item label="Name:" prop="name" class="mb-2">
              <el-input
                v-model="formData.name"
                size="small"
                style="width:100%"
                placeholder="Enter product/organisation name"
              ></el-input>
            </el-form-item>
          </Col>

          <Col :span="24">
            <!-- Description -->
            <el-form-item label="Description:" prop="description" class="mb-2">
              <el-input
                v-model="formData.description"
                size="small"
                style="width:100%"
                placeholder="Enter product/organisation description"
              ></el-input>
            </el-form-item>
          </Col>

          <!-- Client/Supplier Selector -->
          <Col :span="24">
            <el-form-item label="Type:" prop="type" class="mt-2 mb-2">
              <productOrServiceTypeSelector
                class="mb-2"
                :selectedType="formData.type"
                @on-change="formData.type = $event"
              ></productOrServiceTypeSelector>
            </el-form-item>
          </Col>

          <Col :span="12">
            <!-- Purchase Price -->
            <el-form-item label="Purchase Price:" prop="description" class="mb-2">
              <el-input
                v-if="editMode"
                placeholder="e.g) Price"
                v-model="formData.purchase_price"
                size="large"
                class="p-1"
                :style="{ maxWidth:'100%' }"
              ></el-input>
            </el-form-item>
          </Col>

          <Col :span="12">
            <!-- Selling Price -->
            <el-form-item label="Selling Price:" prop="description" class="mb-2">
              <el-input
                v-if="editMode"
                placeholder="e.g) Price"
                v-model="formData.selling_price"
                size="large"
                class="p-1"
                :style="{ maxWidth:'100%' }"
              ></el-input>
            </el-form-item>
          </Col>
        </Row>

        <Row :gutter="20">
          <!-- Show/Hide More -->
          <Col v-if="!hideSummaryToggle" :span="24">
            <span  class="btn btn-link d-block font mt-0 pt-0 text-center"
              @click="summaryMode = !summaryMode">
              <Icon
                :type="summaryMode ? 'ios-eye-outline' : 'ios-eye-off-outline'"
                :size="24"
                class="mr-1"/>
              <span>{{ summaryMode ? 'Show more' : 'Show less' }}</span>
            </span>
          </Col>
        </Row>

        <Row v-if="!hideSaveBtn">
          <Col :span="24">
            <hr class="mt-2">
            <!-- Save Changes Button -->
            <Button class="float-right mt-2" type="success" size="large" @click="saveProduct()">
              <span>Save Changes</span>
            </Button>
          </Col>
        </Row>
      </el-form>
    </Col>
  </Row>
</template>

<script>
/*  Loaders   */
import Loader from "./../../../components/_common/loaders/Loader.vue";

/*  Selectors   */
import productOrServiceTypeSelector from "./../../../components/_common/selectors/productOrServiceTypeSelector.vue";

/*  Image Uploader  */
import imageUploader from "./../../../components/_common/uploaders/imageUploader.vue";

import lodash from "lodash";
Event.prototype._ = lodash;

export default {
  props: {
    editMode: {
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
    showproductOrServiceTypeSelector: {
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
    Loader,
    imageUploader,
    productOrServiceTypeSelector
  },
  data() {
    return {
      localProduct: null,
      summaryMode: this.activateSummaryMode,
      isLoading: false,
      ruleForm: {
        //  VALIDATION RULES
      },
      formData: {
        name: "",
        description: "",
        type: "",               //  product/service
        purchase_price: "",
        selling_price: "",
        primary_image: null
      },
      localEditMode: this.editMode
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
  methods: {
    handleFileAdded(key, fileData){
        
        this.$set(this.formData, key, fileData);

    },
    fetch() {
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

        //  product[key] != undefined if the key does not exist e.g) first_name, last_name, e.t.c
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
            
        var productData = new FormData();

        Object.keys(this.formData).map(key => {
            //  If its an object and also not a file or blob. Then we need to stringify it
            if(typeof self.formData[key] === "object" && !(typeof (self.formData[key] || {}).name == 'string')){
                productData.append(key, JSON.stringify(self.formData[key]) );
            }else{
                productData.append(key, self.formData[key] );
            }
        });

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
      this.fetch();
    }
  }
};
</script>