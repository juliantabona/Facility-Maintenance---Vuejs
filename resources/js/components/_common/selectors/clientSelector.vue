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
            <Button class="pt-1 pb-1" icon="ios-add" type="dashed" size="small" @click="isOpenCreateClientModal = true">Add</Button>
        </Poptip>

        <!-- 
            MODAL TO CREATE NEW CLIENT - INDIVIDUAL/COMPANY
        -->
        <createCompanyOrUserModal 
            v-if="isOpenCreateClientModal"
            @visibility="isOpenCreateClientModal = $event"
            @created:user="updateCreatedUser($event)">
        </createCompanyOrUserModal>

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
    import createCompanyOrUserModal from './../modals/createCompanyOrUserModal.vue';

    export default {
        props: {
            selectedClient:{
                type: Object,
                default: null
            }
        },
        components: { Loader, companyOrUserSelector, companySelector, individualSelector, createCompanyOrUserModal },
        data(){
            return {
                selectedClientType: '',
                isOpenCreateClientModal: false
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
        },
        methods: {
            updateCreatedUser(newUser){
                this.selectedClientType = 'user';
                this.localSelectedClient = newUser;
            }
        }
    };
</script>