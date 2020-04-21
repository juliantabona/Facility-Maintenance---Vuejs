<template>

    <div>

        <!-- Navigation List & Dragger  -->
        <draggable 
            :list="navigations"
            @start="drag=true" 
            @end="drag=false" 
            :options="{
                group:'navigations',
                draggable:'.draggable-option', 
                handle:'.draggable-option-handle'
            }"
            :style="{  minHeight:'50px' }">

            <!-- Single Navigation  -->
            <singleNavigation v-for="(navigation, index) in navigations" :key="index" 
                :navigations="navigations" 
                :navigation="navigation"
                :index="index">
            </singleNavigation>
            
            <!-- No navigations message -->
            <Alert v-if="!navigationsExist" type="info" show-icon style="width:300px;">No Navigations Found</Alert>

        </draggable>

        <div class="clearfix">

            <!-- Create Navigation Button -->
            <Button class="p-1 float-right" @click.native="launchNavigationCreater()">
                <Icon type="ios-add" :size="20" />
                <span class="mr-2">Add Navigation</span>
            </Button>

        </div>
        
        <!-- 
            MODAL TO CREATE NEW EVENT
        -->
        <createNavigationModal
            v-if="isOpenCreateNavigationModal" 
            @visibility="isOpenCreateNavigationModal = $event"
            @created="addNavigation($event)">
        </createNavigationModal>

    </div>

</template>

<script>

    import draggable from 'vuedraggable';

    //  Get the single navigation component
    import singleNavigation from './single-navigation/main.vue';

    //  Get the create new navigation modal
    import createNavigationModal from './create/createNavigationModal.vue';

    export default {
        props: { 
            navigations: {
                type: Array,
                default:() => []
            }
        },
        components: { 
            draggable, singleNavigation, createNavigationModal
        },
        data(){
            return {
                navigationToEdit: null,
                isOpenCreateNavigationModal: false,
            }
        },
        computed: {

            //  Check if the navigations exist
            navigationsExist(){

                return (this.navigations.length) ? true : false ;

            }

        },
        methods: {
            launchNavigationCreater(){
                this.isOpenCreateNavigationModal = true;
            },
            addNavigation( navigation ){

                //  If we have an navigation
                if( navigation ){
                    
                    //  Push the new navigation
                    this.navigations.push( navigation );

                    this.$Notice.success({
                        title: 'Navigation added'
                    });

                }

            }
        }
    };
  
</script>