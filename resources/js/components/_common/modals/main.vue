<template>
    <div>
        <!-- Modal -->
        <Modal :title="title" v-model="modalVisible" :mask-closable="true" 
               @on-visible-change="detectClose">

            <!-- White overlay when loading -->
            <Spin size="large" fix v-if="isSaving"></Spin>

            <slot name="content"></slot>

            <!-- Ok/Cancel Buttons -->
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="cancel()">{{ cancelText }}</el-button>
                <el-button size="small" type="primary" @click="$emit('on-ok')" :loading="isSaving">{{ okText }}</el-button>
            </span>

        </Modal>    
    </div>
</template>
<script>

    export default {
        props:{
            hideModal: {
                type: Boolean,
                default: false,
            },
            isSaving:{
                type: Boolean,
                default: false,               
            },
            title: {
                type: String,
                default: '',
            },
            okText:{
                type: String,
                default: 'Save Changes',               
            },
            cancelText:{
                type: String,
                default: 'Cancel',               
            }
        },
        data(){
            return{
                modalVisible: false
            }
        },
        watch: {

            //  Watch for changes on the hide state
            hideModal: {
                handler: function (val, oldVal) {
                    
                    if(val){

                        //  Hide the modal
                        this.closeModal();

                    }

                }
            }
        },
        methods: {
            detectClose(){
                
                var self = this;

                //  Only after 1/2 second
                setTimeout(function(){

                    //  Notify the parent on value of modal visibility
                    self.$emit('visibility', self.modalVisible);
                
                }, 500);

            },
            closeModal(){
                //  By setting modalVisible = false, we also trigger the detectClose() method
                //  since the modal has the event @on-visible-change="detectClose" to detect
                //  any changes of the "modalVisible". The "detectClose()" method would then
                //  notify the parent on the changes of the modal visibility.
                this.modalVisible = false;
            },
            cancel(){
                this.$emit('on-cancel'); 
                this.closeModal()
            }
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Set modal visibility to true to show modal
                this.modalVisible = true;
            })
        }
    }
</script>