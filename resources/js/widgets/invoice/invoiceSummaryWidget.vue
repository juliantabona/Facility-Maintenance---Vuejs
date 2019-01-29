<style scoped>

    .doubleUnderline{
        padding: 8px 0px;
        border-bottom: 3px solid #dee1e2;
        border-top: 1px solid #dee1e2;
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

</style>

<template>

    <div id="invoice-widget">
        
        <!-- Get the summary header to display the invoice #, status, due date, amount due and menu options -->
        <summaryHeader 
             v-if="!createMode && localInvoice.has_approved"
            :invoice="localInvoice" :editMode="editMode" :createMode="createMode" :currencySymbol="currencySymbol"
            @toggleEditMode="toggleEditMode($event)">
        </summaryHeader>
        
        <!-- Get the staging toolbar to display the invoice approved, sent/re-send and record payment stages -->
        <stagingToolbar 
            v-if="!createMode"
            :invoice="localInvoice" :editMode="editMode" :createMode="createMode" 
            @toggleEditMode="toggleEditMode($event)" @approved="updateInvoiceData($event)" 
            @sent="updateInvoiceData($event)"
            @paid="updateInvoiceData($event)" @cancelled="updateInvoiceData($event)" 
            @reminderSet="updateInvoiceData($event)">
        </stagingToolbar>

        <!-- Loaders for creating/saving invoice -->
        <Row>
            <Col :span="24">
                <div v-if="isCreatingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingInvoice" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>

        <!-- Invoice View/Editor -->
        <Row id="invoice-summary-1">
            <Col :span="24">
                <Card :style="{ width: '100%' }">
                    
                    <!-- White overlay when creating/saving invoice -->
                    <Spin size="large" fix v-if="isSavingInvoice || isCreatingInvoice"></Spin>

                    <!-- Main header -->
                    <div slot="title">
                        <h5>Invoice Summary</h5>
                    </div>

                    <!-- Invoice options -->
                    <div slot="extra" v-if="showMenuBtn">

                        <!-- Preview button -->
                        <Button type="primary" size="small" @click.native="downloadPDF({ preview: true })">
                            <Icon type="ios-eye-outline" :size="20" style="margin-top: -4px;"/>
                            <span>Preview</span>
                        </Button>

                        <!-- Send dropdown button -->
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

                        <!-- Invoice Menu -->
                        <Dropdown class="menu-border" trigger="click" placement="bottom-end">
                            <a href="javascript:void(0)">
                                <Icon type="md-more" :size="16"></Icon>
                            </a>
                            <DropdownMenu slot="list">
                                <DropdownItem v-if="!editMode" @click.native="toggleEditMode(true)">Edit</DropdownItem>
                                <DropdownItem v-if="editMode" @click.native="toggleEditMode(false)">View</DropdownItem>
                                <DropdownItem>Trash</DropdownItem>
                                <DropdownItem>Edit Business Information</DropdownItem>
                                <DropdownItem @click.native="downloadPDF()">Export As PDF</DropdownItem>
                                <DropdownItem @click.native="downloadPDF({ print: true })">Print Invoice</DropdownItem>
                            </DropdownMenu>
                        </Dropdown>
                    </div>

                    <Row>

                        <Col span="24" class="pr-4">

                            <!-- Create button -->
                            <Button v-if="createMode" 
                                    class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                    type="success" size="small" @click="createInvoice()">
                                <div class="circle-ripple"></div>
                                <span :style="{ position:'relative', zIndex:'2' }">Create Invoice</span>
                            </Button>

                            <!-- Save changes button -->
                            <Button v-if="!createMode && invoiceHasChanged" 
                                    class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                    type="success" size="small" @click="saveInvoice()">
                                <div class="circle-ripple"></div>
                                <span :style="{ position:'relative', zIndex:'2' }">Save Changes</span>
                            </Button>

                            <!-- Edit mode switch -->
                            <span class="float-right mb-2">
                                <Poptip word-wrap width="200" trigger="hover" content="Turn on to edit invoice">
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

                            <!-- Company logo -->
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
                        
                        <!-- Loader for when loading the company information -->
                        <Col v-if="isLoadingCompanyInfo" span="12" class="pr-4">
                            <Loader v-if="isLoadingCompanyInfo" :loading="isLoadingCompanyInfo" type="text" class="float-right text-right" :style="{ marginTop:'40px' }">Loading Company Details...</Loader>
                        </Col>

                        <!-- Company information -->
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

                        <!-- No company information alert -->
                        <Col v-if="!company && !isLoadingCompanyInfo" :span="12">
                            <Alert :style="{maxWidth: '250px'}" type="warning">
                                No company details
                            </Alert>
                        </Col>

                    </Row>

                    <Divider dashed class="mt-3 mb-3" />

                    <Row>
                        <Col span="12" class="pl-2">
                            <h3 v-if="!editMode" class="text-dark mb-3">{{ localInvoice.invoice_to_title ? localInvoice.invoice_to_title+':' : '' }}</h3>
                            <el-input v-if="editMode" placeholder="Invoice heading" v-model="localInvoice.invoice_to_title" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>
                            
                            <!-- Loader for when loading the client information -->
                            <div v-if="isLoadingClientInfo">
                                <Loader v-if="isLoadingClientInfo" :loading="isLoadingClientInfo" type="text" :style="{ marginTop:'40px' }">Loading Client Details...</Loader>
                            </div>

                            <!-- Client information -->
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

                            <!-- No client information alert -->
                            <div v-if="!client && !isLoadingClientInfo">
                                <Alert :style="{maxWidth: '250px'}" type="warning">
                                    No client selected
                                </Alert>
                            </div>

                            <!-- Company selector -->
                            <companySelector v-if="editMode" :style="{maxWidth: '250px'}" class="mt-2"
                                @updated="updateClientChanges($event)">
                            </companySelector>

                        </Col>
                        
                        <!-- Invoice details e.g) Reference #, created date, due date, grand total -->
                        <Col span="12">
                            <Row :gutter="20">
                                <Col span="16">
                                    <p v-if="!editMode" class="text-dark text-right"><strong>{{ localInvoice.reference_no_title ? localInvoice.reference_no_title+':' : '___' }}</strong></p>
                                    <el-input v-if="editMode" placeholder="e.g) Reference number" v-model="localInvoice.reference_no_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                                    
                                    <p v-if="!editMode" class="text-dark text-right"><strong>{{ localInvoice.created_date_title ? localInvoice.created_date_title+':' : '___' }}</strong></p>
                                    <el-input v-if="editMode" placeholder="e.g) Created Date" v-model="localInvoice.created_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                                    <p v-if="!editMode" class="text-dark text-right"><strong>{{ localInvoice.expiry_date_title ? localInvoice.expiry_date_title+':' : '___' }}</strong></p>
                                    <el-input v-if="editMode" placeholder="e.g) Expiry Date" v-model="localInvoice.expiry_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                                    <p v-if="!editMode" class="text-dark text-right"><strong>{{ localInvoice.grand_total_title ? localInvoice.grand_total_title+':' : '___' }}</strong></p>
                                    <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="localInvoice.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                                </Col>
                                <Col span="8">
                                    <p v-if="!editMode && !createMode" class="text-dark">{{ localInvoice.reference_no_value || '___' }}</p>
                                    <el-input v-if="editMode && !createMode" placeholder="e.g) 001" v-model="localInvoice.reference_no_value" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

                                    <p v-if="!editMode && createMode" class="text-dark">{{ localInvoice.reference_no_value ? 'Auto Generated' : '___' }}</p>
                                    <el-input v-if="editMode && createMode" :disabled="true" placeholder="Auto Generated" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

                                    <p v-if="!editMode" class="text-dark">{{ localInvoice.created_date_value | moment('MMM DD YYYY') || '___' }}</p>
                                    <!-- Edit Created date -->
                                    <el-date-picker v-if="editMode" v-model="localInvoice.created_date_value" type="date" placeholder="e.g) January 1, 2018" size="mini" class="mb-2" :style="{ maxWidth:'135px', float:'right' }"
                                        format="MMM dd yyyy" value-format="yyyy-MM-dd">
                                    </el-date-picker>

                                    <p v-if="!editMode" class="text-dark">{{ localInvoice.expiry_date_value | moment('MMM DD YYYY') || '___' }}</p>
                                    <!-- Edit Created date -->
                                    <el-date-picker v-if="editMode" v-model="localInvoice.expiry_date_value" type="date" placeholder="e.g) January 7, 2018" size="mini" class="mb-2" :style="{ maxWidth:'135px', float:'right' }"
                                        format="MMM dd yyyy" value-format="yyyy-MM-dd">
                                    </el-date-picker>

                                    <p v-if="!editMode" class="text-dark">{{ localInvoice.grand_total_value | currency(currencySymbol) || '___' }}</p>
                                    <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localInvoice.grand_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }" disabled></el-input>
                                </Col>
                            </Row>
                        </Col>
                    </Row>

                    <!-- Edit mode toolbar e.g) Currency selector, primary/secondary color picker -->
                    <Row>
                        
                        <Col span="24">
                            <div v-if="editMode">

                                <div class="float-right mr-2">
                                    <ColorPicker v-model="secondaryColor" @on-change="updateSecondaryColor" class="float-right" recommend alpha />
                                    <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Secondary Color:</span>
                                </div>
                                
                                <div class="float-right mr-2">
                                    <ColorPicker v-model="primaryColor" @on-change="updatePrimaryColor" class="float-right" okText="Ok" format="hex" recommend alpha />
                                    <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Primary Color:</span>
                                </div>
                                <div class="float-right mr-3">
                                    <Loader v-if="isLoadingCurrencies" :loading="isLoadingCurrencies" type="text" :style="{ marginTop:'40px' }">Loading currencies...</Loader>
                                    
                                    <div v-if="fetchedCurrencies.length">
                                        <currencySelector class="float-right" :style="{maxWidth: '150px'}"
                                            :fetchedCurrencies="fetchedCurrencies" :selectedCurrency="localInvoice.currency_type"
                                            @updated="updateCurrencyChanges($event)">
                                        </currencySelector>
                                        <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Currency:</span>
                                    </div>
                                </div>

                            </div>
                        </Col>

                        <!-- Invoice list items (products/services) -->
                        <Col span="24">
                        
                            <table  class="table table-hover mt-3 mb-0 w-100">
                                <thead :style="'background-color:'+primaryColor+';'">
                                    <tr>
                                        <th colspan="4" class="p-2" style="color: #FFFFFF;">
                                            <span v-if="!editMode">{{ (tableColumns[0] || {}).name || '___' }}</span>
                                            <el-input v-if="editMode" placeholder="e.g) Service/Product" v-model="(tableColumns[0] || {}).name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                        </th>
                                        <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                            <span v-if="!editMode">{{ (tableColumns[1] || {}).name || '___' }}</span>
                                            <el-input v-if="editMode" placeholder="e.g) Quantity" v-model="(tableColumns[1] || {}).name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                        </th>
                                        <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                            <span v-if="!editMode">{{ (tableColumns[2] || {}).name || '___' }}</span>
                                            <el-input v-if="editMode" placeholder="e.g) Price" v-model="(tableColumns[2] || {}).name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                        </th>
                                        <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                            <span v-if="!editMode">{{ (tableColumns[3] || {}).name || '___' }}</span>
                                            <el-input v-if="editMode" placeholder="e.g) Amount" v-model="(tableColumns[3] || {}).name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                        </th>
                                        <th v-if="editMode" class="p-2" style="color: #FFFFFF;">
                                            <span class="d-block mb-2">Tax</span>
                                        </th>
                                        <th v-if="editMode" class="p-2" style="color: #FFFFFF;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="(localInvoice.items || {}).length" v-for="(item, i) in localInvoice.items" :key="i"  :style=" ( (i + 1) % 2 ) ? 'background-color:'+secondaryColor+';' : ''">
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

                                    <!-- No list items alert -->
                                    <tr v-if="!(localInvoice.items || {}).length">
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

                                    <!-- Add item button -->
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

                    <!-- Total details e.g) Sub/grand total and tax amounts -->
                    <Row>
                        <Col span="12" offset="12" class="pr-4">
                            <Row :gutter="20">
                                <Col :span="editMode ? '16':'20'">
                                    <p v-if="!editMode" class="text-dark text-right float-right w-100 mb-2"><strong>{{ localInvoice.sub_total_title | currency(currencySymbol) }} {{ localInvoice.sub_total_title ? localInvoice.sub_total_title + ':'  : '___' }}</strong></p>
                                    <el-input v-if="editMode" placeholder="e.g) Total" v-model="localInvoice.sub_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                                    <div v-if="(localInvoice.calculated_taxes || {}).length">
                                        <p v-if="!editMode" v-for="(calculatedTax , i) in localInvoice.calculated_taxes" :key="i" class="text-dark text-right float-right w-100">
                                            {{ calculatedTax.name }} ({{ calculatedTax.rate*100 }}%):
                                        </p>
                                        <el-input v-if="editMode" v-for="(calculatedTax , i) in localInvoice.calculated_taxes" :key="i" placeholder="e.g) VAT (12%)" :value="calculatedTax.name + ' (' + calculatedTax.rate*100 + '%)'" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                                    </div>
                                </Col>
                                <Col :span="editMode ? '8':'4'">

                                    <p v-if="!editMode" class="text-right float-right w-100 mb-2">{{ localInvoice.sub_total_value | currency(currencySymbol) || '___' }}</p>
                                    <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localInvoice.sub_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                                    <div v-if="(localInvoice.calculated_taxes || {}).length">
                                        <p v-if="!editMode" v-for="(calculatedTax , i) in localInvoice.calculated_taxes" :key="i" class="text-right float-right w-100">
                                            {{ calculatedTax.amount | currency(currencySymbol) || '___' }}
                                        </p>
                                        <el-input v-if="editMode" v-for="(calculatedTax , i) in localInvoice.calculated_taxes" :key="i" placeholder="e.g) 1,500.00" :value="calculatedTax.amount | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                                    </div>
                                </Col>
                                
                            </Row>

                            <Row :gutter="20" class="doubleUnderline mt-3">

                                <Col :span="editMode ? '16':'20'">
                                
                                    <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ localInvoice.grand_total_title | currency(currencySymbol) }} {{ localInvoice.grand_total_title ? localInvoice.grand_total_title + ':'  : '___' }}</strong></p>
                                    <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="localInvoice.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                                </Col>
                                <Col :span="editMode ? '8':'4'">

                                    <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ localInvoice.grand_total_value | currency(currencySymbol) }}</strong></p>
                                    <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localInvoice.grand_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                                
                                </Col>
                                
                            </Row>

                        </Col>
                    </Row>

                    <!-- Invoice summary e.g) For noting payment details/terms and conditions -->

                    <Row class="mb-5">
                        <Col span="24">

                            <h3 v-if="!editMode" class="text-dark mb-2">{{ footerNotes.title }}</h3>
                            <el-input v-if="editMode" placeholder="E.g) Notes/Payment Information" v-model="footerNotes.title" size="large" class="mb-2" :style="{ maxWidth:'400px' }"></el-input>
                            <br>
                            <p v-if="!editMode" v-html="footerNotes.details"></p>
                            <div v-if="editMode">
                                <Summernote
                                    name="editor"
                                    :model="footerNotes.details"
                                    v-on:change="value => { footerNotes.details = value }"
                                    :config="summernoteConfig">
                                </Summernote>
                            </div>
                            
                        </Col>
                    </Row>

                    <!-- Invoice page footer -->

                    <footer :style="'background-color:'+primaryColor+';'">
                        <div class="mt-1">
                            <span v-if="!editMode">{{ localInvoice.footer }}</span>
                            <el-input v-if="editMode" :placeholder="'e.g) Terms And Conditions Apply'" v-model="localInvoice.footer" size="mini" :style="{ width:'50%', margin:'0 auto' }"></el-input>
                        </div>     
                    </footer>

                </Card>
            </Col>
        </Row>

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

    import summaryHeader from './header/main.vue';
    import stagingToolbar from './stagingToolbar/main.vue';


    import Loader from './../../components/_common/loader/Loader.vue';
    import getProductsAndServicesModal from './../quotation/getProductsAndServicesModal.vue';
    import imageUploader from './../quotation/imageUploader.vue';
    import currencySelector from './../quotation/currencySelector.vue';
    import taxSelector from './../quotation/taxSelector.vue';
    import Summernote from './../quotation/Summernote.vue';
     
    import companySelector from './companySelector.vue';   

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            summaryHeader, stagingToolbar,

            Loader, getProductsAndServicesModal, imageUploader,currencySelector, taxSelector, Summernote,
            companySelector
        },
        props: {
            invoice: {
                type: Object,
                default: function () { 
                    return {
                        status: '',
                        heading: '',
                        invoice_to_title: '',
                        reference_no_title: '',
                        reference_no_value: '',
                        created_date_title: '',
                        created_date_value: '',
                        expiry_date_title: '',
                        expiry_date_value: '',
                        sub_total_title: '',
                        sub_total_value: 0,
                        grand_total_title: '',
                        grand_total_value: 0,
                        currency_type: null,
                        customized_company_details: null,
                        customized_client_details: null,
                        client_id: null,
                        calculated_taxes: [],
                        table_columns: [],
                        items: [],
                        notes: {
                            title: '',
                            details: ''
                        },
                        colors: [],
                        footer: ''
                    }
                }
            },
            clientId:{
                type: Number,
                default: null
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            create: {
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

                //  Modes
                editMode: false,
                createMode: this.create,

                //  Loading States
                isSavingInvoice: false,
                isCreatingInvoice: false,
                isLoadingClientInfo: false,
                isLoadingCompanyInfo: false,
                isConvertingToInvoice: false,
                isLoadingTaxes: false,
                isOpenProductsAndServicesModal: false,

                //  Resources
                fetchedTaxes: [],
                fetchedCurrencies: [],

                //  Local Invoice and state changes
                localInvoice: (this.invoice || {}),
                _localInvoiceBeforeChange: {},
                invoiceHasChanged: false,

                //  Invoice Shorthands
                primaryColor: (this.invoice.colors || {})[0],
                secondaryColor: (this.invoice.colors || {})[1],
                company: this.invoice.customized_company_details,
                client: this.invoice.customized_client_details,
                currencySymbol: ((this.invoice.currency_type || {}).currency || {}).symbol,
                tableColumns: this.invoice.table_columns,
                footerNotes: this.invoice.notes,
                
                //  Summernote Configuration
                summernoteConfig: {
                    height: 100,
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']]
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
            toggleEditMode(activate = true){

                var self = this,
                    options = {
                        easing: 'ease-in-out',
                        offset: -100,
                        force: true,
                        cancelable: true,
                        onStart: function(element) {
                            // scrolling started
                        },
                        onDone: function(element) {
                            //  Activate edit mode
                            self.editMode = activate;
                        },
                        onCancel: function() {
                        // scrolling has been interrupted
                        },
                        x: false,
                        y: true
                    }

                //var cancelScroll = VueScrollTo.scrollTo('invoice-summary-1', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#invoice-summary-1', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            updatePrimaryColor(newColor){
                
                /*  We need to use the Vue.set(object, key, value) instead of  this.localInvoice.colors[0] = newColor, 
                 *  simply because the value of "this.localInvoice.colors" will change but the changes will not be
                 *  realized by vue since we change a nested and non-reactive property, unless we set that 
                 *  non-reactive property as a v-model proprty e.g) <Tag v-model="localInvoice.colors[0]"> 
                 *  which would also work. However in this case we will use this.$set() 
                 */ 
                console.log('New Primary Color: ' + newColor);
                this.$set(this.localInvoice.colors, 0, newColor);
            },
            updateSecondaryColor(newColor){
                
                /*  We need to use the Vue.set(object, key, value) instead of  this.localInvoice.colors[0] = newColor, 
                 *  simply because the value of "this.localInvoice.colors" will change but the changes will not be
                 *  realized by vue since we change a nested and non-reactive property, unless we set that 
                 *  non-reactive property as a v-model proprty e.g) <Tag v-model="localInvoice.colors[0]"> 
                 *  which would also work. However in this case we will use this.$set() 
                 */ 
                console.log('New Secondary Color: ' + newColor);
                this.$set(this.localInvoice.colors, 1, newColor);
            },
            fetchCompanyInfo() {
                if(!this.company && this.user.company_id){
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
                                self.company = self.localInvoice.customized_company_details = self.formatCompanyDetails(data);
                            }

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingCompanyInfo = false;

                            console.log('invoiceSummaryWidget.vue - Error getting company details...');
                            console.log(response);    
                        });
                }
            },
            fetchClientInfo() {
                if(!this.client && this.clientId){
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
                                self.client = self.localInvoice.customized_client_details = self.formatCompanyDetails(data);
                            }
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingClientInfo = false;

                            console.log('invoiceSummaryWidget.vue - Error getting client company details...');
                            console.log(response);    
                        });
                }
            },
            formatCompanyDetails(company){
                var companyDetails = {
                        id: company.id,
                        name: company.name || '',
                        email: company.email || '',
                        phone: company.phone || '',
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
            activateCreateMode: function(){
                this.fetchInvoiceTemplate();
            },
            fetchInvoiceTemplate() {
                if(this.user.company_id){
                    const self = this;

                    //  Start loader
                    self.isLoadingInvoiceTemplate = true;

                    console.log('Start getting invoice template from company settings...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+self.user.company_id+'/settings')
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingInvoiceTemplate = false;

                            //  Get currencies
                            var template = (((data || {}).details || {}).invoiceTemplate || null);

                            if(template){
                                //  Activate edit mode
                                self.editMode = true;
                                console.log('Updaing the local invoice with template');
                                console.log(self.localInvoice);
                                self.populateInvoiceTemplate(template);
                            }
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingInvoiceTemplate = false;

                            console.log('invoiceSummaryWidget.vue - Error getting invoice template from company settings...');
                            console.log(response);    
                        });
                }
            },
            populateInvoiceTemplate(template){
                console.log('Populating invoice template with deault settings');
                var date = new Date();
                var dd = ('0' + date.getDate()).slice(-2);
                var mm = ('0' + (date.getMonth() + 1)).slice(-2);
                var yy = date.getFullYear();
                
                //  Update Invoice Object Using Template Data

                this.localInvoice.status = template.status;
                this.localInvoice.heading = template.heading;
                this.localInvoice.reference_no_title = template.reference_no_title;
                this.localInvoice.created_date_title = template.created_date_title;
                this.localInvoice.expiry_date_title = template.expiry_date_title;
                this.localInvoice.sub_total_title = template.sub_total_title;
                this.localInvoice.grand_total_title = template.grand_total_title;
                this.localInvoice.currency_type = template.currency_type;
                this.localInvoice.invoice_to_title = template.invoice_to_title;
                this.localInvoice.table_columns = template.table_columns;
                this.localInvoice.items = template.items;
                this.localInvoice.notes = template.notes;
                this.localInvoice.colors = template.colors;
                this.localInvoice.footer = template.footer;

                //  Update Invoice Dates Using Current Dates
                
                this.localInvoice.created_date_value = yy+'-'+mm+'-'+dd;
                this.localInvoice.expiry_date_value = yy+'-'+mm+'-'+('0' + (date.getDate() + 7) ).slice(-2);

                //  Update Invoice Shorthands

                this.primaryColor = this.localInvoice.colors[0];
                this.secondaryColor = this.localInvoice.colors[1],
                this.currencySymbol = this.localInvoice.currency_type.currency.symbol;
                this.tableColumns = this.localInvoice.table_columns;
                this.footerNotes = this.localInvoice.notes = template.notes;
                
                this.fetchCompanyInfo();

                this.fetchClientInfo();
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

                        console.log('self.isLoadingCurrencies: ' + self.isLoadingCurrencies);
                        console.log('self.fetchedCurrencies.length: ' + self.fetchedCurrencies.length);
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
            updateClientChanges(newClient){
                this.client = this.localInvoice.customized_client_details = this.formatCompanyDetails(newClient);

                this.$Notice.success({
                    title: 'Client changed to ' + newClient.name
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
                
                console.log('Adding new products/services to table');
                console.log(productsOrServices);
                
                for(var x = 0; x < productsOrServices.length; x++){
                    this.localInvoice.items.push(productsOrServices[x]);
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

                        /*
                        *  updateInvoiceData() : This method ensures the property is
                        *  updated as a reactive property and triggers future view updates:
                        */
                        self.updateInvoiceData(data);

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

                var associatedModel = (this.modelType && this.modelId) ? '?model='+this.modelType+'&modelId='+this.modelId: '';
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices'+associatedModel, invoiceData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingInvoice = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the current state of the invoice as the original invoice
                        self.storeOriginalInvoice();

                        self.invoiceHasChanged = self.checkIfinvoiceHasChanged();

                        //  Alert creation success
                        self.$Message.success('Invoice created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('invoiceCreated', data);

                        //  Go to invoice
                        self.$router.push({ name: 'show-invoice', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error creating invoice...');
                        console.log(response);
                    });
            },
            updateInvoiceData(newInvoice){
                /*
                 *  Vue.set()
                 *  We use Vue.set to set a new object property. This method ensures the  
                 *  property is created as a reactive property and triggers view updates:
                 */
                
                for(var x = 0; x <  _.size(newInvoice); x++){
                    var key = Object.keys(this.localInvoice)[x];
                    this.$set(this.localInvoice, key, newInvoice[key]);
                }

                //  Store the current state of the invoice as the original invoice
                this.storeOriginalInvoice();

                //  Update the invoice change status
                this.invoiceHasChanged = this.checkIfinvoiceHasChanged();

                //  Disable edit mode
                this.editMode = false;

            }
        },
        created(){
            //  Get the taxes
            this.fetchTaxes();

            //  Get the currencies
            this.fetchCurrencies();

            this.fetchCompanyInfo(); 

            this.fetchClientInfo(); 

            //  Store the current state of the invoice as the original invoice
            this.storeOriginalInvoice();

            if(this.createMode){
                this.activateCreateMode();
            }
        }
    };
  
</script>