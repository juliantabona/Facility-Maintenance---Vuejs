<template>

    <filterableList :tableColumnsData="tableColumns" :filterableData="filterable" 
                    :requestUpdate="requestUpdate" @generateURL="generateURL()">
    </filterableList>

</template>
<script type="text/javascript">

    import moment from 'moment';
    import filterableList from './../../../components/_common/list/filterableList.vue';

    export default {
        components: { filterableList },
        data() {
            return {

                moment: moment,

                type: this.$route.query.status,

                requestUpdate: 0,

                // Table columns 
                tableColumns: [
                    {
                        width: 60,
                        title: 'ID',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', (params.row.id));
                        }
                    },
                    {
                        width: 180,
                        title: 'Client',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', params.row.model_type == 'user' 
                                        ? params.row.full_name
                                        : params.row.name
                                    );
                        }
                    },
                    {
                        width: 120,
                        title: 'City',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', params.row.city || '...');
                        }
                    },
                    {
                        width: 200,
                        title: 'Email',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', params.row.email || '...');
                        }
                    },
                    {
                        width: 240,
                        title: 'Phone',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', params.row.phone_list || '...');
                        }
                    },
                    {
                        width: 140,
                        title: 'Created Date',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', this.formatDate(params.row.created_date));
                        }
                    },
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
                                            this.$router.push({ name: 'show-company', params: { id: params.row.id } });
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
                        {title: 'Reference No', name: 'reference_no_value'},
                        {title: 'Grand Total', name: 'grand_total_value'},
                        {title: 'Created Date', name: 'created_date_value'},
                        {title: 'Due Date', name: 'expiry_date_value'},                        
                        {title: 'Created At', name: 'created_at'},
                    ],
                    filterGroups: [
                        {
                            name: 'Invoice',
                            filters: [
                                {title: 'Id', name: 'id', type: 'numeric'},
                                {title: 'Reference No', name: 'reference_no_value', type: 'numeric'},
                                {title: 'Grand Total', name: 'grand_total_value', type: 'numeric'},    
                                {title: 'Created Date', name: 'created_date_value', type: 'datetime'},
                                {title: 'Due Date', name: 'expiry_date_value', type: 'datetime'},
                                {title: 'Published Date', name: 'created_at', type: 'datetime'},
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
                        }
                    ]
                }
            }
        },
        watch: {
            //  Watch for changes on the type
            '$route.query.status': function (status) {
                
                //  Update the type query
                this.type = status;

                //  Request an update
                this.requestUpdate = this.requestUpdate + 1;

            }
        },
        methods: {
            generateURL: function () {
                
                //  Get the status e.g) client, supplier, e.t.c
                var type = this.type ? 'type='+this.type : '';

                //  Additional data to eager load along with each company found
                var connections = (type ? '&' : '') + 'connections=phones';

                //  Url generated for the filterable Api call  
                var url = '/api/companies?' + type + connections;

                //  Assign url to the filterable object
                this.filterable.url = url;
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            formatDate(date) {
                return this.moment(date).format('MMM DD YYYY');
            }
        },
        created () {
            this.generateURL();
        }
    }
</script>
