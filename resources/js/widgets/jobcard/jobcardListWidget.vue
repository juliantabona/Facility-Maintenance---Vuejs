<template>

    <filterableList :tableColumnsData="tableColumns" :filterableData="filterable">
        
        <!-- Heading -->
        <slot name="heading">
            <h5><Icon type="ios-copy-outline" :size="24" class="mr-2"/><span>Jobcards</span></h5>
        </slot>

    </filterableList>

</template>
<script type="text/javascript">

    import filterableList from './../../components/_common/list/filterableList.vue';
    import priorityTag from './../../components/_common/priority/priorityTag.vue';
    import lifecycleStatusTag from './../../components/jobcard/lifecycle/lifecycleStatusTag.vue';
    import jobcardListExpandWidget from './jobcardListExpandWidget.vue';

    export default {
        components: { filterableList, priorityTag, lifecycleStatusTag, jobcardListExpandWidget },
        data() {
            return {

                // Used get jobcards in stages e.g) Open, Pending, closed, e.t.c
                lifecycleStep: this.$route.query.step,

                // Table columns 
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
                            return h(lifecycleStatusTag, {
                                props: {
                                    //  Make sure we have a status summary otherwise just send an empty object
                                    jobcard: (params.row),
                                    showText: false
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

                // Filterable data
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
            generateURL: function () {
                
                //  The lifecycleStep: Filter and retrieve jobcards currently at this stage 
                var lifecycleStep = this.lifecycleStep ? '&&step='+this.lifecycleStep : '';
                
                //  Additional data to eager load along with each jobcard found
                var connections = 'connections=priority,categories,costcenters';
                
                //  Url generated for the filterable Api call  
                var url = '/api/jobcards?'+ connections + lifecycleStep;

                //  Assign url to the filterable object
                this.filterable.url = url;
            }
        },
        watch: {
            //  When the lifecycle step changes
            '$route.query.step'() {
                //  Update the local lifecycleStep
                this.lifecycleStep = this.$route.query.step;
            }
        },
        created () {
            this.generateURL();
        }
    }
</script>
