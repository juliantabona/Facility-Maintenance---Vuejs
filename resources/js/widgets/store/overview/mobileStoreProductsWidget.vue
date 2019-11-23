<style>

    .product-container{
        position:relative;
    }

    .sortable-ghost{
        background:#4bff0059 !important;
        transition:all 1s ease;
    }

	.sortable-chosen{
        /*
        background:red !important;
        opacity: 1 !important;
        */
    }

    .sortable-drag{
        opacity: 1 !important;
    }

</style>

<template>

    <div>

        <!-- Loader -->
        <Loader v-if="isLoadingProducts" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading products...</Loader>
        <Loader v-if="isSavingProducts" :loading="true" type="text" class="mt-2 mb-2 text-left" theme="white">Saving products...</Loader>

        <div v-if="!isSavingProducts && !isLoadingProducts" class="clearfix mb-3">
            
            <!-- Save Button -->
            <basicButton v-if="productsHaveChanged" 
                :ripple="true"
                customClass="pr-2 pl-2" 
                class="float-right ml-2" 
                type="success" size="large"
                @click.native="handleSaveProduct()">
                <span>Save Changes</span>
            </basicButton>

            <!-- Save Button -->
            <basicButton 
                class="float-right" 
                customClass="pr-2 pl-2" 
                type="success" size="large" 
                :disabled="productsHaveChanged"
                @click.native="handleCreateProduct()">
                <span>Create Product</span>
            </basicButton>

        </div>

        <div class="product-container">

            <!-- Saving Spinner  -->
            <Spin v-if="isSavingProducts" size="large" fix></Spin>
            
            <!-- Product Dragger  -->
            <draggable v-if="!isLoadingProducts && localProducts.length"
                :list="localProducts"
                @start="drag=true" 
                @end="drag=false" 
                :options="{
                    group:'sections', 
                    draggable:'.single-product', 
                    handle:'.product-dragger-handle'
                }">

                <!-- Single Product  -->
                <singleProduct v-for="(product, index) in localProducts" :key="index"   
                    :index="index"
                    :product="product"
                    :showFooter="false"
                    @removeProduct="handleRemoveProduct(index)"
                    @editProduct="handleEditProduct($event, index)">
                </singleProduct>

            </draggable>
        </div>

        <!-- No products message -->
        <Alert v-if="!isLoadingProducts && !localProducts.length" type="info" :style="{ maxWidth: '250px' }" show-icon>No products found</Alert>

        <!-- 
            DRAWER TO EDIT A PRODUCT
            - This is a global component
        -->
        <editOrCreateProductDrawer 
            :store="store"
            :createUrl="createUrl"
            :updateUrl="updateUrl"
            :product="storedProduct"
            :ussdInterface="ussdInterface"
            :showDrawer="showEditOrCreateProductDrawer"
            @visibility="showEditOrCreateProductDrawer = $event"
            @createSuccess="handleCreatedProduct($event)"
            @updateSuccess="handleUpdatedProduct($event)">
        </editOrCreateProductDrawer>

    </div>

</template>

