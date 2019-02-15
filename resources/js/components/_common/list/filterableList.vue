<template>

    <filterable v-bind="filterable" :key="renderKey">
        
        <!-- Insert the card title as follows -->
        <slot name="card-title">
            <strong>Quotations</strong>
        </slot>

        <!-- Get the collection data and loading state, then render the filterable table -->
        <Table v-if="!loading" border size="small" slot-scope="{ collection, loading }" :columns="tableColumns" :data="collection"></Table>
        
    </filterable>

</template>
<script type="text/javascript">

    import Filterable from './Filterable.vue';

    export default {
        props: {
            tableColumnsData: {
                type: Array,
                default: null
            },
            filterableData: {
                type: Object,
                default: null
            },
            requestUpdate: {
                type: Number,
                default: 0
            }
        },
        components: { Filterable },
        data() {
            return {
                // Used to re-render the filterable table
                renderKey: 0,

                //  store is a global custom class found in store.js
                //  We use it to access data accessible globally
                //  In this case we need to access the data
                //  allocationType to know whether the user
                //  wants company/branch related data
                allocationType: store.allocationType,

                // Table columns 
                tableColumns: this.tableColumnsData,

                // Filterable data
                filterable: this.filterableData
            }
        },
        methods: {
            generateURL: function(){
                
                //  Generate a new url
                this.$emit('generateURL');
            },
            renderFilterableComponent: function(){
                
                //  Re-render the filterable component
                this.renderKey++;
            },
            determineAllocationType: function(){
                //  The allocationType: Whether to get company/branch specific data
                var allocationType = this.allocationType ? '&&allocation='+this.allocationType : ''; 

                //  Attach the allocationType to the Url generated for the filterable Api call
                //  This will allow the Api to either return company/branch related data
                this.filterable.url = this.filterable.url + allocationType;
            },
            reset(){
                //  Generate a new url
                this.generateURL();

                //  Whether we want to get the company/branch related data
                this.determineAllocationType();

                this.renderFilterableComponent();
            }
        },
        watch: {
            requestUpdate: {
                handler: function (val, oldVal) {

                    // Reset
                    this.reset();

                }
            }
        },
        created () {
            //  Generate a new url
            this.generateURL();

            //  Whether we want to get the company/branch related data
            this.determineAllocationType();

            //  Listen for global changes on the allocation type. 
            //  The reource is used to reflect which data we want to get.
            //  It may be the users company/branch specific data.

            var self = this;

            Event.$on('updatedAllocationType', function(updatedAllocationType){
                //  Get the updated allocationType e.g) company/branch
                self.allocationType = updatedAllocationType;
                
                self.reset();
            });
        },
        beforeDestroy() {
            //  Stop listening for global changes on the allocation type.
            Event.$off('updatedAllocationType');
        }
    }
</script>
