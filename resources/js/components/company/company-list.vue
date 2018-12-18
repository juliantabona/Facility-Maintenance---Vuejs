<template>
    <Row :gutter="20" :style="{ margin: '0' }">
        <Col :span="24" :style="{ marginTop: '20px' }">
            <filterable v-bind="filterable">

              <Table border size="small" 
                slot-scope="{collection, loading}" :columns="tableColumns" :data="collection"
                :loading="loading">
              </Table>
              
            </filterable>
        </Col>
    </Row>

</template>
<script type="text/javascript">
    import Filterable from './../Filterable.vue';
    import companyListExpandWidget from './../../widgets/company/company-list-expand.vue';
    export default {
        components: { Filterable, companyListExpandWidget },
        props:{
            modelType: {
                type: String,
                default: 'branch'   //  branch, company
            },
            type: {
                type: String,
                default: 'client'   //  client, contractor
            }
        },
        data() {
            return {
                tableColumns: [
                    {
                        type: 'expand',
                        width: 50,
                        render: (h, params) => {
                            return h(companyListExpandWidget, {
                                props: {
                                    row: params.row,
                                    modelType: this.modelType,
                                    type: this.type
                                }
                            })
                        }
                    },
                    {
                        width: 80,
                        title: 'ID',
                        key: 'id',
                        sortable: true
                    },
                    {
                        width: 350,
                        title: 'Name',
                        key: 'name',
                        sortable: true
                    },
                    {
                        width: 120,
                        title: 'City',
                        key: 'city',
                        sortable: true
                    },
                    {
                        width: 230,
                        title: 'Email',
                        key: 'email',
                        sortable: true
                    },
                    {
                        width: 150,
                        title: 'Phone',
                        key: 'phone',
                        sortable: true
                    },
                    {
                        title: 'Action',
                        key: 'action',
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
                                            console.log(params);
                                            this.$router.push({ name: 'show-'+this.type, params: { id: params.row.id } });
                                        }
                                    }
                                }, 'View')
                            ]);
                        }
                    }
                ],
                filterable: {
                    url: null,
                    orderables: [
                        {title: 'Id', name: 'id'},
                        {title: 'Title', name: 'title'},
                        {title: 'Start Date', name: 'start_date'},
                        {title: 'End Date', name: 'end_date'},
                        {title: 'Created At', name: 'created_at'},
                    ],
                    filterGroups: [
                        {
                            name: 'Jobcard',
                            filters: [
                                {title: 'Id', name: 'id', type: 'numeric'},
                                {title: 'Title', name: 'title', type: 'string'},
                                {title: 'Description', name: 'description', type: 'string'},
                                {title: 'Start Date', name: 'start_date', type: 'datetime'},
                                {title: 'End Date', name: 'end_date', type: 'datetime'},
                                {title: 'Created At', name: 'created_at', type: 'datetime'},
                            ]
                        },
                        {
                            name: '- With Client',
                            filters: [
                                {title: 'Id', name: 'client.id', type: 'numeric'},
                                {title: 'Name', name: 'client.name', type: 'string'},
                                {title: 'City', name: 'client.city', type: 'string'},
                                {title: 'State Or Region', name: 'client.state_or_region', type: 'string'},
                                {title: 'Address', name: 'client.address', type: 'string'},
                                {title: 'Industry', name: 'client.industry', type: 'string'},
                                {title: 'Type', name: 'client.type', type: 'string'},
                                {title: 'Website Link', name: 'client.website_link', type: 'string'},
                                {title: 'Phone ext', name: 'client.phone_ext', type: 'numeric'},
                                {title: 'Phone Number', name: 'client.phone_num', type: 'numeric'},
                                {title: 'Email', name: 'client.email', type: 'string'},
                                {title: 'Created At', name: 'client.created_at', type: 'datetime'},
                            ]
                        },
                        {
                            name: '- With Contractors',
                            filters: [
                                {title: 'Count', name: 'contractorsList.count', type: 'counter'},
                                {title: 'Id', name: 'contractorsList.id', type: 'numeric'},
                                {title: 'Name', name: 'contractorsList.name', type: 'string'},
                                {title: 'City', name: 'contractorsList.city', type: 'string'},
                                {title: 'State Or Region', name: 'contractorsList.state_or_region', type: 'string'},
                                {title: 'Address', name: 'contractorsList.address', type: 'string'},
                                {title: 'Industry', name: 'contractorsList.industry', type: 'string'},
                                {title: 'Type', name: 'contractorsList.type', type: 'string'},
                                {title: 'Website Link', name: 'contractorsList.website_link', type: 'string'},
                                {title: 'Phone ext', name: 'contractorsList.phone_ext', type: 'numeric'},
                                {title: 'Phone Number', name: 'contractorsList.phone_num', type: 'numeric'},
                                {title: 'Email', name: 'contractorsList.email', type: 'string'},
                                {title: 'Created At', name: 'contractorsList.created_at', type: 'datetime'},
                            ]
                        },
                        {
                            name: '- With Documents',
                            filters: [
                                {title: 'Count', name: 'documents.count', type: 'counter'},
                                {title: 'Id', name: 'documents.id', type: 'numeric'},
                                {title: 'Name', name: 'documents.name', type: 'string'},
                                {title: 'Type', name: 'documents.type', type: 'string'},
                                {title: 'Mime', name: 'documents.mime', type: 'string'},
                                {title: 'Size', name: 'documents.size', type: 'numeric'},
                                {title: 'Link', name: 'documents.url', type: 'string'},
                                {title: 'Created At', name: 'documents.created_at', type: 'datetime'},
                            ]
                        }
                    ]
                }
            }
        },
        methods: {
            getURL: function () {
                var connections = 'connections=';
                
                var url = '/api/companies?model='+this.modelType+'&&type='+this.type+'&&'+connections;

                this.filterable.url = url;
            }
        },
        created () {
            this.getURL();
        }
    }
</script>
