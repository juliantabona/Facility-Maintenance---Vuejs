<template>
    <div>
        <Modal
            :title="section.name"
            v-model="modalVisible"
            :mask-closable="true"
            @on-close="abortChanges">
            <Row v-if="(section.fields || {}).length != 0" 
                :gutter="20">
                <oq-Template-Field 
                    v-for="(field, index) in section.fields" :key="index" 
                    :field="field" :section="section" :showToolBar="false">
                </oq-Template-Field>
            </Row>
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="abortChanges">Cancel</el-button>
                <el-button size="small" type="primary" @click="saveChanges" :loading="isSaving">Update Status</el-button>
            </span>
        </Modal>    
    </div>
</template>
<script>
    export default {
        props:{
            show: {
                type: Boolean,
                default: false,
            },
            nextStep: {
                type: Number,
                default: null
            },
            section: {
                type: Object,
                default: () => {}
            },
            template: {
                type: Object,
                default: () => {}
            }
        },
        data(){
            return{
                sectionBeforeChange: {},
                isSaving: false
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
        watch: {
            show: function (val) {
                this.sectionBeforeChange = JSON.stringify( Object.assign({}, this.section) );
            }
        },
        methods: {
            saveChanges(){
                const self = this;

                //  Start loader
                self.isSaving = true;

                console.log('Attempt to update lifecycleusing the following...');
                

                //  Login data to send
                let lifecycleData = {
                    template: this.template,
                    nextStep: this.nextStep
                };

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/jobcards/'+this.$route.params.id+'/lifecycle', lifecycleData)
                    .then(({data}) => {

                        //  Stop loader
                        self.isSaving = false;
                        
                        self.$Notice.success({
                            title: 'Jobcard status updated'
                        });

                        //  Close modal
                        this.$emit('updated');
                    })         
                    .catch(response => { 
                        console.log('Login.vue - Error loggin in...');
                        console.log(response);

                        //  Stop loader
                        self.isSaving = false;

                        //  Close modal
                        this.$emit('closed');
                    });
            },
            abortChanges(){
                Object.assign(this.section, JSON.parse( this.sectionBeforeChange ) );
                this.$emit('closed');
            }
        }
    }
</script>