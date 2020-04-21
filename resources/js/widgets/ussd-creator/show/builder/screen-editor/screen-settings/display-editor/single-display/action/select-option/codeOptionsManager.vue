<template>

    <Row :gutter="4">
        
        <!-- Code Editor -->
        <Col :span="24">

            <!-- Custom PHP Code Editor -->
            <customEditor
                :useCodeEditor="true"
                sampleCodeTemplate="ussd_creator_select_options_action_sample_code"
                :codeContent="localDisplay.content.action.select_option.code_editor_options.code_editor_text"
                @codeChange="localDisplay.content.action.select_option.code_editor_options.code_editor_text = $event">
            </customEditor>

            <!-- Messages / Desclaimers -->
            <div class="bg-grey-light border mt-3 mb-3 p-2">
                
                <!-- Selected Item Reference Name -->
                <span class="d-block font-weight-bold text-dark">Reference Name</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.code_editor_options.reference_name"
                    maxlength="30" type="text" class="w-100 mb-3" placeholder="selected_item">
                    <div slot="prepend">@</div>
                </Input>

                <!-- Heading -->
                <span class="d-block font-weight-bold text-dark mt-3">No Options Message</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.code_editor_options.no_results_message"
                    placeholder="Enter no options message"
                    type="textarea" :rows="2" class="w-100 mb-3">
                </Input>

                <!-- Heading -->
                <span class="d-block font-weight-bold text-dark">Incorrect Option Selected Message</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.code_editor_options.incorrect_option_selected_message"
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