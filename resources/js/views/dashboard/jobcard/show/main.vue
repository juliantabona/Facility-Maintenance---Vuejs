<template>

    <Row :gutter="20">

        <Col span="24">
          <Tabs type="card">
              <TabPane :label="detailLabel">
                <Row :gutter="20" class="m-2">

                    <Col span="16">

                        <jobcardSummaryWidget 

                            :jobcard="jobcard"
                            
                            :showMenuBtn="true" :showMenuEditBtn="true" :showMenuTrashBtn="true" :showMenuAddClientBtn="true"
                            :showMenuAddSupplierBtn="true" :showMenuAddLabourBtn="true" :showMenuAddAssetBtn="true"
                            :showDescriptionSection="true" :showStatusSection="true" :showPublishSection="true" 
                            :showResourceSection="false" :showActionToolbalSection="true">
                        </jobcardSummaryWidget>

                    </Col>

                    <Col span="8">
                        <companySummaryWidget 
                            
                            :company="null" :companyId="null" :companyBranchId="jobcard.client_id" type="client"

                            :showMenuBtn="true"
                            :showViewBtn="true" :showEditBtn="true" :showTrashBtn="true" :showAddContactBtn="true"
                            :showDownloadProfileBtn="true" :showDownloadLogoBtn="true"
                            :showContactsTagBtn="true">
                        </companySummaryWidget>
                    </Col>
                </Row>

              </TabPane>
              <TabPane :label="AccountLabel">
                <Row :gutter="20" class="m-2">
                  
                    <Col span="24">

                      <Alert show-icon closable>

                          <Icon type="ios-bulb-outline" slot="icon"></Icon>
                          Manage Payments
                          <template slot="desc">
                            Create, update and send quotations, invoices and receipts linked to this jobcard. Every payment has its own lifecycle from 
                            quotation to invoice to receipt. You can have more than one Payment Lifecycle e.g) You can have Payment 1 as a lifecycle 
                            for the initial deposit payment, and Payment 2 as a lifecycle for final payment. Watch <a href="#">Short Video</a>.
                          </template>
                      </Alert>
                    </Col>
                
                    <Col span="24">

                      <Card class="box-card form-section mb-2">
                          <div slot="extra">
                              <div class="section-toolbox float-right d-block">
                                  <Poptip
                                      confirm
                                      title="Are you sure you want to delete this payment?"
                                      ok-text="Yes"
                                      cancel-text="No"
                                      @on-ok="$emit('removePaymentStream')">
                                      <Icon type="ios-trash-outline" class="section-icon hidable mr-2" size="20"/>
                                  </Poptip>
                              </div>
                          </div>

                          <Steps :current="1" class="mt-2 mb-4">
                              <Step title="Quotation" content="Create and update jobcard quotation and generate invoices"></Step>
                              <Step title="Invoice" content="Update jobcard invoice and generate receipts"></Step>
                              <Step title="Receipt" content="Download, print or send receipts to your client"></Step>
                          </Steps>

                          <Tabs type="card">
                            <Button size="small" slot="extra">+ Add Invoice 2</Button>
                            <TabPane label="Invoice 1">

                              <div class="mb-2">
                                  Tab Title:
                                  <Input value="Invoice 1" placeholder="Enter tab title..." style="width: auto" />
                              </div>

                              <invoiceSummaryWidget 
                                  
                                  :showMenuBtn="true" :showMenuEditBtn="true" :showMenuTrashBtn="true" :showMenuAddClientBtn="true"
                                  :showMenuAddSupplierBtn="true" :showMenuAddLabourBtn="true" :showMenuAddAssetBtn="true"
                                  :showDescriptionSection="true" :showStatusSection="true" :showPublishSection="true" 
                                  :showResourceSection="true" :showActionToolbalSection="true">

                              </invoiceSummaryWidget>
                            </TabPane>
                          </Tabs>

                      </Card>  

                    </Col>
                </Row>
              </TabPane>
              <TabPane :label="subSupplierLabel">
                <Row :gutter="20" class="m-2">
                    <Col span="24">

                        <Alert show-icon closable>

                            <Icon type="ios-bulb-outline" slot="icon"></Icon>
                            Manage Suppliers
                            <template slot="desc">
                            Invite suppliers to submit their quotations linked to this jobcard. All quotations will be stored safely for future reference.
                            You can also approve suppliers that are fit for the job and start tracking communication from start to finish. Everything is kept 
                            safe and convinent for future reconciliation. Watch <a href="#">Short Video</a>.
                            </template>
                        </Alert>
                    </Col>

                    <supplierListWidget></supplierListWidget>
                
                </Row>
                
              </TabPane>
              <TabPane :label="labourLabel">
                <Row :gutter="20" class="m-2">
                    <Col span="24">

                        <Alert show-icon closable>

                            <Icon type="ios-bulb-outline" slot="icon"></Icon>
                            Manage Labour
                            <template slot="desc">
                            Add members from your team to handle the work linked to this jobcard. Adding members will automatically send notifications via the platform
                            and email to inform assigned members on their new job/task. This is also used for measuring performance and delivery. You can always add additional 
                            notes/details where necessary, its really flexible! Watch <a href="#">Short Video</a>.
                            </template>
                        </Alert>
                    </Col>
                
                </Row>
              </TabPane>
              <TabPane :label="contractsLabel">
                <Row :gutter="20" class="m-2">
                    <Col span="24">

                        <Alert show-icon closable>

                            <Icon type="ios-bulb-outline" slot="icon"></Icon>
                            Manage Contracts
                            <template slot="desc">
                            Make your professional work even more official with legitimate legal documentation. Create and manage contracts with your staff, clients, suppliers, 
                            shareholders and partners. Save signed copies safely for future reconciliation. Watch <a href="#">Short Video</a>.
                            </template>
                        </Alert>

                    </Col>
                </Row>
              </TabPane>
              <TabPane :label="assetsLabel">
                <Row :gutter="20" class="m-2">
                    <Col span="24">
                      <Alert show-icon closable>

                          <Icon type="ios-bulb-outline" slot="icon"></Icon>
                          Manage Assets
                          <template slot="desc">
                            Assign assets to this linked jobcard to track where your assets are being used. This helps generate useful reports to 
                            understand your asset allocations. This can better knowledge on which assets are crucial to your business
                            as well as their frequency of use. Watch <a href="#">Short Video</a>.
                          </template>
                      </Alert>
                    </Col>
                </Row>

              </TabPane>
          </Tabs>
        </Col>

    </Row>

