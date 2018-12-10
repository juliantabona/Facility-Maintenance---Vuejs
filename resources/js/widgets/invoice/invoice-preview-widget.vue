<style scoped>

</style>

<template>

    <Card class="quotation-summary-widget" :style="{ width: '100%' }">

        <div slot="title">
            <h5>Invoice Summary</h5>
        </div>

        <div slot="extra" v-if="showMenuBtn">

            <Button type="primary" size="small">
                <Icon type="ios-refresh" :size="20" style="margin-top: -4px;"/>
                <span>Convert To Receipt</span>
            </Button>

            <Button type="primary" size="small">
                <Icon type="ios-eye-outline" :size="20" style="margin-top: -4px;"/>
                <span>Preview</span>
            </Button>

            <Dropdown trigger="click" class="mr-4">
                <Button type="primary" size="small">
                    <span>Send</span>
                    <Icon type="ios-send-outline" :size="20" style="margin-top: -4px;"/>
                </Button>
                <DropdownMenu slot="list">
                    <DropdownItem v-if="showMenuEditBtn">Send With Email</DropdownItem>
                    <DropdownItem>
                        <p>Share Link</p>
                        <Input value="https://optimumqbw.com/invoices/GUYSD54983IIOWIW728UUIH2344IUH2I332D" style="width: 100%" :readonly="true" />
                    </DropdownItem>
                    <DropdownItem v-if="showMenuAddClientBtn">Export As PDF</DropdownItem>
                    <DropdownItem v-if="showMenuAddAssetBtn">Print Invoice</DropdownItem>
                </DropdownMenu>
            </Dropdown>

            <Dropdown trigger="click">
                <a href="javascript:void(0)">
                    <Icon type="md-more" :size="16"></Icon>
                </a>
                <DropdownMenu slot="list">
                    <DropdownItem v-if="showMenuEditBtn">Edit</DropdownItem>
                    <DropdownItem v-if="showMenuTrashBtn">Trash</DropdownItem>
                    <DropdownItem v-if="showMenuTrashBtn">Edit Business Information</DropdownItem>
                    <DropdownItem v-if="showMenuAddClientBtn">Export As PDF</DropdownItem>
                    <DropdownItem v-if="showMenuAddAssetBtn">Print Invoice</DropdownItem>
                </DropdownMenu>
            </Dropdown>
        </div>

        <Row>
            <Col span="12">
                <img src="https://wave-prod-accounting.s3.amazonaws.com/uploads/invoices/business_logos/7cac2c58-4cc1-471b-a7ff-7055296fffbc.png"
                     style="width: 200px; margin-top: -15px;">
            </Col>
            <Col span="12" class="pr-2">
                <h1 class="text-dark text-right" style="font-size: 35px;">INVOICE</h1>
                <p class="mt-3 text-dark text-right"><strong>Optimum Quality</strong></p>
                <p class="text-right">P O Box 563 AAH Masa Center</p>
                <p class="text-right">Gaborone, South-East</p>
                <p class="text-right">Botswana</p>
                <br />
                <p class="text-right">www.optimumqbw.com</p>
                <p class="text-right">info@optimumqbw.com</p>
                <p class="text-right">+267 77479083</p>
            </Col>
        </Row>

        <Divider dashed class="mt-3 mb-3" />

        <Row>
            <Col span="12" class="pl-4">
                <h3 class="text-dark">INVOICE TO:</h3>
                <p class="mt-3 text-dark"><strong>Leap Interiors</strong></p>
                <p>Email: sales@leapinteriors.co.bw</p>
                <p>Phone: +267 77479467</p>
            </Col>
            <Col span="12">
                <Row :gutter="20">
                    <Col span="16">
                        <p class="text-dark text-right"><strong>Invoice Number:</strong></p>
                        <p class="text-dark text-right"><strong>Invoice Date:</strong></p>
                        <p class="text-dark text-right"><strong>Payment Due:</strong></p>
                        <p class="text-dark text-right"><strong>Amount Due (BWP):</strong></p>
                    </Col>
                    <Col span="8">
                        <p class="text-dark">86</p>
                        <p class="text-dark">November 2, 2018</p>
                        <p class="text-dark">November 2, 2018</p>
                        <p class="text-dark">P3,500.00</p>
                    </Col>
                </Row>
            </Col>
        </Row>

        <Row>
            <Col span="24">
                <table class="mt-3 w-100">
                    <thead style="background-color: #000000;">
                        <tr>
                            <th colspan="4" class="p-2" style="color: #FFFFFF;">Services</th>
                            <th colspan="1" class="p-2" style="color: #FFFFFF;">Quantity</th>
                            <th colspan="1" class="p-2" style="color: #FFFFFF;">Price</th>
                            <th class="p-2" style="color: #FFFFFF;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="p-2">
                                <p class="text-dark mr-5"><strong>Basic Website</strong></p>
                                <p class="mr-5">
                                    This quotation includes the design and development of a basic website which includes a home page, about page, services page, contact page.
                                </p>
                            </td>
                            <td colspan="1" class="p-2">
                                <span>1</span>
                            </td>
                            <td colspan="1" class="p-2">
                                <span>P3,500.00</span>
                            </td>
                            <td colspan="1" class="p-2">
                                <span>P3,500.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" class="p-2">
                                <el-tooltip class="ml-auto mr-auto mb-3 d-block item" effect="dark" content="Add Service/Product" placement="top-start">
                                    <el-button type="primary" icon="el-icon-plus" circle></el-button>
                                    <span>Add an item</span>
                                </el-tooltip>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </Col>
        </Row>

        <Divider dashed class="mt-2 mb-4" />

        <Row>
            <Col span="12" offset="12" class="pr-4">
                <Row :gutter="20">
                    <Col span="20">
                        <p class="text-dark text-right"><strong>Total:</strong></p>
                        <p class="text-dark text-right"><strong>Grand Total (BWP):</strong></p>
                    </Col>
                    <Col span="4">
                        <p class="text-right">P3,500.00</p>
                        <p class="text-dark text-right">P3,500.00</p>
                    </Col>
                </Row>
            </Col>
        </Row>

        <Divider dashed class="mt-2 mb-4" />

        <Row>
            <Col span="24">
                <h3 class="text-dark">Notes</h3>
                <p>Payment Information</p>
                <p class="mt-3 text-dark"><strong>Bank Name:</strong> First National Bank</p>
                <p class="text-dark"><strong>Account Name:</strong> Optimum Quality (PTY) LTD</p>
                <p class="text-dark"><strong>Account Number:</strong> 62688415994</p>
                <p class="text-dark"><strong>Branch:</strong> Gaborone Mall</p>
                <p class="text-dark"><strong>Branch Code:</strong> 02828</p>
            </Col>
        </Row>

        <Divider dashed class="mt-5 mb-2" />

        <Row>
            <Col span="24">
                <p class="text-center">Other Services Hosting (For 1GB)-  P2, 200.00/year Maintenance and Support-  180.00/hour</p>
            </Col>
        </Row>

    </Card>

</template>

<script>

  export default {
    props: {
        quotation: {
            type: Object,
            default: () => {}
        },
        showMenuBtn: {
            type: Boolean,
            default: true
        },
        showMenuEditBtn: {
            type: Boolean,
            default: true
        },
        showMenuTrashBtn: {
            type: Boolean,
            default: true
        },
        showMenuAddClientBtn: {
            type: Boolean,
            default: true
        },
        showMenuAddContractorBtn: {
            type: Boolean,
            default: true
        },
        showMenuAddLabourBtn: {
            type: Boolean,
            default: true
        },
        showMenuAddAssetBtn: {
            type: Boolean,
            default: true
        },
        showDescriptionSection: {
            type: Boolean,
            default: true
        },
        showStatusSection: {
            type: Boolean,
            default: true
        },
        showPublishSection: {
            type: Boolean,
            default: true
        },
        showResourceSection: {
            type: Boolean,
            default: true
        },
        showActionToolbalSection: {
            type: Boolean,
            default: true
        }

    },
    data() {
      return {
           isLoading: false,
      }
    },
    methods: {
        viewJobcard: function(){
            this.$router.push({ name: 'show-jobcard', params: { id: this.jobcard.id } });
        }
    } 
  };
  
</script>