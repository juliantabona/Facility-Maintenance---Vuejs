<template>

    <div>

        <!-- Loader -->
        <Loader v-if="isLoadingCustomers" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading customers...</Loader>
        
        <!-- No customers message -->
        <Alert v-if="!isLoadingCustomers && !localCustomers" type="info" :style="{ maxWidth: '250px' }" show-icon>No customers found</Alert>
        
        <!-- Customer Filter & Create Button -->
        <Card v-if="!isLoadingCustomers && localCustomers" class="mb-3">
            <Row :gutter="20">
                <Col :span="6">
                    <Select v-model="selectedCustomerStatuses" filterable multiple placeholder="Search customer...">
                        <Option v-for="customer in localCustomers" :value="customer.id" :key="customer.id">
                            {{ customer.name }}
                        </Option>
                    </Select>
                </Col>
                <Col :span="7">
                    <DatePicker type="date" placeholder="From"></DatePicker>
                </Col>
                <Col :span="7">
                    <DatePicker type="date" placeholder="To"></DatePicker>
                </Col>
                <Col :span="4">
                    <!-- Add Customer Button -->
                    <div class="clearfix">
                        <basicButton @click.native="$router.push({ name:'create-customer' })" 
                                    size="large" class="float-right">
                                    <span>+ Add Customer</span>
                        </basicButton>
                    </div>
                </Col>
            </Row>
        </Card>

        <!-- Table Column Checkboxes -->
        <CheckboxGroup  
            v-if="!isLoadingCustomers && localCustomers" 
            v-model="tableColumnsToShowByDefault" class="mb-3">
            <Checkbox label="ID"></Checkbox>
            <Checkbox label="Name"></Checkbox>
            <Checkbox label="Type"></Checkbox>
            <Checkbox label="Email"></Checkbox>
            <Checkbox label="Phone"></Checkbox>
            <Checkbox label="Date"></Checkbox>
        </CheckboxGroup>

        <!-- Store Customers -->
        <Table v-if="!isLoadingCustomers && localCustomers" :columns="dynamicColumns" :data="localCustomers"></Table>

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
                localCustomers: null,
                isLoadingCustomers: false,
                localCustomersUrl: this.customersUrl || this.$route.params.customersUrl,
                tableColumnsToShowByDefault:['ID', 'Name', 'Type', 'Email', 'Phone', 'Date'],

                //  Filters
                selectedCustomerStatuses:[],
 
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