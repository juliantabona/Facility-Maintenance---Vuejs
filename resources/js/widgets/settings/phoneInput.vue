<style scoped>
    @import 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css';
</style>

<template>
    
    <!-- Calling Code Selector -->
    <div>
        <Row v-for="(phone, i) in localPhones" class="mb-2" :key="i" :gutter="5">
            <Col :span="24" v-if="!isLoadingCallingCodes && fetchedCallingCodes.length">
                <el-input v-model="localPhones[i].number" @input.native="runUpdate()" size="small" style="width:100%" placeholder="Enter phone number">
             
                    <template slot="prepend">+{{ localPhones[i].calling_code.calling_code }}</template>
                    <template slot="append">
                        <Poptip
                            confirm
                            title="Are you sure you want to remove this phone number?"
                            ok-text="Yes"
                            cancel-text="No"
                            @on-ok="removePhone(localPhones[i], i)"
                            placement="left-start">
                            <Icon class="field-icon mr-2" type="ios-trash-outline" :size="20"/>
                        </Poptip>
                        <Icon class="field-icon" type="ios-create-outline" :size="20" @click="editPhone(localPhones[i])"/>
                    </template>
                
                </el-input>
            </Col>
        </Row>

        <Row v-if="isLoadingCallingCodes  && !fetchedCallingCodes.length">
            <Col :span="24">
                <Loader :loading="isLoadingCallingCodes" type="text" class="text-left">Loading...</Loader>
            </Col>
        </Row>
        
        <Row>
            <Col :span="24">
                <Alert v-if="!isLoadingCallingCodes && !localPhones.length" type="warning" show-icon class="float-left">
                    No Phone Numbers
                </Alert>
                <Button class="mt-1 ml-1" icon="ios-add" type="dashed" size="small" @click="isOpenAddPhoneModal = true">Add Phone</Button>
            </Col>
        </Row>

        <!-- 
            MODAL TO ADD PHONE NUMBERS
        -->
        <createPhoneModal
            :modelId="modelId"
            :modelType="modelType"
            :editablePhone="editablePhone"
            v-show="isOpenAddPhoneModal" 
            :show="isOpenAddPhoneModal"
            v-on:closed="closeModal"
            v-on:created="storeCreated($event)"
            v-on:updated="updateChanges($event)">
        </createPhoneModal>

    </div>
</template>

<script>

    import Loader from './../../components/_common/loader/Loader.vue'; 
    import createPhoneModal from './createPhoneModal.vue'; 

    export default {
        components: { 
            Loader, createPhoneModal
        },
        props:{
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
            }
        },
        data(){
            return {
                editablePhone: null,
                isOpenAddPhoneModal: false,
                localPhones: this.phones,
                isLoadingCallingCodes: false,
                fetchedCallingCodes: []
            }
        },
        watch: {
            phones: function (val) {
                console.log('Phones - stage 1');
                if(this.localPhones != val){
                    console.log('Phones - stage 2');
                    this.localPhones = val;
                }
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
            storeCreated(){
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
            closeModal(){
                this.isOpenAddPhoneModal = !this.isOpenAddPhoneModal;
            },
            updateCallingCode(newCallingCode, i){
                this.localPhones[i].calling_code = JSON.parse(newCallingCode);
            },
            runUpdate(){
                this.$emit('updated',  this.localPhones);
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