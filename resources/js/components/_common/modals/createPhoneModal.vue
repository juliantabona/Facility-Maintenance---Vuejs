<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';

    .phone-content-tabs >>> .ivu-select-dropdown-list{
        overflow-y: auto;
        max-height: 150px;
    }

    .hide-tabs >>> .ivu-tabs-bar{
        display:none !important;
    }

</style>

<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSaving" 
            :hideModal="hideModal"
            :title="editablePhone ? 'Edit Phone' : 'Add Phone'"
            :okText="getOkText" 
            cancelText="Cancel"
            @on-ok="handlePhone()" 
            @on-cancel="abortChanges()"
            @visibility="$emit('visibility', $event)">

            <template slot="content">
                
                <Tabs :value="selectedTab" :animated="false" :class="'phone-content-tabs'+(editablePhone ? ' hide-tabs': '')" 
                      :style="{ minHeight: '250px' }"
                      @on-click="(selectedTab = $event)">
                
                    <!-- Select Existing Account -->
                    <TabPane v-if="!editablePhone" label="Existing Phones" name="existingPhonesTab" :style="{ minHeight: '250px' }">

                        <div :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                            <existingPhoneSelector
                                :modelId="modelId"
                                :modelType="modelType"
                                :disabledTypes="disabledTypes" 
                                @addNew="switchToAddPhoneTab()"    
                                @selected:phone="updateSelectedPhoneChanges($event)">
                            </existingPhoneSelector>
                        </div>

                    </TabPane>
                
                    <!-- Select Existing Account -->
                    <TabPane :label="editablePhone ? '' : 'Add New Phone'" name="addPhoneTab" :style="{ minHeight: '250px' }">

                        <!-- Phone Details -->
                        <Row :gutter="5">
                            
                            <Col :span="24">
                                <!-- Loader -->
                                <Loader v-if="isLoadingCallingCodes" :loading="isLoadingCallingCodes" type="text" class="text-left">Loading...</Loader>
                            </Col>
                            
                            <Col :span="24" class="mt-2" v-if="!isLoadingCallingCodes && fetchedCallingCodes.length">
                                
                                <!-- Calling Code Selector -->
                                <Select :value="stringifyData(localPhone.calling_code)"
                                        @on-change="updateCallingCode($event)"
                                        :style="{ width:'100%' }" placeholder="Select Country Code" not-found-text="No calling codes found" filterable>
                                    <Option
                                        v-for="(item, index) in fetchedCallingCodes" 
                                        :value="stringifyData(item)" 
                                        :label="'+'+item.calling_code"
                                        :key="index">
                                        <span style="width:30px;" class="mr-1" v-html="item.flag"></span>
                                        <span>{{ item.country }} (+{{ item.calling_code }})</span>
                                    </Option>
                                </Select>

                            </Col>

                            <Col :span="24" class="mt-2" v-if="!isLoadingCallingCodes && fetchedCallingCodes.length">
                                <!-- Phone Type Selector -->
                                <Select v-model="localSelectedType" @on-change="localPhone.type = $event" :style="{ width:'100%' }" placeholder="Select Phone Type" not-found-text="No phone types found" filterable>
                                    <Option v-for="(item, index) in phoneType"  :value="item.value"  :label="item.name" :key="index"
                                            :disabled="disabledTypes.includes(item.value)">
                                        {{ item.name }}
                                    </Option>
                                </Select>

                            </Col>
                            <Col v-if="!isLoadingCallingCodes && fetchedCallingCodes.length && localSelectedType == 'mobile'" :span="24" class="mt-2" >
                                <!-- Service Provider Selector -->
                                <Select v-model="localSelectedServiceProvider" @on-change="localPhone.provider = $event" :style="{ width:'100%' }" placeholder="Select Service Provider" not-found-text="No service providers found" filterable>
                                    <Option v-for="(item, index) in serviceProviders"  :value="item"  :label="item" :key="index"
                                            :disabled="disabledServiceProviders.includes(item)">
                                        {{ item }}
                                    </Option>
                                </Select>

                            </Col>
                            <Col :span="24" class="mt-2" v-if="!isLoadingCallingCodes && fetchedCallingCodes.length">
                                
                                <!-- Phone Number -->
                                <el-input type="text" v-model="localPhone.number" @keypress.native="isNumber($event)" :maxlength="8" size="small" style="width:100%" placeholder="Enter phone number"></el-input>
                            
                            </Col>
                        </Row>

                    </TabPane>

                </Tabs>

            </template>
            
        </mainModal>    
    </div>
</template>

