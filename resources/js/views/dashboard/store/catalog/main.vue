<template>

    <Row :gutter="20">

        <Col v-if="!isLoading && products" span="24">

            <!-- Get the store details -->
            <storeSummaryWidget :products="products" :key="renderKey"></storeSummaryWidget>
            
        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Widgets   */
    import storeSummaryWidget from './../../../../widgets/store/catalog/main.vue';


    export default {
        components: { 
          Loader, storeSummaryWidget
        },
        data(){
            return {
                renderKey: 1,
                products: null,
                isLoading: false,
            }
        },
        watch: {
            //  Watch for changes on the product id
            '$route.query.page': function (id) {
                
                // react to route changes by fetching the associated product...
                this.fetchProducts();

            }
        },
        methods: {
            fetchProducts() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoading = true;

                //  Console log to acknowledge the start of api process
                console.log('Start getting store products...');

                var storeId = (this.$route.params.storeId);
                var page = (this.$route.query.page) ? this.$route.query.page : 1;

                var urlParams = {
                        storeId: storeId,
                        page: page
                    }

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/products', null, urlParams)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Store the products
                        self.products = data.data;

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
            },
            renderComponent: function(){
                //  Re-render the component
                this.renderKey++;
            }
        },
        created(){
            //  Fetch the product
            this.fetchProducts();
        }
    };
</script>