<template>

    <Row :gutter="4">
        
        <!-- Foreach Label -->
        <Col :span="4">
        
            <span class="d-block text-center mt-1">Foreach</span>
        
        </Col>
        
        <!-- Foreach Items Group Reference -->
        <Col :span="8">
        
            <!-- Group Reference -->
            <customEditor
                size="small" classes="px-1"
                :useCodeEditor="false" :placeholder="'{{ items }}'"
                :content="localDisplay.content.action.select_option.dynamic_options.group_reference"
                @contentChange="localDisplay.content.action.select_option.dynamic_options.group_reference = $event">
            </customEditor>

        </Col>
        
        <!-- As Label -->
        <Col :span="2">

            <span class="d-block text-center mt-1">As</span>
        
        </Col>
        
        <Col :span="10">
        
            <!-- Template Reference Name -->
            <Input maxlength="30" type="text" class="w-100 mb-2" :placeholder="'item'" 
                :disabled="!localDisplay.content.action.select_option.dynamic_options.group_reference"
                v-model="localDisplay.content.action.select_option.dynamic_options.template_reference_name">
                <div slot="prepend">@</div>
            </Input>
        
        </Col>
        
        <Col :span="24">

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">

                <!-- Single Item Display Name -->
                <span class="d-block font-weight-bold text-dark">Display Name</span>

                <customEditor
                    classes="px-2 py-3 mb-3" :useCodeEditor="false"
                    :placeholder="'{{ item.name }} - {{ item.price }}'"
                    :content="localDisplay.content.action.select_option.dynamic_options.template_display_name"
                    @contentChange="localDisplay.content.action.select_option.dynamic_options.template_display_name = $event">
                </customEditor>



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
                                :value="localDisplay.content.action.select_option.dynamic_options.template_value.code_editor_mode" 
                                @on-change="localDisplay.content.action.select_option.dynamic_options.template_value.code_editor_mode = $event">
                            </i-switch>

                        </Poptip>

                    </div>
                    
                    <!-- Display Instruction Input -->
                    <customEditor
                        sampleCodeTemplate="ussd_creator_instructions_sample_code"
                        :content="localDisplay.content.action.select_option.dynamic_options.template_value.text"
                        @contentChange="localDisplay.content.action.select_option.dynamic_options.template_value.text = $event"
                        :codeContent="localDisplay.content.action.select_option.dynamic_options.template_value.code_editor_text"
                        :useCodeEditor="localDisplay.content.action.select_option.dynamic_options.template_value.code_editor_mode"
                        @codeChange="localDisplay.content.action.select_option.dynamic_options.template_value.code_editor_text = $event">
                    </customEditor>
                
                </div>
                
                <!-- Selected Item Reference Name -->
                <span class="d-block font-weight-bold text-dark">Reference Name</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.dynamic_options.reference_name"
                    maxlength="30" type="text" class="w-100 mb-3" placeholder="selected_item">
                    <div slot="prepend">@</div>
                </Input>

            </div>

            <!-- Screen / Display Link -->
            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">
            
                <!-- Heading -->
                <span class="d-block font-weight-bold text-dark mb-2">Link</span>
                
                <!-- Screen / Display Selector -->
                <screenLinkSelector
                    @on-change="localDisplay.content.action.select_option.dynamic_options.link = $event"
                    :link="localDisplay.content.action.select_option.dynamic_options.link"
                    :display="localDisplay"
                    :screens="screens"
                    :screen="screen">
                </screenLinkSelector>

            </div>

            <!-- Messages / Desclaimers -->
            <div class="bg-grey-light border mt-3 mb-3 p-2">

                <!-- Heading -->
                <span class="d-block font-weight-bold text-dark mt-3">No Options Message</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.dynamic_options.no_results_message"
                    placeholder="Enter no options message"
                    type="textarea" :rows="2" class="w-100 mb-3">
                </Input>

                <!-- Heading -->
                <span class="d-block font-weight-bold text-dark">Incorrect Option Selected Message</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.dynamic_options.incorrect_option_selected_message"
                    placeholder="Enter incorrect option selected message"
                    type="textarea" :rows="2" class="w-100 mb-3">
                </Input>

            </div>
        
        </Col>

    </Row>

</template>

<script>

    import customEditor from '../../../../../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    //  Get the screen settings
    import screenLinkSelector from './../../../../../screenLinkSelector.vue';

    export default {
        props: { 
            display: {
                type: Object,
                default:() => {}
            },
            screen: {
                type: Object,
                default:() => {}
            },
            screens: {
                type: Array,
                default: () => []
            }
        },
        components: { 
            customEditor, screenLinkSelector
        },
        data(){
            return {
                localDisplay: this.display
            }
        }, 
        computed: {

        },
        methods: {

        }
    };
  
</script>