<template>
    
    <div>

        <Row :gutter="12" class="bg-grey-light border pt-3 pb-2 px-2 mb-2">
            

            <!-- Trail -->
            <Col :span="12" class="mb-3">

                <!-- Enable / Disable Active State -->
                <Checkbox v-model="localPagination.active">Active</Checkbox>
            
            </Col>

            <!-- Target -->
            <Col :span="12" class="d-flex">
            
                <span class="d-block font-weight-bold text-dark mt-2 mr-2">Target: </span>
                
                <Select v-model="localPagination.content_target.selected_type" 
                        class="mb-2" placeholder="Type">
                    
                    <Option v-for="(action, key) in paginationTargets" :key="key" class="mb-2"
                            :value="action.type" :label="action.name">
                    </Option>

                </Select>

            </Col>

        </Row>

        <Row :gutter="12" class="bg-grey-light border pt-3 pb-2 px-2">

            <!-- Scroll Down Input -->
            <Col :span="24" class="mb-3">

                <span class="text-dark font-weight-bold mr-1 mt-1">Scroll Down Input: </span>

                <customEditor size="small" class="w-100" classes="px-1"
                    :useCodeEditor="false" :content="localPagination.scroll_down_input"
                    @contentChange="localPagination.scroll_down_input = $event">
                </customEditor>
            
            </Col>

            <!-- Scroll Up Input -->
            <Col :span="24" class="mb-3">

                <span class="text-dark font-weight-bold mr-1 mt-1">Scroll Up Input: </span>

                <customEditor size="small" class="w-100" classes="px-1"
                    :useCodeEditor="false" :content="localPagination.scroll_up_input"
                    @contentChange="localPagination.scroll_up_input = $event">
                </customEditor>
            
            </Col>

        </Row>

        <Row :gutter="12" class="bg-grey-light border pt-3 pb-2 px-2">

            <!-- Separation Type -->
            <Col :span="24" class="d-flex mb-2">
            
                <span class="d-block font-weight-bold text-dark mt-1" style="width: 170px;">Separation Type: </span>
                
                <Select v-model="localPagination.slice.separation_type" 
                        class="mb-2" placeholder="Type">
                    
                    <Option v-for="(action, key) in separationTypes" :key="key" class="mb-2"
                            :value="action.type" :label="action.name">
                    </Option>

                </Select>

            </Col>

            <Col :span="24" class="d-flex">

                <!-- Enable / Disable Pagination By Line Breaks -->
                <Checkbox v-model="localPagination.paginate_by_line_breaks">Don't cut select options or line breaks</Checkbox>

            </Col>

        </Row>

        <Row :gutter="12" class="bg-grey-light border pt-3 pb-2 px-2">

            <!-- Start -->
            <Col :span="12" class="mb-3">

                <span class="text-dark font-weight-bold mr-1 mt-1">Start Position: </span>

                <customEditor size="small" class="w-100" classes="px-1"
                    :useCodeEditor="false" :content="localPagination.slice.start"
                    @contentChange="localPagination.slice.start = $event">
                </customEditor>
            
            </Col>

            <!-- End -->
            <Col :span="12" class="mb-3">

                <span class="text-dark font-weight-bold mr-1 mt-1">End Position: </span>

                <customEditor size="small" class="w-100" classes="px-1"
                    :useCodeEditor="false" :content="localPagination.slice.end"
                    @contentChange="localPagination.slice.end = $event">
                </customEditor>
            
            </Col>

            <!-- Trail -->
            <Col :span="24" class="mb-3">
            
                <Input v-model="localPagination.trailing_end" class="w-100 mb-2 pb-2"
                    type="text" placeholder="More content trail">
                    <span slot="prepend">Trail</span>
                </Input>
            
            </Col>

            <!-- Trail -->
            <Col :span="12" class="mb-3">

                <!-- Enable / Disable Break Line Before Trail -->
                <Checkbox v-model="localPagination.break_line_before_trail">Break line before trail</Checkbox>
            
            </Col>

            <!-- Trail -->
            <Col :span="12" class="mb-3">

                <!-- Enable / Disable Break Line After Trail -->
                <Checkbox v-model="localPagination.break_line_after_trail">Break line after trail</Checkbox>
            
            </Col>

            <!-- Show Text -->
            <Col :span="24" class="mb-2">

                <!-- Enable / Disable Show Option -->
                <Checkbox v-model="localPagination.show_more.visible">Show Text</Checkbox>
                
                <div class="d-flex">

                    <span class="text-dark font-weight-bold mr-1 mt-2">Text: </span>

                    <customEditor size="small" class="w-100" classes="px-1"
                        :useCodeEditor="false" :content="localPagination.show_more.text"
                        @contentChange="localPagination.show_more.text = $event">
                    </customEditor>

                </div>
            
            </Col>

        </Row>
        
    </div>

</template>

<script>

    //  Get the custom editor
    import customEditor from './../../../../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    export default {
        props:{
            pagination: {
                type: Object,
                default: function(){
                    return {}
                }
            }
        },
        components: { customEditor },
        data(){
            return{
                hideModal: false,
                localPagination: this.pagination,
                separationTypes: [
                    {
                        name: 'Words', type: 'words'
                    },
                    {
                        name: 'Characters', type: 'characters'
                    }
                ],
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
        created(){

        }
    }
</script>