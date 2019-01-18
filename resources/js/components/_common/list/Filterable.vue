
<style scoped>
    .filterableCascader >>> .ivu-cascader-menu-item {
        min-width: 160px !important;
    }
</style>

<template>
    <Row :gutter="20" :style="{ margin: '0' }">
        <Col :span="24" :style="{ marginTop: '20px' }">
            <Card :style="{ width: '100%' }">

                <!-- Card header name slot -->
                <div slot="card-title"></div>

                <!-- Card header options -->
                <div slot="extra">

                    <Button type="primary" size="small" class="d-inline-block mr-1">
                        <span>Send</span>
                        <Icon type="ios-send-outline" :size="20" style="margin-top: -4px;"/>
                    </Button>

                    <Dropdown trigger="click" class="d-inline-block mt-1 mr-2">
                        <Button type="primary" size="small">
                            <Icon type="ios-print-outline" :size="16" style="margin-top: -4px;"/>
                            <span>Print / Download</span>
                        </Button>
                        <DropdownMenu slot="list">
                            <DropdownItem>Export As PDF</DropdownItem>
                            <DropdownItem>Export As XML</DropdownItem>
                        </DropdownMenu>
                    </Dropdown>

                </div> 

                <div class="filterable">
                    <Button v-if="!filterCandidates.length" type="primary" @click.native="addFilter" class="mb-2">
                        <Poptip word-wrap width="200" trigger="hover" content="Add filters">
                            <Icon type="md-add" :size="16"></Icon> <span>Filter</span>
                        </Poptip>
                    </Button>

                    <div v-if="filterCandidates.length" class="panel" style=" background: #f8f8f9; padding: 20px 35px 20px 15px;">
                        <div class="panel-heading mb-3">
                            <div class="panel-title">
                                <div class="d-inline">
                                    <span>Order by:</span>
                                    <strong @click="updateOrderDirection">
                                        <span v-if="query.order_direction === 'asc'">
                                            <Button size="small" type="primary"><Icon type="ios-arrow-round-down" /></Button>
                                        </span>
                                        <span v-else>
                                            <Button size="small" type="primary"><Icon type="ios-arrow-round-up" /></Button>
                                        </span>
                                    </strong>
                                    <Select 
                                        size="small"
                                        style="width:100px"
                                        :disabled="loading"
                                        @on-change="updateOrderColumn"
                                        placeholder="Select"
                                        not-found-text="No record found">

                                        <Option 
                                            v-for="(column,index) in orderables"
                                            :value="column.name"
                                            :selected="column && column.name == query.order_column"
                                            :key="index">

                                            {{ column.title }}
                                        
                                        </Option>

                                    </Select>
                                </div>
                                <br>
                                <div class="mt-3">
                                    <span>Customers match</span>
                                    <Select size="small"
                                            style="width:60px"
                                            v-model="query.filter_match"
                                            placeholder="Select Method">
                                        <Option value="and">All</Option>
                                        <Option value="or">Any</Option>
                                    </Select>
                                    <span>of the following:</span>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="filter">
                                <Row :gutter="10" class="filter-item" v-for="(f, index1) in filterCandidates" :key="index1">
                                    <Col span="8" class="filter-column">
                                        <div class="form-group">

                                            <Cascader :data="formatDataForCascader(filterGroups)" 
                                                    placeholder="Select Filter"
                                                    @on-change="selectColumn(f, index1, $event)" 
                                                    :clearable="false"
                                                    class="filterableCascader"
                                                    >
                                            </Cascader>

                                        </div>
                                    </Col>
                                    
                                    <Col span="8" class="filter-operator" v-if="f.column">
                                        <Select placeholder="Select Method"
                                                @on-change="selectOperator(f, index1, $event)" 
                                                :value="JSON.stringify(filterCandidates[index1].operator)"
                                                :key="'operator_'+index1">
                                            <Option 
                                                v-for="(y, index2) in fetchOperators(f)" :key="index1+'_'+index2" 
                                                :value="JSON.stringify(y)"
                                                :selected="f.operator && y.name === f.operator.name">
                                                
                                                {{y.title}}
                                                    
                                            </Option>
                                        </Select>
                                    </Col>
                                    <Col span="8">
                                        <Row :gutter="10">
                                            <template v-if="f.column && f.operator">
                                                <Col span="22" class="filter-full" v-if="f.operator.component === 'single'">
                                                    <Input v-model="f.query_1"/>
                                                </Col>
                                                <template v-if="f.operator.component === 'double'">
                                                    <Col span="11" class="filter-query_1">
                                                        <Input v-model="f.query_1"/>
                                                    </Col>
                                                    <Col span="11" class="filter-query_2">
                                                        <Input v-model="f.query_2"/>
                                                    </Col>
                                                </template>
                                                <template v-if="f.operator.component === 'datetime_1'">
                                                    <Col span="11" class="filter-query_1">
                                                        <Input v-model="f.query_1"/>
                                                    </Col>
                                                    <Col span="11" class="filter-query_2">

                                                        <Dropdown v-model="f.query_2">
                                                            <Button type="primary">
                                                                Filter Type
                                                                <Icon type="ios-arrow-down"></Icon>
                                                            </Button>
                                                            <DropdownMenu slot="list">
                                                                <DropdownItem>hours</DropdownItem>
                                                                <DropdownItem>days</DropdownItem>
                                                                <DropdownItem>months</DropdownItem>
                                                                <DropdownItem>years</DropdownItem>
                                                            </DropdownMenu>
                                                        </Dropdown>

                                                    </Col>
                                                </template>
                                                <template v-if="f.operator.component === 'datetime_2'">
                                                    <Col span="22" class="filter-query_2">

                                                        <Dropdown v-model="f.query_1">
                                                            <Button type="primary">
                                                                Filter Type
                                                                <Icon type="ios-arrow-down"></Icon>
                                                            </Button>
                                                            <DropdownMenu slot="list">
                                                                <DropdownItem value="yesterday">yesterday</DropdownItem>
                                                                <DropdownItem value="today">today</DropdownItem>
                                                                <DropdownItem value="tomorrow">tomorrow</DropdownItem>
                                                                <DropdownItem value="last_month">last month</DropdownItem>
                                                                <DropdownItem value="this_month">this month</DropdownItem>
                                                                <DropdownItem value="next_month">next month</DropdownItem>
                                                                <DropdownItem value="last_year">last year</DropdownItem>
                                                                <DropdownItem value="this_year">this year</DropdownItem>
                                                                <DropdownItem value="next_year">next year</DropdownItem>
                                                            </DropdownMenu>
                                                        </Dropdown>

                                                    </Col>
                                                </template>
                                            </template>
                                            
                                            <Col span="2" class="filter-remove" v-if="f">
                                                <Button type="error" size="small" @click.native="removeFilter(f, index1)" class="pt-1 pb-1">
                                                    <Icon type="ios-trash-outline" :size="18" />
                                                </Button>
                                            </Col>
                                        </Row>
                                    </Col>
                                </Row>
                                <div class="filter-controls">
                                    <ButtonGroup shape="circle">
                                        
                                        <Button type="primary" @click.native="addFilter">
                                            <Poptip word-wrap width="200" trigger="hover" content="Add a new filter">
                                                <Icon type="md-add" :size="16"></Icon>
                                            </Poptip>
                                        </Button>

                                        <Button type="primary" @click.native="resetFilter" v-if="this.appliedFilters.length > 0">
                                            <Poptip word-wrap width="200" trigger="hover" content="Remove all filters">
                                                <Icon type="md-refresh" :size="16"></Icon>
                                            </Poptip>
                                        </Button>
                                        
                                        <Button type="primary" @click.native="applyFilter">
                                            <Poptip word-wrap width="200" trigger="hover" content="Apply filters and get results">
                                                <Icon type="ios-funnel" :size="16"></Icon>
                                            </Poptip>
                                        </Button>          
                                        
                                    </ButtonGroup>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-body">
                            
                            <Loader v-if="loading" :loading="loading" type="text" :style="{ marginTop:'40px' }">Loading...</Loader>
                            
                            <slot v-if="collection.data && collection.data.length"
                                :collection="collection.data"
                                :loading="loading">
                            </slot>
                        
                            <Alert v-if="!collection.data.length && !loading" type="warning" show-icon class="mt-4 mb-4">
                                No Results - We didn't find any records. - <span class="btn btn-link btn-sm p-0" @click="resetFilter">Remove Filters</span>
                            </Alert>

                        </div>
                        <Row :gutter="20" v-if="collection.data && collection.data.length"
                            class="panel-footer mt-3">
                            <Col span="12" class="mb-3">
                                <span>Limit:</span> 
                                <Select 
                                    size="small"
                                    style="width:70px"
                                    v-model="query.limit" 
                                    :disabled="loading" 
                                    @on-change="updateLimit"
                                    placeholder="Limit">

                                    <Option 
                                        v-for="(column,index) in [5,10,20,50,100,200,500]"
                                        :value="column"
                                        :selected="column == query.order_column"
                                        :key="index">

                                        {{ column }}
                                    </Option>

                                </Select>
                                <span> Showing {{ collection.from }} - {{ collection.to }} of {{ collection.total }} entries.</span>
                            </Col>
                            <Col span="12">
                                <ButtonGroup class="float-right">
                                    <Button type="primary" :disabled="!collection.prev_page_url || loading" @click.native="prevPage">
                                            <Icon type="ios-arrow-back"></Icon>
                                            Prev
                                    </Button>
                                    <Button type="primary" :disabled="!collection.next_page_url || loading" @click="nextPage">
                                            Next
                                            <Icon type="ios-arrow-forward"></Icon>
                                    </Button>
                                </ButtonGroup>
                            </Col>
                        </Row>
                    </div>
                </div>

            </Card>
        </Col>
    </Row>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import axios from 'axios'
    import Loader from './../loader/Loader.vue';

    export default {
        props: {
            url: String,
            filterGroups: Array,
            orderables: Array,
            autoShowFilter: {
                type: Boolean,
                deafult: false
            },
            modelType: {
                type: String
            }
        },
        components: { Loader },
        data() {
            return {
                loading: true,
                appliedFilters: [],
                filterCandidates: [],
                query: {
                    order_column: 'created_at',
                    order_direction: 'desc',
                    filter_match: 'and',
                    limit: 10,
                    page: 1
                },
                collection: {
                    data: []
                },

                refreshKey: 0,
            }
        },
        computed: {
            fetchOperators() {
                return (f) => {
                    return this.availableOperators().filter((operator) => {
                        if(f.column && operator.parent.includes(f.column.type)) {
                            return operator
                        }
                    })
                }
            },
        },
        mounted() {
            //  Get the data
            this.fetch();

            //  Show filter if indicated
            if(this.autoShowFilter){
                this.addFilter();
            }
        },
        methods: {
            forceRerender() {
                this.refreshKey += 1;  
            },
            formatDataForCascader(currentFilterGroups){
                
                const filterGroups = (currentFilterGroups || {});
                const cascaderDataObj = [];

                if(filterGroups){
                    
                    for(var x=0; x < filterGroups.length; x++){

                        const filterDataObj = [];

                        for(var y=0; y < filterGroups[x].filters.length; y++){
                            filterDataObj.push({
                                value: JSON.stringify(filterGroups[x].filters[y]),
                                label: filterGroups[x].filters[y].title
                            });
                        }

                        cascaderDataObj.push({
                            value: filterGroups[x].name,
                            label: filterGroups[x].name,
                            children: filterDataObj
                        });
                    }
                }

                return cascaderDataObj;

                console.log('Cascarder Data');
                console.log(cascaderDataObj);
            },
            updateOrderDirection() {
                if(this.query.order_direction === 'desc') {
                    this.query.order_direction = 'asc'
                } else {
                    this.query.order_direction = 'desc'
                }
                this.applyChange()
            },
            updateOrderColumn(currentValue) {
                const value = currentValue
                Vue.set(this.query, 'order_column', value)
                this.applyChange()
            },
            exportToCSV() {
                // next video
            },
            resetFilter() {
                this.appliedFilters = []

                this.filterCandidates = []

                this.forceRerender()

                this.addFilter()
                
                this.query.page = 1
                
                this.applyChange()
            },
            applyFilter() {
                Vue.set(this.$data, 'appliedFilters',
                    JSON.parse(JSON.stringify(this.filterCandidates))
                )
                this.query.page = 1;
                this.applyChange()
            },
            removeFilter(f, i) {
                console.log('remove before');
                console.log(this.filterCandidates);
                this.$delete( this.filterCandidates, i );
                console.log('remove after');
                console.log(this.filterCandidates);
            },
            selectOperator(f, i, currentValue) {
                
                let value = currentValue;

                if(value.length === 0) {
                    Vue.set(this.filterCandidates[i], 'operator', value)
                    return
                }

                let obj = JSON.parse(value)

                Vue.set(this.filterCandidates[i], 'operator', obj)

                this.filterCandidates[i].query_1 = null
                this.filterCandidates[i].query_2 = null

                // set default query

                switch(obj.name) {
                    case 'in_the_past':
                    case 'in_the_next':
                        this.filterCandidates[i].query_1 = 28
                        this.filterCandidates[i].query_2 = 'days'
                        break;
                    case 'in_the_peroid':
                        this.filterCandidates[i].query_1 = 'today'
                        break;
                }
            },
            selectColumn(f, i, currentValue) {
                console.log('currentValue');
                console.log(currentValue[1]);

                let value = (currentValue[1] || []);

                if(value.length === 0) {
                    Vue.set(this.filterCandidates[i], 'column', value)
                    return
                }

                let obj = JSON.parse(value)

                Vue.set(this.filterCandidates[i], 'column', obj)

                // set default operator: todo
                
                switch(obj.type) {
                    case 'numeric':
                        this.initial
                        this.filterCandidates[i].operator = this.availableOperators()[4]
                        this.filterCandidates[i].query_1 = null
                        this.filterCandidates[i].query_2 = null
                        break;
                    case 'string':
                        this.filterCandidates[i].operator = this.availableOperators()[6]
                        this.filterCandidates[i].query_1 = null
                        this.filterCandidates[i].query_2 = null
                        break;
                    case 'datetime':
                        this.filterCandidates[i].operator = this.availableOperators()[9]
                        this.filterCandidates[i].query_1 = 28
                        this.filterCandidates[i].query_2 = 'days'
                        break;
                    case 'counter':
                        this.filterCandidates[i].operator = this.availableOperators()[14]
                        this.filterCandidates[i].query_1 = null
                        this.filterCandidates[i].query_2 = null
                        break;
                }
                
            },
            addFilter() {
                this.filterCandidates.push({
                    column: '',
                    operator: '',
                    query_1: null,
                    query_2: null
                })
            },
            applyChange() {
                this.fetch()
            },
            updateLimit() {
                this.query.page = 1
                this.applyChange()
            },
            prevPage() {
                if(this.collection.prev_page_url) {
                    this.query.page = Number(this.query.page) - 1
                    this.applyChange()
                }
            },
            nextPage() {
                if(this.collection.next_page_url) {
                    this.query.page = Number(this.query.page) + 1
                    this.applyChange()
                }
            },
            getFilters() {
                const f = {}

                this.appliedFilters.forEach((filter, i) => {
                    f[`f[${i}][column]`] = filter.column.name
                    f[`f[${i}][operator]`] = filter.operator.name
                    f[`f[${i}][query_1]`] = filter.query_1
                    f[`f[${i}][query_2]`] = filter.query_2
                })

                return f
            },
            fetch() {
                this.loading = true
                const filters = this.getFilters()

                const params = {
                    ...filters,
                    ...this.query
                }
                axios.get(this.url, {params: params})
                    .then((res) => {

                        //  Get the pagination data and store as collection
                        let collection = res.data;

                        //  Get the collection data from the pagination data
                        let collectionData = collection.data;

                        Vue.set(this.$data, 'collection', collection);

                        this.query.page = collection.current_page;

                    })
                    .catch((error) => {

                    })
                    .finally(() => {
                        this.loading = false
                    })
            },
            availableOperators() {
                return [
                    {title: 'equal to', name: 'equal_to', parent: ['numeric', 'string'], component: 'single'},
                    {title: 'not equal to', name: 'not_equal_to', parent: ['numeric', 'string'], component: 'single'},
                    {title: 'less than', name: 'less_than', parent: ['numeric'], component: 'single'},
                    {title: 'greater than', name: 'greater_than', parent: ['numeric'], component: 'single'},

                    {title: 'between', name: 'between', parent: ['numeric'], component: 'double'},
                    {title: 'not between', name: 'not_between', parent: ['numeric'], component: 'double'},

                    {title: 'contains', name: 'contains', parent: ['string'], component: 'single'},
                    {title: 'starts with', name: 'starts_with', parent: ['string'], component: 'single'},
                    {title: 'ends with', name: 'ends_with', parent: ['string'], component: 'single'},

                    {title: 'in the past', name: 'in_the_past', parent: ['datetime'], component: 'datetime_1'},
                    {title: 'in the next', name: 'in_the_next', parent: ['datetime'], component: 'datetime_1'},
                    {title: 'in the peroid', name: 'in_the_peroid', parent: ['datetime'], component: 'datetime_2'},

                    {title: 'equal to', name: 'equal_to_count', parent: ['counter'], component: 'single'},
                    {title: 'not equal to', name: 'not_equal_to_count', parent: ['counter'], component: 'single'},
                    {title: 'less than', name: 'less_than_count', parent: ['counter'], component: 'single'},
                    {title: 'greater than', name: 'greater_than_count', parent: ['counter'], component: 'single'},
                ]
            }
        }
    }
</script>
