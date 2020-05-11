<template>

    <Card v-if="localNavigation" class="draggable-option mb-2">

        <!-- Navigation Name -->
        <div slot="title">

            <!-- Navigation Name Label  -->
            <span class="navigation-name font-weight-bold cut-text">
                {{ getNavigationNumber ? getNavigationNumber +'. ' : '' }}
                {{ localNavigation.name }}
            </span>
            
        </div>

        <!-- Navigation Toolbar (Edit, Move, Delete Buttons) -->
        <div slot="extra" class="d-flex" style="margin-top: -2px;">

            <div class="option-toolbox">

                <!-- Remove Navigation Button  -->
                <Poptip confirm title="Are you sure you want to remove this navigation?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemove(index)"
                        placement="left">
                    <Icon type="ios-trash-outline" class="option-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Edit Navigation Button  -->
                <Icon type="ios-create-outline" class="option-icon hidable mr-2" size="20" @click="handleEdit()" />

                <!-- Copy Navigation Button  -->
                <Icon type="ios-copy-outline" class="option-icon hidable mr-2" size="20" @click="handleDuplication(index)"/>

                <!-- Move Navigation Button  -->
                <Icon type="ios-move" class="option-icon draggable-option-handle hidable mr-2" size="20" />
            
            </div>

        </div>   
    
        <!-- 
            MODAL TO EDIT EXISTING EVENT
        -->
        <editNavigationModal v-if="isOpenEditNavigationModal" 
            @visibility="isOpenEditNavigationModal = $event"
            :navigation="localNavigation">
        </editNavigationModal> 

    </Card>

</template>

<script>
    //  Get the create new navigation modal
    import editNavigationModal from './../edit/editNavigationModal.vue';

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            navigations: {
                type: Array,
                default:() => []
            },
            navigation: {
                type: Object,
                default:() => {}
            }
        }, 
        components: { editNavigationModal },
        data(){
            return {
                localNavigation: this.navigation,
                isOpenEditNavigationModal: false
            }
        },
        watch: {
            //  Keep track of changes on the navigation
            navigation: {

                handler: function (val, oldVal) {
                    
                    /*  Update the localScreen  */
                    this.localNavigation = val;

                },
                deep: true

            },
        },
        computed: {
            getNavigationNumber(){
                /**
                 *  Returns the navigation number. We use this as we list the navigations.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            }
        },
        methods: {
            handleEdit(){
                this.isOpenEditNavigationModal = true;
            },
            handleDuplication(index) {

                //  Duplicate the navigation
                var duplicateNavigation = _.cloneDeep( this.navigations[index] );

                //  Create the duplicate navigation name
                var duplicateName = 'Navigation - #' + (this.navigations.length + 1);

                //  Set the duplicate navigation name
                duplicateNavigation.name = duplicateName;

                //  Add the duplicate navigation to the rest of the other navigations
                this.navigations.push(duplicateNavigation);

            },
            handleRemove(index) {
                this.navigations.splice(index, 1);
            }
        }
    }

</script>