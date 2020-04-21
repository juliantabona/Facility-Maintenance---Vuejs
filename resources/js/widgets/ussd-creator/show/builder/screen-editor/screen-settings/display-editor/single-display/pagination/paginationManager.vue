<template>

    <div>

        <!-- Pagination List & Dragger  -->
        <draggable 
            :list="paginations"
            @start="drag=true" 
            @end="drag=false" 
            :options="{
                group:'paginations',
                draggable:'.draggable-option', 
                handle:'.draggable-option-handle'
            }"
            :style="{  minHeight:'50px' }">

            <!-- Single Pagination  -->
            <singlePagination v-for="(pagination, index) in paginations" :key="index" 
                :paginations="paginations" 
                :pagination="pagination"
                :index="index">
            </singlePagination>
            
            <!-- No paginations message -->
            <Alert v-if="!paginationsExist" type="info" show-icon style="width:300px;">No Paginations Found</Alert>

        </draggable>

        <div class="clearfix">

            <!-- Create Pagination Button -->
            <Button class="p-1 float-right" @click.native="launchPaginationCreater()">
                <Icon type="ios-add" :size="20" />
                <span class="mr-2">Add Pagination</span>
            </Button>

        </div>
        
        <!-- 
            MODAL TO CREATE NEW EVENT
        -->
        <createPaginationModal
            v-if="isOpenCreatePaginationModal" 
            @visibility="isOpenCreatePaginationModal = $event"
            @created="addPagination($event)">
        </createPaginationModal>

    </div>

</template>

<script>

    import draggable from 'vuedraggable';

    //  Get the single pagination component
    import singlePagination from './single-pagination/main.vue';

    //  Get the create new pagination modal
    import createPaginationModal from './create/createPaginationModal.vue';

    export default {
        props: { 
            paginations: {
                type: Array,
                default:() => []
            }
        },
        components: { 
            draggable, singlePagination, createPaginationModal
        },
        data(){
            return {
                paginationToEdit: null,
                isOpenCreatePaginationModal: false,
            }
        },
        computed: {

            //  Check if the paginations exist
            paginationsExist(){

                return ((this.paginations || {}).length) ? true : false ;

            }

        },
        methods: {
            launchPaginationCreater(){
                this.isOpenCreatePaginationModal = true;
            },
            addPagination( pagination ){

                //  If we have an pagination
                if( pagination ){
                    
                    //  Push the new pagination
                    this.paginations.push( pagination );

                    this.$Notice.success({
                        title: 'Pagination added'
                    });

                }

            }
        }
    };
  
</script>