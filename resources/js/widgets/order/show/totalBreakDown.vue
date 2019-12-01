<style>

    .doubleUnderline{
        padding: 8px 0px;
        border-bottom: 3px solid #dee1e2;
        border-top: 1px solid #dee1e2;
    }

</style>

<template>

    <div>

        <!-- Sub Total -->
        <Row :gutter="20">

            <!-- Sub Total Title -->
            <Col :span="18">
                <p class="text-dark text-right float-right w-100 mb-2">
                    <strong>{{ subTotalTitle ? subTotalTitle + ':'  : '___' }}</strong>
                </p>
            </Col>

            <!-- Sub Total Amount -->
            <Col :span="6">
                <p class="text-right float-right w-100 mb-2">{{ localOrder.sub_total | currency(currencySymbol) || '___' }}</p>
            </Col>
            
        </Row>

        <!-- Tax Total -->
        <Row :gutter="20">

            <!-- Tax Total Title -->
            <Col :span="18">
                <p class="text-dark text-right float-right w-100 mb-2">
                    <strong>Tax Total:</strong>
                </p>
            </Col>

            <!-- Tax Total Amount -->
            <Col :span="6">
                <p class="text-right float-right w-100 mb-2">{{ localOrder.grand_tax_total | currency(currencySymbol) || '___' }}</p>
            </Col>
            
        </Row>

        <!-- Discount Total -->
        <Row :gutter="20">

            <!-- Discount Total Title -->
            <Col :span="18">
                <p class="text-dark text-right float-right w-100 mb-2">
                    <strong>Discount Total:</strong>
                </p>
            </Col>

            <!-- Discount Total Amount -->
            <Col :span="6">
                <p class="text-right float-right w-100 mb-2">{{ localOrder.grand_discount_total | currency(currencySymbol) || '___' }}</p>
            </Col>
            
        </Row>

        <!-- Grand Total -->
        <Row :gutter="20" class="doubleUnderline mt-3">

            <!-- Grand Total Title -->
            <Col :span="18">
            
                <p class="text-dark text-right float-right w-100">
                    <strong>{{ grandTotalTitle ? grandTotalTitle + ':'  : '___' }}</strong>
                </p>

            </Col>

            <!-- Grand Total Amount -->
            <Col :span="6">

                <p class="text-dark text-right float-right w-100">
                    <strong>{{ localOrder.grand_total | currency(currencySymbol) }}</strong>
                </p>
                
            </Col>
            
        </Row>

    </div>

</template>


<script type="text/javascript">


    export default {
        props: {
            order: {
                type: Object,
                default: null
            },
            settings: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            },
        },
        data(){
            return {
                localOrder: this.order,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the order
            order: {
                handler: function (val, oldVal) {

                    //  Update the local order value
                    this.localOrder = val;

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
        computed: {
            subTotalTitle(){
                return this.settings.sub_total_title;
            },
            grandTotalTitle(){
                return this.settings.grand_total_title;
            },
            currencySymbol(){
                return  (this.order.currency || {}).symbol || (this.order.currency || {}).code;
            }
        },
    }

</script>
