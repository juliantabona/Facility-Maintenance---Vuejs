<template>

    <Row :gutter="20" class="m-2">

        <Col span="24">

            <Alert v-if="!localQuotations.length" show-icon closable>

                <Icon type="ios-bulb-outline" slot="icon"></Icon>
                Manage Payments
                <template slot="desc">
                Create, update and send quotations, invoices and receipts linked to this jobcard. Every payment has its own lifecycle from 
                quotation to invoice to receipt. You can have more than one Payment Lifecycle e.g) You can have Payment 1 as a lifecycle 
                for the initial deposit payment, and Payment 2 as a lifecycle for final payment. Watch <a href="#">Short Video</a>.
                </template>
            </Alert>
            
            <Tabs v-if="localQuotations.length" type="card">
                <Button size="small" slot="extra">+ Add Quotation {{ localQuotations.length + 1 }}</Button>
                <TabPane :label="'Quotation ' + localQuotations.length">

                    <quotationSummaryWidget 
                        v-if="localQuotations"
                        v-for="(quotation, i) in localQuotations" 
                        :id="quotation.id"
                        :quotation="quotation"
                        :newQuotation="newQuotation"
                        v-bind="$props"
                        :key="i"
                        @quotationCreated="updateQuotations($event)">
                    </quotationSummaryWidget>

                </TabPane>
            </Tabs>

            <Card v-if="!localQuotations.length" :style="{ width:'320px', margin:'0 auto' }">
                <div style="text-align:center">
                    <img class="mb-2" src="/images/assets/graphics/quotation-paper.jpg" :style="{ width:'100%' }">
                    <Button type="success" long @click="createQuotation()">CREATE QUOTATION</Button>
                </div>
            </Card>
    
        </Col>

    </Row>

</template>

<script>

    import quotationSummaryWidget from './quotationSummaryWidget.vue';

    export default {
        components: { 
            quotationSummaryWidget
        },
        props: {
            quotations: {
                type: Array,
                default: () => []
            },
            modelType:{
                type: String,
                default: ''
            },
            modelId:{
                type: Number,
                default: null
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            }
        },
        data(){
            return {
                newQuotation: false,
                localQuotations: this.quotations,
                renderKey: 1,
            }
        },
        watch: {
            //  When the quotations changes
            quotations: function (val) {
                //  Update the local quotations
                this.localQuotations = val;
            }
        },
        methods: {
            createQuotation: function(){
                var date = new Date();
                var dd = date.getDate();
                var mm = date.getMonth();
                var yy = date.getFullYear();

                this.newQuotation = true;
                this.localQuotations = [{
                        heading: 'QUOTATION',
                        footer: 'Terms & Conditions Apply',
                        primaryColor: '#017BB8',
                        secondaryColor: '#EEF4FF',
                        refNumber: {
                            name: 'Estimate Number',
                            value: '86'
                        },
                        createdDate: {
                            name: 'Estimate Date',
                            value: yy+'-'+mm+'-'+dd
                        },
                        expiryDate: {
                            name: 'Expires On',
                            value: yy+'-'+mm+'-'+( dd + 7 ) 
                        },
                        subTotal: {
                            name: 'Total',
                            value: 5250
                        },
                        grandTotal: {
                            name: 'Grand Total',
                            value: 5880
                        },
                        calculatedTaxes: {
                            '0': {
                            id: 1,
                            name: 'Value Added Tax',
                            abbreviation: 'VAT',
                            rate: 0.12,
                            amount: 630
                            }
                        },
                        currencyType: {
                            country: 'Aruba',
                            currency: {
                            iso: {
                                code: 'AWG',
                                number: '533'
                            },
                            name: 'florin',
                            symbol: 'ƒ'
                            }
                        },
                        quoteTo: 'QUOTE TO',
                        companyDetails: {
                            name: 'Optimum Quality',
                            email: 'info@optimumqbw.co.bw',
                            phone: '+267 3993456',
                            additionalFields: {
                            '0': {
                                value: 'P O Box 563 AAH Masa Center'
                            },
                            '1': {
                                value: 'Gaborone, South-East'
                            },
                            '2': {
                                value: 'Botswana'
                            }
                            }
                        },
                        clientDetails: {
                            name: 'Leap Interiors',
                            email: 'sales@leapinteriors.co.bw',
                            phone: '+267 3908122',
                            additionalFields: {
                            '0': {
                                value: 'Plot 4567, Finance Park'
                            },
                            '1': {
                                value: 'Gaborone, South-East'
                            },
                            '2': {
                                value: 'Botswana'
                            }
                            }
                        },
                        tableColumns: {
                            '0': {
                            name: 'Services',
                            span: '4'
                            },
                            '1': {
                            name: 'Quantity',
                            span: '1'
                            },
                            '2': {
                            name: 'Unit Price',
                            span: '1'
                            },
                            '3': {
                            name: 'Amount',
                            span: '1'
                            }
                        },
                        items: {
                            '0': {
                            name: 'Basic Website',
                            description: 'Design and development of a basic website which includes a home page, about page, services page, contact page.',
                            quantity: 1,
                            unitPrice: 3500,
                            totalPrice: 3500,
                            taxes: {
                                '0': {
                                id: 1,
                                name: 'Value Added Tax',
                                abbreviation: 'VAT',
                                rate: 0.12
                                }
                            }
                            },
                            '1': {
                            name: 'Web Hosting',
                            description: 'Hosting of website files, images, audio and videos',
                            quantity: 1,
                            unitPrice: 1250,
                            totalPrice: 1250,
                            taxes: {
                                '0': {
                                id: 1,
                                name: 'Value Added Tax',
                                abbreviation: 'VAT',
                                rate: 0.12
                                }
                            }
                            },
                            '2': {
                            name: 'Email Hosting',
                            description: 'Provision, maintenance, security and hosting of emails',
                            quantity: 1,
                            unitPrice: 500,
                            totalPrice: 500,
                            taxes: {
                                '0': {
                                id: 1,
                                name: 'Value Added Tax',
                                abbreviation: 'VAT',
                                rate: 0.12
                                }
                            }
                            }
                        },
                        notes: {
                            title: 'Payment Information',
                            details: [
                            {
                                value: 'Bank Name: First National Bank'
                            },
                            {
                                value: 'Account Name: Optimum Quality (PTY) LTD'
                            },
                            {
                                value: 'Account Number: 62688415994'
                            },
                            {
                                value: 'Branch: Gaborone Mall'
                            },
                            {
                                value: 'Branch Code: 02828'
                            }
                            ]
                        }
                    }];
            },
            updateQuotations(newQuotation){
                this.newQuotation = false;
                this.localQuotations = [newQuotation];
            }
        }
    };
  
</script>