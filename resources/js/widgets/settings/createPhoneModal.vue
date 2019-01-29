<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';
</style>

<template>
    
    <!-- Calling Code Selector -->
    <Modal
        title="Add Phone"
        v-model="modalVisible"
        :mask-closable="true"
        @on-close="abortChanges">
        
        <div>
            <Row :gutter="5">
                
                <Col :span="24">
                    <Loader v-if="isLoadingCallingCodes" :loading="isLoadingCallingCodes" type="text" class="text-left">Loading...</Loader>
                </Col>
                
                <Col :span="24" class="mt-2" v-if="!isLoadingCallingCodes && fetchedCallingCodes.length">
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
                    <Select v-model="localPhone.type" :style="{ width:'100%' }" placeholder="Select Phone Type" not-found-text="No phone types found" filterable>
                        <Option v-for="(item, index) in phoneType"  :value="item"  :label="item" :key="index">
                            {{ item }}
                        </Option>
                    </Select>
                </Col>
                <Col :span="24" class="mt-2" v-if="!isLoadingCallingCodes && fetchedCallingCodes.length">
                    <el-input v-model="localPhone.number" size="small" style="width:100%" placeholder="Enter phone number"></el-input>
                </Col>
            </Row>
        </div>

        <span slot="footer" class="dialog-footer">
            <el-button size="small" @click="abortChanges">Cancel</el-button>
            <el-button v-if="!editablePhone" size="small" type="primary" @click="create" :loading="isSaving">Create</el-button>
            <el-button v-if="editablePhone" size="small" type="primary" @click="saveChanges" :loading="isSaving">Save Changes</el-button>
        </span>

    </Modal>  

</template>

<script>

    import Loader from './../../components/_common/loader/Loader.vue'; 

    export default {
        components: { 
            Loader
        },
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
            show: {
                type: Boolean,
                default: false,
            }
        },
        data(){
            return {
                isSaving: false,
                localPhone: {
                    calling_code: '',
                    number:'',
                    type:'',
                },
                phoneType: ['Telephone', 'Mobile'],
                fetchedCallingCodes: [],
                isLoadingCallingCodes: false,
            }
        },
        watch: {
            editablePhone: function (val) {
                if(this.localPhone != val){
                    this.localPhone = val;
                }
            }
        },
        computed:{
            modalVisible:{
                get(){
                    return this.show;
                },
                set(v){ 
                    this.$emit("closed");
                }
            }
        },
        methods: {
            stringifyData(item){

                return item ? JSON.stringify(item) : '';
            },
            updateCallingCode(newCallingCode){
                this.localPhone.calling_code = newCallingCode ? JSON.parse(newCallingCode) : '';
            },
            create() {
                if(this.modelId && this.modelType){

                    const self = this;

                    //  Start loader
                    self.isSaving = true;

                    console.log('Attempt to create phone details...');

                    //  Login data to send
                    let phoneData = {
                        phone: self.localPhone
                    };

                    var modelDetails = '?modelType='+self.modelType+'&modelId='+self.modelId;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', '/api/phones' + modelDetails, phoneData)
                        .then(({data}) => {
                            
                            //  Stop loader
                            self.isSaving = false;

                            var phone = data;
            
                            console.log('newPhone 1');
                            console.log(phone);

                            //  Close modal
                            self.$emit('created',  phone);

                            //  Alert creation success
                            self.$Message.success('Phone details saved sucessfully!');

                            self.resetFields();

                        })         
                        .catch(response => { 
                            console.log('profileWidget.vue - Error creating phone details...');
                            console.log(response);

                            //  Stop loader
                            self.isSaving = false;     
        
                        });

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

                            self.resetFields();

                        })         
                        .catch(response => { 
                            console.log('profileWidget.vue - Error saving phone details...');
                            console.log(response);

                            //  Stop loader
                            self.isSaving = false;     
        
                        });

                }
            },
            abortChanges(){
                this.resetFields();
                this.$emit('closed');
            },
            resetFields(){
                this.localPhone.calling_code = '';
                this.localPhone.number = '';
                this.localPhone.type = '';
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
            }
        },
        created(){
            this.fetchCallingCodes();
        }
    };
</script>