</template>
<script>

    import companySummaryWidget from './../../../../widgets/company/company-summary-widget.vue';
    import jobcardSummaryWidget from './../../../../widgets/jobcard/jobcardSummaryWidget.vue';
    import quotationSummaryWidget from './../../../../widgets/quotation/quotation-preview-widget.vue';
    import invoiceSummaryWidget from './../../../../widgets/invoice/invoice-preview-widget.vue';
    import receiptSummaryWidget from './../../../../widgets/receipt/receipt-preview-widget.vue';
    import supplierListWidget from './../../../../widgets/supplier/supplier-list-widget.vue';


    export default {
        components: { 
          companySummaryWidget, jobcardSummaryWidget, quotationSummaryWidget, invoiceSummaryWidget, 
          receiptSummaryWidget, supplierListWidget 
        },
        props: {
            receipt: {
                type: Object,
                default: () => {}
            },
        },
        data(){
            return {
                jobcard: {},
                detailLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px' },
                            props: { type: 'ios-information-circle-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Details')
                    ])
                },
                AccountLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px' },
                            props: { type: 'ios-cash-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Accounts')
                    ])
                },
                subSupplierLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px'  },
                            props: { type: 'ios-briefcase-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Suppliers')
                    ])
                },
                contractsLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px'  },
                            props: { type: 'ios-paper-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Contracts')
                    ])
                },
                labourLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px'  },
                            props: { type: 'ios-people-outline', size: 22 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Labour')
                    ])
                },
                assetsLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px'  },
                            props: { type: 'ios-cube-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Asset')
                    ])
                },
                client: {}
            }
        },
        created () {
            this.fetch();
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting jobcard...');

                var connections = 'connections=categories,priorities,costcenters';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/jobcards/'+this.$route.params.id+'?'+connections)
                    .then(({data}) => {

                        //  Stop loader
                        self.isLoading = false;

                        //  Get jobcard lifecycle data
                        self.jobcard = data;
                    })         
                    .catch(response => { 
                        console.log('jobcard/show/main.vue - Error getting jobcard...');
                        console.log(response);

                        //  Stop loader
                        self.isLoading = false;     
                    });


            }
        }
    };
</script>