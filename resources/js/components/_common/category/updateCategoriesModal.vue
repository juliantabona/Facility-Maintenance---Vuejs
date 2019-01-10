<template>
    <div>
        <Modal
            title="Categories"
            v-model="modalVisible"
            :mask-closable="true"
            @on-close="abortChanges">

            <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading...</Loader>
            
            <Select v-if="!isLoading && fetchedCategories" v-model="localCategories" :style="{ width:'100%' }" placeholder="Select categories" multiple>
                <Option v-for="item in fetchedCategories" :value="JSON.stringify(item)" :key="item.id">{{ item.name }}</Option>
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
            categories: {
                type: Array,
                default: () => []
            }
        },
        components: { Loader },
        data(){
            return{
                updatedCategories: [],
                fetchedCategories: [],
                isSaving: false,
                isLoading: false
            }
        },
        computed:{
            localCategories:{
                get(){
                    return this.categories.map(item => JSON.stringify(item));
                },
                set(val){
                    this.updatedCategories = val.map(item => JSON.parse(item));
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

                console.log('Start getting jobcard categories...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/categories?category_type=jobcard')
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get jobcard lifecycle data
                        self.fetchedCategories = data;
                    })         
                    .catch(response => { 
                        console.log('updateCategoriesModal.vue - Error getting jobcard categories...');
                        console.log(response);

                        //  Stop loader
                        self.isLoading = false;     
                    });
            },
            saveChanges(){
                //  Close modal
                this.$emit('updated',  this.updatedCategories);
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