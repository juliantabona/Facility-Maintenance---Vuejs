<template>
    <div>
        <Modal
            title="Add Lifecycle"
            v-model="modalVisible"
            :mask-closable="true"
            @on-close="abortChanges">
            <Alert closable>
                <template slot="desc">
                    <Icon type="ios-bulb-outline" :size="24" class="mb-2"></Icon>
                    <br>
                    Jobcard lifecycles help you manage the progress of your jobcard through several stages. An example is monitoring
                    whether the jobcard is "Open", "Pending" or "Closed". This will quickly allow you to handle your jobcards 
                    efficiently. Watch <a href="#">Short Video</a>.
                </template>
            </Alert>
            
            <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading...</Loader>
            
            <Select v-if="!isLoading && lifecycleTemplateOptions" v-model="lifecycleTemplateSelected" :style="{ width:'100%' }" placeholder="Select jobcard lifecycle">
                <Option v-for="item in lifecycleTemplateOptions" :value="item.id" :key="item.id">{{ item.name }}</Option>
            </Select>
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="abortChanges">Cancel</el-button>
                <el-button size="small" type="primary" @click="saveChanges" :loading="isSaving">Update Status</el-button>
            </span>
        </Modal>    
    </div>
</template>
<script>

    import Loader from './../../Loader.vue';

    export default {
        props: {
            jobcard: {
                type: Object,
                //  Props with type Object/Array must use a factory function to return the default value.
                default: () => {}
            },
            show: {
                type: Boolean,
                default: false,
            }
        },
        components: { Loader },
        data(){
            return{
                lifecycleTemplateSelected: '',
                lifecycleTemplateOptions: null,
                isSaving: false,
                isLoading: false
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
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting jobcard lifecycle templates...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/jobcards/lifecycle/templates')
                    .then(({data}) => {

                        //  Stop loader
                        self.isLoading = false;

                        //  Get jobcard lifecycle templates data
                        self.lifecycleTemplateOptions = data;

                        //  Use a selected jobcard template if any
                        var x;

                        for(x = 0; x < self.lifecycleTemplateOptions.length; x++){
                            if(self.lifecycleTemplateOptions[x].selected){
                                self.lifecycleTemplateSelected = self.lifecycleTemplateOptions[x].id;
                            }
                        }
                    })         
                    .catch(response => { 
                        console.log('addLifecycleModal.vue - Error getting jobcard lifecycle templates...');
                        console.log(response);

                        //  Stop loader
                        self.isLoading = false;     
                    });
            },
            saveChanges(){
                const self = this;

                //  Start loader
                self.isSaving = true;

                var jobcardId = this.jobcard.id || this.$route.params.id;

                console.log('Attempt to add the lifecycle template to the jobcard...');

                //  lifecycle template data
                let lifecycleData = {
                    selectedTemplateId: this.lifecycleTemplateSelected
                };

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/jobcards/'+jobcardId+'/addLifecycle', lifecycleData)
                    .then(({data}) => {

                        //  Stop loader
                        self.isSaving = false;
                        
                        self.$Notice.success({
                            title: 'Jobcard lifecycle status updated'
                        });

                        //  Close modal
                        this.$emit('updated', data);
                    })         
                    .catch(response => { 
                        console.log('addLifecycleModal.vue - Error attempting to to add the lifecycle template to the jobcard...');
                        console.log(response);

                        //  Stop loader
                        self.isSaving = false;

                        //  Close modal
                        this.$emit('closed');
                    });
            },
            abortChanges(){
                this.$emit('closed');
            }
        },
        created() {
            this.fetch();
        }
    }
</script>