<template>

    <div :style="align == 'right' ? 'float: right; text-align: right;' : ''">
            
        <!-- Loader for when loading the client information -->
        <Loader v-if="isLoadingClientInfo" :loading="isLoadingClientInfo" type="text" :style="{ marginTop:'40px' }">Loading client details...</Loader>

        <!-- Client Information -->
        <div v-if="localClient">

            <!-- Edit button -->
            <Poptip class="mt-2 mb-2" trigger="hover" :content="'Edit '+(localClient.model_type == 'company' ? 'company' : 'profile')+' details?'">
                <Button class="mt-1 ml-1" icon="ios-create-outline" type="dashed" size="small" @click="editCompanyOrIndividual()">{{ 'Edit '+(localClient.model_type == 'company' ? 'company' : 'profile')+' details' }}</Button>
            </Poptip>

            <div class="clearfix"></div>

            <!-- Client Name - For Company/Individual -->
            <p v-if="!localEditMode" class="mt-3 text-dark"><strong>{{ clientName || '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="Client name" disabled v-model="clientName" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

            <div class="clearfix"></div>

            <!-- Client Email -->
            <p v-if="!localEditMode">{{ localClient.email || '___' }}</p>
            <el-input v-if="localEditMode" placeholder="Client email" disabled v-model="localClient.email" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

            <div class="clearfix mt-1"></div>

            <!-- Client Phones display -->
            <p v-if="!localEditMode && phoneList">{{ phoneList }}</p>

            <div class="clearfix mt-1"></div>

            <!-- Client Address -->
            <p v-if="!localEditMode && localClient.address" class="mt-1">{{ localClient.address }}</p>

            <!-- Client City -->
            <p v-if="!localEditMode && localClient.city" class="mt-1">{{ localClient.city }}</p>

            <!-- Client Country -->
            <p v-if="!localEditMode && localClient.country" class="mt-1">{{ localClient.country }}</p>

            <div class="clearfix"></div>
            
            <!-- Client Phones editor -->
            <phoneInput :style="(align == 'right' ? 'float: right; text-align: left;' : '') + 'max-width:360px'"
                        v-if="localEditMode" class="mb-2"  
                        :modelId="localClient.id" 
                        :modelType="localClient.type" 
                        :phones="localClient.phones" 
                        :numberLimit="5"
                        selectedType="mobile"
                        :disabledTypes="[]"   
                        selectedServiceProvider="Orange"
                        :disabledServiceProviders="[]"  
                        :deletable="false"
                        :hidedable="true"
                        :editable="false"
                        :showIcon="true" 
                        onIcon="ios-eye-outline" offIcon="ios-eye-off-outline" 
                        title="Show:" onText="Yes" offText="No" 
                        poptipMsg="Turn on to show"
                        @updated="updatePhoneChanges($event)">
            </phoneInput>

        </div>

        <!-- No client Information Alert -->
        <div v-if="!localClient && !isLoadingClientInfo">
            <Alert :style="{maxWidth: '250px'}" type="warning">
                No Client selected
            </Alert>
        </div>

        <!-- 
            MODAL TO CREATE/EDIT COMPANY/INDIVIDUAL
        -->
        <createOrEditCompanyOrIndividualModal 
            v-if="isOpenCreateOrEditCompanyOrIndividualModal"
            :editableCompanyOrIndividual="editableCompanyOrIndividual"
            :showCompanyOrUserSelector="showCompanyOrUserSelector"
            :showClientOrSupplierSelector="showClientOrSupplierSelector"
            @visibility="isOpenCreateOrEditCompanyOrIndividualModal = $event"
            @updated="updateClient($event)"
            @created="updateClient($event)">
        </createOrEditCompanyOrIndividualModal>

    </div>

</template>


<script type="text/javascript">
    
    import lodash from 'lodash';
    Event.prototype._ = lodash;

    /*  Inputs  */
    import phoneInput from './../../../components/_common/inputs/phoneInput.vue';   

    /*  Selectors   */
    import citySelector from './../../../components/_common/selectors/citySelector.vue'; 
    import countrySelector from './../../../components/_common/selectors/countrySelector.vue'; 

    /*  Modals  */
    import createOrEditCompanyOrIndividualModal from './../../../components/_common/modals/createOrEditCompanyOrIndividualModal.vue';

    /*  Switches   */
    import showModeSwitch from './../../../components/_common/switches/showModeSwitch.vue'; 

    export default {
        props: {
            client: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            },
            align: {
                type: String,
                default: 'left'
            },
            showCompanyOrUserSelector: { 
                type: Boolean,
                default: false
            },
            showClientOrSupplierSelector: { 
                type: Boolean,
                default: false
            }
        },
        components: { phoneInput, citySelector, countrySelector, createOrEditCompanyOrIndividualModal, showModeSwitch },
        data() {
            return {
                localClient: this.client,
                localEditMode: this.editMode,
                editableCompanyOrIndividual: null,
                isOpenCreateOrEditCompanyOrIndividualModal: false,
                isLoadingClientInfo: false,
                phoneList: null,
                showModeDetails: false
            }
        },
        watch: {

            //  Watch for changes on the client
            client: {
                handler: function (val, oldVal) {
                    if( !_.isEqual(val, this.localClient) ){

                        this.localClient = val;
                        
                        //  Update phones to show/hide
                        this.determinePhonesToShow();

                        //  In a special case, for example when dealing with invoices
                        //  the editor can save an original copy of its own to use to
                        //  detect changes so that we can allow users to save. It happens
                        //  that the "this.determinePhonesToShow()" makes changes when it runs the
                        //  "this.$set()" method which indirectly notifies the parent that
                        //  changes have taken place eventhough that is not desired. To
                        //  avoid this we emit a "reUpdateParent" back to the parent after
                        //  running the "this.determinePhonesToShow()" method to let the parent know
                        //  that the changes made should not be considered for alerting the
                        //  user to save.
                        
                        //this.$emit('reUpdateParent');

                    }
                },
                deep: true
            },
        
            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    this.localEditMode = val;
                }
            }

        },
        computed: {
            clientName: {
                get: function () {
                    //  If this is a company then return the company name
                    //  If this is an individual then return the individual full name
                    return this.localClient.model_type == 'company' ? this.localClient.name : this.localClient.full_name;
                },
                set: function (newVal) {
                    if(this.localClient.model_type == 'company'){
                        this.localClient.name = newVal;
                    }else if(this.localClient.model_type == 'user'){
                        this.localClient.full_name = newVal;
                    }
                }
            }
        },
        methods: {
            updateClient(newCompanyOrIndividual){
                this.localClient = newCompanyOrIndividual;
                this.determinePhonesToShow();
                this.$emit('updated:companyOrIndividual', newCompanyOrIndividual);
            },
            editCompanyOrIndividual(){
                this.isOpenCreateOrEditCompanyOrIndividualModal = true;
                this.editableCompanyOrIndividual = this.localClient;
            },
            determinePhonesToShow(){
                console.log('Phone stage 2');
                if( ((this.localClient || {}).phones || {}).length ){
                    
                    for(var x = 0; x < this.localClient.phones.length; x++){
                            
                        //  Only if we don't already have the show property set to either true/false
                        //  can we continue to add the show property. The show property doesn't exist
                        //  the first time we add the client. At that point we need to set it for the
                        //  first time by setting it equal to true/false. The code below ensures that
                        //  we set it on the first run.
                        if(!('show' in this.localClient.phones[x])){

                            //  Get the current phone and set a new property called show with value "false/true"
                            //  If set to true, it will allow the system to recognize this number as something
                            //  to show when displaying all the phone numbers of the receipent details.

                            //  We will only show the first two and hide the rest incase we have too many phone numbers
                            if(x <= 1){
                                //  Set show to true - meaning that this number will be shown
                                this.$set(this.localClient.phones[x], 'show', true);
                            }else{
                                //  Set show to true - meaning that this number will be hidden                            
                                this.$set(this.localClient.phones[x], 'show', false);
                            }

                        }

                    }
                }

                //  Re-fetch the list of unhidden phones
                this.phoneList = this.getPhones();

                console.log('Phone stage 3');

            },
            getPhones(){
                console.log('Phone stage 2.1');
                var phoneList = '';

                if( ((this.localClient || {}).phones || {}).length ){

                    var showingPhones = this.localClient.phones.filter(function (phone) {
                            //  If this phones show key is set to true - meaning it is not hidden
                            //  then we can continue to get the phone number. Also if the show
                            //  key does not exist at all then we can get the number 
                            return phone.show === true;
                        });

                    console.log('Phone stage 2.2');
                    console.log(showingPhones);

                    if( (showingPhones || {}).length ){
                        
                        console.log('Phone stage 2.3');

                        for(var x = 0; x < showingPhones.length; x++){
                            
                            //  Get the current phone
                            var currentPhone = showingPhones[x];

                            //  Allowed phone types
                            var x, include = ['tel', 'mobile', /* fax */];

                            //  For any of the included phone types
                            for(var y = 0; y < include.length; y++){

                                //  If this number matches the type allowed
                                if( currentPhone.type == include[y]){

                                    //  If this is not the first phone then add a comma
                                    if( x != 0 ){
                                        phoneList += ', ';
                                    }

                                    //  Format the phone numbers as required
                                    phoneList += '(+' + currentPhone.calling_code.calling_code + ') ' + currentPhone.number;

                                }
                            }

                        }

                    }

                    console.log('Phone stage 2.4');

                }

                return phoneList;

            },
            updatePhoneChanges(newPhones){
                
                this.localClient.phones = [];

                for(var x=0; x<newPhones.length; x++){
                    
                    this.localClient.phones.push(newPhones[x]);
                }

                this.determinePhonesToShow();

                this.$emit('updated:phones', newPhones);
            },
        },
        created(){

            this.determinePhonesToShow();

        }
    }

</script>