<script>
    
    import draggable from 'vuedraggable';

    /*  Single Products Widget  */
    import singleProduct from './mobileStoreProductWidget.vue';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    export default {
        props: {
            store: {
                type: Object,
                default: null
            },
            ussdInterface: {
                type: Object,
                default: null
            }
        }, 
        components: { 
            draggable, singleProduct, basicButton, Loader
        },
        data(){
            return {
                createUrl:null,
                updateUrl:null,
                localProducts: [],
                storedProduct:null,
                isSavingProducts: false,
                isLoadingProducts: false,
                productsBeforeChange: null,
                productsHaveChanged: false,
                showEditOrCreateProductDrawer:false,
                localProductsUrl: (((this.ussdInterface || {})._links || {})['oq:products'] || {}).href,
 
            }
        },
        watch: {
            /*  Keep track of changes on the localProducts.  */
            localProducts: {

                handler: function (val, oldVal) {

                    /*  Check if the the localProducts has changed  */
                    this.productsHaveChanged = this.checkIfProductsHaveChanged(val);

                },
                deep: true

            }
        },
        methods: {
            handleRemoveProduct(index){

                //  Remove the product from the list
                this.localProducts.splice(index, 1);

                /*  Store the original products data  */
                this.storeOriginalProductsData();

                /*  Check if the the localProducts has changed  */
                this.productsHaveChanged = this.checkIfProductsHaveChanged();

            },
            handleCreateProduct(){

                //  Remove the update url
                this.updateUrl = null;

                //  Use the products url for the Post Request on create
                this.createUrl = this.createUrl;

                //  Remove any stored product
                this.storedProduct = null;

                //  Open the product drawer
                this.openEditOrCreateProductDrawer();

            },
            handleEditProduct(product){

                //  Remove the create url
                this.createUrl = null;

                //  Use the product url for the Post Request on update
                this.updateUrl = product['_links'].self.href;

                //  Store the current product for editting
                this.storedProduct = product;

                //  Open the edit product drawer
                this.openEditOrCreateProductDrawer();

            },
            handleCreatedProduct(createdProduct){

                //  Check if the product data has already been changed
                var isAlreadyChanged = this.productsHaveChanged;
                
                //  Add the new create product to the top of the list
                this.localProducts.unshift(createdProduct);

                /*  If the product data is not changed then keep the state as unchanged
                 *  even after updaing this product item. If its alrady changed it means
                 *  the user had already done something to the product data and needs to
                 *  save those changes e.g the user had used the drag and drop and did 
                 *  not save the new product arrangement.
                 */
                if( !isAlreadyChanged ){

                    /*  Store the original products data  */
                    this.storeOriginalProductsData();

                    /*  Check if the the localProducts has changed  */
                    this.productsHaveChanged = this.checkIfProductsHaveChanged();
                    
                }

                //  Close the drawer
                this.showEditOrCreateProductDrawer = false;

                //  Show success message
                this.$Notice.success({
                    title: 'Created successfully'
                });

            },
            handleUpdatedProduct(updatedProduct){

                //  Check if the product data has already been changed
                var isAlreadyChanged = this.productsHaveChanged;
                
                /*  Get all the products and find the index value of the product
                 *  that matcches the updated product id. Once the index is found
                 *  use the $set method to update the product of that index position
                 */
                this.$set(this.localProducts, this.localProducts.findIndex(
                    product => product.id == updatedProduct.id
                ), updatedProduct);

                /*  If the product data is not changed then keep the state as unchanged
                 *  even after updaing this product item. If its alrady changed it means
                 *  the user had already done something to the product data and needs to
                 *  save those changes e.g the user had used the drag and drop and did 
                 *  not save the new product arrangement.
                 */
                if( !isAlreadyChanged ){

                    /*  Store the original products data  */
                    this.storeOriginalProductsData();

                    /*  Check if the the localProducts has changed  */
                    this.productsHaveChanged = this.checkIfProductsHaveChanged();
                    
                }

            },
            openEditOrCreateProductDrawer(){
                this.showEditOrCreateProductDrawer = true;
            },
            handleSaveProduct() {

                if( this.localProductsUrl ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isSavingProducts = true;

                    //  Store data
                    let updateData = self.localProducts.map((product, index) => {
                        return { 
                            "id": product.id, 
                            "arrangement": (index + 1)
                        }; 
                    });

                    //  Console log to acknowledge the start of api process
                    console.log('Start saving the products...');
                    console.log(updateData);

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('post', this.localProductsUrl, updateData)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isSavingProducts = false;

                            /*  Store the original products data  */
                            self.storeOriginalProductsData();

                            /*  Check if the the localProducts has changed  */
                            self.productsHaveChanged = self.checkIfProductsHaveChanged();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isSavingProducts = false;

                            //  Console log Error Location
                            console.log('resources/js/widgets/store/overview/mobileStoreProductWidget.vue - Error getting products...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            fetchProducts() {

                if( this.localProductsUrl ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingProducts = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting products...');

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.localProductsUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingProducts = false;

                            //  Get the url to create a new product
                            self.createUrl = (((data || {})._links || {}).self || {}).href;

                            //  Get the product data
                            self.localProducts = ((data || {})._embedded || {}).products || [];

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingProducts = false;

                            //  Console log Error Location
                            console.log('resources/js/widgets/store/overview/mobileStoreProductWidget.vue - Error getting products...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            checkIfProductsHaveChanged(products){
                
                var currentForm = _.cloneDeep(products || this.localProducts);
                var isNotEqual = !_.isEqual(currentForm, this.productsBeforeChange);

                return isNotEqual;
            },
            storeOriginalProductsData(){
                //  Store the original products data
                this.productsBeforeChange = _.cloneDeep(this.localProducts);
            },
        },
        created(){
            //  Fetch the products
            this.fetchProducts().then((products) => {
                /*  Store the original products data  */
                this.storeOriginalProductsData();

                /*  Check if the the localProducts has changed  */
                this.productsHaveChanged = this.checkIfProductsHaveChanged();
            });
        }
    };
  
</script>