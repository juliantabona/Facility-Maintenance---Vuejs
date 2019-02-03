<template>
    <div>
        <!-- Modal -->
        <Modal title="Products/Services" v-model="modalVisible" :mask-closable="true" @on-visible-change="detectClose">

            <!-- Loader -->
            <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading...</Loader>
            
            <!-- Product/Service Selector -->
            <Select v-if="!isLoading && fetchedProductsAndServices" v-model="productsAndServices" :style="{ width:'100%' }" placeholder="Select product/service" multiple filterable not-found-text="No products/services found">
                <Option v-for="item in fetchedProductsAndServices" :value="JSON.stringify(item)" :key="item.id">{{ item.name }} ({{ item.type }}) - {{ item.price }}</Option>
            </Select>
            
            <!-- Send/Cancel Buttons -->
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="closeModal">Cancel</el-button>
                <el-button size="small" type="primary" @click="saveChanges" :loading="isSaving">Add</el-button>
            </span>
        </Modal>    
    </div>
</template>
<script>

    import Loader from './../../../components/_common/loader/Loader.vue';

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
                isLoading: false,

                modalVisible: false
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

                this.$emit('selected',  productsAndServices);

                this.emptyProductsAndServices();

                //  Close the modal
                this.closeModal();

            },
            detectClose(){
                
                var self = this;

                //  Only after 1 second
                setTimeout(function(){
                    //  Notify the parent on change of modal visibility
                    self.$emit('visibility', self.modalVisible)
                    self.emptyProductsAndServices();
                }, 500);
            },
            closeModal(){
                //  By setting modalVisible = false, we also trigger the detectClose() method
                //  since the modal has the event @on-visible-change="detectClose" to detect
                //  any changes of the "modalVisible". The "detectClose()" method would then
                //  notify the parent on the changes of the modal visibility.
                this.modalVisible = false;
            },
            emptyProductsAndServices(){
                this.productsAndServices = [];
            },

        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Set modal visibility to true to show modal
                this.modalVisible = true;
            })
        },
        created () {
            this.fetch();
        }
    }
</script>