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

    footer {
        position: fixed; 
        bottom: 0; 
        left: 0; 
        right: 0;
        height: 40px;

        /** Extra personal styles **/
        font-size:12px;
        background-color: #000;
        color: white;
        text-align: center;
        line-height: 30px;
    }

</style>

<template>
    <div id="testcase">
        
        <div v-if="isCreatingQuotation" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
        <div v-if="isSavingQuotation" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>

        <Card :style="{ width: '100%' }">
            
            <Spin size="large" fix v-if="isSavingQuotation || isCreatingQuotation"></Spin>

            <div slot="title">
                <h5>Quotation Summary</h5>
            </div>

            <div slot="extra" v-if="showMenuBtn">

                <Button type="primary" size="small">
                    <Icon type="ios-refresh" :size="20" style="margin-top: -4px;"/>
                    <span>Convert To Invoice</span>
                </Button>

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
                        <span :style="{ position:'relative', zIndex:'2' }">Create Changes</span>
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

                <Col span="12" class="pr-4">

                    <h1 v-if="!editMode" class="text-dark text-right" style="font-size: 35px;">{{ quoteDetails.heading }}</h1>
                    <el-input v-if="editMode" placeholder="Quotation heading" v-model="quoteDetails.heading" size="large" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <p v-if="!editMode" class="mt-3 text-dark text-right"><strong>{{ quoteDetails.companyDetails.name }}</strong></p>
                    <el-input v-if="editMode" placeholder="Company name" v-model="quoteDetails.companyDetails.name" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <p v-if="!editMode" class="text-right">{{ quoteDetails.companyDetails.email }}</p>
                    <el-input v-if="editMode" placeholder="Company email" v-model="quoteDetails.companyDetails.email" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <p v-if="!editMode" class="text-right">{{ quoteDetails.companyDetails.phone }}</p>
                    <el-input v-if="editMode" placeholder="Company tel/phone" v-model="quoteDetails.companyDetails.phone" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                    <br />
                    <p v-if="!editMode" v-for="(field, i) in quoteDetails.companyDetails.additionalFields" :key="i" class="text-right">
                        {{ field.value }}
                    </p>
                    <el-input v-if="editMode" v-for="(field, i) in quoteDetails.companyDetails.additionalFields" :key="i" 
                              size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"
                              :placeholder="'Company details ' + i" v-model="quoteDetails.companyDetails.additionalFields[i].value"></el-input>
                    </el-input>

                </Col>
            </Row>

            <Divider dashed class="mt-3 mb-3" />

            <Row>
                <Col span="12" class="pl-2">
                    <h3 v-if="!editMode" class="text-dark mb-3">{{ quoteDetails.quoteTo }}:</h3>
                    <el-input v-if="editMode" placeholder="Quotation heading" v-model="quoteDetails.quoteTo" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>
                    

                    <p v-if="!editMode" class="mt-3 text-dark"><strong>{{ quoteDetails.clientDetails.name }}</strong></p>
                    <el-input v-if="editMode" placeholder="Company name" v-model="quoteDetails.clientDetails.name" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

                    <p v-if="!editMode">{{ quoteDetails.clientDetails.email }}</p>
                    <el-input v-if="editMode" placeholder="Company email" v-model="quoteDetails.clientDetails.email" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

                    <p v-if="!editMode">{{ quoteDetails.clientDetails.phone }}</p>
                    <el-input v-if="editMode" placeholder="Company tel/phone" v-model="quoteDetails.clientDetails.phone" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>
                    <br />
                    <p v-if="!editMode" v-for="(field, i) in quoteDetails.clientDetails.additionalFields" :key="i">
                        {{ field.value }}
                    </p>
                    <el-input v-if="editMode" v-for="(field, i) in quoteDetails.clientDetails.additionalFields" :key="i" 
                        size="mini" class="mb-1" :style="{ maxWidth:'250px' }"
                        :placeholder="'Client details ' + i" v-model="quoteDetails.clientDetails.additionalFields[i].value"></el-input>
                    </el-input>

                </Col>
                
                <Col span="12">
                    <Row :gutter="20">
                        <Col span="16">
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quoteDetails.refNumber.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Reference number" v-model="quoteDetails.refNumber.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                            
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quoteDetails.createdDate.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Created Date" v-model="quoteDetails.createdDate.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quoteDetails.expiryDate.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Expiry Date" v-model="quoteDetails.expiryDate.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quoteDetails.grandTotal.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="quoteDetails.grandTotal.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col span="8">
                            <p v-if="!editMode" class="text-dark">{{ quoteDetails.refNumber.value }}</p>
                            <el-input v-if="editMode" placeholder="e.g) 001" v-model="quoteDetails.refNumber.value" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark">{{ quoteDetails.createdDate.value | moment('MMM DD YYYY') }}</p>
                            <!-- Edit Created date -->
                            <el-date-picker v-if="editMode" v-model="quoteDetails.createdDate.value" type="datetime" placeholder="e.g) January 1, 2018" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"
                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                            </el-date-picker>

                            <p v-if="!editMode" class="text-dark">{{ quoteDetails.expiryDate.value | moment('MMM DD YYYY') }}</p>
                            <!-- Edit Created date -->
                            <el-date-picker v-if="editMode" v-model="quoteDetails.expiryDate.value" type="datetime" placeholder="e.g) January 7, 2018" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"
                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                            </el-date-picker>

                            <p v-if="!editMode" class="text-dark">{{ quoteDetails.grandTotal.value | currency(quoteDetails.currencyType.currency.symbol) }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+quoteDetails.currencyType.currency.symbol+'5,000.00'" :value="quoteDetails.grandTotal.value | currency(quoteDetails.currencyType.currency.symbol)" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }" disabled></el-input>
                        </Col>
                    </Row>
                </Col>
            </Row>

            <Row>
                <Col span="24">
                    <div v-if="editMode">

                        <div class="float-right mr-2">
                            <el-color-picker class="float-right" v-model="quoteDetails.secondaryColor"></el-color-picker>
                            <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Secondary Color:</span>
                        </div>
                        
                        <div class="float-right mr-2">
                            <el-color-picker class="float-right" v-model="quoteDetails.primaryColor"></el-color-picker>
                            <span class="float-right d-inline-block font-weight-bold mr-2 mt-2">Primary Color:</span>
                        </div>

                        <div class="float-right mr-3">
                            <Loader v-if="isLoadingCurrencies" :loading="isLoadingCurrencies" type="text" :style="{ marginTop:'40px' }">Loading currencies...</Loader>
                            
                            <currencySelector v-if="!isLoadingCurrencies && fetchedCurrencies.length" class="float-right" :style="{maxWidth: '150px'}"
                                :fetchedCurrencies="fetchedCurrencies" :selectedCurrency="quoteDetails.currencyType"
                                @updated="updateCurrencyChanges($event)">
                            </currencySelector>
                            <span v-if="!isLoadingCurrencies && fetchedCurrencies.length" class="float-right d-inline-block font-weight-bold mr-2 mt-2">Currency:</span>
                        </div>

                    </div>
                </Col>
                <Col span="24">
                    <table  class="table table-hover mt-3 mb-0 w-100">
                        <thead :style="'background-color:'+quoteDetails.primaryColor+';'">
                            <tr>
                                <th :colspan="quoteDetails.tableColumns[0].span" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ quoteDetails.tableColumns[0].name }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Service/Product" v-model="quoteDetails.tableColumns[0].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th :colspan="quoteDetails.tableColumns[1].span" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ quoteDetails.tableColumns[1].name }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Quantity" v-model="quoteDetails.tableColumns[1].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th :colspan="quoteDetails.tableColumns[2].span" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ quoteDetails.tableColumns[2].name }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Price" v-model="quoteDetails.tableColumns[2].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th :colspan="quoteDetails.tableColumns[3].span" class="p-2" style="color: #FFFFFF;">
                                    <span v-if="!editMode">{{ quoteDetails.tableColumns[3].name }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) Amount" v-model="quoteDetails.tableColumns[3].name" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </th>
                                <th v-if="editMode" class="p-2" style="color: #FFFFFF;">
                                    <span class="d-block mb-2">Tax</span>
                                </th>
                                <th v-if="editMode" class="p-2" style="color: #FFFFFF;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="quoteDetails.items.length" v-for="(item, i) in quoteDetails.items" :key="i"  :style=" ( (i + 1) % 2 ) ? 'background-color:'+quoteDetails.secondaryColor+';' : ''">
                                <td :colspan="quoteDetails.tableColumns[0].span" class="p-2">
                                
                                    <p v-if="!editMode" class="text-dark mr-5">
                                        <strong>{{ item.name }}</strong>
                                    </p>
                                    <el-input v-if="editMode" :placeholder="'e.g) Item '+ (i+1)" v-model="quoteDetails.items[i].name" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                    
                                    <p v-if="!editMode" class="mr-5">
                                        <span v-if="!editMode">{{ item.description }}</span>
                                    </p>
                                    <el-input v-if="editMode" placeholder="e.g) Item" v-model="quoteDetails.items[i].description" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                
                                </td>
                                <td :colspan="quoteDetails.tableColumns[1].span" class="p-2">
                                    <span v-if="!editMode">{{ item.quantity }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2" 
                                              v-model="quoteDetails.items[i].quantity" 
                                              @input.native="updateSubAndGrandTotal()"
                                              size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td :colspan="quoteDetails.tableColumns[2].span" class="p-2">
                                    <span v-if="!editMode">{{ item.unitPrice | currency(quoteDetails.currencyType.currency.symbol) }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2,500.00" 
                                              v-model="quoteDetails.items[i].unitPrice" 
                                              @input.native="updateSubAndGrandTotal()"
                                              size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td :colspan="quoteDetails.tableColumns[3].span" class="p-2">
                                    <span v-if="!editMode">{{ item.totalPrice | currency(quoteDetails.currencyType.currency.symbol) }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 5,000.00" :value="getItemTotal(quoteDetails.items[i])" size="mini" class="p-1" :style="{ maxWidth:'100%' }" disabled></el-input>
                                </td>
                                <td v-if="editMode" class="p-2">
                                    <Loader v-if="isLoadingTaxes" :loading="isLoadingTaxes" type="text" :style="{ marginTop:'40px' }">Loading taxes...</Loader>
                                    <taxSelector v-if="!isLoadingTaxes && fetchedTaxes.length" 
                                        :fetchedTaxes="fetchedTaxes" :selectedTaxes="quoteDetails.items[i].taxes"
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
                            <tr v-if="!quoteDetails.items.length">
                                <td colspan="9" class="p-2">
                                <Alert show-icon>
                                    No items added
                                    <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                    <template slot="desc">Start adding products/services to your quotation. You will be able to modify your item name, details, quantity, price and any applicable taxes.</template>
                                </Alert>
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
                            <p v-if="!editMode" class="text-dark text-right float-right w-100 mb-2"><strong>{{ quoteDetails.subTotal.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Total" v-model="quoteDetails.subTotal.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" v-for="(calculatedTax , i) in quoteDetails.calculatedTaxes" :key="i" class="text-dark text-right float-right w-100">
                                {{ calculatedTax.name }} ({{ calculatedTax.rate*100 }}%):
                            </p>
                            <el-input v-if="editMode" v-for="(calculatedTax , i) in quoteDetails.calculatedTaxes" :key="i" placeholder="e.g) VAT (12%)" :value="calculatedTax.name + ' (' + calculatedTax.rate*100 + '%)'" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                            
                        </Col>
                        <Col :span="editMode ? '8':'4'">

                            <p v-if="!editMode" class="text-right float-right w-100 mb-2">{{ quoteDetails.subTotal.value | currency(quoteDetails.currencyType.currency.symbol) }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+quoteDetails.currencyType.currency.symbol+'5,000.00'" :value="quoteDetails.subTotal.value | currency(quoteDetails.currencyType.currency.symbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                            <p v-if="!editMode" v-for="(calculatedTax , i) in quoteDetails.calculatedTaxes" :key="i" class="text-right float-right w-100">
                                {{ calculatedTax.amount | currency(quoteDetails.currencyType.currency.symbol) }}
                            </p>
                            <el-input v-if="editMode" v-for="(calculatedTax , i) in quoteDetails.calculatedTaxes" :key="i" placeholder="e.g) 1,500.00" :value="calculatedTax.amount | currency(quoteDetails.currencyType.currency.symbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                        </Col>
                        
                    </Row>

                    <Row :gutter="20" class="doubleUnderline mt-3">

                        <Col :span="editMode ? '16':'20'">
                        
                            <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ quoteDetails.grandTotal.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="quoteDetails.grandTotal.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col :span="editMode ? '8':'4'">

                            <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ quoteDetails.grandTotal.value | currency(quoteDetails.currencyType.currency.symbol) }}</strong></p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+quoteDetails.currencyType.currency.symbol+'5,000.00'" :value="quoteDetails.grandTotal.value | currency(quoteDetails.currencyType.currency.symbol)" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                        
                        </Col>
                        
                    </Row>

                </Col>
            </Row>

            <Row class="mb-5">
                <Col span="24">

                    <h3 v-if="!editMode" class="text-dark mb-2">{{ quoteDetails.notes.title }}</h3>
                    <el-input v-if="editMode" placeholder="E.g) Notes/Payment Information" v-model="quoteDetails.notes.title" size="large" class="mb-2" :style="{ maxWidth:'400px' }"></el-input>

                    <p v-if="!editMode" v-for="(field, i) in quoteDetails.notes.details" :key="i">
                        {{ field.value }}
                    </p>
                    <div v-if="editMode" v-for="(field, i) in quoteDetails.notes.details" :key="i">
                        <el-input 
                            size="mini" class="mb-1" :style="{ maxWidth:'350px' }"
                            :placeholder="'Additional notes ' + i" v-model="quoteDetails.notes.details[i].value"></el-input>
                        </el-input>
                        <br>
                    </div>
                </Col>
            </Row>

            <footer :style="'background-color:'+quoteDetails.primaryColor+';'">
                <div class="mt-1">
                    <span v-if="!editMode">{{ quoteDetails.footer }}</span>
                    <el-input v-if="editMode" :placeholder="'e.g) Terms And Conditions Apply'" v-model="quoteDetails.footer" size="mini" :style="{ width:'50%', margin:'0 auto' }"></el-input>
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
    import getProductsAndServicesModal from './getProductsAndServicesModal.vue';
    import imageUploader from './imageUploader.vue';
    import currencySelector from './currencySelector.vue';
    import taxSelector from './taxSelector.vue';

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            Loader, getProductsAndServicesModal, imageUploader,currencySelector, taxSelector
        },
        props: {
            id: {
                type: Number,
                default: null
            },
            quotation: {
                type: Object,
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
            },
        },
        data(){
            return {
                isSavingQuotation: false,
                isCreatingQuotation: false,
                editMode: false,
                isLoadingTaxes: false,
                isOpenProductsAndServicesModal: false,
                fetchedTaxes: [],
                fetchedCurrencies: [],
                quoteDetails: this.quotation.details || this.quotation,
                _quoteDetailsBeforeChange: {},
                quoteHasChanged: false
            }
        },
        watch: {
            quoteDetails: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    this.quoteHasChanged = this.checkIfQuoteHasChanged(val);
                },
                deep: true
            }
        },
        methods: {
            checkIfQuoteHasChanged: function(updatedQuoteDetails = null){
                
                var currentQuoteDetails = Object.assign({}, updatedQuoteDetails || this.quoteDetails);
                var isNotEqual = !_.isEqual(currentQuoteDetails, this._quoteDetailsBeforeChange);

                console.log('currentQuoteDetails');
                console.log(currentQuoteDetails);
                console.log('quoteDetailsBeforeChange');
                console.log(this._quoteDetailsBeforeChange);
                console.log(isNotEqual);

                return isNotEqual;
            },
            storeOriginalQuotation(){
                console.log('storing _quoteDetailsBeforeChange');
                //  Store the original quotation details
                this._quoteDetailsBeforeChange = _.cloneDeep(this.quoteDetails);
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
                this.quoteDetails.items[i].taxes = newTaxes;
                this.quoteDetails.calculatedTaxes = this.runCalculateTaxes();
                this.updateSubAndGrandTotal();
            },
            updateCurrencyChanges(newCurrency){
                this.quoteDetails.currencyType = newCurrency;

                this.$Notice.success({
                    title: 'Currency changed to ' + newCurrency.country + ' (' + newCurrency.currency.iso.code + ')'
                });
            },
            updateSubAndGrandTotal(){
                console.log('run updateSubAndGrandTotal()');

                //  Re-Calculate the sub total amount
                this.quoteDetails.subTotal.value = this.runGetTotal();

                //  Re-Calculate the grand total amount
                this.quoteDetails.grandTotal.value = this.runGetGrandTotal();

                //  Re-Calculate the taxes
                this.quoteDetails.calculatedTaxes = this.runCalculateTaxes();
            },
            runGetTotal: function(){
                var quoteDetails = this.quoteDetails;
                var itemAmounts = (quoteDetails.items || []).map(item => item.quantity * item.unitPrice);
                var total = itemAmounts.length ? itemAmounts.reduce(this.getSum): 0;

                return total;
            },
            runGetGrandTotal: function(){
                var quoteDetails = this.quoteDetails;
                var taxAmounts = (this.runCalculateTaxes() || []).map(appliedTax => appliedTax.amount);
                var sumOfTaxAmounts = taxAmounts.length ? taxAmounts.reduce(this.getSum): 0;

                return  this.runGetTotal(quoteDetails) + sumOfTaxAmounts;
            },
            getItemTotal: function(item){
                return item.unitPrice * item.quantity
            },
            getSum(total, num) {
                return total + num;
            },
            runCalculateTaxes: function(){
                var quoteDetails = this.quoteDetails;
                var itemTaxAmounts = quoteDetails.items.map(item => {
                        
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
                   result = this.quoteDetails.items.push(productsOrServices[x]);
                }

                //  Re-calculate the taxes
                this.quoteDetails.calculatedTaxes = this.runCalculateTaxes();

                //  Re-Calculate the sub and grand total amount
                this.updateSubAndGrandTotal();
                
                this.$Notice.success({
                    title: productsOrServices.length == 1 ? 'Product added successfully': 'Products added successfully'
                });

                // Close modal
                this.closeModal();
            },
            removeItem: function(index){
                this.quoteDetails.items.splice(index, 1);

                this.$Notice.success({
                    title: 'Item removed sucessfully'
                });
            },
            closeModal(){
                this.isOpenProductsAndServicesModal = !this.isOpenProductsAndServicesModal;
            },
            downloadPDF(download = { preview: false }){
                if(this.id){
                    var showAsPreview = download.preview ? '?preview=1' : '';
                    let routeData = this.$router.resolve({
                            path: '/api/download/quotations/'+this.id + showAsPreview
                        });

                    window.open(routeData.href.replace("#", ""), '_blank');

                }
            },
            saveQuotation(){

                var self = this;

                //  Start loader
                self.isSavingQuotation = true;

                console.log('Attempt to save quotation...');

                console.log( self.quotation );

                //  Form data to send
                let quoteData = { quotation: self.quotation };

                console.log(quoteData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations/'+self.quotation.id, quoteData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingQuotation = false;

                        //  Store the original quotation details
                        self.storeOriginalQuotation();

                        self.quoteHasChanged = self.checkIfQuoteHasChanged(data.details);

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

                console.log( self.quotation );

                //  Form data to send
                let quoteData = { details: self.quotation };

                console.log(quoteData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/quotations?model='+this.modelType+'&modelId='+this.modelId, quoteData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingQuotation = false;

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

            //  Store the original quotation details
            this.storeOriginalQuotation();
        }
    };
  
</script>