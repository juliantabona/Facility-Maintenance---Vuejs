<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isCreating || isSaving" 
            :hideModal="hideModal"
            :title="title"
            :okText="okText" cancelText="Cancel"
            @on-ok="handleResponse()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Company Or User Selector -->
                <companyOrUserSelector class="pb-2 mb-2 border-bottom"
                     v-if="showCompanyOrUserSelector"
                    :selectedClientType="selectedClientType"
                    @on-change="selectedClientType = $event">
                </companyOrUserSelector>

                <!-- Create/Edit Individual -->
                <individualWidget 
                    v-if="selectedClientType == 'user'" 
                    :userId="(editableCompanyOrIndividual || {}).id"
                    :showClientOrSupplierSelector="showClientOrSupplierSelector"
                    :hideBio="true" :hideSaveBtn="true"
                    :activateDetailMode="true"
                    :canSaveOnCreate="isCreating || isSaving"
                    @created:user="getCreatedEntity($event)"
                    @updated:user="getUpdatedEntity($event)">
                </individualWidget>

                <!-- Create/Edit Company -->
                <companyWidget 
                    v-if="selectedClientType == 'company'" 
                    :companyId="(editableCompanyOrIndividual || {}).id"
                    :showClientOrSupplierSelector="showClientOrSupplierSelector"
                    :hideBio="true" :hideSaveBtn="true"
                    :activateDetailMode="true"
                    :canSaveOnCreate="isCreating || isSaving"
                    @created:company="getCreatedEntity($event)"
                    @updated:company="getUpdatedEntity($event)">
                </companyWidget>

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
    import individualWidget from './../../../widgets/user/show/main.vue'; 
    import companyWidget from './../../../widgets/company/show/main.vue'; 

    export default {
        props:{
            //  This holds the company/individual to be edited if the entity exists
            editableCompanyOrIndividual: {
                type: Object,
                default: null
            },
            showCompanyOrUserSelector: { 
                type: Boolean,
                default: false
            },
            showClientOrSupplierSelector: { 
                type: Boolean,
                default: false
            },
        },
        components: { Loader, companyOrUserSelector, mainModal, individualWidget, companyWidget  },
        data(){
            return{
                //  The selectedClientType must be the entity model type e.g) company/user
                //  Sometimes the user may provide the "editableCompanyOrIndividual" which
                //  we can then extract the model type. If its not provided then we leave
                //  it empty so that the user can specify using the "companyOrUserSelector"
                selectedClientType: (this.editableCompanyOrIndividual || {}).model_type,
                hideModal: false,
                isCreating: false,
                isSaving: false,
            }
        },
        computed: {
            title: function(){

                if( this.editableCompanyOrIndividual ){
                    return this.selectedClientType == 'company' ? 'Edit Company': 'Edit Individual';
                }else{
                    return 'Add Company/Individual';
                }

            },
            okText: function(){

                if( this.editableCompanyOrIndividual ){
                    return 'Save Changes';
                }else{
                    return this.selectedClientType == 'company' ? 'Create Company': 'Create Individual';
                }

            }
        },
        methods: {
            handleResponse(){
                if( this.editableCompanyOrIndividual ){

                    this.isSaving = true;
                    
                }else{
                    
                    this.isCreating = true;
                }

            },
            //  The updated entity could be a company or user
            getUpdatedEntity(entity){
                this.$emit('updated', entity);
                this.closeModal();
            },
            //  The created entity could be a company or user
            getCreatedEntity(entity){
                this.$emit('created', entity);
                this.closeModal();
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>