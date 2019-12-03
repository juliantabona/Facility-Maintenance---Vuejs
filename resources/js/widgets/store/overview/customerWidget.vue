<template>

    <div>

        <!-- Manage Columns Button / Add Customer Button -->
        <Row :gutter="12">

            <Col :span="24">
                <div class="clearfix">
                    
                    <!-- Add Customer Button -->
                    <basicButton @click.native="$router.push({ name:'create-Customer' })" 
                                size="large" class="float-right">
                                <span>Create Customer</span>
                    </basicButton>

                </div>
            </Col>

        </Row>

        <el-tabs value="first">
            
            <!-- Search / Filter Tools -->
            <el-tab-pane label="Search / Filter" name="first">
        
                <Card class="mb-3">
                    <Row :gutter="20">
                        <Col :span="6">
                            <Select v-model="selectedCustomers" filterable multiple placeholder="Search customer...">
                                <Option v-for="customer in localCustomers" :value="customer.id" :key="customer.id">
                                    {{ customer.name }}
                                </Option>
                            </Select>
                        </Col>
                        <Col :span="6">
                            <Select v-model="selectedCustomerTypes" placeholder="Filter customers">

                                <OptionGroup label="Customer Type">
                                    <Option v-for="item in ['Individual', 'Company']" :value="item" :key="item">{{ item }}</Option>
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
                            <!-- Refresh Orders Button -->
                            <div class="clearfix">
                                <basicButton @click.native="fetchCustomers()" 
                                            size="default" class="float-right mr-4"
                                            :disabled="isLoadingCustomers">
                                            <Icon type="ios-refresh" :size="20"/>
                                            <span>Refresh</span>
                                </basicButton>
                            </div>
                        </Col>
                    </Row>
                </Card>

            </el-tab-pane>
            
            <!-- Manage Table Columns Tools -->
            <el-tab-pane label="Manage Columns" name="second">

                <Card class="mb-3">
                    <Row :gutter="12">

                        <Col :span="24" class="clearfix">
                            <span class="font-weight-bold d-block mt-2 mb-3">Select Columns to show:</span>
                        </Col>

                        <Col :span="24" class="clearfix">
                                    
                            <!-- Table Column Checkboxes -->
                            <CheckboxGroup  
                                v-model="tableColumnsToShowByDefault" class="mb-3">
                                <Checkbox label="ID"></Checkbox>
                                <Checkbox label="Name"></Checkbox>
                                <Checkbox label="Type"></Checkbox>
                                <Checkbox label="Email"></Checkbox>
                                <Checkbox label="Phone"></Checkbox>
                                <Checkbox label="Date"></Checkbox>
                            </CheckboxGroup>

                        </Col>

                    </Row>
                </Card>

            </el-tab-pane>

        </el-tabs>

        <!-- Store Orders -->
        <Table :columns="dynamicColumns" :data="localCustomers"
               no-data-text="No customers found"
               :loading="isLoadingCustomers"
               class="order-table">
        </Table>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /* Expand Table Row  */
    import orderRowDropDown from './../../order/show/main.vue';  

    import moment from 'moment';

    export default {
        props: {
            customersUrl: {
                type: String,
                default: null
            }
        }, 
        components: { 
            basicButton, Loader, orderRowDropDown
        },
        data(){
            return {
                moment: moment,

                //  Customers
                localCustomers: [],
                selectedCustomers: [],
                selectedCustomerTypes: [],
                isLoadingCustomers: false,
                localCustomersUrl: this.customersUrl || this.$route.params.customersUrl,
                tableColumnsToShowByDefault:['ID', 'Name', 'Type', 'Email', 'Phone', 'Date'],
 
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
                        return h(orderRowDropDown, {
                            props: {
                                customer: params.row
                            },
                            on: {
                                updated: (customer) => {

                                    //  Update the row data
                                    this.$set(this.localCustomers, params.index, customer);

                                    //  Automatically open the expandable data
                                    this.$set(this.localCustomers[params.index], '_expanded', true);
                                    
                                }
                            }
                        })
                    }
                });
                
                //  Customer ID
                if(this.tableColumnsToShowByDefault.includes('ID')){
                    allowedColumns.push(
                    {
                        title: 'ID',
                        render: (h, params) => {
                            return h('span', (params.row.id));
                        }
                    });
                }
                
                //  Customer Name
                if(this.tableColumnsToShowByDefault.includes('Name')){
                    allowedColumns.push(
                    {
                        title: 'Name',
                        render: (h, params) => {
                            return h('Poptip', {
                                style: {
                                    width: '100%',
                                    textAlign:'left'
                                },
                                props: {
                                    width: 280,
                                    wordWrap: true,
                                    trigger:'hover',
                                    placement: 'top-start',
                                    content: 'Name: '+(params.row.name) || '...'
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text']
                                }, (params.row.name) || '...')
                            ])
                        }
                    });
                }
                
                //  Customer Type
                if(this.tableColumnsToShowByDefault.includes('Type')){
                    allowedColumns.push(
                    {
                        title: 'Type',
                        render: (h, params) => {
                            return h('span', {
                                class: ['cut-text']
                            },(
                                params.row.type
                            ));
                        }
                    });
                }
                
                //  Customer Email
                if(this.tableColumnsToShowByDefault.includes('Email')){
                    allowedColumns.push(
                    {
                        title: 'Email',
                        render: (h, params) => {
                            return h('Poptip', {
                                style: {
                                    width: '100%',
                                    textAlign:'left'
                                },
                                props: {
                                    width: 280,
                                    wordWrap: true,
                                    trigger:'hover',
                                    placement: 'top-start',
                                    content: 'Email: '+((params.row.default_email || {}).email  || '...')
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text']
                                }, ((params.row.default_email || {}).email) || '...')
                            ])
                        }
                    })
                }
                
                //  Customer Phones
                if(this.tableColumnsToShowByDefault.includes('Phone')){
                    allowedColumns.push(
                    {
                        title: 'Phone',
                        render: (h, params) => {
                            return h('Poptip', {
                                style: {
                                    width: '100%',
                                    textAlign:'left'
                                },
                                props: {
                                    width: 280,
                                    wordWrap: true,
                                    trigger:'hover',
                                    placement: 'top-start',
                                    content: 'Phone: '+((params.row.default_mobile || {}).full_number  || '...')
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text']
                                }, ((params.row.default_mobile || {}).full_number  || '...'))
                            ])
                        }
                    })
                }
                
                //  Date
                if(this.tableColumnsToShowByDefault.includes('Date')){
                    allowedColumns.push(
                    {
                        title: 'Date',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', {
                                class: ['cut-text']
                            }, this.formatDate(params.row.created_at.date));
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
                                        this.$router.push({ name: 'show-customer', params: { id: params.row.id } });
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
            formatDate(date) {
                return this.moment(date).format('MMM DD YYYY');
            },
            fetchCustomers() {

                if( this.localCustomersUrl ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingCustomers = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting customers...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', this.localCustomersUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingCustomers = false;

                            //  Customer the customer data
                            self.localCustomers = ((data || {})._embedded || {}).contacts || [];

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingCustomers = false;

                            //  Console log Error Location
                            console.log('resources/js/widgets/store/overview/customerWidget.vue - Error getting customers...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){
            //  Fetch the customers
            this.fetchCustomers();
        }
    };
  
</script>