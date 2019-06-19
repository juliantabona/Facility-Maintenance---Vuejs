<template>

    <Row :gutter="20">
        
        <Col v-if="isLoading" span="8" offset="8">
            <!-- Loader -->
            <Loader v-if="true" :loading="true" type="text" class="text-left" theme="white">Loading invoice...</Loader>
        </Col>

        <Col v-else span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :fallbackRoute="{ name: 'invoices' }"></pageToolbar>

            <!-- Get the invoice details -->
            <invoiceSummaryWidget v-if="invoice" :invoice="invoice" :key="renderKey"></invoiceSummaryWidget>
            
        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Toolbars   */
    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';

    /*  Widgets   */
    import invoiceSummaryWidget from './../../../../widgets/invoice/show/main.vue';


    export default {
        components: { 
          Loader, pageToolbar, invoiceSummaryWidget
        },
        data(){
            return {
                renderKey: 1,
                invoice: null,
                isLoading: false,
            }
        },
        watch: {
            //  Watch for changes on the invoice id
            '$route.params.id': function (id) {
                
                // react to route changes by fetching the associated invoice...
                this.fetchInvoice();

            }
        },
        methods: {
            fetchInvoice() {

                //  If we have the route id set
                if( this.$route.params.id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    console.log('Start getting invoice details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/invoices/'+this.$route.params.id)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            //  Store the invoice data
                            self.invoice = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoading = false;

                            //  Error Location
                            console.log('dashboard/invoice/show/main.vue - Error getting invoice details...');

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
            //  Fetch the invoice
            this.fetchInvoice();
        }
    };
</script>