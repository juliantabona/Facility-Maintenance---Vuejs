<style scoped>

    .product-widget{
        position: relative;
    }

    .fade-enter,
    .fade-leave-active {
        opacity: 0;
        transform: translateX(50px);
    }
    .fade-leave-active {
        position: absolute;
    }
 
    .animated {
        transition: all 0.5s;
        display: flex;
        width: 100%;
    }

</style>

<template>

    <div id="product-widget">

        <!-- Get the summary header to display the product #, status, , amount due and menu options -->
        <overview 
            v-if="!createMode && localProduct.has_approved"
            :product="localProduct" 
            :editMode="editMode" 
            :createMode="createMode"
            @toggleEditMode="toggleEditMode($event)">
        </overview>

        <!-- Loaders for creating/saving product -->
        <Row v-if="!createMode">
            <Col :span="24">
                <div v-if="isCreatingProduct" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingProduct" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>

        
        <transition-group name="fade">
            
            <!-- Product View/Editor -->
            <Row id="product-summary"  key="product_template" class="animated mb-5">
                <Col :span="24">

                    <Row>

                        <Col v-if="editMode" :span="24" class="mt-2 mb-2">
                            <h4 class="ml-2 mb-2 text-dark">Product Details</h4>
                        </Col>

                        <Col span="24" class="pl-2">
                        
                            <!-- Create/Edit Product -->
                            <productWidget 
                                :editMode="editMode"
                                :createMode="createMode"
                                :productId="null"
                                v-bind:product.sync="localProduct"
                                @update:product="localProduct = $event"
                                :showClientOrSupplierSelector="true"
                                :hideBio="false" 
                                :hideSaveBtn="true"
                                :hideSummaryToggle="true" 
                                :activateSummaryMode="true"
                                :canSaveOnCreate="false"
                                @created:product=""
                                @updated:product="">
                            </productWidget>
                            
                        </Col>

                    </Row>
                    
                </Col>
                
            </Row>

        </transition-group>

    </div>

</template>

