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

        <div v-if="!isSavingProducts && !isLoadingProducts && localProducts.length" class="clearfix mb-3">

            <!-- Save Button -->
            <basicButton 
                class="float-right" customClass="pr-2 pl-2" 
                type="success" size="large" 
                :disabled="!productsHaveChanged"
                :ripple="productsHaveChanged"
                @click.native="handleSave()">
                <span>Save Changes</span>
            </basicButton>

        </div>

        <div class="product-container">

            <!-- Saving Spinnner  -->
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
                    :product="product">
                </singleProduct>

            </draggable>
        </div>

        <!-- No products message -->
        <Alert v-if="!isLoadingProducts && !localProducts.length" type="info" :style="{ maxWidth: '250px' }" show-icon>No products found</Alert>

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
            productsUrl: {
                type: String,
                default: null
            }
        }, 
        components: { 
            draggable, singleProduct, basicButton, Loader
        },
        data(){
            return {

                //  Products
                localProducts: [],
                isSavingProducts: false,
                isLoadingProducts: false,
                productsHaveChanged: false,
                productsBeforeChange: null,
                localProductsUrl: this.productsUrl,
 
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
            handleSave() {

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