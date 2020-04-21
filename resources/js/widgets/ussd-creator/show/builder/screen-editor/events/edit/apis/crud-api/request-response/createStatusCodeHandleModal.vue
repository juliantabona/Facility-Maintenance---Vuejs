<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="false" 
            :hideModal="hideModal"
            title="Add Status Code"
            cancelText="Close"
            okText="" 
            @on-ok="true"
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Events -->
                <Row :gutter="20">

                    <Col :span="8" v-for="(statusType, key) in statusTypes" :key="key" class="mb-2">
                        
                        <Card @click.native="handleSelectedStatus(statusType)" :padding="0"
                              :class="isGoodStatus(statusType) ? 'bg-success' : 'bg-danger'">
                            
                            <div style="padding: 30px;">
                                
                                <!-- Status Name -->
                                <p class="text-center text-white">{{ statusType }}</p>

                            </div>

                        </Card>

                    </Col>

                </Row>

            </template>
            
        </mainModal>    
        
    </div>

</template>

<script>

    /*  Main Modal   */
    import mainModal from './../../../../../../../../../../components/_common/modals/main.vue';

    export default {
        components: { mainModal },
        data(){
            return{
                hideModal: false,
                statusTypes: ['200', '201', '203', '204', '205', '300', '400', '401', '403', '404', '405', '500']
            }
        },
        methods: {
            isGoodStatus( statusType ){

                return ['1', '2', '3'].includes(statusType.substring(0, 1)) ? true : false;

            },
            handleSelectedStatus(statusType){

                var statusCodeHandle = this.createStatusCodeHandle( statusType );

                //  Notify the parent component of the selected status handle
                this.$emit('createdStatusHandle', statusCodeHandle);

                //  Close the modal
                this.closeModal();

            },
            createStatusCodeHandle( statusType ){

                return {
                    status: statusType,
                    attributes: [
                        {
                            name: '',
                            value: ''
                        }
                    ],
                    on_handle: {
                        selected_type: 'use_custom_msg',   //  do_nothing, use_custom_msg
                        use_custom_msg: {
                            text: '',
                            code_editor_text: '',
                            code_editor_mode: false
                        }
                    }
                };

            },
            closeModal(){

                this.hideModal = true;

            }
        }
    }
</script>