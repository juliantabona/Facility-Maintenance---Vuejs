<style scoped>

    .drop-to-left-edge{
        position:relative !important;
    }

    .drop-to-left-edge >>> .ivu-select-dropdown{
        left:-159px !important;
    }

    .doubleUnderline{
        padding: 8px 0px;
        border-bottom: 3px solid #dee1e2;
        border-top: 1px solid #dee1e2;
    }

    .animate-opacity {
        animation: opacityAnimation 2s linear infinite;
    }

    @keyframes opacityAnimation {
        50% {
            opacity: 0;
        }
    }

    .circle-ripple {
        width: 1px;
        height: 1px;
        border-radius: 50%;
        position:absolute;
        top: 50%;
        left: 50%;
        transform: translateX(-50%);
        transform: translateY(-50%);
        animation: ripple 3s linear infinite;
        z-index:1;
    }

    @-webkit-keyframes ripple {
        0% {
            box-shadow: 0 0 0 0 rgba(101, 255, 120, 0.3),
                        0 0 0 0 rgba(101, 255, 120, 0.3);
        }
        50% {
            box-shadow: 0 0 0 7em rgba(101, 255, 120, 0),
                        0 0 0 3.5em rgba(101, 255, 120, 0.3);
        }
        100% {
            box-shadow: 0 0 0 7em rgba(101, 255, 120, 0),
                        0 0 0 5em rgba(101, 255, 120, 0);
        }
    }

    @keyframes ripple {
        0% {
            box-shadow: 0 0 0 0 rgba(101, 255, 120, 0.3),
                        0 0 0 0 rgba(101, 255, 120, 0.3);
        }
        50% {
            box-shadow: 0 0 0 7em rgba(101, 255, 120, 0),
                        0 0 0 3.5em rgba(101, 255, 120, 0.3);
        }
        100% {
            box-shadow: 0 0 0 7em rgba(101, 255, 120, 0),
                        0 0 0 5em rgba(101, 255, 120, 0);
        }
    }

    .invoice-widget{
        position: relative;
    }

    footer {
        position: absolute; 
        bottom: 0; 
        left: 0; 
        right: 0;
        height: 40px;
        border-radius: 0 0 8px 8px;

        /** Extra personal styles **/
        font-size:12px;
        background-color: #000;
        color: white;
        text-align: center;
        line-height: 30px;
    }

    .invoice-steps {
        display: block;
        padding: 0px 10px;
        margin-bottom: 12px;
        border-radius: 8px;
        border: 1px solid #b2c2cd;
        background-color: #fff;
    }

    .invoice-steps:nth-child(1) {
        position: relative;
        z-index: 3;
    }

    .invoice-steps:nth-child(2) {
        position: relative;
        z-index: 2;
    }

    .invoice-steps:nth-child(3) {
        position: relative;
        z-index: 1;
    }

    .invoice-steps.is-highlighted {
        box-shadow: 0 8px 32px rgba(77,101,117,0.35);
        border-radius: 12px;
        border-color: transparent;
    }

    .invoice-steps.invoice-hide-step{
        margin-top: -115px;
    }

    .invoice-steps.is-highlighted:hover ~ .invoice-hide-step{
        margin-top: 0;
    }

    .invoice-steps.disabled {
        opacity:0.4;
    }

    .invoice-vertical-line{
        margin: -13px 0 0 39px;
        border: 2px solid #b2c2cd;
        height: 18px;
        width: 0;
    }

    .invoice-header {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fcfbe3;
        margin: -27px -27px 15px !important;
        border-radius: 10px 10px 0 0;
        padding: 15px;
    }

    .invoice-step-badge {
        color: #cdd1d3;
        float: left;
        font-size: 25px;
        line-height: 50px;
        margin-right: 18px;
        text-align: center;
        border-width: 2px;
    }

    .invoice-step-badge-inner {
        background: #fff;
        border: 2px solid #136acd;
        border-radius: 50%;
        color: #136acd;
        width: 50px;
        height: 50px;
        line-height: 46px;
        box-sizing: border-box;
    }

</style>

