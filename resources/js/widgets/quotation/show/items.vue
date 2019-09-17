<template>

    <div>
        
        <table  class="table table-hover mt-3 mb-0 w-100">
            <thead :style="'background-color:'+primaryColor+';'">
                <tr>
                    <th colspan="4" class="p-2" style="color: #FFFFFF;white-space: nowrap">
                        <span v-if="!editMode">{{ tableColumns[0] || '___' }}</span>
                        <el-input v-if="editMode" placeholder="e.g) Service/Product" v-model="tableColumns[0]" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                    </th>
                    <th colspan="1" class="p-2" style="color: #FFFFFF;white-space: nowrap">
                        <span v-if="!editMode">{{ tableColumns[1] || '___' }}</span>
                        <el-input v-if="editMode" placeholder="e.g) Quantity" v-model="tableColumns[1]" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                    </th>
                    <th colspan="1" class="p-2" style="color: #FFFFFF;white-space: nowrap">
                        <span v-if="!editMode">{{ tableColumns[2] || '___' }}</span>
                        <el-input v-if="editMode" placeholder="e.g) Price" v-model="tableColumns[2]" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                    </th>
                    <th colspan="1" class="p-2" style="color: #FFFFFF;white-space: nowrap">
                        <span v-if="!editMode">{{ tableColumns[3] || '___' }}</span>
                        <el-input v-if="editMode" placeholder="e.g) Amount" v-model="tableColumns[3]" size="large" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                    </th>
                    <th v-if="editMode" class="p-2" style="color: #FFFFFF;white-space: nowrap">
                        <span class="d-block mb-2">Tax</span>
                    </th>
                    <th v-if="editMode" class="p-2" style="color: #FFFFFF;"></th>
                </tr>
            </thead>
            <tbody>
                <!-- No list items alert -->
                <tr v-if="!(localQuotation.items || {}).length">
                    <td colspan="9" class="p-2">
                        <Alert show-icon>
                            No items added
                            <Icon type="ios-bulb-outline" slot="icon"></Icon>
                            <template slot="desc">Start adding products/services to your quotation. You will be able to modify your item name, details, quantity, price and any applicable taxes.</template>
                        </Alert>
                    </td>
                </tr>

                <tr v-if="(localQuotation.items || {}).length" v-for="(item, i) in localQuotation.items" :key="i"  :style=" ( (i + 1) % 2 ) ? 'background-color:'+secondaryColor+';' : ''">
                    <td colspan="4" class="p-2">
                        <Row>
                            <Col v-if="editMode" :span="2">
                                <Poptip content="Edit the main product?"trigger="hover" placement="right-start">
                                    <Icon type="ios-create-outline" class="float-left mr-1" size="20" @click="openModalToEditItem(item)"/>
                                </Poptip>
                            </Col>

                            <Col :span="editMode ? 22 : 24">
                                <p v-if="!editMode" class="text-dark mr-5">
                                    <strong>{{ item.name || '___' }}</strong>
                                </p>
                                <el-input v-if="editMode" :placeholder="'Item '+ (i+1) + ' name'" v-model="localQuotation.items[i].name" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                                
                                <p v-if="!editMode" class="mr-5">
                                    <span v-if="!editMode">{{ item.description }}</span>
                                </p>
                                <el-input v-if="editMode" placeholder="Item description" v-model="localQuotation.items[i].description" size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                            </Col>
                        </Row>
                    </td>
                    <td colspan="1" class="p-2">
                        <span v-if="!editMode">{{ item.quantity || '___' }}</span>
                        <el-input v-if="editMode" placeholder="e.g) 2" 
                                type="number" min="1"
                                v-model="localQuotation.items[i].quantity" 
                                @input.native="updateSubAndGrandTotal()"
                                size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                    </td>
                    <td colspan="1" class="p-2">
                        <span v-if="!editMode">{{ item.unit_price | currency(currencySymbol) || '___' }}</span>
                        <el-input v-if="editMode" placeholder="e.g) 2,500.00" 
                                type="text"  @keypress.native="isNumber($event)" :maxlength="10"
                                v-model="localQuotation.items[i].unit_price" 
                                @input.native="updateSubAndGrandTotal()"
                                size="mini" class="p-1" :style="{ maxWidth:'100%' }"></el-input>
                    </td>
                    <td colspan="1" class="p-2">
                        <span v-if="!editMode">{{ getItemTotal(localQuotation.items[i]) | currency(currencySymbol) || '___' }}</span>
                        <el-input v-if="editMode" placeholder="e.g) 5,000.00" :value="getItemTotal(localQuotation.items[i]) | currency(currencySymbol)" size="mini" class="p-1" :style="{ maxWidth:'100%' }" disabled></el-input>
                    </td>
                    <td v-if="editMode" class="p-2">
                        <Loader v-if="isLoadingTaxes" :loading="isLoadingTaxes" type="text" :style="{ marginTop:'40px' }">Loading taxes...</Loader>
                        <taxSelector v-if="!isLoadingTaxes && fetchedTaxes.length" 
                            :fetchedTaxes="fetchedTaxes" :selectedTaxes="localQuotation.items[i].taxes"
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

                <tr v-if="editMode">
                    <td colspan="4" class="p-2">
                        <productOrServiceSelector 
                            :key="productRenderKey"
                            :clearable="true"
                            @updated="addProduct($event)">
                        </productOrServiceSelector>
                    </td>
                    <td colspan="1" class="p-2">
                        <span class="d-block text text-center">.....</span>
                    </td>
                    <td colspan="1" class="p-2">
                        <span class="d-block text text-center">.....</span>
                    </td>
                    <td colspan="1" class="p-2">
                        <span class="d-block text text-center">.....</span>
                    </td>
                    <td colspan="1" class="p-2"></td>
                    <td colspan="1" class="p-2"></td>
                </tr>

                <!-- Add item button -->
                <tr v-if="editMode">
                    <td colspan="10" class="p-2">
                        <el-tooltip class="ml-auto mr-auto mb-3 d-block item" effect="dark" content="Add New Product/Service" placement="top-start">
                            <el-button @click="openModalToCreateItem()" type="primary" icon="el-icon-plus" circle></el-button>
                            <span>Add an item</span>
                        </el-tooltip>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- 
            MODAL TO GET PRODUCTS AND SERVICES
        -->
        <createOrEditProductOrServiceModal
            v-if="isOpenProductsAndServicesModal" 
            :editableProduct="editableProduct"
            @visibility="isOpenProductsAndServicesModal = $event"
            @updated="addUpdatedProduct($event)"
            @created="addProduct($event, true)">
        </createOrEditProductOrServiceModal>

    </div>

