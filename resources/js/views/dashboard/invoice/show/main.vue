<template>

    <Row :gutter="20">
        
        <Col span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :fallbackRoute="{ name: 'invoices' }"></pageToolbar>

            <!-- Get the invoice details -->
            <invoiceSummaryWidget v-if="invoice" :invoice="invoice" :key="renderKey"></invoiceSummaryWidget>
            
        </Col>

    </Row>

</template>

<script>

    import pageToolbar from './../../../../components/_common/toolbar/pageToolbar.vue';
    import invoiceSummaryWidget from './../../../../widgets/invoice/invoiceSummaryWidget.vue';


    export default {
        components: { 
          pageToolbar, invoiceSummaryWidget
        },
        data(){
            return {
                renderKey: 1,
                invoice: null,
                isLoadingInvoice: false,
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
                    self.isLoadingInvoice = true;

                    console.log('Start getting invoice details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/invoices/'+this.$route.params.id)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingInvoice = false;

                            //  Store the invoice data
                            self.invoice = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingInvoice = false;

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