<template>
    <div id="invoice-widget">
        
        <div>

            <Card :bordered="false" class="invoice-steps is-highlighted">

                <Row :gutter="20" class="invoice-header">
                    <Col span="24">
                       <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                        <span>This is a DRAFT invoice. You can take further actions once you approve it. <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
                    </Col>
                </Row>
                <Row :gutter="20">
                    <Col span="12">
                        <div class="invoice-step-badge">
                            <div class="invoice-step-badge-inner">1</div>
                        </div>
                        <h4 class="text-secondary">Approve invoice</h4>
                        <p class="mt-2 mb-2"><span class="font-weight-bold">Created:</span> 7 hours ago from <a href="#"><span class="font-weight-bold">Estimate #87</span></a></p>
                    </Col>
                    <Col span="12">

                        <Button class="float-right ml-2" type="default" size="large" @click="downloadPDF({ preview: true })">
                            <span>Edit Draft</span>
                        </Button>

                        <Button class="float-right" type="primary" size="large" @click="downloadPDF({ preview: true })">
                            <span>Approve Draft</span>
                        </Button>

                    </Col>
                </Row>
        
            </Card>

            <div class="invoice-vertical-line"></div>

            <Card :bordered="false" class="invoice-steps invoice-hide-step disabled">
                <Row :gutter="20">
                    <Col span="12">
                        <div class="invoice-step-badge">
                            <div class="invoice-step-badge-inner">2</div>
                        </div>
                        <h4 class="text-secondary">Send invoice</h4>
                        <p class="mt-2 mb-2"><span class="font-weight-bold">Last Sent:</span> 2 hours ago from</p>
                    </Col>
                    <Col span="12">

                        <Button class="float-right ml-2" type="default" size="large" @click="downloadPDF({ preview: true })">
                            <span>Skip</span>
                        </Button>

                        <Button class="float-right" type="primary" size="large" @click="downloadPDF({ preview: true })">
                            <span>Send Invoice</span>
                        </Button>

                    </Col>
                </Row>
            </Card>

            <div class="invoice-vertical-line"></div>

            <Card :bordered="false" class="invoice-steps invoice-hide-step disabled">
                <Row :gutter="20">
                    <Col span="12">
                        <div class="invoice-step-badge">
                            <div class="invoice-step-badge-inner">3</div>
                        </div>
                        <h4 class="text-secondary">Get Paid</h4>
                        <p class="mt-2 mb-2"><span class="font-weight-bold">Amount Due:</span> P2,890.00</a></p>
                    </Col>
                    <Col span="12">

                        <Icon class="float-right" :size="50" type="md-checkmark-circle-outline" color="#19be6b"/>

                    </Col>
                </Row>
            </Card>


        </div>

        <div v-if="isCreatingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
        <div v-if="isSavingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>

        <Card :style="{ width: '100%' }">
            
            <Spin size="large" fix v-if="isSavingInvoice || isCreatingInvoice"></Spin>

            <div slot="title">
                <h5>Invoice Summary</h5>
            </div>

            <div slot="extra" v-if="showMenuBtn">

                <Button type="primary" size="small" @click="downloadPDF({ preview: true })">
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
                            <Input value="https://optimumqbw.com/invoice/GUYSD54983IIOWIW728UUIH2344IUH2I332D" style="width: 100%" :readonly="true" />
                        </DropdownItem>
                    </DropdownMenu>
                </Dropdown>

                <Dropdown class="drop-to-left-edge" trigger="click">
                    <a href="javascript:void(0)">
                        <Icon type="md-more" :size="16"></Icon>
                    </a>
                    <DropdownMenu slot="list">
                        <DropdownItem v-if="!editMode" @click.native="editMode = true">Edit</DropdownItem>
                        <DropdownItem v-if="editMode" @click.native="editMode = false">View</DropdownItem>
                        <DropdownItem>Trash</DropdownItem>
                        <DropdownItem>Edit Business Information</DropdownItem>
                        <DropdownItem @click.native="downloadPDF()">Export As PDF</DropdownItem>
                        <DropdownItem>Print Invoice</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
            </div>

            <Row>

                <Col span="24" class="pr-4">

                    <!-- Create Button -->
                    <Button v-if="newInvoice" 
                            class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                            type="success" size="small" @click="createInvoice()">
                        <div class="circle-ripple"></div>
                        <span :style="{ position:'relative', zIndex:'2' }">Create Invoice</span>
                    </Button>

                    <!-- Save Changes Button -->
                    <Button v-if="!newInvoice && invoiceHasChanged" 
                            class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                            type="success" size="small" @click="saveInvoice()">
                        <div class="circle-ripple"></div>
                        <span :style="{ position:'relative', zIndex:'2' }">Save Changes</span>
                    </Button>

                    <!-- Edit Mode Switch -->
                    <span class="float-right mb-2">
                        <Poptip word-wrap width="200" trigger="hover" content="Edit this invoice">
                            <span>
                                <Icon type="ios-create-outline mr-1" :size="24" />
                                <strong>Edit Mode: </strong>
                                <i-switch v-model="editMode" class="ml-1" size="large">
                                    <span slot="open">On</span>
                                    <span slot="close">Off</span>
                                </i-switch>
                            </span>
                        </Poptip>
                    </span>

                    <div class="clearfix"></div>

                </Col>
                
                <Col span="12">
                    <imageUploader 
                        uploadMsg="Upload or change logo"
                        :thumbnailStyle="{ width:'200px', height:'auto' }"
                        :allowUpload="editMode"
                        :multiple="false"
                        :imageList="
                            [{
                                'name': 'Company Logo',
                                'url': 'https://wave-prod-accounting.s3.amazonaws.com/uploads/invoices/business_logos/7cac2c58-4cc1-471b-a7ff-7055296fffbc.png'
                            }]">
                    </imageUploader>
                </Col>
                
                <Col v-if="isLoadingCompanyInfo" span="12" class="pr-4">
                    <Loader v-if="isLoadingCompanyInfo" :loading="isLoadingCompanyInfo" type="text" :style="{ marginTop:'40px' }">Loading Company Details...</Loader>
                </Col>
                <Col v-if="company" span="12" class="pr-4">

                    <h1 v-if="!editMode" class="text-dark text-right" style="font-size: 35px;">{{ localInvoice.heading || '___' }}</h1>
                    <el-input v-if="editMode" placeholder="Invoice heading" v-model="localInvoice.heading" size="large" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                    
                    <div class="clearfix"></div>

                    <p v-if="!editMode" class="mt-3 text-dark text-right"><strong>{{ company.name || '___' }}</strong></p>
                    <el-input v-if="editMode" placeholder="Company name" v-model="company.name" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <div class="clearfix"></div>

                    <p v-if="!editMode" class="text-right">{{ company.email || '___' }}</p>
                    <el-input v-if="editMode" placeholder="Company email" v-model="company.email" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <div class="clearfix"></div>

                    <p v-if="!editMode" class="text-right">{{ company.phone || '___' }}</p>
                    <el-input v-if="editMode" placeholder="Company tel/phone" v-model="company.phone" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                    
                    
                    <div class="clearfix"> <br> </div>

                    <p v-if="!editMode" v-for="(field, i) in company.additionalFields" :key="i" class="text-right">
                        {{ field.value }}
                    </p>
                    <el-input v-if="editMode" v-for="(field, i) in company.additionalFields" :key="i" 
                              size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"
                              :placeholder="'Company details ' + i" v-model="company.additionalFields[i].value"></el-input>
                    </el-input>

                </Col>
            </Row>

            <Divider dashed class="mt-3 mb-3" />

            <Row>
                <Col span="12" class="pl-2">
                    <h3 v-if="!editMode" class="text-dark mb-3">{{ localInvoice.invoice_to_title || '___' }}:</h3>
                    <el-input v-if="editMode" placeholder="Invoice heading" v-model="localInvoice.invoice_to_title" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>
                    
                    <div v-if="isLoadingClientInfo">
                        <Loader v-if="isLoadingClientInfo" :loading="isLoadingClientInfo" type="text" :style="{ marginTop:'40px' }">Loading Client Details...</Loader>
                    </div>

                    <div v-if="client">
                        <p v-if="!editMode" class="mt-3 text-dark"><strong>{{ client.name || '___' }}</strong></p>
                        <el-input v-if="editMode" placeholder="Company name" v-model="client.name" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

                        <div class="clearfix"></div>

                        <p v-if="!editMode">{{ client.email || '___' }}</p>
                        <el-input v-if="editMode" placeholder="Company email" v-model="client.email" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

                        <div class="clearfix"></div>

                        <p v-if="!editMode">{{ client.phone || '___' }}</p>
                        <el-input v-if="editMode" placeholder="Company tel/phone" v-model="client.phone" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>
                        
                        <div class="clearfix"> <br> </div>

                        <p v-if="!editMode" v-for="(field, i) in client.additionalFields" :key="i">
                            {{ field.value }}
                        </p>
                        <el-input v-if="editMode" v-for="(field, i) in client.additionalFields" :key="i" 
                            size="mini" class="mb-1" :style="{ maxWidth:'250px' }"
                            :placeholder="'Client details ' + i" 
                            v-model="client.additionalFields[i].value">
                        </el-input>
                    </div>

                </Col>
                
                <Col span="12">
                    <Row :gutter="20">
                        <Col span="16">
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ localInvoice.reference_no_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Reference number" v-model="localInvoice.reference_no_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                            
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ localInvoice.created_date_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Created Date" v-model="localInvoice.created_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ localInvoice.expiry_date_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Expiry Date" v-model="localInvoice.expiry_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ localInvoice.grand_total_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="localInvoice.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col span="8">
                            <p v-if="!editMode" class="text-dark">{{ localInvoice.reference_no_value || '___' }}</p>
                            <el-input v-if="editMode" placeholder="e.g) 001" v-model="localInvoice.reference_no_value" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark">{{ localInvoice.created_date_value | moment('MMM DD YYYY') || '___' }}</p>
                            <!-- Edit Created date -->
                            <el-date-picker v-if="editMode" v-model="localInvoice.created_date_value" type="datetime" placeholder="e.g) January 1, 2018" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"
                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                            </el-date-picker>

                            <p v-if="!editMode" class="text-dark">{{ localInvoice.expiry_date_value | moment('MMM DD YYYY') || '___' }}</p>
                            <!-- Edit Created date -->
                            <el-date-picker v-if="editMode" v-model="localInvoice.expiry_date_value" type="datetime" placeholder="e.g) January 7, 2018" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"
                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                            </el-date-picker>

                            <p v-if="!editMode" class="text-dark">{{ localInvoice.grand_total_value | currency(currencySymbol) || '___' }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localInvoice.grand_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }" disabled></el-input>
                        </Col>
                    </Row>
                </Col>
            </Row>

            <Row>
                <Col span="24">
                    <div v-if="editMode">

                        <div class="float-right mr-2">
                            <el-color-picker class="float-right" v-model="secondaryColor"></el-color-picker>
                            <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Secondary Color:</span>
                        </div>
                        
                        <div class="float-right mr-2">
                            <el-color-picker class="float-right" v-model="primaryColor"></el-color-picker>
                            <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Primary Color:</span>
                        </div>

                        <div class="float-right mr-3">
                            <Loader v-if="isLoadingCurrencies" :loading="isLoadingCurrencies" type="text" :style="{ marginTop:'40px' }">Loading currencies...</Loader>
                            
                            <currencySelector v-if="!isLoadingCurrencies && fetchedCurrencies.length" class="float-right" :style="{maxWidth: '150px'}"
                                :fetchedCurrencies="fetchedCurrencies" :selectedCurrency="localInvoice.currency_type"
                                @updated="updateCurrencyChanges($event)">
                            </currencySelector>
                            <span v-if="!isLoadingCurrencies && fetchedCurrencies.length" class="float-right d-inline-block font-weight-bold mr-2 mt-2">Currency:</span>
                        </div>

                    </div>
                </Col>
                <Col span="24">
                    <table  class="table table-hover mt-3 mb-0 w-100">
                        <thead :style="'background-color:'+primaryColor+';'">
                            <tr>
                                <th colspan="4" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ localInvoice.table_columns[0].name || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Service/Product" v-model="localInvoice.table_columns[0].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ localInvoice.table_columns[1].name || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Quantity" v-model="localInvoice.table_columns[1].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ localInvoice.table_columns[2].name || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Price" v-model="localInvoice.table_columns[2].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ localInvoice.table_columns[3].name || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Amount" v-model="localInvoice.table_columns[3].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th v-if="editMode" class="p-2" style="color: #FFFFFF;">
                                    <span class="d-block mb-2">Tax</span>
                                </th>
                                <th v-if="editMode" class="p-2" style="color: #FFFFFF;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="localInvoice.items.length" v-for="(item, i) in localInvoice.items" :key="i"  :style=" ( (i + 1) % 2 ) ? 'background-color:'+secondaryColor+';' : ''">
                                <td colspan="4" class="p-2">
                                
                                    <p v-if="!editMode" class="text-dark mr-5">
                                        <strong>{{ item.name || '___' }}</strong>
                                    </p>
                                    <el-input v-if="editMode" :placeholder="'e.g) Item '+ (i+1)" v-model="localInvoice.items[i].name" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                    
                                    <p v-if="!editMode" class="mr-5">
                                        <span v-if="!editMode">{{ item.description }}</span>
                                    </p>
                                    <el-input v-if="editMode" placeholder="e.g) Item" v-model="localInvoice.items[i].description" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.quantity || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2" 
                                              v-model="localInvoice.items[i].quantity" 
                                              @input.native="updateSubAndGrandTotal()"
                                              size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.unitPrice | currency(currencySymbol) || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2,500.00" 
                                              v-model="localInvoice.items[i].unitPrice" 
                                              @input.native="updateSubAndGrandTotal()"
                                              size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.totalPrice | currency(currencySymbol) || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 5,000.00" :value="getItemTotal(localInvoice.items[i])" size="mini" class="p-1" :style="{ maxWidth:'100%' }" disabled></el-input>
                                </td>
                                <td v-if="editMode" class="p-2">
                                    <Loader v-if="isLoadingTaxes" :loading="isLoadingTaxes" type="text" :style="{ marginTop:'40px' }">Loading taxes...</Loader>
                                    <taxSelector v-if="!isLoadingTaxes && fetchedTaxes.length" 
                                        :fetchedTaxes="fetchedTaxes" :selectedTaxes="localInvoice.items[i].taxes"
                                        @updated="updateTaxChanges($event, i)">
                                    </taxSelector>
                                </td>
                                <td v-if="editMode" class="p-2">
                                    <Poptip
                                      confirm
                                      title="Are you sure you want to remove this item?"
                                      ok-text="Yes"
                                      cancel-text="No"
                                      @on-ok="removeItem(i)"
                                      placement="left-start">
                                      <Icon type="ios-trash-outline" class="mr-2" size="20"/>
                                  </Poptip>
                                </td>
                            </tr>
                            <tr v-if="!localInvoice.items.length">
                                <td colspan="9" class="p-2">
                                    <Alert show-icon>
                                        No items added
                                        <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                        <template slot="desc">Start adding products/services to your invoice. You will be able to modify your item name, details, quantity, price and any applicable taxes.</template>
                                    </Alert>

                                    <!-- Edit Mode Switch -->
                                    <span v-if="!editMode" class="d-block m-auto" :style="{ width: '200px' }">
                                        <Poptip word-wrap width="200" trigger="hover" content="Edit this invoice">
                                            <span>
                                                <Icon type="ios-create-outline mr-1" :size="24" />
                                                <strong>Edit Mode: </strong>
                                                <i-switch v-model="editMode" class="ml-1" size="large">
                                                    <span slot="open">On</span>
                                                    <span slot="close">Off</span>
                                                </i-switch>
                                            </span>
                                        </Poptip>
                                    </span>

                                </td>
                            </tr>
                            <tr v-if="editMode">
                                <td colspan="10" class="p-2">
                                    <el-tooltip class="ml-auto mr-auto mb-3 d-block item" effect="dark" content="Add Service/Product" placement="top-start">
                                        <el-button @click="isOpenProductsAndServicesModal = true" type="primary" icon="el-icon-plus" circle></el-button>
                                        <span>Add an item</span>
                                    </el-tooltip>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </Col>
            </Row>
            
            <Divider dashed class="mt-0 mb-4" />

            <Row>
                <Col span="12" offset="12" class="pr-4">
                    <Row :gutter="20">
                        <Col :span="editMode ? '16':'20'">
                            <p v-if="!editMode" class="text-dark text-right float-right w-100 mb-2"><strong>{{ localInvoice.sub_total_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Total" v-model="localInvoice.sub_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" v-for="(calculatedTax , i) in localInvoice.calculated_taxes" :key="i" class="text-dark text-right float-right w-100">
                                {{ calculatedTax.name }} ({{ calculatedTax.rate*100 }}%):
                            </p>
                            <el-input v-if="editMode" v-for="(calculatedTax , i) in localInvoice.calculated_taxes" :key="i" placeholder="e.g) VAT (12%)" :value="calculatedTax.name + ' (' + calculatedTax.rate*100 + '%)'" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                            
                        </Col>
                        <Col :span="editMode ? '8':'4'">

                            <p v-if="!editMode" class="text-right float-right w-100 mb-2">{{ localInvoice.sub_total_value | currency(currencySymbol) || '___' }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localInvoice.sub_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                            <p v-if="!editMode" v-for="(calculatedTax , i) in localInvoice.calculated_taxes" :key="i" class="text-right float-right w-100">
                                {{ calculatedTax.amount | currency(currencySymbol) || '___' }}
                            </p>
                            <el-input v-if="editMode" v-for="(calculatedTax , i) in localInvoice.calculated_taxes" :key="i" placeholder="e.g) 1,500.00" :value="calculatedTax.amount | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                        </Col>
                        
                    </Row>

                    <Row :gutter="20" class="doubleUnderline mt-3">

                        <Col :span="editMode ? '16':'20'">
                        
                            <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ localInvoice.grand_total_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="localInvoice.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col :span="editMode ? '8':'4'">

                            <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ localInvoice.grand_total_value | currency(currencySymbol) || '___' }}</strong></p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localInvoice.grand_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                        
                        </Col>
                        
                    </Row>

                </Col>
            </Row>

            <Row class="mb-5">
                <Col span="24">

                    <h3 v-if="!editMode" class="text-dark mb-2">{{ localInvoice.notes.title }}</h3>
                    <el-input v-if="editMode" placeholder="E.g) Notes/Payment Information" v-model="localInvoice.notes.title" size="large" class="mb-2" :style="{ maxWidth:'400px' }"></el-input>
                    <br>
                    <p v-if="!editMode" v-html="localInvoice.notes.details"></p>
                    <div v-if="editMode">
                        <Summernote
                            name="editor"
                            :model="localInvoice.notes.details"
                            v-on:change="value => { localInvoice.notes.details = value }"
                            :config="summernoteConfig">
                        </Summernote>
                    </div>
                    
                </Col>
            </Row>

            <footer :style="'background-color:'+primaryColor+';'">
                <div class="mt-1">
                    <span v-if="!editMode">{{ localInvoice.footer }}</span>
                    <el-input v-if="editMode" :placeholder="'e.g) Terms And Conditions Apply'" v-model="localInvoice.footer" size="mini" :style="{ width:'50%', margin:'0 auto' }"></el-input>
                </div>     
            </footer>

        </Card>

        <!-- 
            MODAL TO GET PRODUCTS AND SERVICES
        -->
        <getProductsAndServicesModal
            v-show="isOpenProductsAndServicesModal" 
            :show="isOpenProductsAndServicesModal"
            v-on:closed="closeModal"
            v-on:selected="addProductOrService">
        </getProductsAndServicesModal>

    </div>

</template>

<script>
    import Loader from './../../components/_common/loader/Loader.vue';
    import getProductsAndServicesModal from './../quotation/getProductsAndServicesModal.vue';
    import imageUploader from './../quotation/imageUploader.vue';
    import currencySelector from './../quotation/currencySelector.vue';
    import taxSelector from './../quotation/taxSelector.vue';
    import Summernote from './../quotation/Summernote.vue';    

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            Loader, getProductsAndServicesModal, imageUploader,currencySelector, taxSelector, Summernote
        },
        props: {
            invoice: {
                type: Object,
                default: null
            },
            clientId:{
                type: Number,
                default: null
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            newInvoice: {
                type: Boolean,
                default: false
            },
            modelType:{
                type: String,
                default: ''
            },
            modelId:{
                type: Number,
                default: null
            }
        },
        data(){
            return {
                user: auth.user,
                isSavingInvoice: false,
                isCreatingInvoice: false,
                isLoadingClientInfo: false,
                isLoadingCompanyInfo: false,
                isConvertingToInvoice: false,
                editMode: false,
                isLoadingTaxes: false,
                isOpenProductsAndServicesModal: false,
                fetchedTaxes: [],
                fetchedCurrencies: [],
                localInvoice: this.invoice,
                _localInvoiceBeforeChange: {},
                invoiceHasChanged: false,
                //  The company details
                company: this.invoice.customized_company_details,
                //  The client details
                client: this.invoice.customized_client_details,
                currencySymbol: this.invoice.currency_type.currency.symbol,
                primaryColor: this.invoice.colors[0],
                secondaryColor: this.invoice.colors[1],
                
                summernoteConfig: {
                    height: 100,
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['gxcode']], // plugin config: summernote-ext-codewrapper
                    ],
                },
            }
        },
        watch: {
            localInvoice: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    this.invoiceHasChanged = this.checkIfinvoiceHasChanged(val);
                },
                deep: true
            }
        },
        methods: {
            fetchCompanyInfo() {
                const self = this;

                //  Start loader
                self.isLoadingCompanyInfo = true;

                console.log('Start getting company details...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/companies/'+self.user.company_id+'?model=Company')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingCompanyInfo = false;

                        if(data){
                            //  Format the company details
                            self.company = self.formatCompanyDetails(data);
                        }

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingCompanyInfo = false;

                        console.log('invoiceSummaryWidget.vue - Error getting company details...');
                        console.log(response);    
                    });
            },
            fetchClientInfo() {
                const self = this;

                //  Start loader
                self.isLoadingClientInfo = true;

                console.log('Start getting client company details...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/companies/'+this.clientId+'?model=Company')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingClientInfo = false;
                        
                        if(data){
                            //  Format the company details
                            self.client = self.formatCompanyDetails(data);
                        }
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingClientInfo = false;

                        console.log('invoiceSummaryWidget.vue - Error getting client company details...');
                        console.log(response);    
                    });
            },
            formatCompanyDetails(company){
                var companyDetails = {
                        id: company.id,
                        name: company.name,
                        email: company.email,
                        phone: company.phone,
                        additionalFields: []
                    }

                var x, include = ['website', 'address', 'city', 'country'];

                for(x = 0; x < include.length; x++){
                    if(company[ include[x] ]){
                        companyDetails.additionalFields.push({ value: company[ include[x] ] });
                    }
                }

                return companyDetails;
            },
            checkIfinvoiceHasChanged: function(updatedInvoice = null){
                
                var currentInvoice = _.cloneDeep(updatedInvoice || this.localInvoice);
                var isNotEqual = !_.isEqual(currentInvoice, this._localInvoiceBeforeChange);

                console.log('currentInvoice');
                console.log(currentInvoice);
                console.log('localInvoiceBeforeChange');
                console.log(this._localInvoiceBeforeChange);
                console.log(isNotEqual);

                return isNotEqual;
            },
            storeOriginalInvoice(){
                console.log('storing _localInvoiceBeforeChange');
                //  Store the original invoice
                this._localInvoiceBeforeChange = _.cloneDeep(this.localInvoice);
            },
            fetchTaxes() {
                const self = this;

                //  Start loader
                self.isLoadingTaxes = true;

                console.log('Start getting taxes...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/taxes')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingTaxes = false;

                        //  Get taxes
                        self.fetchedTaxes = data.data.length ? data.data.map(tax => [{
                                id: tax.id,
                                name: tax.name,
                                abbreviation: tax.abbreviation,
                                rate: tax.rate
                            }]).flat() : []

                            console.log('New fetched taxes');

                            console.log(self.fetchedTaxes);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingTaxes = false;

                        console.log('invoiceSummaryWidget.vue - Error getting taxes...');
                        console.log(response);    
                    });
            },
            fetchCurrencies() {
                const self = this;

                //  Start loader
                self.isLoadingCurrencies = true;

                console.log('Start getting currencies...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/currencies')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingCurrencies = false;

                        //  Get currencies
                        self.fetchedCurrencies = data;

                        console.log('New fetched currencies');

                        console.log(self.fetchedCurrencies);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingCurrencies = false;

                        console.log('invoiceSummaryWidget.vue - Error getting currencies...');
                        console.log(response);    
                    });
            },
            updateTaxChanges(newTaxes, i){
                this.localInvoice.items[i].taxes = newTaxes;
                this.localInvoice.calculated_taxes = this.runCalculateTaxes();
                this.updateSubAndGrandTotal();
            },
            updateCurrencyChanges(newCurrency){
                this.localInvoice.currency_type = newCurrency;
                this.currencySymbol = this.localInvoice.currency_type.currency.symbol;

                this.$Notice.success({
                    title: 'Currency changed to ' + newCurrency.country + ' (' + newCurrency.currency.iso.code + ')'
                });
            },
            updateSubAndGrandTotal(){
                console.log('run updateSubAndGrandTotal()');

                //  Re-Calculate the sub total amount
                this.localInvoice.sub_total_value = this.runGetTotal();

                //  Re-Calculate the grand total amount
                this.localInvoice.grand_total_value = this.runGetGrandTotal();

                //  Re-Calculate the taxes
                this.localInvoice.calculated_taxes = this.runCalculateTaxes();
            },
            runGetTotal: function(){
                var itemAmounts = (this.localInvoice.items || []).map(item => item.quantity * item.unitPrice);
                var total = itemAmounts.length ? itemAmounts.reduce(this.getSum): 0;

                return total;
            },
            runGetGrandTotal: function(){
                var taxAmounts = (this.runCalculateTaxes() || []).map(appliedTax => appliedTax.amount);
                var sumOfTaxAmounts = taxAmounts.length ? taxAmounts.reduce(this.getSum): 0;

                return  this.runGetTotal() + sumOfTaxAmounts;
            },
            getItemTotal: function(item){
                return item.unitPrice * item.quantity
            },
            getSum(total, num) {
                return total + num;
            },
            runCalculateTaxes: function(){
                
                var itemTaxAmounts = this.localInvoice.items.map(item => {
                        
                        var x, collection = [];

                        for(x = 0; x < item.taxes.length; x++){
                            collection.push({
                                id: parseInt(item.taxes[x].id),
                                name: item.taxes[x].name,
                                abbreviation: item.taxes[x].abbreviation,
                                rate: item.taxes[x].rate,
                                amount: item.taxes[x].rate * this.getItemTotal(item)
                            })
                        }
   
                        return collection;

                    }).flat();
                
                var x, combinedTaxAmounts = [];

                for(x = 0; x < itemTaxAmounts.length; x++){
                    combinedTaxAmounts[ itemTaxAmounts[x].id ] = {
                        id: itemTaxAmounts[x].id,
                        name: itemTaxAmounts[x].name,
                        abbreviation: itemTaxAmounts[x].abbreviation,
                        rate: itemTaxAmounts[x].rate,
                        amount: ((combinedTaxAmounts[ itemTaxAmounts[x].id ] || {}).amount || 0) + itemTaxAmounts[x].amount,
                    };
                }

                var filtered = combinedTaxAmounts.filter(function (el) {
                    return el != null;
                });
                
                return filtered;

            },
            addProductOrService(productsOrServices){
                var result;
                
                for(var x = 0; x < productsOrServices.length; x++){
                   result = this.localInvoice.items.push(productsOrServices[x]);
                }

                //  Re-calculate the taxes
                this.localInvoice.calculated_taxes = this.runCalculateTaxes();

                //  Re-Calculate the sub and grand total amount
                this.updateSubAndGrandTotal();
                
                this.$Notice.success({
                    title: productsOrServices.length == 1 ? 'Product added successfully': 'Products added successfully'
                });

                // Close modal
                this.closeModal();
            },
            removeItem: function(index){
                this.localInvoice.items.splice(index, 1);

                this.$Notice.success({
                    title: 'Item removed sucessfully'
                });
            },
            closeModal(){
                this.isOpenProductsAndServicesModal = !this.isOpenProductsAndServicesModal;
            },
            downloadPDF(download = { preview: false }){
                if(this.invoice.id){
                    var showAsPreview = download.preview ? '?preview=1' : '';
                    let routeData = this.$router.resolve({
                            path: '/api/download/invoices/'+this.invoice.id + showAsPreview
                        });

                    window.open(routeData.href.replace("#", ""), '_blank');

                }
            },
            saveInvoice(){

                var self = this;

                //  Start loader
                self.isSavingInvoice = true;

                console.log('Attempt to save invoice...');

                console.log( self.localInvoice );

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                console.log(invoiceData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id, invoiceData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingInvoice = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the original invoice details
                        self.storeOriginalInvoice();

                        self.invoiceHasChanged = self.checkIfinvoiceHasChanged();

                        //  Alert creation success
                        self.$Message.success('Invoice saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error saving invoice...');
                        console.log(response);
                    });
            },
            createInvoice(){

                var self = this;

                //  Start loader
                self.isCreatingInvoice = true;

                console.log('Attempt to create invoice...');

                console.log( self.localInvoice );

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                console.log(invoiceData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices?model='+this.modelType+'&modelId='+this.modelId, invoiceData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingInvoice = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the original invoice details
                        self.storeOriginalInvoice();

                        self.invoiceHasChanged = self.checkIfinvoiceHasChanged();

                        //  Alert creation success
                        self.$Message.success('Invoice created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('invoiceCreated', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error creating invoice...');
                        console.log(response);
                    });
            }
        },
        created(){
            //  Get the taxes
            this.fetchTaxes();

            //  Get the currencies
            this.fetchCurrencies();

            if(!this.company){
                this.fetchCompanyInfo(); 
            }

            if(!this.client && this.clientId){
                this.fetchClientInfo(); 
            }

            //  Store the original invoice details
            this.storeOriginalInvoice();
        }
    };
  
</script>