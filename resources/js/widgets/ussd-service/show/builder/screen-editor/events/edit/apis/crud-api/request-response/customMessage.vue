<template>

    <div>
        
        <!-- Display Custom Message Explainer -->
        <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
            Write a <span class="font-italic text-success font-weight-bold">Custom Message</span>. This is the message
            that will be returned if the request returns a status <span :class="'font-italic '+errorOrSuccessClass+' font-weight-bold'">{{ localResponseStatusHandle.status }}</span>.
        </Alert>

        <!-- Use Code Editor Swtich -->
        <div class="mt-2 mb-2">
            <Poptip trigger="hover" width="350" placement="right" word-wrap>

                <template slot="content">
                    <span class="d-block">Use the Code Editor to write code in PHP and edit your screen with full control</span>
                
                    <span style="margin-top: -15px;" class="border-top d-block pt-2">
                        <span class="font-weight-bold">Note:</span> Always wrap strings in <span class="text-primary">single quotes ('')</span> and use the <span class="text-primary">period (.)</span> to concatenat values. Use <span class="text-primary">double pipe (||)</span> or <span class="text-primary">("\n")</span> for line-breaks. Make sure to include the <span class="text-primary">return</span> statement as soon as you want to output your result. Keep all your code within PHP Tags <span class="text-primary"><?php ?></span>
                    </span>
                </template>

                <Icon type="ios-code" class="border rounded-circle p-1" :size="20" />
                <span class="font-weight-bold text-dark">Use Code Editor: </span>
                <i-switch 
                    size="small"
                    class="ml-1"
                    :disabled="false"
                    true-color="#13ce66" 
                    false-color="#ff4949" 
                    :value="localResponseStatusHandle.on_handle.use_custom_msg.code_editor_mode" 
                    @on-change="localResponseStatusHandle.on_handle.use_custom_msg.code_editor_mode = $event">
                </i-switch>

            </Poptip>
        </div>

        <!-- Display Instruction Input -->
        <customEditor
            :content="localResponseStatusHandle.on_handle.use_custom_msg.text"
            sampleCodeTemplate="ussd_service_instructions_sample_code"
            @contentChange="localResponseStatusHandle.on_handle.use_custom_msg.text = $event"
            :codeContent="localResponseStatusHandle.on_handle.use_custom_msg.code_editor_text"
            :useCodeEditor="localResponseStatusHandle.on_handle.use_custom_msg.code_editor_mode"
            @codeChange="localResponseStatusHandle.on_handle.use_custom_msg.code_editor_text = $event">
        </customEditor>
        
    </div>

</template>

<script>

    import customEditor from '../../../../../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    export default {
        props: { 
            responseStatusHandle: {
                type: Object,
                default:() => {}
            },
        },
        components: { 
            customEditor
        },
        data(){
            return {
               localResponseStatusHandle: this.responseStatusHandle 
            }
        }, 
        computed: {
            errorOrSuccessClass(){

                return ['1', '2', '3'].includes(this.localResponseStatusHandle.status.substring(0, 1)) ? 'text-success' : 'text-danger';

            }
        },
        methods: {

        }
    };
  
</script>