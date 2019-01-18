<template>

    <Row :gutter="20">

        <Col span="20" offset="2">
            <invoiceSummaryWidget 
                v-if="invoice"
                :invoice="invoice"
                v-bind="$props">
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
                isLoadingInvoice: false,
                invoice: null
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

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingInvoice = false;

                            console.log('dashboard/invoice/main.vue - Error getting invoice details...');
                            console.log(response);    
                        });

                }
            },
        },
        created(){
            this.fetchInvoice();
        }
    };
</script>