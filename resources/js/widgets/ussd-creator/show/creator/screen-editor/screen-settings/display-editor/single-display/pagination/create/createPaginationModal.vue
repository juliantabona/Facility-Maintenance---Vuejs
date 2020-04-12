<template>
    <div>
        <!-- Modal -->
        <mainModal 
            okText="Create" 
            @on-ok="create()"
            v-bind="$props" 
            :isSaving="false" 
            cancelText="Close"
            :hideModal="hideModal"
            title="Create Pagination"
            @visibility="$emit('visibility', $event)">

            <div slot="content">

                <!-- Pagination Name -->
                <Input v-model="pagination.name" class="w-100 mb-2 pb-2"
                    type="text" placeholder="Enter pagination name">
                    <span slot="prepend">Name</span>
                </Input>

                <Row :gutter="12" class="bg-grey-light border pt-3 pb-2 px-2 mb-2">

                    <!-- Type -->
                    <Col :span="12" class="d-flex">
                    
                        <span class="d-block font-weight-bold text-dark mt-2 mr-2">Type: </span>
                        
                        <Select v-model="pagination.selected_type" 
                                class="mb-2" placeholder="Type">
                            
                            <Option v-for="(action, key) in paginationTypes" :key="key" class="mb-2"
                                    :value="action.type" :label="action.name">
                            </Option>

                        </Select>

                    </Col>
                    
                    <!-- Target -->
                    <Col :span="12" class="d-flex">
                    
                        <span class="d-block font-weight-bold text-dark mt-2 mr-2">Target: </span>
                        
                        <Select v-model="pagination.content_target.selected_type" 
                                class="mb-2" placeholder="Type">
                            
                            <Option v-for="(action, key) in paginationTargets" :key="key" class="mb-2"
                                    :value="action.type" :label="action.name">
                            </Option>

                        </Select>

                    </Col>

                </Row>

                <Row :gutter="12" class="bg-grey-light border pt-3 pb-2 px-2">

                    <!-- Start -->
                    <Col :span="12" class="mb-3">

                        <span class="text-dark font-weight-bold mr-1 mt-1">Start Position: </span>

                        <customEditor size="small" class="w-100" classes="px-1"
                            :useCodeEditor="false" :content="pagination.slice.start"
                            @contentChange="pagination.slice.start = $event">
                        </customEditor>
                    
                    </Col>

                    <!-- End -->
                    <Col :span="12" class="mb-3">

                        <span class="text-dark font-weight-bold mr-1 mt-1">End Position: </span>

                        <customEditor size="small" class="w-100" classes="px-1"
                            :useCodeEditor="false" :content="pagination.slice.end"
                            @contentChange="pagination.slice.end = $event">
                        </customEditor>
                    
                    </Col>

                    <!-- Input -->
                    <Col :span="24" class="mb-3">

                        <span class="text-dark font-weight-bold mr-1 mt-1">Input: </span>

                        <customEditor size="small" class="w-100" classes="px-1"
                            :useCodeEditor="false" :content="pagination.input"
                            @contentChange="pagination.input = $event">
                        </customEditor>
                    
                    </Col>

                    <!-- Show Text -->
                    <Col :span="24" class="mb-2">

                        <!-- Enable / Disable Show Option -->
                        <Checkbox v-model="pagination.show_more.visible">Show Text</Checkbox>
                        
                        <div class="d-flex">

                            <span class="text-dark font-weight-bold mr-1 mt-2">Text: </span>

                            <customEditor size="small" class="w-100" classes="px-1"
                                :useCodeEditor="false" :content="pagination.show_more.text"
                                @contentChange="pagination.show_more.text = $event">
                            </customEditor>

                        </div>
                    
                    </Col>

                </Row>

            </div>
            
        </mainModal>    
        
    </div>

</template>

<script>

    /*  Main Modal   */
    import mainModal from './../../../../../../../../../../components/_common/modals/main.vue';

    //  Get the custom editor
    import customEditor from './../../../../../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    export default {
        components: { mainModal, customEditor },
        data(){
            return{
                hideModal: false,
                pagination: {
                    name: 'Scroll Down',
                    selected_type: 'scroll_down',     //  scroll_up, scroll_down
                    content_target: {
                        selected_type: 'both'         //  instruction, action, both
                    },
                    slice: {
                        start: '0',
                        end: '160'
                    },
                    input: '99',
                    show_more: {
                        visible: true,
                        text: '99. More'
                    }
                },
                paginationTypes: [
                    {
                        name: 'Scroll Up', type: 'scroll_up'
                    },
                    {
                        name: 'Scroll Down', type: 'scroll_down'
                    }
                ],
                paginationTargets: [
                    {
                        name: 'Instruction Content', type: 'instruction'
                    },
                    {
                        name: 'Action Content', type: 'action'
                    },
                    {
                        name: 'Both', type: 'both'
                    }
                ]
            }
        },
        methods: {
            closeModal(){

                this.hideModal = true;

            },
            create(){

                this.$emit('created', this.pagination);

                this.closeModal();

            }
        },
        created(){

        }
    }
</script>