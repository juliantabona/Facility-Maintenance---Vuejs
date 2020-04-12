<template>

    <Card v-if="localPagination" class="draggable-option mb-2">

        <!-- Pagination Name -->
        <div slot="title">

            <!-- Pagination Name Label  -->
            <span class="pagination-name font-weight-bold cut-text">
                {{ getPaginationNumber ? getPaginationNumber +'. ' : '' }}
                {{ localPagination.name }}
            </span>
            
        </div>

        <!-- Pagination Toolbar (Edit, Move, Delete Buttons) -->
        <div slot="extra" class="d-flex" style="margin-top: -2px;">

            <span class="d-flex blue-highlighter mr-2" style="border-radius: 5px;">

                <template v-if="localPagination.selected_type == 'scroll_up'">

                    <!-- Icon -->
                    <Icon type="ios-arrow-round-up" :size="20" class="text-primary mr-1"></Icon>

                    <!-- Pagination Type -->
                    <span>Scroll Up</span>

                </template>

                <template v-else>

                    <!-- Icon -->
                    <Icon type="ios-arrow-round-down" :size="20" class="text-primary mr-1"></Icon>

                    <!-- Pagination Type -->
                    <span>Scroll Down</span>

                </template>

            </span>

            <div class="option-toolbox">

                <!-- Remove Pagination Button  -->
                <Poptip confirm title="Are you sure you want to remove this pagination?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemove(index)"
                        placement="left">
                    <Icon type="ios-trash-outline" class="option-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Edit Pagination Button  -->
                <Icon type="ios-create-outline" class="option-icon hidable mr-2" size="20" @click="handleEdit()" />

                <!-- Copy Pagination Button  -->
                <Icon type="ios-copy-outline" class="option-icon hidable mr-2" size="20" @click="handleDuplication(index)"/>

                <!-- Move Pagination Button  -->
                <Icon type="ios-move" class="option-icon draggable-option-handle hidable mr-2" size="20" />
            
            </div>

        </div>   
    
        <!-- 
            MODAL TO EDIT EXISTING PAGINATION
        -->
        <editPaginationModal v-if="isOpenEditPaginationModal" 
            @visibility="isOpenEditPaginationModal = $event"
            :pagination="localPagination">
        </editPaginationModal> 

    </Card>

</template>

<script>
    //  Get the create new pagination modal
    import editPaginationModal from './../edit/editPaginationModal.vue';

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            paginations: {
                type: Array,
                default:() => []
            },
            pagination: {
                type: Object,
                default:() => {}
            }
        }, 
        components: { editPaginationModal },
        data(){
            return {
                localPagination: this.pagination,
                isOpenEditPaginationModal: false
            }
        },
        watch: {
            //  Keep track of changes on the pagination
            pagination: {

                handler: function (val, oldVal) {
                    
                    /*  Update the localScreen  */
                    this.localPagination = val;

                },
                deep: true

            },
        },
        computed: {
            getPaginationNumber(){
                /**
                 *  Returns the pagination number. We use this as we list the paginations.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            }
        },
        methods: {
            handleEdit(){
                this.isOpenEditPaginationModal = true;
            },
            handleDuplication(index) {

                //  Duplicate the pagination
                var duplicatePagination = _.cloneDeep( this.paginations[index] );

                //  Create the duplicate pagination name
                var duplicateName = 'Pagination - #' + (this.paginations.length + 1);

                //  Set the duplicate pagination name
                duplicatePagination.name = duplicateName;

                //  Add the duplicate pagination to the rest of the other paginations
                this.paginations.push(duplicatePagination);

            },
            handleRemove(index) {
                this.paginations.splice(index, 1);
            }
        }
    }

</script>