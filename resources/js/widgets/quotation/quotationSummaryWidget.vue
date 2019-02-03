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

    .quotation-widget{
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
    <div id="quotation-widget">
        
        <div v-if="isCreatingQuotation" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
        <div v-if="isSavingQuotation" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>

        <Card :style="{ width: '100%' }">
            
            <Spin size="large" fix v-if="isSavingQuotation || isCreatingQuotation"></Spin>

            <div slot="title">
                <h5>Quotation Summary</h5>
            </div>

            <div slot="extra" v-if="showMenuBtn">
                <Poptip
                    confirm
                    title="Convert this Quotation into an Invoice?"
                    ok-text="Yes"
                    cancel-text="No"
                    @on-ok="convertQuoteToInvoice()"
                    placement="bottom">
                    <Button type="primary" size="small" :loading="isConvertingToInvoice">
                        <Icon v-if="!isConvertingToInvoice" type="ios-refresh" :size="20" style="margin-top: -4px;"/>
                        <span>{{ isConvertingToInvoice ? 'Converting...' : 'Convert To Invoice' }}</span>
                    </Button>
                </Poptip>

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
                            <Input value="https://optimumqbw.com/quotation/GUYSD54983IIOWIW728UUIH2344IUH2I332D" style="width: 100%" :readonly="true" />
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
                        <DropdownItem>Print Quotation</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
            </div>

            <Row>

                <Col span="24" class="pr-4">

                    <!-- Create Button -->
                    <Button v-if="newQuotation" 
                            class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                            type="success" size="small" @click="createQuotation()">
                        <div class="circle-ripple"></div>
                        <span :style="{ position:'relative', zIndex:'2' }">Create Quotation</span>
                    </Button>

                    <!-- Save Changes Button -->
                    <Button v-if="!newQuotation && quoteHasChanged" 
                            class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                            type="success" size="small" @click="saveQuotation()">
                        <div class="circle-ripple"></div>
                        <span :style="{ position:'relative', zIndex:'2' }">Save Changes</span>
                    </Button>

                    <!-- Edit Mode Switch -->
                    <span class="float-right mb-2">
                        <Poptip word-wrap width="200" trigger="hover" content="Edit this quotation">
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

                    <h1 v-if="!editMode" class="text-dark text-right" style="font-size: 35px;">{{ localQuote.heading || '___' }}</h1>
                    <el-input v-if="editMode" placeholder="Quotation heading" v-model="localQuote.heading" size="large" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                    
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
                    <h3 v-if="!editMode" class="text-dark mb-3">{{ localQuote.quote_to_title || '___' }}:</h3>
                    <el-input v-if="editMode" placeholder="Quotation heading" v-model="localQuote.quote_to_title" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>
                    
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
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ localQuote.reference_no_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Reference number" v-model="localQuote.reference_no_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                            
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ localQuote.created_date_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Created Date" v-model="localQuote.created_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ localQuote.expiry_date_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Expiry Date" v-model="localQuote.expiry_date_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ localQuote.grand_total_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="localQuote.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col span="8">
                            <p v-if="!editMode" class="text-dark">{{ localQuote.reference_no_value || '___' }}</p>
                            <el-input v-if="editMode" placeholder="e.g) 001" v-model="localQuote.reference_no_value" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark">{{ localQuote.created_date_value | moment('MMM DD YYYY') || '___' }}</p>
                            <!-- Edit Created date -->
                            <el-date-picker v-if="editMode" v-model="localQuote.created_date_value" type="datetime" placeholder="e.g) January 1, 2018" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"
                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                            </el-date-picker>

                            <p v-if="!editMode" class="text-dark">{{ localQuote.expiry_date_value | moment('MMM DD YYYY') || '___' }}</p>
                            <!-- Edit Created date -->
                            <el-date-picker v-if="editMode" v-model="localQuote.expiry_date_value" type="datetime" placeholder="e.g) January 7, 2018" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"
                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                            </el-date-picker>

                            <p v-if="!editMode" class="text-dark">{{ localQuote.grand_total_value | currency(currencySymbol) || '___' }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localQuote.grand_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }" disabled></el-input>
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
                                :fetchedCurrencies="fetchedCurrencies" :selectedCurrency="localQuote.currency_type"
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
                                    <span v-if="!editMode">{{ localQuote.table_columns[0].name || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Service/Product" v-model="localQuote.table_columns[0].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ localQuote.table_columns[1].name || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Quantity" v-model="localQuote.table_columns[1].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ localQuote.table_columns[2].name || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Price" v-model="localQuote.table_columns[2].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th colspan="1" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ localQuote.table_columns[3].name || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Amount" v-model="localQuote.table_columns[3].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th v-if="editMode" class="p-2" style="color: #FFFFFF;">
                                    <span class="d-block mb-2">Tax</span>
                                </th>
                                <th v-if="editMode" class="p-2" style="color: #FFFFFF;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="localQuote.items.length" v-for="(item, i) in localQuote.items" :key="i"  :style=" ( (i + 1) % 2 ) ? 'background-color:'+secondaryColor+';' : ''">
                                <td colspan="4" class="p-2">
                                
                                    <p v-if="!editMode" class="text-dark mr-5">
                                        <strong>{{ item.name || '___' }}</strong>
                                    </p>
                                    <el-input v-if="editMode" :placeholder="'e.g) Item '+ (i+1)" v-model="localQuote.items[i].name" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                    
                                    <p v-if="!editMode" class="mr-5">
                                        <span v-if="!editMode">{{ item.description }}</span>
                                    </p>
                                    <el-input v-if="editMode" placeholder="e.g) Item" v-model="localQuote.items[i].description" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.quantity || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2" 
                                              v-model="localQuote.items[i].quantity" 
                                              @input.native="updateSubAndGrandTotal()"
                                              size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.unitPrice | currency(currencySymbol) || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2,500.00" 
                                              v-model="localQuote.items[i].unitPrice" 
                                              @input.native="updateSubAndGrandTotal()"
                                              size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td colspan="1" class="p-2">
                                    <span v-if="!editMode">{{ item.totalPrice | currency(currencySymbol) || '___' }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 5,000.00" :value="getItemTotal(localQuote.items[i])" size="mini" class="p-1" :style="{ maxWidth:'100%' }" disabled></el-input>
                                </td>
                                <td v-if="editMode" class="p-2">
                                    <Loader v-if="isLoadingTaxes" :loading="isLoadingTaxes" type="text" :style="{ marginTop:'40px' }">Loading taxes...</Loader>
                                    <taxSelector v-if="!isLoadingTaxes && fetchedTaxes.length" 
                                        :fetchedTaxes="fetchedTaxes" :selectedTaxes="localQuote.items[i].taxes"
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
                            <tr v-if="!localQuote.items.length">
                                <td colspan="9" class="p-2">
                                    <Alert show-icon>
                                        No items added
                                        <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                        <template slot="desc">Start adding products/services to your quotation. You will be able to modify your item name, details, quantity, price and any applicable taxes.</template>
                                    </Alert>

                                    <!-- Edit Mode Switch -->
                                    <span v-if="!editMode" class="d-block m-auto" :style="{ width: '200px' }">
                                        <Poptip word-wrap width="200" trigger="hover" content="Edit this quotation">
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
                            <p v-if="!editMode" class="text-dark text-right float-right w-100 mb-2"><strong>{{ localQuote.sub_total_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Total" v-model="localQuote.sub_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" v-for="(calculatedTax , i) in localQuote.calculated_taxes" :key="i" class="text-dark text-right float-right w-100">
                                {{ calculatedTax.name }} ({{ calculatedTax.rate*100 }}%):
                            </p>
                            <el-input v-if="editMode" v-for="(calculatedTax , i) in localQuote.calculated_taxes" :key="i" placeholder="e.g) VAT (12%)" :value="calculatedTax.name + ' (' + calculatedTax.rate*100 + '%)'" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                            
                        </Col>
                        <Col :span="editMode ? '8':'4'">

                            <p v-if="!editMode" class="text-right float-right w-100 mb-2">{{ localQuote.sub_total_value | currency(currencySymbol) || '___' }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localQuote.sub_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                            <p v-if="!editMode" v-for="(calculatedTax , i) in localQuote.calculated_taxes" :key="i" class="text-right float-right w-100">
                                {{ calculatedTax.amount | currency(currencySymbol) || '___' }}
                            </p>
                            <el-input v-if="editMode" v-for="(calculatedTax , i) in localQuote.calculated_taxes" :key="i" placeholder="e.g) 1,500.00" :value="calculatedTax.amount | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                        </Col>
                        
                    </Row>

                    <Row :gutter="20" class="doubleUnderline mt-3">

                        <Col :span="editMode ? '16':'20'">
                        
                            <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ localQuote.grand_total_title || '___' }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="localQuote.grand_total_title" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col :span="editMode ? '8':'4'">

                            <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ localQuote.grand_total_value | currency(currencySymbol) || '___' }}</strong></p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+currencySymbol+'5,000.00'" :value="localQuote.grand_total_value | currency(currencySymbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                        
                        </Col>
                        
                    </Row>

                </Col>
            </Row>

            <Row class="mb-5">
                <Col span="24">

                    <h3 v-if="!editMode" class="text-dark mb-2">{{ localQuote.notes.title }}</h3>
                    <el-input v-if="editMode" placeholder="E.g) Notes/Payment Information" v-model="localQuote.notes.title" size="large" class="mb-2" :style="{ maxWidth:'400px' }"></el-input>
                    <br>
                    <p v-if="!editMode" v-html="localQuote.notes.details"></p>
                    <div v-if="editMode">
                        <froalaEditor
                            name="editor"
                            :model="localQuote.notes.details"
                            v-on:change="value => { localQuote.notes.details = value }"
                            :config="froalaEditorConfig">
                        </froalaEditor>
                    </div>
                    
                </Col>
            </Row>

            <footer :style="'background-color:'+primaryColor+';'">
                <div class="mt-1">
                    <span v-if="!editMode">{{ localQuote.footer }}</span>
                    <el-input v-if="editMode" :placeholder="'e.g) Terms And Conditions Apply'" v-model="localQuote.footer" size="mini" :style="{ width:'50%', margin:'0 auto' }"></el-input>
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
    import getProductsAndServicesModal from './../invoice/modals/getProductsAndServicesModal.vue';
    import imageUploader from './../invoice/upload/imageUploader.vue';
    import currencySelector from './../invoice/selectors/currencySelector.vue';
    import taxSelector from './../invoice/selectors/taxSelector.vue';
    import froalaEditor from './../invoice/wiziwigEditors/froalaEditor.vue';    

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            Loader, getProductsAndServicesModal, imageUploader,currencySelector, taxSelector, froalaEditor
        },
        props: {
            quotation: {
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
            newQuotation: {
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
                isSavingQuotation: false,
                isCreatingQuotation: false,
                isLoadingClientInfo: false,
                isLoadingCompanyInfo: false,
                isConvertingToInvoice: false,
                editMode: false,
                isLoadingTaxes: false,
                isOpenProductsAndServicesModal: false,
                fetchedTaxes: [],
                fetchedCurrencies: [],
                localQuote: this.quotation,
                _localQuoteBeforeChange: {},
                quoteHasChanged: false,
                //  The company details
                company: this.quotation.customized_company_details,
                //  The client details
                client: this.quotation.customized_client_details,
                currencySymbol: this.quotation.currency_type.currency.symbol,
                primaryColor: this.quotation.colors[0],
                secondaryColor: this.quotation.colors[1],
                
                froalaEditorConfig: {
                    height: 100,
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['gxcode']], // plugin config: froalaEditor-ext-codewrapper
                    ],
                },
            }
        },
        watch: {
            localQuote: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    this.quoteHasChanged = this.checkIfQuoteHasChanged(val);
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

                        console.log('quotationSummaryWidget.vue - Error getting company details...');
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

                        console.log('quotationSummaryWidget.vue - Error getting client company details...');
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
            convertQuoteToInvoice(){
                const self = this;

                //  Start loader
                self.isConvertingToInvoice = true;


                //  Form data to send
                let quoteData = { quotation: self.localQuote };

                console.log('Start converting quotation to invoice...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations/'+this.quotation.id+'/convert', quoteData)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isConvertingToInvoice = false;
                        
                        //  Alert creation success
                        self.$Message.success('Invoice created sucessfully!');
                        
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isConvertingToInvoice = false;

                        console.log('quotationSummaryWidget.vue - Error converting quotation to invoice...');
                        console.log(response);    
                    });
            },
            checkIfQuoteHasChanged: function(updatedQuote = null){
                
                var currentQuote = _.cloneDeep(updatedQuote || this.localQuote);
                var isNotEqual = !_.isEqual(currentQuote, this._localQuoteBeforeChange);

                console.log('currentQuote');
                console.log(currentQuote);
                console.log('localQuoteBeforeChange');
                console.log(this._localQuoteBeforeChange);
                console.log(isNotEqual);

                return isNotEqual;
            },
            storeOriginalQuotation(){
                console.log('storing _localQuoteBeforeChange');
                //  Store the original quotation
                this._localQuoteBeforeChange = _.cloneDeep(this.localQuote);
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

                        console.log('quotationSummaryWidget.vue - Error getting taxes...');
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

                        console.log('quotationSummaryWidget.vue - Error getting currencies...');
                        console.log(response);    
                    });
            },
            updateTaxChanges(newTaxes, i){
                this.localQuote.items[i].taxes = newTaxes;
                this.localQuote.calculated_taxes = this.runCalculateTaxes();
                this.updateSubAndGrandTotal();
            },
            updateCurrencyChanges(newCurrency){
                this.localQuote.currency_type = newCurrency;
                this.currencySymbol = this.localQuote.currency_type.currency.symbol;

                this.$Notice.success({
                    title: 'Currency changed to ' + newCurrency.country + ' (' + newCurrency.currency.iso.code + ')'
                });
            },
            updateSubAndGrandTotal(){
                console.log('run updateSubAndGrandTotal()');

                //  Re-Calculate the sub total amount
                this.localQuote.sub_total_value = this.runGetTotal();

                //  Re-Calculate the grand total amount
                this.localQuote.grand_total_value = this.runGetGrandTotal();

                //  Re-Calculate the taxes
                this.localQuote.calculated_taxes = this.runCalculateTaxes();
            },
            runGetTotal: function(){
                var itemAmounts = (this.localQuote.items || []).map(item => item.quantity * item.unitPrice);
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
                
                var itemTaxAmounts = this.localQuote.items.map(item => {
                        
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
                   result = this.localQuote.items.push(productsOrServices[x]);
                }

                //  Re-calculate the taxes
                this.localQuote.calculated_taxes = this.runCalculateTaxes();

                //  Re-Calculate the sub and grand total amount
                this.updateSubAndGrandTotal();
                
                this.$Notice.success({
                    title: productsOrServices.length == 1 ? 'Product added successfully': 'Products added successfully'
                });

                // Close modal
                this.closeModal();
            },
            removeItem: function(index){
                this.localQuote.items.splice(index, 1);

                this.$Notice.success({
                    title: 'Item removed sucessfully'
                });
            },
            closeModal(){
                this.isOpenProductsAndServicesModal = !this.isOpenProductsAndServicesModal;
            },
            downloadPDF(download = { preview: false }){
                if(this.quotation.id){
                    var showAsPreview = download.preview ? '?preview=1' : '';
                    let routeData = this.$router.resolve({
                            path: '/api/download/quotations/'+this.quotation.id + showAsPreview
                        });

                    window.open(routeData.href.replace("#", ""), '_blank');

                }
            },
            saveQuotation(){

                var self = this;

                //  Start loader
                self.isSavingQuotation = true;

                console.log('Attempt to save quotation...');

                console.log( self.localQuote );

                //  Form data to send
                let quoteData = { quotation: self.localQuote };

                console.log(quoteData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations/'+self.localQuote.id, quoteData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingQuotation = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the original quotation details
                        self.storeOriginalQuotation();

                        self.quoteHasChanged = self.checkIfQuoteHasChanged();

                        //  Alert creation success
                        self.$Message.success('Quotation saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingQuotation = false;

                        console.log('quotationSummaryWidget.vue - Error saving quotation...');
                        console.log(response);
                    });
            },
            createQuotation(){

                var self = this;

                //  Start loader
                self.isCreatingQuotation = true;

                console.log('Attempt to create quotation...');

                console.log( self.localQuote );

                //  Form data to send
                let quoteData = { quotation: self.localQuote };

                console.log(quoteData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations?model='+this.modelType+'&modelId='+this.modelId, quoteData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingQuotation = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the original quotation details
                        self.storeOriginalQuotation();

                        self.quoteHasChanged = self.checkIfQuoteHasChanged();

                        //  Alert creation success
                        self.$Message.success('Quotation created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('quotationCreated', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingQuotation = false;

                        console.log('quotationSummaryWidget.vue - Error creating quotation...');
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

            //  Store the original quotation details
            this.storeOriginalQuotation();
        }
    };
  
</script>