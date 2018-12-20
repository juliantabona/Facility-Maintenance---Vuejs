<template>

    <Row :gutter="20">

        <Col span="24">
          <Tabs type="card">
                <TabPane :label="detailLabel">

                    <Row :gutter="20" class="m-2">

                        <Col span="16">
                            Client communication here...
                            

                        </Col>

                        <Col span="8">
                            <companySummaryWidget 
                                
                                :company="null" :companyId="companyId" :companyBranchId="null" type="client"

                                :showMenuBtn="true"
                                :showViewBtn="true" :showEditBtn="true" :showTrashBtn="true" :showAddContactBtn="true"
                                :showDownloadProfileBtn="true" :showDownloadLogoBtn="true"
                                :showContactsTagBtn="true">
                            </companySummaryWidget>
                        </Col>

                    </Row>

                </TabPane>
                <TabPane :label="jobcardsLabel">
                    <Row :gutter="20" class="m-2">
                    
                        <Col span="24">

                            <Alert show-icon closable>

                                <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                Manage Client Jobcards
                                <template slot="desc">
                                    Create, update, print and send jobcards linked to this client. Add reminders to jobcards to receive email and 
                                    sms reminders before deadlines are met. Watch <a href="#">Short Video</a>.
                                </template>

                            </Alert>

                        </Col>
                    
                        <Col span="24">

                            <jobcardListWidget modelType="client" :modelId="companyId"></jobcardListWidget> 

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

    import jobcardListWidget from './../../../../widgets/jobcard/jobcardListWidget.vue';
    import companySummaryWidget from './../../../../widgets/company/company-summary-widget.vue';


    export default {
        components: { jobcardListWidget, companySummaryWidget },
        data(){
            return {
                jobcards: [],
                companyId: parseInt(this.$route.params.id),
                detailLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px' },
                            props: { type: 'ios-information-circle-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Details')
                    ])
                },
                jobcardsLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px'  },
                            props: { type: 'ios-copy-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Jobcards')
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
                contractsLabel: (h) => {
                    return h('span', [
                        h('Icon', { style: { display: 'inline-block', 'marginTop': '-10px', 'marginRight': '12px'  },
                            props: { type: 'ios-paper-outline', size: 20 }
                        }),
                        h('span', { style: { display: 'inline-block' } }, 'Contracts')
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
            }
        }
    };
</script>