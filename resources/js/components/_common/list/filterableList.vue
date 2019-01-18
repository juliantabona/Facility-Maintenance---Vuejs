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
            'tableColumnsData': {
                type: Array,
                default: null
            },
            'filterableData': {
                type: Object,
                default: null
            }
        },
        components: { Filterable },
        data() {
            return {
                // Used to re-render the filterable table
                renderKey: 0,

                // Used to determine whether to get company/branch jobcards. 
                // We get the result from the global store in auth.js
                modelType: auth.modelType,

                // Table columns 
                tableColumns: this.tableColumnsData,

                // Filterable data
                filterable: this.filterableData
            }
        },
        methods: {
            generateURL: function(){
                //  Generate a new url
                Event.$emit('generateURL');
            },
            renderFilterableComponent: function(){
                //  Re-render the filterable component
                this.renderKey++;
            },
            determineResourceType: function(){
                //  The modelType: Whether to get company/branch specific data
                var modelType = this.modelType ? '&&model='+this.modelType : ''; 

                //  Attach the modelType to the Url generated for the filterable Api call
                //  This will allow the Api to either return company/branch related data
                this.filterable.url = this.filterable.url + modelType;
            }
        },
        watch: {
            //  When the filterableData changes e.g) jobcard lifecycle step changes the url
            'filterableData'() {
                //  Re-render the filterable component to re-run the Api call
                this.renderFilterableComponent();
            },
            //  When the tableColumnsData changes e.g) we add or remove columns
            'tableColumnsData'() {
                //  Re-render the filterable component to re-run the Api call
                this.renderFilterableComponent();
            }
        },
        created () {
            //  Generate a new url
            this.generateURL();

            //  Whether we want to get the company/branch related data
            this.determineResourceType();

            //  Listen for global changes on the resource type. 
            //  The reource is used to reflect which data we want to get.
            //  It may be the users company/branch specific data.

            var self = this;

            Event.$on('updatedResourceType', function(updatedResourceType){
                //  Get the updated resourceType e.g) company/branch
                self.modelType = updatedResourceType;
            
                self.determineResourceType();

                self.renderFilterableComponent();
            });
        },
        beforeDestroy() {
            //  Stop listening for global changes on the resource type.
            Event.$off('updatedResourceType');
        }
    }
</script>
