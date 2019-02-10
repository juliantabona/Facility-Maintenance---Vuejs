<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isCreating" 
            :hideModal="hideModal"
            title="Add Company/Individual"
            okText="Create" cancelText="Cancel"
            @on-ok="createClient()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Company Or User Selector -->
                <companyOrUserSelector class="mb-2"@on-change="selectedClientType = $event"></companyOrUserSelector>

                <!-- Create Individual -->
                <createIndividual 
                    v-if="selectedClientType == 'user'" 
                    :showDirectoryAllocation="true"
                    :hideBio="true" :hideSaveBtn="true"
                    :activateDetailMode="true"
                    :createUser="isCreating"
                    @created:user="getCreatedUser($event)"
                    class="border-top mt-2">
                </createIndividual>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue';

    /*  Selectors  */
    import companyOrUserSelector from './../selectors/companyOrUserSelector.vue'; 

    /*  Modal Structure  */
    import mainModal from './main.vue';

    /*  Widgets  */
    import createIndividual from './../../../widgets/user/show/main.vue'; 

    export default {
        components: { Loader, companyOrUserSelector, mainModal, createIndividual  },
        data(){
            return{
                selectedClientType: '',
                clientType: ['Company', 'Individual'],

                hideModal: false,
                isLoading: false,
                isCreating: false,
            }
        },
        methods: {
            createClient(){
                this.isCreating = true;
            },
            getCreatedUser(user){
                this.$emit('created:user', user);
                this.closeModal();
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>