<style scoped>

    .roundedShape{
        width: 60px;
        height: 60px;
        bproduct: 1px solid #c5c5c5;
        padding: 3px;
        margin: -15px 10px 0 0;
        bproduct-radius: 100%;
        display:inline-block;
    }

    .product-table >>> .product-status{
        width: 10px;
        height: 10px;
        background: #b3b3b3;
        bproduct-radius: 10px;
    }

    .product-table >>> .product-open-status{
        background:#2d8cf0;
    }

    .product-table >>> .product-in-progress-status{
        background: #e8c207;
    }

    .product-table >>> .product-fail-status{
        background:#ff0000;
    }

    .product-table >>> .product-completed-status{
        background:#24d806;
    }

</style>

<template>

    <div>

        <!-- Loader -->
        <Loader v-if="isLoadingProducts" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading products...</Loader>
        
        <!-- No products message -->
        <Alert v-if="!isLoadingProducts && !localProducts" type="info" :style="{ maxWidth: '250px' }" show-icon>No products found</Alert>
        
        <Card v-if="!isLoadingProducts && localProducts" class="mb-3">
            <Row :gutter="20">
                <Col :span="6">
                    <Select v-model="selectedProductStatuses" filterable multiple placeholder="Search product...">
                        <Option v-for="product in localProducts" :value="product.id" :key="product.id">
                            {{ product.name }} {{ product.type ? '('+product.type+')' : '' }}
                        </Option>
                    </Select>
                </Col>
                <Col :span="6">
                    <Select v-model="selectedProductStatuses" filterable multiple placeholder="Filter by">

                        <OptionGroup label="Product Type">
                            <Option v-for="item in ['Physical', 'Service', 'Event', 'Ticket', 'Donation', 'Membership']" 
                                    :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                        <OptionGroup label="Product Sale">
                            <Option v-for="item in ['On Sale', 'Not On Sale']" :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                        <OptionGroup label="Product Stock">
                            <Option v-for="item in ['Has Stock', 'Has No Stock']" :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                        <OptionGroup label="Product Variations">
                            <Option v-for="item in ['Has Variations', 'Has No Variations']" :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                        <OptionGroup label="Product Visibility">
                            <Option v-for="item in ['Visible In Store', 'Not Visible In Store']" :value="item" :key="item">{{ item }}</Option>
                        </OptionGroup>

                    </Select>
                </Col>
                <Col :span="4">
                    <DatePicker type="date" placeholder="From"></DatePicker>
                </Col>
                <Col :span="4">
                    <DatePicker type="date" placeholder="To"></DatePicker>
                </Col>
                <Col :span="4">
                    <!-- Add Product Button -->
                    <div class="clearfix">
                        <basicButton @click.native="$router.push({ name:'create-product' })" 
                                    size="large" class="float-right">
                                    <span>+ Add Product</span>
                        </basicButton>
                    </div>
                </Col>
            </Row>
        </Card>

        <CheckboxGroup
            v-if="!isLoadingProducts && localProducts" 
            v-model="tableColumnsToShow" class="mb-3">
            <Checkbox label="Image"></Checkbox>
            <Checkbox label="Name"></Checkbox>
            <Checkbox label="Type"></Checkbox>
            <Checkbox label="Unit Price"></Checkbox>
            <Checkbox label="Sale Price"></Checkbox>
            <Checkbox label="Stock"></Checkbox>
        </CheckboxGroup>

        <!-- Store Products -->
        <Table v-if="!isLoadingProducts && localProducts" 
                :columns="dynamicColumns" :data="localProducts"
                class="product-table">
        </Table>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /* Expand Table Row  */
    import productRowDropDown from './productRowDropDown.vue';  

    import moment from 'moment';

    export default {
        props: {
            storeId: {
                type: Number,
                default: null
            }
        }, 
        components: { 
            basicButton, Loader, productRowDropDown
        },
        data(){
            return {
                moment: moment,

                localStoreId: this.storeId || this.$route.params.storeId,
                isLoadingStore: false,

                //  Products
                localProducts: null,
                isLoadingProducts: false,

                selectedProductStatuses:[],

                tableColumnsToShow:['Image', 'Name', 'Type', 'Unit Price', 'Sale Price', 'Stock']
 
            }
        },
        computed:{
            dynamicColumns(status){ 
                var allowedColumns = [];
                
                //  Expand Arrow
                allowedColumns.push(
                {
                    type: 'expand',
                    width: 30,
                    render: (h, params) => {
                        return h(productRowDropDown, {
                            props: {
                                product: params.row
                            }
                        })
                    }
                });

                //  Product Name
                if(this.tableColumnsToShow.includes('Image')){
                    //  Action
                    allowedColumns.push(
                    {
                        width: 100,
                        title: 'Image',
                        align: 'center',
                        render: (h, params) => {
                            return h('div', [
                                h('img', {
                                    attrs: {
                                        src: (params.row.primary_image || {}).url,
                                    }, 
                                    src: (params.row.primary_image || {}).url,
                                    style: {
                                        maxWidth: '80px',
                                        maxHeight: '80px',
                                        padding: '10px'
                                    },
                                    class:['w-100'],
                                    on: {
                                        click: () => {
                                            this.$router.push({ name: 'show-product', params: { id: params.row.id } });
                                        }
                                    }
                                })
                            ]);
                        }
                    });
                }
                //  Product Name
                if(this.tableColumnsToShow.includes('Name')){
                    allowedColumns.push(
                    {
                        width: 220,
                        title: 'Name',
                        render: (h, params) => {
                            return h('span', (params.row.name));
                        }
                    });
                }
                
                //  Product Type
                if(this.tableColumnsToShow.includes('Type')){
                    allowedColumns.push(
                    {
                        width: 150,
                        title: 'Type',
                        render: (h, params) => {
                            return h('span', (params.row.type));
                        }
                    });
                }
                
                //  Grand Total
                if(this.tableColumnsToShow.includes('Unit Price')){
                    allowedColumns.push(
                    {
                        width: 120,
                        title: 'Price',
                        sortable: true,
                        render: (h, params) => {
                            var unit_regular_price = (params.row.unit_regular_price || 0) 
                            var symbol = ((params.row.currency_type || {}).currency || {}).symbol || '';
                            return h('span', this.formatPrice(unit_regular_price, symbol) );
                        }
                    });
                }
                
                //  Grand Total
                if(this.tableColumnsToShow.includes('Sale Price')){
                    allowedColumns.push(
                    {
                        width: 120,
                        title: 'Sale Price',
                        sortable: true,
                        render: (h, params) => {
                            var unit_sale_price = (params.row.unit_sale_price || 0) 
                            var symbol = ((params.row.unit_sale_price || {}).currency || {}).symbol || '';
                            return h('span', this.formatPrice(unit_sale_price, symbol) );
                        }
                    });
                }

                //  Product Type
                if(this.tableColumnsToShow.includes('Stock')){
                    allowedColumns.push(
                    {
                        width: 80,
                        title: 'Stock',
                        render: (h, params) => {
                            return h('span', 
                                (params.row.allow_stock_management) ?
                                    (params.row.stock_quantity) :
                                        'N/A'
                            );
                        }
                    });
                }

                //  Action
                allowedColumns.push(
                {
                    title: 'Action',
                    align: 'center',
                    render: (h, params) => {
                        return h('div', [
                            h('Button', {
                                props: {
                                    type: 'primary',
                                    size: 'small'
                                },
                                style: {
                                    marginRight: '5px'
                                },
                                on: {
                                    click: () => {
                                        this.$router.push({ name: 'show-product', params: { id: params.row.id } });
                                    }
                                }
                            }, 'View')
                        ]);
                    }
                });

                return allowedColumns;
            }
        },
        methods: {
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return (symbol ? symbol : '') + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            formatDate(date) {
                return this.moment(date).format('MMM DD YYYY');
            },
            fetchProducts() {

                if( this.localStoreId ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingProducts = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting products...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/products?storeId='+this.localStoreId)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingProducts = false;

                            //  Product the product data
                            self.localProducts = data.data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingProducts = false;

                            //  Console log Error Location
                            console.log('dashboard/product/show/main.vue - Error getting products...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){
            //  Fetch the products
            this.fetchProducts();
        }
    };
  
</script>