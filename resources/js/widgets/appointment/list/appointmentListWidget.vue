<template>

    <filterableList :tableColumnsData="tableColumns" :filterableData="filterable" 
                    :requestUpdate="requestUpdate" @generateURL="generateURL()">
    </filterableList>

</template>
<script type="text/javascript">

    import moment from 'moment';
    
    /*  Statuses   */
    import AppointmentStatusTag from './../../../components/_common/statuses/AppointmentStatusTag.vue';  

    import filterableList from './../../../components/_common/list/filterableList.vue';

    export default {
        components: { AppointmentStatusTag, filterableList },
        data() {
            return {

                moment: moment,

                status: this.$route.query.status,

                requestUpdate: 0,

                // Table columns 
                tableColumns: [
                    {
                        width: 70,
                        title: 'ID',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', (params.row.id));
                        }
                    },
                    {
                        width: 270,
                        title: 'Subject',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', params.row.subject || '...');
                        }
                    },
                    {
                        width: 180,
                        title: 'Client',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', params.row.client.model_type == 'user' 
                                        ? params.row.client.full_name
                                        : params.row.client.name
                                    );
                        }
                    },
                    {
                        title: 'Status',
                        sortable: true,
                        render: (h, params) => {
                            return h(AppointmentStatusTag, {
                                props: {
                                    appointment: params.row
                                }
                            })
                        }
                    },
                    {
                        width: 140,
                        title: 'Meeting Date',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', this.formatDate(params.row.start_date));
                        }
                    },
                    {
                        width: 100,
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
                                            this.$router.push({ name: 'show-appointment', params: { id: params.row.id } });
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
            //  Watch for changes on the status
            '$route.query.status': function (status) {
                
                //  Update the status query
                this.status = status;

                //  Request an update
                this.requestUpdate = this.requestUpdate + 1;

            }
        },
        methods: {
            generateURL: function () {
                
                //  Get the status e.g) client, supplier, e.t.c
                var status = this.status ? 'status='+this.status : '';

                //  Additional data to eager load along with each appointment found
                var connections = (status ? '&' : '') + 'connections=client';

                //  Url generated for the filterable Api call  
                var url = '/api/appointments?' + status + connections;

                //  Assign url to the filterable object
                this.filterable.url = url;
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
