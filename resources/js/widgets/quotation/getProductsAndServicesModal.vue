<template>
    <div>
        <Modal
            title="Products/Services"
            v-model="modalVisible"
            :mask-closable="true"
            @on-close="abortChanges">

            <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading...</Loader>
            
            <Select v-if="!isLoading && fetchedProductsAndServices" v-model="productsAndServices" :style="{ width:'100%' }" placeholder="Select product/service" multiple not-found-text="No products/services found">
                <Option v-for="item in fetchedProductsAndServices" :value="JSON.stringify(item)" :key="item.id">{{ item.name }} ({{ item.type }}) - {{ item.price }}</Option>
            </Select>
            
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="abortChanges">Cancel</el-button>
                <el-button size="small" type="primary" @click="saveChanges" :loading="isSaving">Add</el-button>
            </span>
        </Modal>    
    </div>
</template>
<script>

    import Loader from './../../components/_common/loader/Loader.vue';

    export default {
        props:{
            show: {
                type: Boolean,
                default: false,
            }
        },
        components: { Loader },
        data(){
            return{
                productsAndServices: [],
                fetchedProductsAndServices: [],
                isSaving: false,
                isLoading: false
            }
        },
        computed:{
            modalVisible:{
                get(){
                    return this.show;
                },
                set(v){ 
                    this.$emit("closed");
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
                        console.log('getProductsAndServicesModal.vue - Error getting products and services...');
                        console.log(response);

                        //  Stop loader
                        self.isLoading = false;     
                    });
            },
            saveChanges(){
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

                console.log('New fetched and formatted items');
                console.log(productsAndServices);

                this.$emit('selected',  productsAndServices);

                this.emptyProductsAndServices();
            },
            abortChanges(){
                this.emptyProductsAndServices();
                this.$emit('closed');
            },
            emptyProductsAndServices(){
                this.productsAndServices = [];
            },

        },
        created () {
            this.fetch();
        }
    }
</script>