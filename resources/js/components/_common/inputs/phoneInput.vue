<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';
</style>

<template>
    
    <!-- Calling Code Selector -->
    <div>
        <Row v-for="(phone, i) in localPhones" class="mb-2" :key="i" :gutter="5">
            <Col>
            
                <!-- Phone Number -->
                <el-input :value="localPhones[i].number" disabled size="small" style="width:100%" placeholder="Enter phone number">
                    
                    <!-- Prefix - Calling Code -->
                    <template slot="prepend">+{{ localPhones[i].calling_code.calling_code }}</template>

                    <template v-if="deletable || hidedable || editable" slot="append">
                        
                        <!-- Show mode switch -->
                        <toggleSwitch 
                            v-if="hidedable"
                            v-bind:toggleValue.sync="localPhones[i].show"
                            @update:toggleValue="runUpdate()"
                            :ripple="false" :showIcon="showIcon" :onIcon="onIcon" :offIcon="offIcon" 
                            :title="title" :onText="onText" :offText="offText" :poptipMsg="poptipMsg"
                            class="mr-2">
                        </toggleSwitch>

                        <!-- Delete button -->
                        <Poptip v-if="deletable" confirm title="Are you sure you want to delete this phone number?"
                                ok-text="Yes" cancel-text="No" @on-ok="removePhone(localPhones[i], i)" placement="left-start">
                                <Icon class="field-icon mr-2" type="ios-trash-outline" :size="20"/>
                        </Poptip>

                        <!-- Edit button -->
                        <Poptip v-if="editable" trigger="hover" content="Edit this phone number?">
                            <Icon class="field-icon" type="ios-create-outline" :size="20" @click="editPhone(localPhones[i])"/>
                        </Poptip>
                    </template>
                
                </el-input>
            </Col>
        </Row>
        
        <Row>
            <Col :span="24">

                <!-- No phones alert -->
                <Alert v-if="!(localPhones || {}).length" type="warning" show-icon class="float-left">
                    No Phone Numbers
                </Alert>
                
                <!-- Add phone button -->
                <Button v-if="(numberLimit == 0) || (((localPhones || {}).length || 0) < numberLimit)" 
                    class="mt-1 ml-1" icon="ios-add" type="dashed" size="small" @click="isOpenAddPhoneModal = true">Add Phone</Button>
            </Col>
        </Row>

        <!-- 
            MODAL TO ADD PHONE NUMBERS
        -->
        <createPhoneModal 
            v-if="isOpenAddPhoneModal"
            :modelId="modelId"         
            :modelType="modelType"
            :selectedType="selectedType"
            :disabledTypes="disabledTypes"
            :editablePhone="editablePhone"
            :selectedServiceProvider="selectedServiceProvider"
            :disabledServiceProviders="disabledServiceProviders"  
            @visibility="closeModal($event)"
            @created="storeCreated($event)"
            @updated="updateChanges($event)">
        </createPhoneModal>

    </div>
</template>

<script>

    /*  Loaders   */
    import Loader from './../loaders/Loader.vue'; 

    /*  Switches   */
    import toggleSwitch from './../switches/toggleSwitch.vue'; 

    /*  Modals   */
    import createPhoneModal from './../modals/createPhoneModal.vue'; 

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            Loader, createPhoneModal, toggleSwitch
        },
        props:{
            //  For the modal
            modelId: {
                type: Number,
                default: null,  
            },
            modelType: {
                type: String,
                default: null,  
            },
            phones: {
                type: Array,
                default: null,
            },
            deletable: {
                type: Boolean,
                default: false,
            },
            hidedable: {
                type: Boolean,
                default: false,
            },
            editable: {
                type: Boolean,
                default: false,
            },
            numberLimit: {
                type: Number,
                default: 0,
            },
            selectedType: {
                type: String,
                default: null, 
            },
            disabledTypes:{
                type: Array,
                default: () => { return [] },  
            },
            selectedServiceProvider: {
                type: String,
                default: null, 
            },
            disabledServiceProviders:{
                type: Array,
                default: () => { return [] },  
            },

            //  For the toggle switch
            showIcon:{
                type: Boolean,
                default: true                
            },
            onIcon:{
                type: String,
                default: 'ios-eye-off-outline'    
            },
            offIcon:{
                type: String,
                default: 'ios-eye-outline'    
            },
            title:{
                type: String,
                default: 'Show:'                
            },
            onText:{
                type: String,
                default: 'Yes'    
            },
            offText:{
                type: String,
                default: 'No'    
            },
            poptipMsg:{
                type: String,
                default: 'Turn on to show'    
            }
        },
        data(){
            return {
                editablePhone: null,
                isOpenAddPhoneModal: false,
                localPhones: this.phones
            }
        },
        watch: {

            //  Watch for changes on the phones
            phones: {
                handler: function (val, oldVal) {
                    this.localPhones = val;
                },
                deep: true
            }

        },
        methods: {
            editPhone(phone){
                this.editablePhone = phone;
                this.isOpenAddPhoneModal = true;
            },
            removePhone(phone, index){
                console.log('delete');
                console.log(phone);
                if(phone){

                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    console.log('Attempt to remove phone details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('delete', '/api/phones/'+phone.id)
                        .then(({data}) => {
                            
                            //  Stop loader
                            self.isLoading = false;

                            //  Remove phone from list
                            self.localPhones.splice(index, 1);

                            //  Update parent
                            self.runUpdate();

                            //  Alert creation success
                            self.$Message.success('Phone details removed sucessfully!');

                        })         
                        .catch(response => { 
                            console.log('profileWidget.vue - Error remove phone details...');
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;     
        
                        });

                }
            },
            storeCreated(newPhone){
                //  Incase the localPhones is null and not an array
                //  make sure to force it to be an array so that we
                //  don't get an error when using the push() method
                //  which requires the object to be an array
                if( !(this.localPhones || {}).length ){
                    this.localPhones = [];
                }
                this.localPhones.push(newPhone);
                this.runUpdate();
                this.closeModal();               
            },
            updateChanges(newPhone){
                for(var x=0; x < this.localPhones.length; x++){
                    if(this.localPhones[x].id == newPhone.id){
                        this.localPhones[x] = newPhone;
                    }
                }
                this.runUpdate();
                this.closeModal();
            },
            closeModal($visibility){
                this.isOpenAddPhoneModal = $visibility;
                if( !$visibility ){
                    this.editablePhone = null;
                }
            },
            updateCallingCode(newCallingCode, i){
                this.localPhones[i].calling_code = JSON.parse(newCallingCode);
            },
            runUpdate(){
                this.$emit('updated',  this.localPhones);
            }
        }
    };
</script>