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
            
            <Loader v-if="isLoadingTemplate" :loading="isLoadingTemplate" type="text" :style="{ marginTop:'40px' }">Loading template...</Loader>

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

            <Card v-if="!localQuotations.length && !isLoadingTemplate" :style="{ width:'320px', margin:'0 auto' }">
                <div style="text-align:center">
                    <img class="mb-2" src="/images/assets/graphics/quotation-paper.jpg" :style="{ width:'100%' }">
                    <Button type="success" long @click="createQuotation()">CREATE QUOTATION</Button>
                </div>
            </Card>
    
        </Col>

    </Row>

</template>

<script>

    import Loader from './../../components/_common/loader/Loader.vue';
    import quotationSummaryWidget from './quotationSummaryWidget.vue';

    export default {
        components: { 
            Loader, quotationSummaryWidget
        },
        props: {
            quotations: {
                type: Array,
                default: () => []
            },
            clientId:{
                type: Number,
                default: null
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
                user: auth.user,
                newQuotation: false,
                localQuotations: this.quotations,
                isLoadingTemplate: false,
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
                this.newQuotation = true;
                this.fetchTemplate();
            },
            fetchTemplate() {
                const self = this;

                //  Start loader
                self.isLoadingTemplate = true;

                console.log('Start getting company settings...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/companies/'+self.user.company_id+'/settings')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingTemplate = false;

                        //  Get currencies
                        var template = (((data || {}).details || {}).quotationTemplate || {});

                        if(template){
                            self.localQuotations.push(template);
                            self.populateTemplate();
                        }
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingTemplate = false;

                        console.log('quotationCardListWidget.vue - Error getting company settings...');
                        console.log(response);    
                    });
            },
            populateTemplate(){
                var date = new Date();
                var dd = date.getDate();
                var mm = date.getMonth() + 1;
                var yy = date.getFullYear();

                this.localQuotations[0].createdDate.value = yy+'-'+mm+'-'+dd;
                this.localQuotations[0].expiryDate.value = yy+'-'+mm+'-'+( dd + 7 );
                
                this.fetchCompanyInfo();

                this.fetchClientInfo();
                

            },
            updateQuotations(newQuotation){
                this.newQuotation = false;
                this.localQuotations = [newQuotation];
            }
        }
    };
  
</script>