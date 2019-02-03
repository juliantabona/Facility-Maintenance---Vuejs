<template>

    <Row class="border-bottom mb-3 pb-3">

        <!-- Invoice Reference Number  -->
        <Col span="5">
            <h2 class="text-dark">Invoice #{{ localInvoice.reference_no_value }}</h2>
        </Col>

        <!-- Invoice Status  -->
        <Col span="3">
            <h6 class="text-secondary">Status</h6>
            <h5><statusTag :invoice="invoice"></statusTag></h5>   
        </Col>

        <!-- Invoice Client  -->
        <Col span="6">
            <h6 class="text-secondary">Customer</h6>
            <h5><a href="#">{{ localInvoice.customized_client_details.name }}</a></h5>            
        </Col>

        <!-- Invoice Amount  -->
        <Col span="5">
            <h6 class="text-secondary">Amount</h6>
            <h5>{{ localInvoice.grand_total_value | currency(currencySymbol)  }}</h5>            
        </Col>

        <!-- Invoice Due Date  -->
        <Col span="4">
            <h6 class="text-secondary">Due</h6>
            <h5>{{ localInvoice.expiry_date_value | moment("from", "now")  }}</h5>            
        </Col>

        <!-- Invoice Menu -->
        <Col span="1">
            <menuToggle :invoiceId="localInvoice.id" :editMode="localEditMode" @toggleEditMode="$emit('toggleEditMode', $event)"></menuToggle>
        </Col>
    </Row>

</template>
<script type="text/javascript">

    import statusTag from './statusTag.vue';  
    import menuToggle from './../menus/menuToggle.vue';

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            invoice: {
                type: Object,
                default: null
            }
        },
        components: { statusTag, menuToggle },
        data() {
            return {
                localInvoice: this.invoice,
                currencySymbol: ((this.invoice.currency_type || {}).currency || {}).symbol,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {

                    //  Update the local invoice value
                    this.localInvoice = val;

                    //  Update the currency value
                    this.currencySymbol = ((val.currency_type || {}).currency || {}).symbol;

                },
                deep: true
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    //  Update the edit mode value
                    this.localEditMode = val;
                }
            }
        },
        methods: {
            
        },
        created () {
            
        }
    }
</script>