<script>

    /*  Local components    */
    import overview from './overview.vue';
    import steps from './steps.vue';
    import mainHeader from './header.vue';
    import recurringSettingsSteps from './recurringSettingsSteps.vue';
    import productWidget from './productDetails.vue'; 
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';
    import editModeSwitch from './../../../components/_common/switches/editModeSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    /*  Selectors  */
    import companyOrIndividualDetails from './companyOrIndividualDetails.vue';
    import clientOrVendorSelector from './../../../components/_common/selectors/clientOrVendorSelector.vue'; 
    
    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            overview, steps, mainHeader, productWidget,
            basicButton, toggleSwitch, editModeSwitch,
            Loader, IconAndCounterCard, companyOrIndividualDetails,
            clientOrVendorSelector, recurringSettingsSteps
        },
        props: {
            product: {
                type: Object,
                default: null
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            create: {
                type: Boolean,
                default: false
            },
            modelType:{
                type: String,
                default: ''
            },
            modelId:{
                type: Number,
                default: null
            }
        },
        data(){
            return {

                user: auth.user,

                //  Modes
                editMode: false,
                createMode: this.create,

                //  Loading States
                isSavingProduct: false,
                isCreatingProduct: false,

                //  Local Product and state changes
                localProduct: (this.product || {}),
                _localProductBeforeChange: {},
                productHasChanged: false,
                showRecurringSettings: false
                
            }
        },
        watch: {
            localProduct: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    console.log(val);
                    console.log('checkIfproductHasChanged - 1');
                    this.productHasChanged = this.checkIfproductHasChanged(val);
                },
                deep: true
            }
        },
        methods: {
            updateProduct(product){
                //  Update the product
                this.localProduct = product;

                //  Store the current state of the product as the original product
                this.storeOriginalProduct();
                
                this.productHasChanged = this.checkIfproductHasChanged();
            },
            toggleEditMode(activate = true){

                var self = this,
                    options = {
                        easing: 'ease-in-out',
                        offset: -100,
                        force: true,
                        cancelable: true,
                        onStart: function(element) {
                            // scrolling started
                        },
                        onDone: function(element) {
                            //  Activate edit mode
                            self.editMode = activate;
                        },
                        onCancel: function() {
                        // scrolling has been interrupted
                        },
                        x: false,
                        y: true
                    }

                //var cancelScroll = VueScrollTo.scrollTo('product-summary', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#product-summary', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            updateReccuring(val){
                
                this.localProduct.isRecurring = val ? 1 : 0;
                
                this.showRecurringSettings = val;
                
            },
            activateCreateMode: function(){
                //  Activate edit mode
                this.editMode = true;
            },
            changeClient(newClient){

                if(newClient.model_type == 'user'){
                    this.$Notice.success({
                        title: 'Client changed to ' + newClient.first_name +  ' ' + newClient.last_name
                    });

                }else if(newClient.model_type == 'company'){
                    this.$Notice.success({
                        title: 'Client changed to ' + newClient.name
                    });
                }

                this.client = this.$set(this.localProduct, 'client', newClient);
                
                this.productHasChanged = this.checkIfproductHasChanged();

            },
            updateClient(newClientDetails){

                this.client = this.$set(this.localProduct, 'client', newClientDetails);

                this.productHasChanged = this.checkIfproductHasChanged();

            },
            updatePhoneChanges(companyOrIndividual, phones){
                
                //  Only update if the phones have changed
                var currentPhone = _.cloneDeep(phones);
                var isNotEqual = !_.isEqual(currentPhone, companyOrIndividual.phones);

                if(isNotEqual){

                    companyOrIndividual.phones = phones;
                    
                    this.productHasChanged = this.checkIfproductHasChanged();

                }

            },
            checkIfproductHasChanged: function(updatedProduct = null){
                
                var currentProduct = _.cloneDeep(updatedProduct || this.localProduct);
                var isNotEqual = !_.isEqual(currentProduct, this._localProductBeforeChange);

                return isNotEqual;
            },
            storeOriginalProduct(){
                //  Store the original product
                this._localProductBeforeChange = _.cloneDeep(this.localProduct);
                console.log('stored _localProductBeforeChange');
                console.log(this._localProductBeforeChange);
            },
            saveProduct(){
                var self = this;

                //  Start loader
                this.isSavingProduct = true;

                console.log('Attempt to create product...');

                console.log( this.localProduct );

                //  Form data to send
                let productData = { 
                        product: {
                            subject: this.localProduct.subject,
                            agenda: this.localProduct.agenda,
                            start_date: this.localProduct.start_date,
                            end_date: this.localProduct.end_date,
                            location: this.localProduct.location,
                            categories: this.localProduct.categories.map( (category) => { return category.id } ),
                            assigned_staff: this.localProduct.assigned_staff.map( (staff) => { return staff.id } ),
                            client_id: (this.localProduct.client || {}).id,
                            client_model_type: (this.localProduct.client || {}).model_type
                        }
                 };

                console.log(productData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/products/'+this.localProduct.id, productData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingProduct = false;

                        /*
                        *  updateProductData() : This method ensures the property is
                        *  updated as a reactive property and triggers future view updates:
                        */
                        self.updateProductData(data);

                        //  Alert creation success
                        self.$Message.success('Product saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingProduct = false;

                        console.log('productSummaryWidget.vue - Error saving product...');
                        console.log(response);
                    });
            },
            createProduct(){

                var self = this;

                //  Start loader
                this.isCreatingProduct = true;

                console.log('Attempt to create product...');

                console.log( this.localProduct );

                //  Form data to send
                let productData = { 
                        product: {
                            subject: this.localProduct.subject,
                            agenda: this.localProduct.agenda,
                            start_date: this.localProduct.start_date,
                            end_date: this.localProduct.end_date,
                            location: this.localProduct.location,
                            categories: this.localProduct.categories.map( (category) => { return category.id } ),
                            assigned_staff: this.localProduct.assigned_staff.map( (staff) => { return staff.id } ),
                            client_id: (this.localProduct.client || {}).id,
                            client_model_type: (this.localProduct.client || {}).model_type
                        }
                 };

                console.log(productData);

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/products', productData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingProduct = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the current state of the product as the original product
                        self.storeOriginalProduct();
                        
                        console.log('checkIfproductHasChanged - 3');
                        self.productHasChanged = self.checkIfproductHasChanged();

                        //  Alert creation success
                        self.$Message.success('Product created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('productCreated', data);

                        //  Go to product
                        self.$router.push({ name: 'show-product', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingProduct = false;

                        console.log('productSummaryWidget.vue - Error creating product...');
                        console.log(response);
                    });


            },
            updateProductData(newProduct){
                
                //  Disable edit mode
                this.editMode = false;
                
                /*
                 *  Vue.set()
                 *  We use Vue.set to set a new object property. This method ensures the  
                 *  property is created as a reactive property and triggers view updates:
                 */
            
                for(var x = 0; x <  _.size(newProduct); x++){
                    var key = Object.keys(this.localProduct)[x];
                    this.$set(this.localProduct, key, newProduct[key]);
                }

                //  Store the current state of the product as the original product
                this.storeOriginalProduct();

                console.log('checkIfproductHasChanged - 4');

                //  Update the product change status
                this.productHasChanged = this.checkIfproductHasChanged();

            },
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Store the current state of the product as the original product
                console.log('Now lets store that original product!');

                this.storeOriginalProduct();

                //  Update the product change status
                this.productHasChanged = this.checkIfproductHasChanged();

                if(this.createMode){
                    this.activateCreateMode();
                }

            })
        }
    };
  
</script>