<script>

    import mainModal from './main.vue'; 
    import Loader from './../loaders/Loader.vue';

    /*  Selectors   */
    import existingPhoneSelector from './../../../components/_common/selectors/existingPhoneSelector.vue'; 


    export default {
        components: { mainModal, Loader, existingPhoneSelector },
        props:{
            editablePhone: {
                type: Object,
                default: null, 
            },
            modelId: {
                type: Number,
                default: null,  
            },
            modelType: {
                type: String,
                default: null,  
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
        },
        data(){
            return {
                selectedTab: null,
                localPhone: this.currentPhone(),
                localSelectedType: this.selectedType,
                _localPhoneBeforeChange: this.storedPhone(),
                phoneType: [
                    { name: 'Telephone', value: 'tel' }, 
                    { name: 'Mobile', value: 'mobile' },
                    { name: 'Fax', value: 'fax' }
                ],
                localSelectedServiceProvider: this.selectedServiceProvider,
                serviceProviders: ['Orange', 'Mascom', 'BeMobile'],
                fetchedCallingCodes: [],

                isLoadingCallingCodes: false,
                hideModal: false,
                isSaving: false,
            }
        },
        computed:{
            getOkText: function(){
                return this.editablePhone ? 'Save Changes' : (this.selectedTab == 'existingPhonesTab' ? '' : 'Create');
            }
        },
        methods: {
            switchToAddPhoneTab(){
                this.selectedTab = 'addPhoneTab';
            },
            updateSelectedPhoneChanges(phone){
                this.$emit('selected',  phone);
                this.closeModal();
            },
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            },
            currentPhone(){
                //  If we have an editable phone we need to use Object.assign because
                //  the object is still reactive to the parent. So when we edit the 
                //  modal the changes reflec to the parent eventhough we have not
                //  saved the details to the database. This is not desirable as
                //  changes should not show until this is publised and modal closes.
                //  In case we don't have any editable data, we will default to a
                //  template
                return this.editablePhone ? Object.assign({}, this.editablePhone) : 
                        {
                            calling_code: '',
                            number:'',
                            type:'',
                        };
            },
            storedPhone(){
                //  We need to store an instance of the editable phone if exists so that
                //  when the user decides to cancel we can return this data to overide
                //  the changes the user made.
                return this.editablePhone ? Object.assign({}, this.editablePhone) : null;
            },
            stringifyData(item){

                return item ? JSON.stringify(item) : '';
            },
            updateCallingCode(newCallingCode){
                this.localPhone.calling_code = newCallingCode ? JSON.parse(newCallingCode) : '';
            },
            fetchCallingCodes() {
                const self = this;

                //  Start loader
                self.isLoadingCallingCodes = true;

                console.log('Start getting calling codes...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/callingcodes')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoadingCallingCodes = false;

                        //  Get calling codes
                        self.fetchedCallingCodes = data;

                        console.log('New fetched calling codes');

                        console.log(self.fetchedCallingCodes);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingCallingCodes = false;

                        console.log('callingCodesSelector.vue - Error getting calling codes...');
                        console.log(response);    
                    });
            },
            handlePhone(){

                if( !this.editablePhone ){
                    this.create();
                }else{
                    this.saveChanges();
                }

            },
            create() {
                const self = this;

                //  Login data to send
                let phoneData = {
                    phone: self.localPhone
                };

                if(this.modelId && this.modelType){
                    
                    //  Start loader
                    self.isSaving = true;

                    console.log('Attempt to create phone details...');

                    var modelDetails = '?modelType='+self.modelType+'&modelId='+self.modelId;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', '/api/phones' + modelDetails, phoneData)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isSaving = false;

                            var phone = data;

                            //  Alert parent and pass updated created phone data
                            self.$emit('created',  phone);

                            //  Alert creation success
                            self.$Message.success('Phone details saved sucessfully!');

                            //  Close modal
                            self.closeModal();

                        })         
                        .catch(response => { 
                            console.log('profileWidget.vue - Error creating phone details...');
                            console.log(response);

                            //  Stop loader
                            self.isSaving = false;     
        
                        });

                }else{
                    
                    //  Alert parent and pass phone data

                    //  Close modal
                    this.$emit('created',  self.localPhone);

                    //  Close modal
                    this.closeModal();

                }
            },
            saveChanges() {
                if(this.editablePhone && this.modelType){

                    const self = this;

                    //  Start loader
                    self.isSaving = true;

                    console.log('Attempt to save phone details...');

                    //  Login data to send
                    let phoneData = {
                        phone: self.localPhone
                    };

                    var modelDetails = '?modelType='+self.modelType;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', '/api/phones/'+this.editablePhone.id + modelDetails, phoneData)
                        .then(({data}) => {
                            
                            //  Stop loader
                            self.isSaving = false;

                            var phone = data;

                            //  Close modal
                            self.$emit('updated',  phone);

                            //  Alert creation success
                            self.$Message.success('Phone details saved sucessfully!');

                            self.closeModal();

                        })         
                        .catch(response => { 
                            console.log('profileWidget.vue - Error saving phone details...');
                            console.log(response);

                            //  Stop loader
                            self.isSaving = false;     
        
                        });

                }else{
                    
                    //  Alert parent and pass phone data

                    //  Close modal
                    this.$emit('updated',  this.localPhone);

                    //  Close modal
                    this.closeModal();

                }
            },
            closeModal(){
                this.hideModal = true;
            },
            abortChanges(){
                Object.assign(this.localPhone, this._localPhoneBeforeChange);
            }
        },
        created(){
            for(var x=0; x < this.phoneType.length; x++){
                if( this.phoneType[x].name == this.selectedType ){
                    this.localPhone.type = [ this.phoneType[x] ];
                }
            }
            
            this.fetchCallingCodes();
        }
    };
</script>