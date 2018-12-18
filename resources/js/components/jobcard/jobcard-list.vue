<template>
    <Row :gutter="20" :style="{ margin: '0' }">
        <Col :span="24" :style="{ marginTop: '20px' }">
            <filterable v-bind="filterable" :key="renderKey">

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
    import priorityTag from './../priority/priority-tag.vue';
    import statusSummaryTag from './../jobcard/status-summary-tag.vue';
    import jobcardListExpandWidget from './../../widgets/jobcard/jobcard-list-expand.vue';
    export default {
        components: { Filterable, priorityTag, statusSummaryTag, jobcardListExpandWidget },
        props:{
            lifecycleStep: {
                type: Number,
                default: null
            },
            modelType: {
                type: String,
                default: ''
            },
            modelId: {
                type: Number,
                default: null
            }
        },
        data() {
            return {
                renderKey: 0,
                tableColumns: [
                    {
                        type: 'expand',
                        width: 50,
                        render: (h, params) => {
                            return h(jobcardListExpandWidget, {
                                props: {
                                    row: params.row
                                }
                            })
                        }
                    },
                    {
                        width: 80,
                        title: 'ID',
                        key: 'id',
                        sortable: true,
                    },
                    {
                        width: 400,
                        title: 'Title',
                        key: 'title',
                        sortable: true
                    },
                    {
                        width: 120,
                        title: 'Due',
                        key: 'deadline',
                        sortable: true
                    },
                    {
                        width: 120,
                        title: 'Priority',
                        sortable: true,
                        render: (h, params) => {
                            return h(priorityTag, {
                                props: {
                                    priorities: params.row.priorities,
                                    showEditBtn: false
                                }
                            })
                        }
                    },
                    {
                        title: 'Status',
                        sortable: true,
                        render: (h, params) => {
                            return h(statusSummaryTag, {
                                props: {
                                    //  Make sure we have a status summary otherwise just send an empty object
                                    statusSummary: (params.row.statusSummary)
                                }
                            })
                        }
                    },
                    {
                        title: 'Action',
                        width: 150,
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
                                            this.$router.push({ name: 'show-jobcard', params: { id: params.row.id } });
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
                            name: '- With Suppliers',
                            filters: [
                                {title: 'Count', name: 'suppliersList.count', type: 'counter'},
                                {title: 'Id', name: 'suppliersList.id', type: 'numeric'},
                                {title: 'Name', name: 'suppliersList.name', type: 'string'},
                                {title: 'City', name: 'suppliersList.city', type: 'string'},
                                {title: 'State Or Region', name: 'suppliersList.state_or_region', type: 'string'},
                                {title: 'Address', name: 'suppliersList.address', type: 'string'},
                                {title: 'Industry', name: 'suppliersList.industry', type: 'string'},
                                {title: 'Type', name: 'suppliersList.type', type: 'string'},
                                {title: 'Website Link', name: 'suppliersList.website_link', type: 'string'},
                                {title: 'Phone ext', name: 'suppliersList.phone_ext', type: 'numeric'},
                                {title: 'Phone Number', name: 'suppliersList.phone_num', type: 'numeric'},
                                {title: 'Email', name: 'suppliersList.email', type: 'string'},
                                {title: 'Created At', name: 'suppliersList.created_at', type: 'datetime'},
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
                var modelType = this.modelType ? 'model='+this.modelType : ''; 
                var modelId = this.modelId ? '&&modelId='+this.modelId : ''; 

                var lifecycleStep = this.lifecycleStep ? '&&step='+this.lifecycleStep : ''; 
                var connections = '&&connections=categories,priorities,costcenters';
                
                var url = '/api/jobcards?'+modelType + modelId + lifecycleStep + connections;

                this.filterable.url = url;
            }
        },
        watch: {
            'lifecycleStep'() {
                this.getURL();
                this.renderKey++;
            },
            'modelType'() {
                this.getURL();
                this.renderKey++;
            }
        },
        created () {
            this.getURL();
        }
    }
</script>
