<template>
    
    <div>
        <Loader v-if="isCheckingAvailableClients" :loading="true" type="text" class="text-left">Loading...</Loader>
        <template v-else>
            <!-- Company Or User Selector -->
            <companyOrUserSelector 
                :key="renderKey"
                v-if="showCompanyDropDownList || showIndividualDropDownList"
                :disabled="getDisabledOptions"
                class="mr-1 mb-2" :style="{ width:'55%', float:'left' }" 
                @on-change="selectedClientType = $event">
            </companyOrUserSelector>

            <!-- Company Selector -->
            <companySelector 
                v-if="selectedClientType == 'company' && showCompanyDropDownList" 
                :style="{ width:'55%', float:'left' }"
                :selectedCompany="localSelectedClient"
                @updated:company="runUpdate($event)">
            </companySelector>    

            <!-- Individual Selector -->
            <individualSelector 
                v-if="selectedClientType == 'user' && showIndividualDropDownList" 
                :style="{ width:'55%', float:'left' }"
                :selectedUser="localSelectedClient"
                @updated:user="runUpdate($event)">
            </individualSelector> 

            <span v-if="showCompanyDropDownList || showIndividualDropDownList" class="d-block float-left ml-1 mt-1">or</span>

            <!-- Add Company Button -->
            <Poptip v-if="showCompanyDropDownList || showIndividualDropDownList" class="float-left" trigger="hover" content="Add New Client">
                <Button class="mt-1 ml-2 float-left" icon="ios-add" type="dashed" size="small" @click="isOpenCreateOrEditCompanyOrIndividualModal = true">Create</Button>
            </Poptip>

            <stripeButton 
                v-if="!showCompanyDropDownList && !showIndividualDropDownList"  
                icon="ios-add"
                :showIcon="true"
                :ripple="false"
                customClass="w-100"
                @click.native="isOpenCreateOrEditCompanyOrIndividualModal = true">
                Add Client
            </stripeButton>

            <!-- 
                MODAL TO CREATE/EDIT COMPANY/INDIVIDUAL
            -->
            <createOrEditCompanyOrIndividualModal 
                v-if="isOpenCreateOrEditCompanyOrIndividualModal"
                :editableCompanyOrIndividual="null"
                :showCompanyOrUserSelector="showCompanyOrUserSelector"
                :showClientOrSupplierSelector="showClientOrSupplierSelector"
                defaultRelationship="client"
                @visibility="isOpenCreateOrEditCompanyOrIndividualModal = $event"
                @updated="runUpdate($event)"
                @created="runUpdate($event)">
            </createOrEditCompanyOrIndividualModal>
        </template>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    /*  Selectors  */
    import companyOrUserSelector from './companyOrUserSelector.vue'; 
    import companySelector from './companySelector.vue'; 
    import individualSelector from './individualSelector.vue'; 

    /*  Buttons   */
    import stripeButton from './../buttons/stripeButton.vue'; 

    /*  Modals  */
    import createOrEditCompanyOrIndividualModal from './../modals/createOrEditCompanyOrIndividualModal.vue';

    export default {
        props: {
            companyId:{
                type: Number,
                default: null
            },
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
        components: { Loader, companyOrUserSelector, companySelector, individualSelector, stripeButton, createOrEditCompanyOrIndividualModal },
        data(){
            return {
                selectedClientType: '',
                showCompanyDropDownList: false,
                showIndividualDropDownList: false,
                isOpenCreateOrEditCompanyOrIndividualModal: false,
                isCheckingAvailableClients: false,
                renderKey: 0,
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
            },
            getDisabledOptions(){
                return [ 
                    !this.showCompanyDropDownList ? 'Company' : '',
                    !this.showIndividualDropDownList ? 'Individual' : ''
                ];
            }
        },
        methods:{
            runUpdate(client){
                //  Get the new client
                this.localSelectedClient = client;

                //  Reset the client selector
                this.renderKey = this.renderKey + 1;
                this.selectedClientType = '';

                //  Close modal
                this.isOpenCreateOrEditCompanyOrIndividualModal = false;

                //  Check which clients are available
                this.checkAvailableClients();
            },
            checkAvailableClients() {
                
                if(this.companyId){
                    
                    const self = this;

                    //  Start loader
                    self.isCheckingAvailableClients = true;

                    console.log('Start checking if the company has clients...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.companyId+'/clients?count=1')
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isCheckingAvailableClients = false;

                            self.showCompanyDropDownList = (data.companies ? true : false);
                            self.showIndividualDropDownList = (data.users ? true : false);

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isCheckingAvailableClients = false;

                            console.log('invoiceSummaryWidget.vue - Error checking if company has clients...');
                            console.log(response);    
                        });
                }
            }
        },
        mounted: function () {
            this.checkAvailableClients();
        }
    };
</script>