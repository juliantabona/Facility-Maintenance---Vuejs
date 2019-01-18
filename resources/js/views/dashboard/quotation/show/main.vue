<template>

    <Row :gutter="20">

        <Col span="24">
            <quotationSummaryWidget 
                v-if="quotation"
                :quotation="quotation"
                v-bind="$props">
            </quotationSummaryWidget>
        </Col>

    </Row>

</template>
<script>

    import quotationSummaryWidget from './../../../../widgets/quotation/quotationSummaryWidget.vue';


    export default {
        components: { 
          quotationSummaryWidget
        },
        props: {
            receipt: {
                type: Object,
                default: null
            },
        },
        data(){
            return {
                isLoadingQuotation: false,
                quotation: null
            }
        },
        methods: {
            fetchQuotation() {
                const self = this;

                //  Start loader
                self.isLoadingQuotation = true;

                console.log('Start getting quotation details...');

                if( this.$route.params.id ){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/quotations/'+this.$route.params.id)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingQuotation = false;

                            self.quotation = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingQuotation = false;

                            console.log('dashboard/quotation/main.vue - Error getting quotation details...');
                            console.log(response);    
                        });

                }
            },
        },
        created(){
            this.fetchQuotation();
        }
    };
</script>