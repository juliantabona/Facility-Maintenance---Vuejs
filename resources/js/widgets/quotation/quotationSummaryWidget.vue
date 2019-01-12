<style scoped>
    .doubleUnderline{
        padding: 8px 0px;
        border-bottom: 3px solid #dee1e2;
        border-top: 1px solid #dee1e2;
    }
</style>

<template>
    <div id="testcase">
        <Card :style="{ width: '100%' }">

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

                    <!-- Save Changes Button -->
                    <Button type="success" size="small" class="float-right mb-2 ml-3">
                        <span>Save Changes</span>
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

                    <h1 v-if="!editMode" class="text-dark text-right" style="font-size: 35px;">{{ quotation.heading }}</h1>
                    <el-input v-if="editMode" placeholder="Quotation heading" v-model="quotation.heading" size="large" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <p v-if="!editMode" class="mt-3 text-dark text-right"><strong>{{ quotation.companyDetails.name }}</strong></p>
                    <el-input v-if="editMode" placeholder="Company name" v-model="quotation.companyDetails.name" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <p v-if="!editMode" class="text-right">{{ quotation.companyDetails.email }}</p>
                    <el-input v-if="editMode" placeholder="Company email" v-model="quotation.companyDetails.email" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                    <p v-if="!editMode" class="text-right">{{ quotation.companyDetails.phone }}</p>
                    <el-input v-if="editMode" placeholder="Company tel/phone" v-model="quotation.companyDetails.phone" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                    <br />
                    <p v-if="!editMode" v-for="(field, i) in quotation.companyDetails.additionalFields" :key="i" class="text-right">
                        {{ field.value }}
                    </p>
                    <el-input v-if="editMode" v-for="(field, i) in quotation.companyDetails.additionalFields" :key="i" 
                              size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"
                              :placeholder="'Company details ' + i" v-model="quotation.companyDetails.additionalFields[i].value"></el-input>
                    </el-input>
                </Col>
            </Row>

            <Divider dashed class="mt-3 mb-3" />

            <Row>
                <Col span="12" class="pl-2">
                    <h3 v-if="!editMode" class="text-dark mb-3">{{ quotation.quoteTo }}:</h3>
                    <el-input v-if="editMode" placeholder="Quotation heading" v-model="quotation.quoteTo" size="large" class="mb-2" :style="{ maxWidth:'250px' }"></el-input>
                    

                    <p v-if="!editMode" class="mt-3 text-dark"><strong>{{ quotation.clientDetails.name }}</strong></p>
                    <el-input v-if="editMode" placeholder="Company name" v-model="quotation.clientDetails.name" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

                    <p v-if="!editMode">{{ quotation.clientDetails.email }}</p>
                    <el-input v-if="editMode" placeholder="Company email" v-model="quotation.clientDetails.email" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

                    <p v-if="!editMode">{{ quotation.clientDetails.phone }}</p>
                    <el-input v-if="editMode" placeholder="Company tel/phone" v-model="quotation.clientDetails.phone" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>
                    <br />
                    <p v-if="!editMode" v-for="(field, i) in quotation.clientDetails.additionalFields" :key="i">
                        {{ field.value }}
                    </p>
                    <el-input v-if="editMode" v-for="(field, i) in quotation.clientDetails.additionalFields" :key="i" 
                        size="mini" class="mb-1" :style="{ maxWidth:'250px' }"
                        :placeholder="'Client details ' + i" v-model="quotation.clientDetails.additionalFields[i].value"></el-input>
                    </el-input>

                </Col>
                
                <Col span="12">
                    <Row :gutter="20">
                        <Col span="16">
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quotation.refNumber.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Reference number" v-model="quotation.refNumber.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>
                            
                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quotation.createdDate.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Created Date" v-model="quotation.createdDate.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quotation.expiryDate.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Expiry Date" v-model="quotation.expiryDate.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark text-right"><strong>{{ quotation.grandTotal.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="quotation.grandTotal.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col span="8">
                            <p v-if="!editMode" class="text-dark">{{ quotation.refNumber.value }}</p>
                            <el-input v-if="editMode" placeholder="e.g) 001" v-model="quotation.refNumber.value" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"></el-input>

                            <p v-if="!editMode" class="text-dark">{{ quotation.createdDate.value | moment('MMM DD YYYY') }}</p>
                            <!-- Edit Created date -->
                            <el-date-picker v-if="editMode" v-model="quotation.createdDate.value" type="datetime" placeholder="e.g) January 1, 2018" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"
                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                            </el-date-picker>

                            <p v-if="!editMode" class="text-dark">{{ quotation.expiryDate.value | moment('MMM DD YYYY') }}</p>
                            <!-- Edit Created date -->
                            <el-date-picker v-if="editMode" v-model="quotation.expiryDate.value" type="datetime" placeholder="e.g) January 7, 2018" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }"
                                format="MMM dd yyyy" value-format="yyyy-MM-dd">
                            </el-date-picker>

                            <p v-if="!editMode" class="text-dark">{{ quoteGrandTotal | currency }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+quotation.currencySymbol+'5,000.00'" :value="quoteGrandTotal | currency" size="mini" class="mb-2" :style="{ maxWidth:'155px', float:'right' }" disabled></el-input>
                        </Col>
                    </Row>
                </Col>
            </Row>

            <Row>
                <Col span="24">
                    <table  class="table table-hover mt-3 mb-0 w-100">
                        <thead style="background-color: #2d8cf0;">
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
                                <th v-if="editMode" class="p-2" style="color: #FFFFFF;">
                                    <span class="d-block mb-2">Tax</span>
                                </th>
                                <th v-if="editMode" class="p-2" style="color: #FFFFFF;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, i) in quotation.items" :key="i">
                                <td :colspan="quotation.tableColumns[0].span" class="p-2">
                                
                                    <p v-if="!editMode" class="text-dark mr-5">
                                        <strong>{{ item.name }}</strong>
                                    </p>
                                    <el-input v-if="editMode" :placeholder="'e.g) Item '+ (i+1)" v-model="quotation.items[i].name" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                    
                                    <p v-if="!editMode" class="mr-5">
                                        <span v-if="!editMode">{{ item.description }}</span>
                                    </p>
                                    <el-input v-if="editMode" placeholder="e.g) Item" v-model="quotation.items[i].description" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                
                                </td>
                                <td :colspan="quotation.tableColumns[1].span" class="p-2">
                                    <span v-if="!editMode">{{ item.quantity }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2" v-model="quotation.items[i].quantity" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td :colspan="quotation.tableColumns[2].span" class="p-2">
                                    <span v-if="!editMode">{{ item.unitPrice }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 2,500.00" v-model="quotation.items[i].unitPrice" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                </td>
                                <td :colspan="quotation.tableColumns[3].span" class="p-2">
                                    <span v-if="!editMode">{{ item.totalPrice | currency }}</span>
                                    <el-input v-if="editMode" placeholder="e.g) 5,000.00" :value="getItemTotal(quotation.items[i])" size="mini" class="p-1" :style="{ maxWidth:'100%' }" disabled></el-input>
                                </td>
                                <td v-if="editMode" class="p-2">
                                    <taxSelector v-if="!isLoadingTaxes" 
                                        :fetchedTaxes="fetchedTaxes" :selectedTaxes="quotation.items[i].taxes"
                                        @updated="quotation.items[i].taxes = $event">
                                    </taxSelector>
                                </td>
                                <td v-if="editMode" class="p-2">
                                    <Poptip
                                      confirm
                                      title="Are you sure you want to remove this item?"
                                      ok-text="Yes"
                                      cancel-text="No"
                                      @on-ok="removeItem(i)">
                                      <Icon type="ios-trash-outline" class="mr-2" size="20"/>
                                  </Poptip>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="10" class="p-2">
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
            
            <Divider dashed class="mt-0 mb-4" />

            <Row>
                <Col span="12" offset="12" class="pr-4">
                    <Row :gutter="20">
                        <Col :span="editMode ? '16':'20'">
                            <p v-if="!editMode" class="text-dark text-right float-right w-100 mb-2"><strong>{{ quotation.subTotal.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Total" v-model="quotation.subTotal.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                            <p v-if="!editMode" v-for="(calculatedTax , i) in calculatedTaxes" :key="i" class="text-dark text-right float-right w-100">
                                {{ calculatedTax.name }} ({{ calculatedTax.rate*100 }}%):
                            </p>
                            <el-input v-if="editMode" v-for="(calculatedTax , i) in calculatedTaxes" :key="i" placeholder="e.g) VAT (12%)" :value="calculatedTax.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                            
                        </Col>
                        <Col :span="editMode ? '8':'4'">

                            <p v-if="!editMode" class="text-right float-right w-100 mb-2">{{ quoteTotal | currency }}</p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+quotation.currencySymbol+'5,000.00'" :value="quoteTotal | currency" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                            <p v-if="!editMode" v-for="(calculatedTax , i) in calculatedTaxes" :key="i" class="text-right float-right w-100">
                                {{ calculatedTax.amount | currency }}
                            </p>
                            <el-input v-if="editMode" v-for="(calculatedTax , i) in calculatedTaxes" :key="i" placeholder="e.g) 1,500.00" :value="calculatedTax.amount | currency" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>

                        </Col>
                        
                    </Row>

                    <Row :gutter="20" class="doubleUnderline mt-3">

                        <Col :span="editMode ? '16':'20'">
                        
                            <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ quotation.grandTotal.name }}:</strong></p>
                            <el-input v-if="editMode" placeholder="e.g) Grand Total" v-model="quotation.grandTotal.name" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

                        </Col>
                        <Col :span="editMode ? '8':'4'">

                            <p v-if="!editMode" class="text-dark text-right float-right w-100"><strong>{{ quoteGrandTotal | currency }}</strong></p>
                            <el-input v-if="editMode" :placeholder="'e.g) '+quotation.currencySymbol+'5,000.00'" :value="quoteGrandTotal | currency" size="mini" class="mb-2" :style="{ maxWidth:'250px', float:'right' }" disabled></el-input>
                        
                        </Col>
                        
                    </Row>

                </Col>
            </Row>

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

            <Divider dashed class="mt-3 mb-2" />

            <Row>
                <Col span="24">
                    <p class="text-center">Other Services Hosting (For 1GB)-  P2, 200.00/year Maintenance and Support-  180.00/hour</p>
                </Col>
            </Row>

        </Card>
    </div>

</template>

<script>

    import imageUploader from './imageUploader.vue';
    import taxSelector from './taxSelector.vue';

    //  jsPDF used for HTML/CSS to PDF Conversion
    import jsPDF from 'jspdf';
    import html2canvas from 'html2canvas';

    export default {
        components: { 
            imageUploader, taxSelector
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
            }
        },
        data(){
            return {
                editMode: false,
                isLoadingTaxes: false,
                fetchedTaxes: [
                    {
                        id:'1',
                        name:'VAT',
                        description:'Default Tax',
                        rate:'0.12'
                    },
                    {
                        id:'2',
                        name:'VAT 2',
                        description:'Second Tax',
                        rate:'0.25'
                    },
                    {
                        id:'3',
                        name:'VAT 3',
                        description:'Third Tax',
                        rate:'0.50'
                    }
                ],
                calculatedTaxes: this.runCalculateTaxes(this.quotation),
                quoteTotal: this.runGetTotal(this.quotation),
                quoteGrandTotal: this.quotation.calculatedTaxes,
            }
        },
        watch: {
            //  When the quotation data changes
            quotation: {
                handler(updatedQuotation) {
                    
                    this.quoteTotal = this.runGetTotal(updatedQuotation);
                    this.quoteGrandTotal = this.runGetGrandTotal(updatedQuotation);
                        
                    //  Re-calculate taxes
                    this.calculatedTaxes = this.runCalculateTaxes(updatedQuotation);
                    
                },
                deep: true
            },
        },
        methods: {
            removeItem: function(index){
                this.quotation.items.splice(index, 1);

                this.$Notice.success({
                    title: 'Item removed sucessfully'
                });
            },
            runCalculateTaxes: function(quotation){
                
                var itemTaxAmounts = quotation.items.map(item => {
                        
                        var x, collection = [];

                        for(x = 0; x < item.taxes.length; x++){
                            collection.push({
                                id: parseInt(item.taxes[x].id),
                                name: item.taxes[x].name,
                                description: item.taxes[x].description,
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
                        description: itemTaxAmounts[x].description,
                        rate: itemTaxAmounts[x].rate,
                        amount: ((combinedTaxAmounts[ itemTaxAmounts[x].id ] || {}).amount || 0) + itemTaxAmounts[x].amount,
                    };
                }

                var filtered = combinedTaxAmounts.filter(function (el) {
                    return el != null;
                });

                return filtered;

            },
            getItemTotal: function(item){
                return item.unitPrice * item.quantity
            },
            runGetTotal: function(quotation){
                var itemAmounts = (quotation.items || []).map(item => item.quantity * item.unitPrice);
                var total = itemAmounts.length ? itemAmounts.reduce(this.getSum): 0;

                return total;
            },
            runGetGrandTotal: function(quotation){
                var taxAmounts = (this.runCalculateTaxes(quotation) || []).map(appliedTax => appliedTax.amount);
                var sumOfTaxAmounts = taxAmounts.length ? taxAmounts.reduce(this.getSum): 0;

                return  this.runGetTotal(quotation) + sumOfTaxAmounts;
            },
            getSum(total, num) {
                return total + num;
            },
            downloadPDF(){
                if(this.id){
                 
                    let routeData = this.$router.resolve({
                            path: '/api/download/quotations/'+this.id
                        });

                    window.open(routeData.href.replace("#", ""), '_blank');

                }
            }
        }
    };
  
</script>