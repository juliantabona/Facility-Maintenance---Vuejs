<template>
    <div>
        <Modal
            title="Priorities"
            v-model="modalVisible"
            :mask-closable="true"
            @on-close="abortChanges">

            <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading...</Loader>
            
            <Select v-if="!isLoading && fetchedPriorities" v-model="localPriority" :style="{ width:'100%' }" placeholder="Select priority" not-found-text="No priorities found">
                <Option v-for="item in fetchedPriorities" :value="JSON.stringify(item)" :key="item.id">
                    {{ item.name }}
                </Option>
            </Select>
            
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="abortChanges">Cancel</el-button>
                <el-button size="small" type="primary" @click="saveChanges" :loading="isSaving">Update</el-button>
            </span>
        </Modal>    
    </div>
</template>
<script>

    import Loader from './../loader/Loader.vue';

    export default {
        props:{
            show: {
                type: Boolean,
                default: false,
            },
            priority: {
                type: Object,
                default: null
            }
        },
        components: { Loader },
        data(){
            return{
                updatedPriority: null,
                fetchedPriorities: [],
                isSaving: false,
                isLoading: false
            }
        },
        computed:{
            localPriority:{
                get(){
                    if(this.priority){
                        return JSON.stringify(this.priority);
                    }
                },
                set(newVal){
                    this.updatedPriority = JSON.parse(newVal);
                }
            },
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

                console.log('Start getting jobcard priorities...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/priorities?priority_type=jobcard')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get jobcard lifecycle data
                        self.fetchedPriorities = data;
                    })         
                    .catch(response => { 
                        console.log('updatePrioritiesModal.vue - Error getting jobcard priorities...');
                        console.log(response);

                        //  Stop loader
                        self.isLoading = false;     
                    });
            },
            saveChanges(){
                console.log('stage 3');
                //  Close modal
                this.$emit('updated',  this.updatedPriority);
            },
            abortChanges(){
                this.$emit('closed');
            }
        },
        created () {
            this.fetch();
        }
    }
</script>