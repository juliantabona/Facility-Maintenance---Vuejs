<template>

    <Row :gutter="20">
        
        <Col span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="true" :fallbackRoute="{ name: 'show-invoice', params: { id: invoiceId } }">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px' }" type="ios-send-outline" :size="34" class="mr-1"></Icon>
                    <h1 :style="{ fontSize:'2rem' }" class="text-dark d-inline">Sent Invoices</h1>
                </template>

            </pageToolbar>
            
        </Col>

        <Col span="24">

            <!-- Get the filterable invoice activity list -->
            <invoiceSentActivityListWidget :invoiceId="invoiceId"></invoiceSentActivityListWidget>
            
        </Col>


    </Row>
    
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import invoiceSentActivityListWidget from './../../../../widgets/invoice/list/invoiceSentActivityListWidget.vue';

    export default {
        components: { pageToolbar, invoiceSentActivityListWidget },
        data(){
            return {
                invoiceId: this.$route.params.id
            }
        },
        watch: {
            //  Watch for changes on the invoice id
            '$route.params.id': function (id) {
                
                // react to route changes by updating invoice id...
                this.invoiceId = id;

            }
        }
    }
</script>
