<template>

    <Row :gutter="20">
        <Col span="16">
            <!-- Quotation Reference No Title -->
            <p v-if="!localEditMode" class="text-dark text-right"><strong>{{ localQuotation.reference_no_title ? localQuotation.reference_no_title+':' : '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="e.g) Reference number" v-model="localQuotation.reference_no_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            <!-- Quotation Created Date Title -->  
            <p v-if="!localEditMode" class="text-dark text-right"><strong>{{ localQuotation.created_date_title ? localQuotation.created_date_title+':' : '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="e.g) Created Date" v-model="localQuotation.created_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            <!-- Quotation Expiry Date Title -->
            <p v-if="!localEditMode" class="text-dark text-right"><strong>{{ localQuotation.expiry_date_title ? localQuotation.expiry_date_title+':' : '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="e.g) Expiry Date" v-model="localQuotation.expiry_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            <!-- Quotation Grand Total Title -->
            <p v-if="!localEditMode" class="text-dark text-right"><strong>{{ localQuotation.grand_total_title ? localQuotation.grand_total_title+':' : '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="e.g) Grand Total" v-model="localQuotation.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

        </Col>
        <Col span="8">
            <!-- Quotation Reference No Value -->
                
                <!-- When Create Mode Is Off    -->
                <p v-if="!localEditMode && !localCreateMode" class="text-dark">{{ localQuotation.reference_no_value || '___' }}</p>
                <el-input v-if="localEditMode && !localCreateMode" placeholder="e.g) 001" v-model="localQuotation.reference_no_value" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

                <!-- When Create Mode Is On -->
                <p v-if="!localEditMode && localCreateMode" class="text-primary">Auto Generated</p>
                <el-input v-if="localEditMode && localCreateMode" :disabled="true" placeholder="Auto Generated" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

            <!-- Quotation Created Date Value -->  
            <p v-if="!localEditMode" class="text-dark">{{ localQuotation.created_date_value | moment('MMM DD YYYY') || '___' }}</p>
            <el-date-picker v-if="localEditMode" v-model="localQuotation.created_date_value" type="date" :clearable="false" placeholder="e.g) January 1, 2018" size="mini" class="mb-2" :style="{ maxWidth:'135px', float:'right' }"
                format="MMM dd yyyy" value-format="yyyy-MM-dd">
            </el-date-picker>

            <!-- Quotation Expiry Date Value -->
            <p v-if="!localEditMode" class="text-dark">{{ localQuotation.expiry_date_value | moment('MMM DD YYYY') || '___' }}</p>
            <el-date-picker v-if="localEditMode" v-model="localQuotation.expiry_date_value" type="date" :clearable="false" placeholder="e.g) January 7, 2018" size="mini" class="mb-2" :style="{ maxWidth:'135px', float:'right' }"
                format="MMM dd yyyy" value-format="yyyy-MM-dd">
            </el-date-picker>

            <!-- Quotation Grand Total Value -->
            <p v-if="!localEditMode" class="text-dark">{{ localQuotation.grand_total_value | currency(currencySymbol) || '___' }}</p>
            <el-input v-if="localEditMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localQuotation.grand_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }" disabled></el-input>
        </Col>
    </Row>

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
            createMode: {
                type: Boolean,
                default: false
            },
        },
        data(){
            return {
                localQuotation: this.quotation,
                localEditMode: this.editMode,
                localCreateMode: this.createMode,
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
            },

            //  Watch for changes on the create mode value
            createMode: {
                handler: function (val, oldVal) {

                    //  Update the create mode value
                    this.localCreateMode = val;

                }
            }
        },
    }

</script>
