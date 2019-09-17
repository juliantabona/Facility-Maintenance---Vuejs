<template>

    <Row :gutter="20">
        <Col span="16">
            <!-- Invoice Reference No Title -->
            <p v-if="!localEditMode" class="text-dark text-right"><strong>{{ localInvoice.reference_no_title ? localInvoice.reference_no_title+':' : '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="e.g) Reference number" v-model="localInvoice.reference_no_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            <!-- Invoice Created Date Title -->  
            <p v-if="!localEditMode" class="text-dark text-right"><strong>{{ localInvoice.created_date_title ? localInvoice.created_date_title+':' : '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="e.g) Created Date" v-model="localInvoice.created_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            <!-- Invoice Expiry Date Title -->
            <p v-if="!localEditMode" class="text-dark text-right"><strong>{{ localInvoice.expiry_date_title ? localInvoice.expiry_date_title+':' : '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="e.g) Expiry Date" v-model="localInvoice.expiry_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            <!-- Invoice Grand Total Title -->
            <p v-if="!localEditMode" class="text-dark text-right"><strong>{{ localInvoice.grand_total_title ? localInvoice.grand_total_title+':' : '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="e.g) Grand Total" v-model="localInvoice.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

        </Col>
        <Col span="8">
            <!-- Invoice Reference No Value -->
                
                <!-- When Create Mode Is Off    -->
                <p v-if="!localEditMode && !localCreateMode" class="text-dark">{{ localInvoice.reference_no_value || '___' }}</p>
                <el-input v-if="localEditMode && !localCreateMode" placeholder="e.g) 001" v-model="localInvoice.reference_no_value" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

                <!-- When Create Mode Is On -->
                <p v-if="!localEditMode && localCreateMode" class="text-primary">Auto Generated</p>
                <el-input v-if="localEditMode && localCreateMode" :disabled="true" placeholder="Auto Generated" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

            <!-- Invoice Created Date Value -->  
            <p v-if="!localEditMode" class="text-dark">{{ localInvoice.created_date | moment('MMM DD YYYY') || '___' }}</p>
            <el-date-picker v-if="localEditMode" v-model="localInvoice.created_date" type="date" :clearable="false" placeholder="e.g) January 1, 2018" size="mini" class="mb-2" :style="{ maxWidth:'135px', float:'right' }"
                format="MMM dd yyyy" value-format="yyyy-MM-dd">
            </el-date-picker>

            <!-- Invoice Expiry Date Value -->
            <p v-if="!localEditMode" class="text-dark">{{ localInvoice.expiry_date | moment('MMM DD YYYY') || '___' }}</p>
            <el-date-picker v-if="localEditMode" v-model="localInvoice.expiry_date" type="date" :clearable="false" placeholder="e.g) January 7, 2018" size="mini" class="mb-2" :style="{ maxWidth:'135px', float:'right' }"
                format="MMM dd yyyy" value-format="yyyy-MM-dd">
            </el-date-picker>

            <!-- Invoice Grand Total Value -->
            <p v-if="!localEditMode" class="text-dark">{{ localInvoice.grand_total | currency(currencySymbol) || '___' }}</p>
            <el-input v-if="localEditMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localInvoice.grand_total | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }" disabled></el-input>
        </Col>
    </Row>

</template>


<script type="text/javascript">


    export default {
        props: {
            invoice: {
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
                localInvoice: this.invoice,
                localEditMode: this.editMode,
                localCreateMode: this.createMode,
                currencySymbol: ((this.invoice.currency_type || {}).currency || {}).symbol,
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {

                    //  Update the local invoice value
                    this.localInvoice = val;

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
