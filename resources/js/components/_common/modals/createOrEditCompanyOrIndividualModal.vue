<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="700"
            :modalClosable="false"
            :isSaving="isCreating || isSaving" 
            :hideModal="hideModal"
            :title="title"
            :okText="okText" cancelText="Cancel"
            @on-ok="handleResponse()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <Row>
                    <Col :span="24">
                        <basicButton  
                            v-show="selectedClientType && !editableCompanyOrIndividual"
                            :style="{ position:'relative' }"
                            icon="md-arrow-back"
                            :showIcon="true"
                            iconDirection="left"
                            type="success" 
                            size="small" 
                            :ripple="false"
                            @click.native="selectedClientType = ''">
                            Go Back
                        </basicButton>

                        <!-- Company Or User Selector -->
                        <Row v-if="showCompanyOrUserSelector && !selectedClientType">
                            <Col :span="20" offset="2">
                                <Row :gutter="12" class="mt-3 mb-5">
                                    <Col v-for="(choice, i) in availableChoices" :key="i" :span="12">
                                        <IconAndCounterCard :title="choice.label" 
                                                            :icon="choice.icon"
                                                            :style="{ width: '100%' }" 
                                                            class="p-4 mb-2" type="success"
                                                            :showCheckMark="false"
                                                            :checkMarkVisibility="false"
                                                            @click.native="selectedClientType = choice.name">
                                        </IconAndCounterCard>
                                    </Col>
                                </Row>
                            </Col>
                        </Row>

                    </Col>
                </Row>

                <!-- Create/Edit Individual -->
                <individualWidget 
                    v-if="selectedClientType == 'user'" 
                    :editMode="true"
                    :userId="(editableCompanyOrIndividual || {}).id"
                    :showClientOrSupplierSelector="showClientOrSupplierSelector"
                    :hideBio="true" :hideSaveBtn="true"
                    :hideSummaryToggle="false"
                    :activateSummaryMode="true"
                    :canSaveOrCreate="isCreating || isSaving"
                    :defaultRelationship="defaultRelationship"
                    @created:user="getCreatedEntity($event)"
                    @updated:user="getUpdatedEntity($event)">
                </individualWidget>

                <!-- Create/Edit Company -->
                <companyWidget 
                    v-if="selectedClientType == 'company'" 
                    :editMode="true"
                    :companyId="(editableCompanyOrIndividual || {}).id"
                    :showClientOrSupplierSelector="showClientOrSupplierSelector"
                    :hideBio="true" 
                    :hideSaveBtn="true"
                    :hideSummaryToggle="false"
                    :activateSummaryMode="true"
                    :canSaveOrCreate="isCreating || isSaving"
                    :defaultRelationship="defaultRelationship"
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

    /*  Modal Structure  */
    import mainModal from './main.vue';

    /*  Widgets  */
    import individualWidget from './../../../widgets/user/show/main.vue'; 
    import companyWidget from './../../../widgets/company/show/companyDetails.vue'; 

    /*  Cards  */
    import IconAndCounterCard from './../cards/IconAndCounterCard.vue';

    import basicButton from './../buttons/basicButton.vue';

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
            defaultRelationship:{
                type: String,
                default: null
            }
        },
        components: { Loader, mainModal, individualWidget, companyWidget, IconAndCounterCard, basicButton  },
        data(){
            return{
                //  The selectedClientType must be the entity model type e.g) company/user
                //  Sometimes the user may provide the "editableCompanyOrIndividual" which
                //  we can then extract the model type. If its not provided then we leave
                //  it empty so that the user can specify
                selectedClientType: (this.editableCompanyOrIndividual || {}).model_type,
                availableChoices: [
                    {
                        name: 'company',
                        label: 'Create Company',
                        icon: 'ios-cash-outline'
                    },
                    {
                        name: 'user',
                        label: 'Create Individual',
                        icon: 'ios-man-outline',
                    }
                ],
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
                    if( this.selectedClientType == 'company' ){
                        return 'Create Company';
                    }else if( this.selectedClientType == 'user' ){
                        return 'Create Individual';
                    }else{
                        return '';
                    }
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