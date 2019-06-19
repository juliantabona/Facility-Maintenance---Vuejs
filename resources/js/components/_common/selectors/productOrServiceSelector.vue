<template>

    <!-- Category Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left">Loading products...</Loader>
        <Select v-if="!isLoading && fetchedProductsAndServices" 
                v-model="localProduct" 
                :key="renderKey"
                :style="{ width:'100%' }" 
                placeholder="Select product or service" 
                filterable not-found-text="No products/services found">
            <Option 
                v-for="item in fetchedProductsAndServices" 
                :value="JSON.stringify(item)" 
                :key="item.id">{{ item.name }} ({{ item.type }})</Option>
        </Select>
        
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedProduct:{
                type: Object,
                default: null
            },
            clearable:{
                type: Boolean,
                default: false
            },
        },
        components: { Loader },
        data(){
            return {
                fetchedProductsAndServices: [],
                renderKey: 0,
                isLoading: false,
            }
        },
        watch: {

            //  Watch for changes on the selected product
            selectedProduct: {
                handler: function (val, oldVal) {
                    this.localProduct = JSON.stringify(val);

                }
            }
        },
        computed:{
            localProduct:{
                get(){
                    return JSON.stringify(this.selectedProduct);
                },
                set(val){
                    if(val != null){
                        
                        this.$emit('updated', JSON.parse(val));

                        if(this.clearable){
                            this.localProduct = null;
                            this.renderKey = this.renderKey + 1;
                        }

                    }
                }
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting products and services...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/products?connections=taxes')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;
                        
                        //  Get products and services
                        self.fetchedProductsAndServices = data.data;

                    })         
                    .catch(response => { 
                        console.log('selectedProductSelector.vue - Error getting products and services...');
                        console.log(response);

                        //  Stop loader
                        self.isLoading = false;     
                    });
            },
            saveChanges(){
                
                this.isSaving = true;

                //  Format the products and services
                var productsAndServices = this.productsAndServices.map(item => [{
                        id: JSON.parse(item).id,
                        name: JSON.parse(item).name,
                        description: JSON.parse(item).description,
                        quantity: 1,
                        unitPrice: JSON.parse(item).price, 
                        totalPrice: JSON.parse(item).price,
                        taxes: JSON.parse(item).taxes.length ? JSON.parse(item).taxes.map(tax => [{
                                    id: tax.id,
                                    name: tax.name,
                                    abbreviation: tax.abbreviation,
                                    rate: tax.rate
                                }]).flat() : []
                    }]).flat();

                this.$emit('selected',  productsAndServices);

                //  Close the modal
                this.closeModal();

            },
            closeModal(){
                this.hideModal = true;
            },
        },
        created () {
            this.fetch();
        }
    };
</script>