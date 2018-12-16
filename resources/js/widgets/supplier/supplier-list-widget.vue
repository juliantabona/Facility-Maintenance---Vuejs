<template>
    <Row :gutter="20" :style="{ margin: '0' }">

        <Col :span="24" :style="{ marginTop: '0px' }">
            <filterable v-bind="filterable">

              <Table border size="small" 
                slot-scope="{ collection, loading }" :columns="tableColumns" :data="collection"
                :loading="loading">
              </Table>
              
            </filterable>
        </Col>
    </Row>

</template>
<script type="text/javascript">
    import contractorListExpandWidget from './../../widgets/supplier/supplier-list-expand.vue';
    import Filterable from './../../components/Filterable.vue'
    export default {
        components: { Filterable, contractorListExpandWidget },
        data() {
            return {
                tableColumns: [
                    {
                        type: 'expand',
                        width: 50,
                        render: (h, params) => {
                            return h(contractorListExpandWidget, {
                                props: {
                                    row: params.row
                                }
                            })
                        }
                    },
                    {
                        title: 'ID',
                        key: 'id',
                        width: 100,
                        sortable: true
                    },
                    {
                        title: 'Logo',
                        key: 'logo_url',
                        width: 100,
                        render: (h, params) => {
                            return h('div', [
                                h('img', {
                                    style: {
                                        width: '40px !important',
                                        height: '40px !important',
                                        marginTop: '5px'
                                    },
                                    class: 'img-thumbnail mb-2 p-2 rounded rounded-circle',
                                    attrs: {
                                        src: params.row.logo_url
                                    },
                                })
                            ]);
                        }
                    },
                    {
                        title: 'Name',
                        key: 'name',
                        sortable: true
                    },
                    {
                        title: 'Email',
                        key: 'email',
                        sortable: true
                    },
                    {
                        title: 'Phone',
                        key: 'phone',
                        sortable: true
                    },
                    {
                        title: 'Submission Date',
                        key: 'submission_date',
                        sortable: true
                    },
                    {
                        title: 'Action',
                        key: 'action',
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
                                            this.$router.push({ name: 'show-contractor', params: { id: params.row.id } });
                                        }
                                    }
                                }, 'View')
                            ]);
                        }
                    }
                ],
                filterable: {
                    url: '/api/jobcards/'+this.$route.params.id+'/contractors'
                }
            }
        },
        methods: {
            navigateTo(id) {
                this.$router.push({ name: 'show-jobcard', params: { id: id } });
            }
        }
    }
</script>
