<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSaving" 
            :hideModal="hideModal"
            title="Products/Services"
            okText="Add" cancelText="Cancel"
            @on-ok="saveChanges()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Loader -->
                <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading...</Loader>
                
                <!-- Product/Service Selector -->
                <Select v-if="!isLoading && fetchedProductsAndServices" v-model="productsAndServices" :style="{ width:'100%' }" placeholder="Select product/service" multiple filterable not-found-text="No products/services found">
                    <Option v-for="item in fetchedProductsAndServices" :value="JSON.stringify(item)" :key="item.id">{{ item.name }} ({{ item.type }}) - {{ item.price }}</Option>
                </Select>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    import mainModal from './main.vue';
    import Loader from './../loaders/Loader.vue';

    export default {
        components: { mainModal, Loader },
        data(){
            return{
                productsAndServices: [],
                fetchedProductsAndServices: [],

                hideModal: false,
                isLoading: false,
                isSaving: false
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
                        console.log('getProductsAndServicesModal.vue - Error getting products and services...');
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
    }
</script>