<style>

    .doubleUnderline{
        padding: 8px 0px;
        border-bottom: 3px solid #dee1e2;
        border-top: 1px solid #dee1e2;
    }

</style>

<template>

    <div>

        <Row :gutter="20">
            <Col :span="localEditMode ? '16':'20'">
                <p v-if="!localEditMode" class="text-dark text-right float-right w-100 mb-2"><strong>{{ localQuotation.sub_total_title | currency(currencySymbol) }} {{ localQuotation.sub_total_title ? localQuotation.sub_total_title + ':'  : '___' }}</strong></p>
                <el-input v-if="localEditMode" placeholder="e.g) Total" v-model="localQuotation.sub_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                <div v-if="(localQuotation.calculated_taxes || {}).length">
                    <p v-if="!localEditMode" v-for="(calculatedTax , i) in localQuotation.calculated_taxes" :key="i" class="text-dark text-right float-right w-100">
                        {{ calculatedTax.name }} ({{ calculatedTax.rate*100 }}%):
                    </p>
                    <el-input v-if="localEditMode" v-for="(calculatedTax , i) in localQuotation.calculated_taxes" :key="i" placeholder="e.g) VAT (12%)" :value="calculatedTax.name + ' (' + calculatedTax.rate*100 + '%)'" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                </div>
            </Col>
            <Col :span="localEditMode ? '8':'4'">

                <p v-if="!localEditMode" class="text-right float-right w-100 mb-2">{{ localQuotation.sub_total_value | currency(currencySymbol) || '___' }}</p>
                <el-input v-if="localEditMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localQuotation.sub_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                <div v-if="(localQuotation.calculated_taxes || {}).length">
                    <p v-if="!localEditMode" v-for="(calculatedTax , i) in localQuotation.calculated_taxes" :key="i" class="text-right float-right w-100">
                        {{ calculatedTax.amount | currency(currencySymbol) || '___' }}
                    </p>
                    <el-input v-if="localEditMode" v-for="(calculatedTax , i) in localQuotation.calculated_taxes" :key="i" placeholder="e.g) 1,500.00" :value="calculatedTax.amount | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                </div>
            </Col>
            
        </Row>

        <Row :gutter="20" class="doubleUnderline mt-3">

            <Col :span="localEditMode ? '16':'20'">
            
                <p v-if="!localEditMode" class="text-dark text-right float-right w-100"><strong>{{ localQuotation.grand_total_title | currency(currencySymbol) }} {{ localQuotation.grand_total_title ? localQuotation.grand_total_title + ':'  : '___' }}</strong></p>
                <el-input v-if="localEditMode" placeholder="e.g) Grand Total" v-model="localQuotation.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            </Col>
            <Col :span="localEditMode ? '8':'4'">

                <p v-if="!localEditMode" class="text-dark text-right float-right w-100"><strong>{{ localQuotation.grand_total_value | currency(currencySymbol) }}</strong></p>
                <el-input v-if="localEditMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localQuotation.grand_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
            
            </Col>
            
        </Row>

    </div>

</template>


<script type="text/javascript">


    export default {
        props: {
            quotation: {
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
                localQuotation: this.quotation,
                localEditMode: this.editMode,
                currencySymbol: ((this.quotation.currency_type || {}).currency || {}).symbol,
            }
        },
        watch: {

            //  Watch for changes on the quotation
            quotation: {
                handler: function (val, oldVal) {

                    //  Update the local quotation value
                    this.localQuotation = val;

                    //  Update the currency symbol shortcut
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
    }

</script>
