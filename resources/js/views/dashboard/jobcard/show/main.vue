<template>

    <Row :gutter="20">

        <Col span="24">
          <Tabs type="card">
              <TabPane :label="detailLabel">
                <Row :gutter="20" class="m-2">

                    <Col span="16">

                        <statusLifecycleWidget class="mb-1">
                          
                        </statusLifecycleWidget>

                        <jobcardSummaryWidget 

                            :jobcard="jobcard"
                            
                            :showMenuBtn="true" :showMenuEditBtn="true" :showMenuTrashBtn="true" :showMenuAddClientBtn="true"
                            :showMenuAddContractorBtn="true" :showMenuAddLabourBtn="true" :showMenuAddAssetBtn="true"
                            :showDescriptionSection="true" :showStatusSection="true" :showPublishSection="true" 
                            :showResourceSection="false" :showActionToolbalSection="true">
                        </jobcardSummaryWidget>

                    </Col>

                    <Col span="8">
                        <companySummaryWidget 
                        
                            :company="company" type="client"

                            :showMenuBtn="true"
                            :showViewBtn="true" :showEditBtn="true" :showRemoveBtn="true" :showAddContactBtn="true"
                            :showDownloadProfileBtn="true" :showDownloadLogoBtn="true"
                            :showContactsTagBtn="true">
                        </companySummaryWidget>
                    </Col>
                </Row>

              </TabPane>
              <TabPane :label="AccountLabel">
                <Row :gutter="20" class="m-2">
                  
                    <Col span="24">

                      <Alert show-icon>

                          <Icon type="ios-bulb-outline" slot="icon"></Icon>
                          Manage Payments
                          <template slot="desc">
                            Create, update and send quotations, invoices and receipts linked to this jobcard. Every payment has its own lifecycle from 
                            quotation to invoice to receipt. You can have more than one Payment Lifecycle e.g) You can have Payment 1 as a lifecycle 
                            for the intial deposit payment, and Payment 2 as a lifecycle for final payment. Watch <a href="#">Short Video</a>.
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

                              <receiptSummaryWidget 

                                  :receipt="receipt"
                                  
                                  :showMenuBtn="true" :showMenuEditBtn="true" :showMenuTrashBtn="true" :showMenuAddClientBtn="true"
                                  :showMenuAddContractorBtn="true" :showMenuAddLabourBtn="true" :showMenuAddAssetBtn="true"
                                  :showDescriptionSection="true" :showStatusSection="true" :showPublishSection="true" 
                                  :showResourceSection="true" :showActionToolbalSection="true">

                              </receiptSummaryWidget>
                            </TabPane>
                          </Tabs>

                      </Card>  

                    </Col>
                </Row>
              </TabPane>
              <TabPane :label="subContractorLabel">
                <contractorListWidget></contractorListWidget>
              </TabPane>
              <TabPane :label="contractsLabel">Contracts</TabPane>
              <TabPane :label="labourLabel">Labour</TabPane>
              <TabPane :label="assetsLabel">Assets</TabPane>
          </Tabs>
        </Col>

    </Row>

</template>
<script>

    import companySummaryWidget from './../../../../widgets/company/company-summary-widget.vue';
    import statusLifecycleWidget from './../../../../widgets/jobcard/status-lifecycle-widget.vue';
    import jobcardSummaryWidget from './../../../../widgets/jobcard/jobcard-summary-widget.vue';
    import quotationSummaryWidget from './../../../../widgets/quotation/quotation-preview-widget.vue';
    import invoiceSummaryWidget from './../../../../widgets/invoice/invoice-preview-widget.vue';
    import receiptSummaryWidget from './../../../../widgets/receipt/receipt-preview-widget.vue';
    import contractorListWidget from './../../../../widgets/contractor/contractor-list-widget.vue';


    export default {
        components: { 
          companySummaryWidget , statusLifecycleWidget, jobcardSummaryWidget, quotationSummaryWidget, invoiceSummaryWidget, 
          receiptSummaryWidget, contractorListWidget 
        },
        props: {
            jobcard: {
                type: Object,
                default: () => {}
            },
            receipt: {
                type: Object,
                default: () => {}
            },
        },
        data(){
            return {
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
                subContractorLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px'  },
                            props: { type: 'ios-briefcase-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Sub-Contractors')
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
                            props: { type: 'ios-people-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Labour')
                    ])
                },
                assetsLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px'  },
                            props: { type: 'logo-tux', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Asset')
                    ])
                },
                company: {
                    logo_url: 'http://acmelogos.com/images/logo-8.svg',
                    name: 'Optimum Quality (Pty) Ltd',
                    city: 'Gaborone',
                    address: 'Plot 2356, Fairgrounds',
                    phone_ext: '267',
                    phone_num: '3909083',
                    email: 'info@optimumqbw.com'
                }
            }
        }
    };
</script>