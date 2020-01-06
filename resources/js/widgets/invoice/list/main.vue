<template>

    <div>

        <!-- Invoice Heading / Add Invoice Button -->
        <Row class="border-bottom pb-3">

            <Col :span="4">

                <!-- Invoice Heading -->
                <h2 class="font-weight-bold pl-4">Invoices</h2>

            </Col>

            <Col :span="20">
                <div class="clearfix">
                    
                    <!-- Add Invoice Button -->
                    <basicButton @click.native="$router.push({ name:'create-invoice' })" 
                                size="large" class="float-right">
                                <span>+ Create Invoice</span>
                    </basicButton>

                </div>
            </Col>

        </Row>

        <!-- Invoice Table List With Search Input, Filters & Sort Functionality -->
        <Row>

            <Col :span="24">

                <!-- Invoice Table Widget -->
                <invoiceTableWidget :invoicesUrl="invoicesUrl"></invoiceTableWidget>

            </Col>

        </Row>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue'; 
    
    /*  Active Invoice Widget  */
    import showActiveInvoiceWidget from './../show/main.vue';

    /*  Local Widgets  */
    import invoiceTableWidget from './table.vue'

    export default {
        props: {
            invoicesUrl: {
                type: String,
                default: null
            }
        }, 
        components: { 
            basicButton, showActiveInvoiceWidget, invoiceTableWidget
        },
        data(){
            return {
                //  Current store invoices
                localInvoices: []
            }
        }
    };
  
</script>