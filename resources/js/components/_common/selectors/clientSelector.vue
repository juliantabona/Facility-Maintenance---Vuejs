<template>
    
    <div>
        
        <!-- Company Or User Selector -->
        <companyOrUserSelector class="mr-1 mb-2" :style="{ width:'74%', float:'left' }" 
                            @on-change="selectedClientType = $event">
        </companyOrUserSelector>

        <!-- Company Selector -->
        <companySelector v-if="selectedClientType == 'company'" 
                         :style="{ width:'74%', float:'left' }"
                         :selectedCompany="localSelectedClient"
                         @updated:company="localSelectedClient = $event">
        </companySelector>    

        <!-- Individual Selector -->
        <individualSelector v-if="selectedClientType == 'user'" 
                         :style="{ width:'74%', float:'left' }"
                         :selectedUser="localSelectedClient"
                         @updated:user="localSelectedClient = $event">
        </individualSelector> 

        <!-- Add Company Button -->
        <Poptip :style="{ float:'left' }" class="ml-1" word-wrap width="200" trigger="hover" content="Add a new client">
            <Button class="pt-1 pb-1" icon="ios-add" type="dashed" size="small" @click="isOpenCreateOrEditCompanyOrIndividualModal = true">Add</Button>
        </Poptip>

        <!-- 
            MODAL TO CREATE/EDIT COMPANY/INDIVIDUAL
        -->
        <createOrEditCompanyOrIndividualModal 
            v-if="isOpenCreateOrEditCompanyOrIndividualModal"
            :editableCompanyOrIndividual="null"
            :showCompanyOrUserSelector="showCompanyOrUserSelector"
            :showClientOrSupplierSelector="showClientOrSupplierSelector"
            @visibility="isOpenCreateOrEditCompanyOrIndividualModal = $event"
            @updated="localSelectedClient = $event"
            @created="localSelectedClient = $event">
        </createOrEditCompanyOrIndividualModal>

    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    /*  Selectors  */
    import companyOrUserSelector from './companyOrUserSelector.vue'; 
    import companySelector from './companySelector.vue'; 
    import individualSelector from './individualSelector.vue'; 

    /*  Modals  */
    import createOrEditCompanyOrIndividualModal from './../modals/createOrEditCompanyOrIndividualModal.vue';

    export default {
        props: {
            selectedClient:{
                type: Object,
                default: null
            },
            showCompanyOrUserSelector:{
                type: Boolean,
                default: true
            },
            showClientOrSupplierSelector:{
                type: Boolean,
                default: true
            }
        },

        components: { Loader, companyOrUserSelector, companySelector, individualSelector, createOrEditCompanyOrIndividualModal },
        data(){
            return {
                selectedClientType: '',
                isOpenCreateOrEditCompanyOrIndividualModal: false
            }
        },
        computed:{
            localSelectedClient:{
                get(){
                    return this.selectedClient;
                },
                set(newClient){
                    this.$emit('updated',  newClient );

                }
            }
        }
    };
</script>