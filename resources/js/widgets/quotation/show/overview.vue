<template>

    <Row class="border-bottom mb-3 pb-3">

        <!-- Quotation Reference Number  -->
        <Col span="5">
            <h2 class="text-dark">Quotation #{{ localQuotation.reference_no_value }}</h2>
        </Col>

        <!-- Quotation Status  -->
        <Col span="3">
            <h6 class="text-secondary">Status</h6>
            <h5><statusTag :quotation="quotation"></statusTag></h5>   
        </Col>

        <!-- Quotation Client  -->
        <Col span="6">
            <h6 class="text-secondary">Customer</h6>
            <h5><a href="#">{{ customerName }}</a></h5>            
        </Col>

        <!-- Quotation Amount  -->
        <Col span="5">
            <h6 class="text-secondary">Amount</h6>
            <h5>{{ localQuotation.grand_total_value | currency(currencySymbol)  }}</h5>            
        </Col>

        <!-- Quotation Due Date  -->
        <Col span="4">
            <h6 class="text-secondary">Due</h6>
            <h5>{{ localQuotation.expiry_date_value | moment("from", "now")  }}</h5>            
        </Col>

        <!-- Quotation Menu -->
        <Col span="1">
            <menuToggle :quotationId="localQuotation.id" :editMode="localEditMode" @toggleEditMode="$emit('toggleEditMode', $event)"></menuToggle>
        </Col>
    </Row>

</template>
<script type="text/javascript">

    import statusTag from './../../../components/_common/statuses/QuotationStatusTag.vue';  
    import menuToggle from './../../../components/_common/dropdowns/quotationMenuDropdown.vue';

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            quotation: {
                type: Object,
                default: null
            }
        },
        components: { statusTag, menuToggle },
        data() {
            return {
                localQuotation: this.quotation,
                currencySymbol: ((this.quotation.currency_type || {}).currency || {}).symbol,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the quotation
            quotation: {
                handler: function (val, oldVal) {

                    //  Update the local quotation value
                    this.localQuotation = val;

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
        computed:{
            customerName: function(){
                if( (this.localQuotation.customized_client_details || {}).model_type == 'user'){
                    return (this.localQuotation.customized_client_details || {}).full_name;
                }else if( (this.localQuotation.customized_client_details || {}).model_type == 'company'){
                    return (this.localQuotation.customized_client_details || {}).name;
                }
            }
        },
        methods: {
            
        },
        created () {
            
        }
    }
</script>
