<template>

    <Row :gutter="20">

        <Col span="20" offset="2">
            <invoiceSummaryWidget 
                v-if="invoice"
                :invoice="invoice"
                v-bind="$props"
                :key="renderKey">
            </invoiceSummaryWidget>
        </Col>

    </Row>

</template>
<script>

    import invoiceSummaryWidget from './../../../../widgets/invoice/invoiceSummaryWidget.vue';


    export default {
        components: { 
          invoiceSummaryWidget
        },
        props: {
            receipt: {
                type: Object,
                default: null
            },
        },
        data(){
            return {
                renderKey: 1,
                isLoadingInvoice: false,
                invoice: null
            }
        },
        watch: {
            '$route.params.id': function (id) {
                
                // react to route changes...
                this.fetchInvoice();

            }
        },
        methods: {
            fetchInvoice() {
                const self = this;

                //  Start loader
                self.isLoadingInvoice = true;

                console.log('Start getting invoice details...');

                if( this.$route.params.id ){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/invoices/'+this.$route.params.id)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingInvoice = false;

                            self.invoice = data;

                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingInvoice = false;

                            console.log('dashboard/invoice/main.vue - Error getting invoice details...');
                            console.log(response);    
                        });

                }
            },
            renderComponent: function(){
                console.log('Re-rendering currencies');
                //  Re-render the component
                this.renderKey++;
            }
        },
        created(){
            this.fetchInvoice();
        }
    };
</script>