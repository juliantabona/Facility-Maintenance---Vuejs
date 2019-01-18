<template>

    <Row :gutter="20">

        <Col span="24">
          <Tabs type="card">
              <TabPane :label="detailLabel">
                <Row :gutter="20" class="m-2">

                    <Col span="16">

                        <!-- Get the jobcard summary details -->

                        <jobcardSummaryWidget 

                            v-model="jobcard" :jobcardId="parseInt($route.params.id)"
                            
                            :showMenuBtn="true" :showAuthourizedStatus="true" :showProcessStatus="true"
                            :showTitle="true" :showDescription="true" :showDeadline="true" :showStartDate="true" :showEndDate="true"
                            :showPriority="true" :showCategory="true" :showCostCenters="true" :showCreatedBy="true" :showCreatedByDate="true"
                            :showAuthourizedBy="true" :showAuthourizedByDate="true" :showResourceTags="false" 
                            :showViewBtn="true" :showDownloadBtn="true" :showSendBtn="true" :showPublicBtn="true">
                        </jobcardSummaryWidget>

                    </Col>

                    <Col span="8">

                        <!-- Get the company summary details -->

                        <companySummaryWidget 
                            
                            v-model="company" :companyId="null" :companyBranchId="jobcard.client_id" relationType="client"

                            :showMenuBtn="true" :showAuthourizedStatus="true" 
                            :showLogo="true" :showName="true" :showAddress="true" :showCityOrTown="true"
                            :showPhone="true" :showEmail="true" :showContacts="true"
                            :showViewBtn="true">
                        </companySummaryWidget>
                    </Col>
                    
                </Row>

              </TabPane>
              <TabPane :label="AccountLabel">
                <Row :gutter="20" class="m-2">
                
                    <Col span="24">

                      <Card class="box-card form-section mb-2">

                            <quotationCardListWidget 
                                :quotations="jobcard.quotations"
                                :clientId="jobcard.client_id"
                                modelType="jobcard" :modelId="jobcard.id"

                                :showMenuBtn="true" :showMenuEditBtn="true" :showMenuTrashBtn="true" :showMenuAddClientBtn="true"
                                :showMenuAddSupplierBtn="true" :showMenuAddLabourBtn="true" :showMenuAddAssetBtn="true"
                                :showDescriptionSection="true" :showStatusSection="true" :showPublishSection="true" 
                                :showResourceSection="true" :showActionToolbalSection="true">

                            </quotationCardListWidget>

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

    import companySummaryWidget from './../../../../widgets/company/companySummaryWidget.vue';
    import jobcardSummaryWidget from './../../../../widgets/jobcard/jobcardSummaryWidget.vue';
    import quotationCardListWidget from './../../../../widgets/quotation/quotationCardListWidget.vue';
    import invoiceSummaryWidget from './../../../../widgets/invoice/invoice-preview-widget.vue';
    import receiptSummaryWidget from './../../../../widgets/receipt/receipt-preview-widget.vue';
    import supplierListWidget from './../../../../widgets/supplier/supplier-list-widget.vue';


    export default {
        components: { 
          companySummaryWidget, jobcardSummaryWidget, quotationCardListWidget, invoiceSummaryWidget, 
          receiptSummaryWidget, supplierListWidget 
        },
        props: {
            receipt: {
                type: Object,
                default: null
            },
        },
        data(){
            return {
                jobcard: {},
                company: {},
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
                }
            }
        }
    };
</script>