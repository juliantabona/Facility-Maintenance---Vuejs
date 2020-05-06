<template>
    <div>
        <!-- Modal -->
        <mainModal  
            okText="" 
            :width="800"
            v-bind="$props"
            :isSaving="false" 
            cancelText="Done"
            :hideModal="hideModal"
            :title="'Edit Event (' + localEvent.type +')'"
            @visibility="$emit('visibility', $event)">

            <template slot="content">
                
                <!-- Edit CRUD API Event --> 
                <editCrudApiEvent v-if="localEvent.type == 'CRUD API'" :event="localEvent"></editCrudApiEvent>

                <!-- Edit Validation Event --> 
                <editValidationEvent v-else-if="localEvent.type == 'Validation'" :event="localEvent"></editValidationEvent>

                <!-- Edit Local Storage Event --> 
                <editLocalStorageEvent v-else-if="localEvent.type == 'Local Storage'" :event="localEvent"></editLocalStorageEvent>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    //  Main Modal
    import mainModal from './../../../../../../../components/_common/modals/main.vue';

    //  Get the CRUD API Event component used to edit
    import editCrudApiEvent from './../edit/apis/crud-api/main.vue';

    //  Get the Validation Event component used to edit
    import editValidationEvent from './validation/main.vue';

    //  Get the Validation Event component used to edit
    import editLocalStorageEvent from './local-storage/main.vue';

    export default {
        props:{
            event: {
                type: Object,
                default: null
            }
        },
        components: { mainModal, editCrudApiEvent, editValidationEvent, editLocalStorageEvent },
        data(){
            return{
                hideModal: false,
                localEvent: this.event
            }
        },
        methods: {
            
        },
        created(){

        }
    }
</script>