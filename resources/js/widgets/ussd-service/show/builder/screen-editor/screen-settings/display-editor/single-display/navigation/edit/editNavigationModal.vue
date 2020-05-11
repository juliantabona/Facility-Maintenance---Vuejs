<template>
    <div>
        <!-- Modal -->
        <mainModal 
            okText="" 
            @on-ok="true"
            v-bind="$props" 
            :isSaving="false" 
            cancelText="Done"
            :hideModal="hideModal"
            title="Edit Navigation"
            @visibility="$emit('visibility', $event)">

            <div slot="content">

                <!-- Navigation Name -->
                <Input v-model="localNavigation.name" class="w-100 border-bottom mb-2 pb-2"
                    type="text" placeholder="Enter navigation name">
                    <span slot="prepend">Name</span>
                </Input>

                <div class="bg-grey-light border pt-3 pb-2 px-2">

                    <div :span="12" class="d-flex">
                    
                        <span class="d-block font-weight-bold text-dark mt-2 mr-2">Type: </span>
                        
                        <Select v-model="localNavigation.selected_type" 
                                class="w-50 mb-2" placeholder="Type">
                            
                            <Option v-for="(action, key) in navigationTypes" :key="key" class="mb-2"
                                    :value="action.type" :label="action.name">
                            </Option>

                        </Select>

                    </div>

                    <!-- Custom Navigation Settings  -->
                    <template v-if="localNavigation.selected_type == 'custom'">
                        
                        <!-- Custom Navigation Inputs -->
                        <Input v-model="localNavigation.custom.inputs" 
                            type="text" class="w-100" placeholder="Enter custom inputs">
                            <span slot="prepend">Input</span>
                        </Input>

                        <div class="mt-3 mb-3">

                            <div class="d-flex mb-2">

                                <span class="text-dark font-weight-bold mr-1">Step: </span>
                                
                                <Poptip trigger="hover" width="350" placement="right" word-wrap>

                                    <template slot="content">
                                        <span class="d-block">Use the Code Editor to write code in PHP and edit set the step value</span>
                                    
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
                                        :value="localNavigation.custom.step.code_editor_mode" 
                                        @on-change="localNavigation.custom.step.code_editor_mode = $event">
                                    </i-switch>

                                </Poptip>

                            </div>
                            
                            <!-- Input -->
                            <customEditor
                                :content="localNavigation.custom.step.text"
                                sampleCodeTemplate="ussd_service_instructions_sample_code"
                                @contentChange="localNavigation.custom.step.text = $event"
                                :codeContent="localNavigation.custom.step.code_editor_text"
                                :useCodeEditor="localNavigation.custom.step.code_editor_mode"
                                @codeChange="localNavigation.custom.step.code_editor_text = $event">
                            </customEditor>
                        
                        </div>

                    </template>

                    <!-- Regex Navigation Settings  -->
                    <template v-if="localNavigation.selected_type == 'regex'">
                        
                        <!-- Regex Navigation Rule -->
                        <Input v-model="localNavigation.regex.rule" 
                            class="regex-input" type="text" placeholder="/[a-zA-Z0-9]+/">
                            <span slot="prepend">Regex Rule</span>
                        </Input>

                        <div class="mt-3 mb-3">

                            <div class="d-flex mb-2">

                                <span class="text-dark font-weight-bold mr-1">Step: </span>
                                
                                <Poptip trigger="hover" width="350" placement="right" word-wrap>

                                    <template slot="content">
                                        <span class="d-block">Use the Code Editor to write code in PHP and edit set the step value</span>
                                    
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
                                        :value="localNavigation.regex.step.code_editor_mode" 
                                        @on-change="localNavigation.regex.step.code_editor_mode = $event">
                                    </i-switch>

                                </Poptip>

                            </div>
                            
                            <!-- Input -->
                            <customEditor
                                :content="localNavigation.regex.step.text"
                                sampleCodeTemplate="ussd_service_instructions_sample_code"
                                @contentChange="localNavigation.regex.step.text = $event"
                                :codeContent="localNavigation.regex.step.code_editor_text"
                                :useCodeEditor="localNavigation.regex.step.code_editor_mode"
                                @codeChange="localNavigation.regex.step.code_editor_text = $event">
                            </customEditor>
                        
                        </div>

                    </template>

                </div>

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
        props:{
            navigation: {
                type: Object,
                default: function(){
                    return {}
                }
            }
        },
        components: { mainModal, customEditor },
        data(){
            return{
                hideModal: false,
                localNavigation: this.navigation,
                navigationTypes: [
                    {
                        name: 'Custom Inputs', type: 'custom'
                    },
                    {
                        name: 'Regex Match', type: 'regex'
                    }
                ]
            }
        },
        methods: {
            closeModal(){

                this.hideModal = true;

            }
        }
    }
</script>