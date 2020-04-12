<template>
    <div>
        <!-- Modal -->
        <Modal :width="width" 
               :title="title" 
               v-model="modalVisible" 
               :closable="showCloseBtn"
               :mask-closable="maskClosable" 
               :footer-hide="!okText && !cancelText"
               @on-visible-change="detectClose">

            <!-- White overlay when loading -->
            <Spin size="large" fix v-if="isSaving" style="border-radius: 15px;"></Spin>

            <slot name="content"></slot>

            <!-- Ok/Cancel Buttons -->
            <span v-if="okText || cancelText" slot="footer" class="dialog-footer">
                <el-button v-if="cancelText" size="small" @click="cancel()">{{ cancelText }}</el-button>
                <el-button v-if="okText" size="small" type="primary" @click="$emit('on-ok')" :loading="isSaving">{{ okText }}</el-button>
            </span>

        </Modal>    
    </div>
</template>
<script>

    export default {
        props:{
            width:{
                type: Number,
                default: 520
            },
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
                default: null,           
            },
            cancelText:{
                type: String,   
                default: null,             
            },
            showCloseBtn:{
                type: Boolean,
                default: true,
            },
            maskClosable:{
                type: Boolean,
                default: true,
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