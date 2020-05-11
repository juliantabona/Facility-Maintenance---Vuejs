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
                <editCrudApiEvent v-if="localEvent.type == 'CRUD API'" :event="localEvent" :builder="builder"></editCrudApiEvent>

                <!-- Edit Validation Event --> 
                <editValidationEvent v-else-if="localEvent.type == 'Validation'" :event="localEvent" :builder="builder"></editValidationEvent>

                <!-- Edit Local Storage Event --> 
                <editLocalStorageEvent v-else-if="localEvent.type == 'Local Storage'" :event="localEvent" :builder="builder"></editLocalStorageEvent>

                <!-- Edit Revisit Event --> 
                <editRevisitEvent v-else-if="localEvent.type == 'Revisit'" :screen="screen" :event="localEvent" :builder="builder"></editRevisitEvent>

                <!-- Edit Redirect Event --> 
                <editRedirectEvent v-else-if="localEvent.type == 'Redirect'" :screen="screen" :event="localEvent" :builder="builder"></editRedirectEvent>

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

    //  Get the Revisit Event component used to edit
    import editRevisitEvent from './revisit/main.vue';

    //  Get the Redirect Event component used to edit
    import editRedirectEvent from './redirect/main.vue';

    export default {
        props:{
            screen: {
                type: Object,
                default: () => {}
            },
            event: {
                type: Object,
                default: null
            },
            builder: {
                type: Object,
                default: () => {}
            }
        },
        components: { mainModal, editCrudApiEvent, editValidationEvent, editLocalStorageEvent, editRevisitEvent, editRedirectEvent },
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