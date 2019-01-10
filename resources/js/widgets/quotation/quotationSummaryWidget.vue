<template>
    <div>
        <Card class="quotation-summary-widget" :style="{ width: '100%' }">

            <div slot="title">
                <h5>Quotation Summary</h5>
            </div>

            <div slot="extra" v-if="showMenuBtn">

                <Button type="primary" size="small">
                    <Icon type="ios-refresh" :size="20" style="margin-top: -4px;"/>
                    <span>Convert To Invoice</span>
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
                        <DropdownItem>Send With Email</DropdownItem>
                        <DropdownItem>
                            <p>Share Link</p>
                            <Input value="https://optimumqbw.com/quotation/GUYSD54983IIOWIW728UUIH2344IUH2I332D" style="width: 100%" :readonly="true" />
                        </DropdownItem>
                        <DropdownItem>Export As PDF</DropdownItem>
                        <DropdownItem>Print Quotation</DropdownItem>
                    </DropdownMenu>
                </Dropdown>

                <Dropdown trigger="click">
                    <a href="javascript:void(0)">
                        <Icon type="md-more" :size="16"></Icon>
                    </a>
                    <DropdownMenu slot="list">
                        <DropdownItem>Edit</DropdownItem>
                        <DropdownItem>Trash</DropdownItem>
                        <DropdownItem>Edit Business Information</DropdownItem>
                        <DropdownItem>Export As PDF</DropdownItem>
                        <DropdownItem>Print Quotation</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
            </div>

            <Row>

                <Col span="24" class="pr-4">

                    <!-- Edit Mode Switch -->
                    <span class="float-right mb-2">
                        <Poptip word-wrap width="200" trigger="hover" content="Edit this quotation">
                            <span>
                                <Icon type="md-globe mr-1" :size="18" />
                                <strong>Edit Mode: </strong>
                                <i-switch v-model="editMode" class="ml-1" size="large">
                                    <span slot="open">Yes</span>
                                    <span slot="close">No</span>
                                </i-switch>
                            </span>
                        </Poptip>
                    </span>

                    <div class="clearfix"></div>

                </Col>

                <Col span="12">
                    <img src="https://wave-prod-accounting.s3.amazonaws.com/uploads/invoices/business_logos/7cac2c58-4cc1-471b-a7ff-7055296fffbc.png"
                        style="width: 200px; margin-top: -15px;">
                </Col>

                <Col span="12" class="pr-4">

                    <h1 v-if="!editMode" class="text-dark text-right" style="font-size: 35px;">{{ quotation.heading }}</h1>
                    <el-input v-if="editMode" placeholder="Quotation heading" v-model="quotation.heading" size="large" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <p v-if="!editMode" class="mt-3 text-dark text-right"><strong>{{ quotation.company.name }}</strong></p>
                    <el-input v-if="editMode" placeholder="Company name" v-model="quotation.company.name" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <p v-if="!editMode" v-for="(field, i) in quotation.company.additionalFields" :key="i" class="text-right">
                        {{ field.value }}
                    </p>
                    <el-input v-if="editMode" v-for="(field, i) in quotation.company.additionalFields" :key="i" 
                              size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"
                              :placeholder="'Company details ' + i" v-model="quotation.company.additionalFields[i].value"></el-input>
                    </el-input>
                </Col>
            </Row>

            <Divider dashed class="mt-3 mb-3" />

            <Row>
                <Col span="12" class="pl-2">
                    <h3 v-if="!editMode" class="text-dark mb-3">{{ quotation.quote_to }}:</h3>
                    <el-input v-if="editMode" placeholder="Quotation heading" v-model="quotation.quote_to" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>

                    <p v-if="!editMode" v-for="(field, i) in quotation.clientFields" :key="i">
                        <span :class="JSON.parse(field.highlight) && !JSON.parse(field.showFieldName) ? 'text-dark font-weight-bold': ''">
                            <span v-if="JSON.parse(field.showFieldName)" :class="JSON.parse(field.highlight) && JSON.parse(field.showFieldName) ? 'text-dark font-weight-bold': ''">{{ field.name }}:</span> 
                            {{ field.value }}
                        </span>
                    </p>
                    <el-input v-if="editMode" v-for="(field, i) in quotation.clientFields" :key="i" 
                        size="mini" class="mb-1" :style="{ maxWidth:'250px' }"
                        :placeholder="'Client details ' + i" v-model="quotation.clientFields[i].value"></el-input>
                    </el-input>

                </Col>
                <Col span="12">
                    <Row :gutter="20">
                        <Col span="16">
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quotation.ref_number.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Reference number" v-model="quotation.ref_number.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                            
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quotation.created_date.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Created Date" v-model="quotation.created_date.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                            
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quotation.payment_date.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Expiry Date" v-model="quotation.payment_date.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quotation.grand_total.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="quotation.grand_total.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col span="8">
                            <p v-if="!editMode" class="text-dark">{{ quotation.ref_number.value }}</p>
                            <el-input v-if="editMode" placeholder="e.g) 001" v-model="quotation.ref_number.value" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark">{{ quotation.created_date.value }}</p>
                            <el-input v-if="editMode" placeholder="e.g) January 1, 2018" v-model="quotation.created_date.value" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark">{{ quotation.payment_date.value }}</p>
                            <el-input v-if="editMode" placeholder="e.g) January 7, 2018" v-model="quotation.payment_date.value" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark">{{ quotation.grand_total.value }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+quotation.currency_symbol+'2,000.00'" v-model="quotation.grand_total.value" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                        </Col>
                    </Row>
                </Col>
            </Row>

            <Row>
                <Col span="24">
                    <table class="mt-3 w-100">
                        <thead style="background-color: #000000;">
                            <tr>
                                <th :colspan="quotation.tableColumns[0].span" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ quotation.tableColumns[0].name }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Service/Product" v-model="quotation.tableColumns[0].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th :colspan="quotation.tableColumns[1].span" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ quotation.tableColumns[1].name }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Quantity" v-model="quotation.tableColumns[1].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th :colspan="quotation.tableColumns[2].span" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ quotation.tableColumns[2].name }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Price" v-model="quotation.tableColumns[2].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th :colspan="quotation.tableColumns[3].span" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ quotation.tableColumns[3].name }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Amount" v-model="quotation.tableColumns[3].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, i) in quotation.items" :key="i">
                                <td colspan="4" class="p-2">
                                    <p class="text-dark mr-5">
                                        <strong v-if="!editMode">{{ item.name }}</strong>
                                        <el-input v-if="editMode" :placeholder="'e.g) Item '+ (i+1)" v-model="quotation.items[i].name" size="mini" class="p-1" :style="{ maxWidth:'250px' }"></el-input>
                                    </p>
                                    <p class="mr-5">
                                        <span v-if="!editMode">{{ item.description }}</span>
                                        <el-input v-if="editMode" placeholder="e.g) Item" v-model="quotation.items[i].description" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                    </p>
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.quantity }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2" v-model="quotation.items[i].quantity" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.unit_price }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2,500.00" v-model="quotation.items[i].unit_price" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.total_price }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 5,000.00" v-model="quotation.items[i].total_price" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
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
                            <p class="text-dark text-right"><strong>{{ quotation.grand_total.name }}:</strong></p>
                        </Col>
                        <Col span="4">
                            <p class="text-right">P3,500.00</p>
                            <p class="text-dark text-right">{{ quotation.grand_total.value }}</p>
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
    </div>

</template>

<script>

  export default {
    props: {
        quotation: {
            type: Object,
            default: null
        },
        showMenuBtn: {
            type: Boolean,
            default: true
        }
    },
    data(){
        return {
            editMode: false
        }
    }
  };
  
</script>