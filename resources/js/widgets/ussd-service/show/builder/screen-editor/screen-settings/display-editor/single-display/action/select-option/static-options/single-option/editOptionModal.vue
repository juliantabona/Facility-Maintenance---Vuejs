<template>
    <div>
        <!-- Modal -->
        <mainModal  
            okText="" 
            :width="500"
            v-bind="$props"
            :isSaving="false" 
            cancelText="Done"
            :hideModal="hideModal"
            title="Edit Static Option"
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Option Name -->
                <div class="d-flex mb-3">

                    <span class="text-dark font-weight-bold mr-1 mt-1">Name: </span>

                    <customEditor size="small" class="w-100" classes="px-1"
                        :useCodeEditor="false" :content="option.name"
                        @contentChange="option.name = $event">
                    </customEditor>
                
                </div>

                <!-- Option Value -->
                <div class="mb-3">

                    <div class="d-flex mb-2">

                        <span class="text-dark font-weight-bold mr-1">Value: </span>
                        
                        <Poptip trigger="hover" width="350" placement="right" word-wrap>

                            <template slot="content">
                                <span class="d-block">Use the Code Editor to write code in PHP and edit your screen with full control</span>
                            
                                <span style="margin-top: -15px;" class="border-top d-block pt-2">
                                    <span class="font-weight-bold">Note:</span> Always wrap strings in <span class="text-primary">single quotes ('')</span> and use the <span class="text-primary">period (.)</span> to concatenat values. Use <span class="text-primary">double pipe (||)</span> or <span class="text-primary">("\n")</span> for line-breaks. Make sure to include the <span class="text-primary">return</span> statement as soon as you want to output your result. Keep all your code within PHP Tags <span class="text-primary"><?php ?></span>
                                </span>
                            </template>
                            
                            <i-switch 
                                size="small"
                                class="ml-1"
                                :disabled="false"
                                true-color="#13ce66" 
                                false-color="#ff4949" 
                                :value="option.value.code_editor_mode" 
                                @on-change="option.value.code_editor_mode = $event">
                            </i-switch>

                        </Poptip>

                    </div>
                    
                    <!-- Display Instruction Input -->
                    <customEditor
                        :content="option.value.text"
                        sampleCodeTemplate="ussd_service_instructions_sample_code"
                        @contentChange="option.value.text = $event"
                        :codeContent="option.value.code_editor_text"
                        :useCodeEditor="option.value.code_editor_mode"
                        @codeChange="option.value.code_editor_text = $event">
                    </customEditor>
                
                </div>

                <!-- Top Separator -->
                <Input 
                    type="text"
                    class="w-100 mb-2" :max="40"
                    v-model="option.separator.top"
                    placeholder="---">

                    <span slot="prepend" class="font-weight-bold text-dark">Top</span>

                </Input>

                <!-- Bottom Separator -->
                <Input 
                    type="text"
                    class="w-100 mb-2" :max="40"
                    v-model="option.separator.bottom"
                    placeholder="---">

                    <span slot="prepend" class="font-weight-bold text-dark">Bottom</span>

                </Input>

                <!-- Option Input -->
                <div class="d-flex mb-2">

                    <span class="text-dark font-weight-bold mr-1 mt-1">Input: </span>

                    <customEditor size="small" class="w-100" classes="px-1"
                        :useCodeEditor="false" :content="option.input"
                        @contentChange="option.input = $event">
                    </customEditor>
                
                </div>

                <!-- Option Link -->
                <div class="mb-2">

                    <span class="text-dark font-weight-bold">Link: </span>

                    <screenLinkSelector
                        @on-change="option.link = $event"
                        :link="option.link"
                        :display="display"
                        :builder="builder"
                        :screen="screen"
                        layout="inline">
                    </screenLinkSelector>
                
                </div>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    //  Main Modal
    import mainModal from '../../../../../../../../../../../../components/_common/modals/main.vue';

    //  Get the custom editor
    import customEditor from '../../../../../../../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    //  Get the link selector
    import screenLinkSelector from './../../../../../../../screenLinkSelector.vue';

    export default {
        props:{
            display: {
                type: Object,
                default:() => {}
            },
            screen: {
                type: Object,
                default:() => {}
            },
            builder: {
                type: Object,
                default: () => {}
            },
            option: {
                type: Object,
                default: null
            }
        },
        components: { mainModal, customEditor, screenLinkSelector },
        data(){
            return{
                hideModal: false,
                localEvent: this.option
            }
        },
        methods: {
            
        },
        created(){

        }
    }
</script>