<template>

    <Row :gutter="20">
        
        <Col v-if="isLoading" span="8" offset="8">
            <!-- Loader -->
            <Loader :loading="true" type="text" class="text-left" theme="white">Loading products</Loader>
        </Col>

        <Col v-if="!isLoading && product" span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar 
                :showBackBtn="true"
                :fallbackRoute="{ name: 'products', params: { id: product.id } }">

            </pageToolbar>

            <!-- Get the product details -->
            <productSummaryWidget :product="product" :key="renderKey"></productSummaryWidget>
            
        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Toolbars   */
    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';

    /*  Widgets   */
    import productSummaryWidget from './../../../../widgets/product/show/main.vue';


    export default {
        components: { 
          Loader, pageToolbar, productSummaryWidget
        },
        data(){
            return {
                renderKey: 1,
                product: null,
                isLoading: false,
            }
        },
        watch: {
            //  Watch for changes on the product id
            '$route.params.id': function (id) {
                
                // react to route changes by fetching the associated product...
                this.fetchProduct();

            }
        },
        methods: {
            fetchProduct() {

                //  If we have the route id set
                if( this.$route.params.id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting product details...');

                    //  Additional data to eager load along with the product found
                    var connections = '';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/products/'+this.$route.params.id+connections)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            //  Store the product data
                            self.product = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoading = false;

                            //  Console log Error Location
                            console.log('dashboard/product/show/main.vue - Error getting product details...');

                            //  Log the responce
                            console.log(response);    
                        });

                }
            },
            renderComponent: function(){
                //  Re-render the component
                this.renderKey++;
            }
        },
        created(){
            //  Fetch the product
            this.fetchProduct();
        }
    };
</script>