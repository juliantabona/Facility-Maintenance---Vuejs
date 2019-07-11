<template>
    
    <div>
        <Loader v-if="isCheckingAvailableClients" :loading="true" type="text" class="text-left">Loading...</Loader>
        <template v-else>
            <div v-if="localSelectedCompanyOrIndividual && localShowSelectedCompanyOrIndividual">
                <el-input :value="localSelectedCompanyOrIndividualName" size="small" class="float-left" style="max-width: 70%;" disabled></el-input>
                <span class="btn btn-link mt-1 float-left" style="font-size: 12px;" @click="changeCompanyOrIndividual()">Change</span>
            </div>

            <div v-else :class="( localSelectedCompanyOrIndividual && showSelectedCompanyOrIndividual ? 'border p-2' : '' )">
                
                <!-- Company Or User Selector -->
                <companyOrUserTypeSelector 
                    :key="renderKey"
                    v-if="showCompanyDropDownList || showIndividualDropDownList"
                    :selectedClientType="localSelectedCompanyOrIndividualType"
                    :disabled="getDisabledOptions"
                    class="mr-1 mb-2" :style="{ width:(localSelectedCompanyOrIndividualType ? '100%' : '55%'), float:'left' }" 
                    @on-change="localSelectedCompanyOrIndividualType = $event">
                </companyOrUserTypeSelector>

                <!-- Company Selector -->
                <companySelector 
                    v-if="localSelectedCompanyOrIndividualType == 'company' && showCompanyDropDownList" 
                    :style="{ width:'55%', float:'left' }"
                    :selectedCompany="localSelectedCompanyOrIndividual"
                    @updated:company="runUpdate($event)">
                </companySelector>    

                <!-- Individual Selector -->
                <individualSelector 
                    v-if="localSelectedCompanyOrIndividualType == 'user' && showIndividualDropDownList" 
                    :style="{ width:'55%', float:'left' }"
                    :selectedUser="localSelectedCompanyOrIndividual"
                    @updated:user="runUpdate($event)">
                </individualSelector> 

                <div v-if="showCompanyDropDownList || showIndividualDropDownList" class="clearfix float-left">
                    <span class="d-block float-left ml-1">or</span>

                    <!-- Add Company Button -->
                    <Poptip class="float-left" trigger="hover" content="Add New Client">
                        <Button :class="(localSelectedCompanyOrIndividualType ? 'mt-2 ' : 'mt-1 ') +'ml-2 float-left'" icon="ios-add" type="dashed" size="small" @click="isOpenCreateOrEditCompanyOrIndividualModal = true">Create</Button>
                    </Poptip>
                </div>

                <span v-if="localSelectedCompanyOrIndividual && showSelectedCompanyOrIndividual"
                    class="btn btn-link text-danger pl-0"
                    @click="localShowSelectedCompanyOrIndividual = true">
                    <Icon type="md-close-circle" class="inline-block" />
                    <span style="font-size: 12px;" class="inline-block">Cancel</span>
                </span>

                <stripeButton 
                    v-if="!showCompanyDropDownList && !showIndividualDropDownList"  
                    icon="ios-add"
                    :showIcon="true"
                    :ripple="false"
                    customClass="w-100"
                    @click.native="isOpenCreateOrEditCompanyOrIndividualModal = true">
                    Add Client
                </stripeButton>

            </div>

            <!-- 
                MODAL TO CREATE/EDIT COMPANY/INDIVIDUAL
            -->
            <createOrEditCompanyOrIndividualModal 
                v-if="isOpenCreateOrEditCompanyOrIndividualModal"
                :editableCompanyOrIndividual="null"
                :showCompanyOrUserSelector="showCompanyOrUserSelector"
                :showClientOrVendorSelector="showClientOrVendorSelector"
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
    import companyOrUserTypeSelector from './companyOrUserTypeSelector.vue'; 
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
            selectedCompanyOrIndividual:{
                type: Object,
                default: null
            },
            companyOrIndividualType:{
                type: String,
                default: 'clients'
            },
            showCompanyOrUserSelector:{
                type: Boolean,
                default: true
            },
            showClientOrVendorSelector:{
                type: Boolean,
                default: true
            },
            showSelectedCompanyOrIndividual:{
                type: Boolean,
                default: true
            }
        },
        components: { Loader, companyOrUserTypeSelector, companySelector, individualSelector, stripeButton, createOrEditCompanyOrIndividualModal },
        data(){
            return {
                localSelectedCompanyOrIndividual: this.selectedCompanyOrIndividual,
                localShowSelectedCompanyOrIndividual: this.showSelectedCompanyOrIndividual,
                localSelectedCompanyOrIndividualType: (this.selectedCompanyOrIndividual  || {}).model_type,
                showCompanyDropDownList: false,
                showIndividualDropDownList: false,
                isOpenCreateOrEditCompanyOrIndividualModal: false,
                isCheckingAvailableClients: false,
                renderKey: 0,
            }
        },
        computed:{
            localSelectedCompanyOrIndividualName(){
                var modelType = (this.localSelectedCompanyOrIndividual || {}).model_type;
                var userOrCompany = (this.localSelectedCompanyOrIndividual || {});
                return (modelType == 'user') ? userOrCompany.full_name : userOrCompany.name 
            },
            getDisabledOptions(){
                return [ 
                    !this.showCompanyDropDownList ? 'Company' : '',
                    !this.showIndividualDropDownList ? 'Individual' : ''
                ];
            }
        },
        methods:{
            changeCompanyOrIndividual()
            {
                this.localShowSelectedCompanyOrIndividual = false;
                this.localSelectedCompanyOrIndividualType = (this.localSelectedCompanyOrIndividual || {}).model_type;
            },
            runUpdate(client){
                //  Get the new client
                this.localSelectedCompanyOrIndividual = client;

                //  Reset the client selector
                this.renderKey = this.renderKey + 1;
                this.localSelectedCompanyOrIndividualType = '';

                //  Close modal
                this.isOpenCreateOrEditCompanyOrIndividualModal = false;

                //  Notify parent on update
                this.$emit('updated',  client );

                //  Check which clients are available
                this.checkAvailableClients();

                if(this.showSelectedCompanyOrIndividual){
                    this.localShowSelectedCompanyOrIndividual = true;
                }
            },
            checkAvailableClients() {
                
                if(this.companyId){
                    
                    const self = this;

                    //  Start loader
                    self.isCheckingAvailableClients = true;

                    console.log('Start checking if the company has clients/vendors...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.companyId+'/'+this.companyOrIndividualType+'?count=1')
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

                            console.log('clientOrVendorSelector.vue - Error checking if company has clients/vendors...');
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