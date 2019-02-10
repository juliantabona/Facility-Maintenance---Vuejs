<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';
</style>

<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSaving" 
            :hideModal="hideModal"
            :title="editablePhone ? 'Edit Phone' : 'Add Phone'"
            :okText="editablePhone ? 'Save Changes' : 'Create'" 
            cancelText="Cancel"
            @on-ok="handlePhone()" 
            @on-cancel="abortChanges()"
            @visibility="$emit('visibility', $event)">

            <template slot="content">
                
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
                        <Select v-model="localPhone.type" :style="{ width:'100%' }" placeholder="Select Phone Type" not-found-text="No phone types found" filterable>
                            <Option v-for="(item, index) in phoneType"  :value="item.value"  :label="item.name" :key="index">
                                {{ item.name }}
                            </Option>
                        </Select>

                    </Col>

                    <Col :span="24" class="mt-2" v-if="!isLoadingCallingCodes && fetchedCallingCodes.length">
                        
                        <!-- Phone Number -->
                        <el-input v-model="localPhone.number" size="small" style="width:100%" placeholder="Enter phone number"></el-input>
                    
                    </Col>
                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>

<script>

    import mainModal from './main.vue'; 
    import Loader from './../loaders/Loader.vue';

    export default {
        components: { mainModal, Loader },
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
            }
        },
        data(){
            return {
                localPhone: this.currentPhone(),
                _localPhoneBeforeChange: this.storedPhone(),
                phoneType: [
                    { name: 'Telephone', value: 'tel' }, 
                    { name: 'Mobile', value: 'mobile' },
                    { name: 'Fax', value: 'fax' }
                ],
                fetchedCallingCodes: [],

                isLoadingCallingCodes: false,
                hideModal: false,
                isSaving: false,
            }
        },
        methods: {
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
                if(this.editablePhone){

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
            this.fetchCallingCodes();
        }
    };
</script>