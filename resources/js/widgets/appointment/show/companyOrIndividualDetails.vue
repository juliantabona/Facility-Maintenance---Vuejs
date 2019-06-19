<style scoped>

    .profile-summary >>> input {
        color: #343a40 !important;
    }

</style>

<template>

    <div class="profile-summary" :style="align == 'right' ? 'float: right; text-align: right;' : ''">
            
        <!-- Loader for when loading the profile information -->
        <Loader v-if="isLoadingProfileInfo" :loading="isLoadingProfileInfo" type="text" :style="{ marginTop:'40px' }">Loading {{ refName | lowercase }} details...</Loader>

        <!-- Profile Information -->
        <div v-if="localProfile">

            <!-- View button -->
            <basicButton v-if="!localEditMode && (localProfile.model_type == 'company' || localProfile.model_type == 'user')"
                            :style="{ position:'absolute', bottom:'10px', right:'10px' }" 
                            type="primary" size="small" 
                            :ripple="false"
                            @click.native="$router.push({ name: (localProfile.model_type == 'company' ? 'show-company': 'show-user'), params: { id: localProfile.id } })">
                {{ 'View '+ refName }}
            </basicButton>

            <!-- Edit button -->
            <Poptip v-if="localEditMode" class="mt-2 mb-2" trigger="hover" :content="'Edit '+(localProfile.model_type == 'company' ? 'company' : 'profile')+' details?'">
                <Button class="mt-1 ml-1" icon="ios-create-outline" type="dashed" size="small" @click="editCompanyOrIndividual()">{{ 'Edit '+(localProfile.model_type == 'company' ? 'company' : 'profile')+' details' }}</Button>
            </Poptip>

            <div class="clearfix"></div>

            <!-- Profile Name - For Company/Individual -->
            <p v-if="!localEditMode" class="mt-3 text-dark"><strong>{{ profileName || '___' }}</strong></p>
            <el-input v-if="localEditMode" :placeholder="refName +' name'" disabled v-model="profileName" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

            <div class="clearfix"></div>

            <!-- Profile Email -->
            <p v-if="!localEditMode">{{ localProfile.email || '___' }}</p>
            <el-input v-if="localEditMode" :placeholder="refName +' email'" disabled v-model="localProfile.email" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

            <div class="clearfix mt-1"></div>

            <!-- Profile Phones display -->
            <p v-if="!localEditMode && phoneList">{{ phoneList }}</p>

            <div class="clearfix mt-1"></div>

            <!-- Profile Address -->
            <p v-if="!localEditMode && localProfile.address" class="mt-1">{{ localProfile.address }}</p>

            <!-- Profile City -->
            <p v-if="!localEditMode && localProfile.city" class="mt-1">{{ localProfile.city }}</p>

            <!-- Profile Country -->
            <p v-if="!localEditMode && localProfile.country" class="mt-1">{{ localProfile.country }}</p>

            <div class="clearfix"></div>
            
            <!-- Profile Phones editor -->
            <phoneInput :style="(align == 'right' ? 'float: right; text-align: left;' : '') + 'max-width:360px'"
                        v-if="localEditMode" class="mb-2"  
                        :modelId="localProfile.id" 
                        :modelType="localProfile.model_type" 
                        :phones="localProfile.phones" 
                        :minLimit="1"
                        :maxLimit="3"
                        selectedType="mobile"
                        :disabledTypes="[]"   
                        selectedServiceProvider="Orange"
                        :disabledServiceProviders="[]"  
                        :deletable="false"
                        :hidedable="isPhoneHideable"
                        :editable="false"
                        :removeDuplicates="true"
                        :showIcon="true" 
                        :showAddPhoneBtn="showAddPhoneBtn"
                        onIcon="ios-eye-outline" offIcon="ios-eye-off-outline" 
                        title="Show:" onText="Yes" offText="No" 
                        poptipMsg="Turn on to show"
                        @updated="updatePhoneChanges($event)">
            </phoneInput>

        </div>

        <!-- No profile Information Alert -->
        <div v-if="!localProfile && !isLoadingProfileInfo">
            <Alert :style="{maxWidth: '250px'}" type="warning">
                No {{ refName | lowercase }} selected
            </Alert>
        </div>

        <!-- 
            MODAL TO CREATE/EDIT COMPANY/INDIVIDUAL
        -->
        <createOrEditCompanyOrIndividualModal 
            v-if="isOpenCreateOrEditCompanyOrIndividualModal"
            :editableCompanyOrIndividual="editableCompanyOrIndividual"
            :showCompanyOrUserSelector="showCompanyOrUserSelector"
            :showProfileOrSupplierSelector="showProfileOrSupplierSelector"
            @visibility="isOpenCreateOrEditCompanyOrIndividualModal = $event"
            @updated="updateProfile($event)"
            @created="updateProfile($event)">
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

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    export default {
        props: {
            refName: {
                type: String,
                default: 'Profile'
            },
            profile: {
                type: Object,
                default: null
            },
            modelId: {
                type: Number,
                default: null
            },
            modelType: {
                type: String,
                default: ''
            },
            align: {
                type: String,
                default: 'left'
            },
            showCompanyOrUserSelector: { 
                type: Boolean,
                default: false
            },
            showProfileOrSupplierSelector: { 
                type: Boolean,
                default: false
            },
            editMode: { 
                type: Boolean,
                default: false
            },
            showAddPhoneBtn:{
                type: Boolean,
                default: true                
            },
            isPhoneHideable:{
                type: Boolean,
                default: true                
            },
        },
        components: { phoneInput, citySelector, countrySelector, createOrEditCompanyOrIndividualModal, showModeSwitch, basicButton },
        data() {
            return {
                localProfile: this.profile,
                localEditMode: this.editMode,
                editableCompanyOrIndividual: null,
                isOpenCreateOrEditCompanyOrIndividualModal: false,
                isLoadingProfileInfo: false,
                phoneList: null,
                showModeDetails: false
            }
        },
        watch: {

            //  Watch for changes on the profile
            profile: {
                handler: function (val, oldVal) {
                    if( !_.isEqual(val, this.localProfile) ){
                        
                        this.localProfile = val;
                        
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
            profileName: {
                get: function () {
                    //  If this is a company then return the company name
                    //  If this is an individual then return the individual full name
                    return this.localProfile.model_type == 'company' ? this.localProfile.name : this.localProfile.full_name;
                },
                set: function (newVal) {
                    if(this.localProfile.model_type == 'company'){
                        this.localProfile.name = newVal;
                    }else if(this.localProfile.model_type == 'user'){
                        this.localProfile.full_name = newVal;
                    }
                }
            }
        },
        methods: {
            updateProfile(newCompanyOrIndividual){
                this.localProfile = newCompanyOrIndividual;
                this.determinePhonesToShow();
                this.$emit('updated:companyOrIndividual', newCompanyOrIndividual);
            },
            editCompanyOrIndividual(){
                this.isOpenCreateOrEditCompanyOrIndividualModal = true;
                this.editableCompanyOrIndividual = this.localProfile;
            },
            determinePhonesToShow(){
                console.log('Phone stage 2');
                if( ((this.localProfile || {}).phones || {}).length ){
                    
                    for(var x = 0; x < this.localProfile.phones.length; x++){
                            
                        //  Only if we don't already have the show property set to either true/false
                        //  can we continue to add the show property. The show property doesn't exist
                        //  the first time we add the profile. At that point we need to set it for the
                        //  first time by setting it equal to true/false. The code below ensures that
                        //  we set it on the first run.
                        if(!('show' in this.localProfile.phones[x])){

                            //  Get the current phone and set a new property called show with value "false/true"
                            //  If set to true, it will allow the system to recognize this number as something
                            //  to show when displaying all the phone numbers of the receipent details.

                            //  We will only show the first two and hide the rest incase we have too many phone numbers
                            if(x <= 1){
                                //  Set show to true - meaning that this number will be shown
                                this.$set(this.localProfile.phones[x], 'show', true);
                            }else{
                                //  Set show to true - meaning that this number will be hidden                            
                                this.$set(this.localProfile.phones[x], 'show', false);
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

                if( ((this.localProfile || {}).phones || {}).length ){

                    var showingPhones = this.localProfile.phones.filter(function (phone) {
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
                
                this.localProfile.phones = [];

                for(var x=0; x < newPhones.length; x++){
                    if(x <= 1){
                        newPhones[x].show = true;
                    }
                    
                    this.localProfile.phones.push(newPhones[x]);
                }

                this.determinePhonesToShow();

                this.$emit('updated:phones', newPhones);
            },
            fetchProfile(){
                
                if( ( this.modelId && (this.modelType == 'user' || this.modelType == 'company') ) ){

                    if( this.modelType == 'user' ){
                        var destination = 'users';
                    }else if( this.modelType == 'company' ){
                        var destination = 'companies'; 
                    }

                    const self = this;

                    //  Additional data to eager load along with the company found
                    var connections = '?connections=phones';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/'+destination+'/'+this.modelId+connections)
                        .then(({data}) => {

                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            self.localProfile = data;

                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                    }
            },
        },
        created(){

            this.determinePhonesToShow();

            if( !this.profile ){
                this.fetchProfile();
            }

        }
    }

</script>