</template>


<script type="text/javascript">

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';

    /*  Selectors  */
    import taxSelector from './../../../components/_common/selectors/taxSelector.vue';
    import productOrServiceSelector from './../../../components/_common/selectors/productOrServiceSelector.vue';
    
    /*  Switches  */
    import editModeSwitch from './../../../components/_common/switches/editModeSwitch.vue';

    /*  Modals  */
    import createOrEditProductOrServiceModal from './../../../components/_common/modals/createOrEditProductOrServiceModal.vue';

    export default {
        props: {
            quotation: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            },
        },
        components: { Loader, taxSelector, editModeSwitch, productOrServiceSelector, createOrEditProductOrServiceModal },
        data(){
            return {
                editableProduct: null,
                isOpenProductsAndServicesModal: false,

                localQuotation: this.quotation,
                localEditMode: this.editMode,
                primaryColor: (this.quotation.colors || {})[0],
                secondaryColor: (this.quotation.colors || {})[1],
                tableColumns: this.quotation.table_columns,
                currencySymbol: ((this.quotation.currency_type || {}).currency || {}).symbol,
                
                isLoadingTaxes: false,

                productRenderKey: 0,
        
                //  Resources
                fetchedTaxes: [],
            }
        },
        watch: {

            //  Watch for changes on the quotation
            quotation: {
                handler: function (val, oldVal) {
                    
                    //  Update the local quotation value
                    this.localQuotation = val;

                    //  Update the primary color shortcut
                    this.primaryColor = (val.colors || {})[0];

                    //  Update the secondary color shortcut
                    this.secondaryColor = (val.colors || {})[1];

                    //  Update the table columns shortcut
                    this.tableColumns = val.table_columns;

                    //  Update the currency symbol shortcut
                    this.currencySymbol = ((val.currency_type || {}).currency || {}).symbol;

                },
                deep: true
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {

                    //  Update the edit mode value
                    this.localEditMode = val;

                }
            }
        },
        methods: {
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            },
            openModalToCreateItem(){
                this.editableProduct = null;
                this.isOpenProductsAndServicesModal = true;
            },
            openModalToEditItem(product){
                this.editableProduct = product;
                this.isOpenProductsAndServicesModal = true;
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
            updateTaxChanges(newTaxes, i){
                this.localQuotation.items[i].taxes = newTaxes;
                this.localQuotation.calculated_taxes = this.runCalculateTaxes();
                this.updateSubAndGrandTotal();
            },
            updateSubAndGrandTotal(){
                console.log('run updateSubAndGrandTotal()');

                //  Re-Calculate the sub total amount
                this.localQuotation.sub_total_value = this.runGetTotal();

                //  Re-Calculate the grand total amount
                this.localQuotation.grand_total = this.runGetGrandTotal();

                //  Re-Calculate the taxes
                this.localQuotation.calculated_taxes = this.runCalculateTaxes();
            },
            runGetTotal: function(){
                var itemAmounts = (this.localQuotation.items || []).map(item => item.quantity * item.unit_price);
                var total = itemAmounts.length ? itemAmounts.reduce(this.getSum): 0;

                return total;
            },
            runGetGrandTotal: function(){
                var taxAmounts = (this.runCalculateTaxes() || []).map(appliedTax => appliedTax.amount);
                var sumOfTaxAmounts = taxAmounts.length ? taxAmounts.reduce(this.getSum): 0;

                return  this.runGetTotal() + sumOfTaxAmounts;
            },
            getItemTotal: function(item){
                return item.unit_price * item.quantity
            },
            getSum(total, num) {
                return total + num;
            },
            runCalculateTaxes: function(){
                
                var itemTaxAmounts = this.localQuotation.items.map(item => {
                        
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
            addUpdatedProduct(product){
                for(var x=0; x < this.localQuotation.items.length; x++){
                    if( this.localQuotation.items[x].id == this.editableProduct.id){
                        this.$set(this.localQuotation.items, x, this.buildItem(product));
                    }
                }

                this.productRenderKey = this.productRenderKey + 1;
            },
            addProduct(product, renderProductList = false){
                
                console.log('Adding new products/services to table');
                console.log(product);

                if(renderProductList){
                    this.productRenderKey = this.productRenderKey + 1;
                }

                if(!this.localQuotation.items){
                    this.$set(this.localQuotation, 'items', []);
                }

                this.localQuotation.items.push(this.buildItem(product));

                //  Re-calculate the taxes
                this.localQuotation.calculated_taxes = this.runCalculateTaxes();

                //  Re-Calculate the sub and grand total amount
                this.updateSubAndGrandTotal();
                
                this.$Notice.success({
                    title: product.length == 1 ? 'Product added successfully': 'Products added successfully'
                });
            },
            buildItem(item){
                return {
                        id: item.id,
                        name: item.name,
                        description: item.description,
                        type: item.type,
                        taxes: item.taxes,
                        purchasePrice: item.cost_per_item,
                        unit_sale_price: item.unit_sale_price,
                        unit_price: item.unit_price,
                        quantity: 1
                    }
            },
            removeItem: function(index){
                this.localQuotation.items.splice(index, 1);

                this.$Notice.success({
                    title: 'Item removed sucessfully'
                });
            } 
        },
        created(){
            //  Get the taxes
            this.fetchTaxes();
        }
    }

</script>
