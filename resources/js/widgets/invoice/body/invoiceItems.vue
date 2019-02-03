<template>

    <div>
        
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

        <!-- 
            MODAL TO GET PRODUCTS AND SERVICES
        -->
        <getProductsAndServicesModal
            v-if="isOpenProductsAndServicesModal" 
            @visibility="isOpenProductsAndServicesModal = $event"
            @selected="addProductOrService($event)">
        </getProductsAndServicesModal>

    </div>

</template>


<script type="text/javascript">

    /*  Selectors  */
    import taxSelector from './../selectors/taxSelector.vue';
    
    /*  Switches  */
    import editModeSwitch from './../switches/editModeSwitch.vue';

    /*  Modals  */
    import getProductsAndServicesModal from './../modals/getProductsAndServicesModal.vue';

    export default {
        props: {
            invoice: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            },
        },
        components: { taxSelector, editModeSwitch, getProductsAndServicesModal },
        data(){
            return {
                isOpenProductsAndServicesModal: false,

                localInvoice: this.invoice,
                localEditMode: this.editMode,
                primaryColor: (this.invoice.colors || {})[0],
                secondaryColor: (this.invoice.colors || {})[1],
                tableColumns: this.invoice.table_columns,
                currencySymbol: ((this.invoice.currency_type || {}).currency || {}).symbol,
                isLoadingTaxes: false,
        
                //  Resources
                fetchedTaxes: [],
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {
                    
                    //  Update the local invoice value
                    this.localInvoice = val;

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
            updateTaxChanges(newTaxes, i){
                this.localInvoice.items[i].taxes = newTaxes;
                this.localInvoice.calculated_taxes = this.runCalculateTaxes();
                this.updateSubAndGrandTotal();
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
            },
            removeItem: function(index){
                this.localInvoice.items.splice(index, 1);

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
