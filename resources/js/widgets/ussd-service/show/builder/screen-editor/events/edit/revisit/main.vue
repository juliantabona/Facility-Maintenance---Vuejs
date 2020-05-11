<template>

    <div>

        <!-- Event Name Input  -->
        <el-input type="text" v-model="localEvent.name" size="small" class="w-50"></el-input>

        <!-- Activate Event Checkbox (Marks the event as active / inactive) -->  
        <Checkbox v-model="localEvent.active" class="mt-2">Active</Checkbox>

        <!-- Event Settings -->
        <div class="mt-2">

            <!-- Validation Rules Instruction -->
            <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
                Use <span class="font-italic text-success font-weight-bold">Revisit</span> to go back to the
                <span class="font-italic text-success font-weight-bold">Main Menu</span>, a
                <span class="font-italic text-success font-weight-bold">Named Screen</span> or a
                <span class="font-italic text-success font-weight-bold">Marked Screen</span>
                
            </Alert>

            <template>

                <!-- Revisit Type -->
                <Row :gutter="20" class="bg-grey-light p-2 mx-0 mb-2">
                    
                    <Col :span="12" class="mb-2">

                        <div class="d-flex">

                            <span class="font-weight-bold text-dark mt-1 mr-2">Type:</span>
                            
                            <Select v-model="localEvent.event_data.revisit_type.selected_type">
                                <Option v-for="(revisit_type, index) in revisit_types" :value="revisit_type.value" :key="index">
                                    {{ revisit_type.name }}
                                </Option>
                            </Select>
                        
                        </div>

                    </Col>    

                    <!-- Trigger Type -->
                    <Col :span="12" class="mb-2">

                        <div class="d-flex">

                            <span class="font-weight-bold text-dark mt-1 mr-2">Trigger:</span>

                            <Select v-model="localEvent.event_data.general.trigger.selected_type">
                                <Option v-for="(trigger_type, index) in trigger_types" :value="trigger_type.value" :key="index">
                                    {{ trigger_type.name }}
                                </Option>
                            </Select>
                        
                        </div>

                    </Col>

                    <!-- Manual Trigger Input -->
                    <Col v-if="localEvent.event_data.general.trigger.selected_type == 'manual'" 
                         :span="24" class="mb-2">
                    
                        <div class="d-flex mb-2">

                            <span class="text-dark font-weight-bold mr-1 mt-1">Input: </span>

                            <customEditor size="small" class="w-100" classes="px-1"
                                :useCodeEditor="false" :content="localEvent.event_data.general.trigger.manual.input"
                                @contentChange="localEvent.event_data.general.trigger.manual.input = $event">
                            </customEditor>
                        
                        </div>

                    </Col>

                </Row>

                <!-- Revisit Settings -->
                <template v-if="localEvent.event_data.revisit_type.selected_type == 'screen_revisit' || 
                                localEvent.event_data.revisit_type.selected_type == 'marked_revisit'">

                    <Row :gutter="20" class="bg-grey-light p-2 mx-0 mb-2">
                        
                        <Col :span="24" class="mb-2">

                            <!-- Screen Revisit -->
                            <div v-if="localEvent.event_data.revisit_type.selected_type == 'screen_revisit'" class="d-flex">

                                <span class="font-weight-bold text-dark mt-1 mr-2">Screen:</span>

                                <!-- Screen Selector -->
                                <screenLinkSelector
                                    @on-change="localEvent.event_data.revisit_type.screen_revisit.link = $event"
                                    :link="localEvent.event_data.revisit_type.screen_revisit.link"
                                    :builder="builder"
                                    :screen="screen"
                                    :display="null"
                                    layout="inline">
                                </screenLinkSelector>
                            
                            </div>

                            <!-- Marked Revisit -->
                            <div v-if="localEvent.event_data.revisit_type.selected_type == 'marked_revisit'" class="d-flex">

                                <span class="font-weight-bold text-dark mt-1 mr-2">Marker:</span>

                                <!-- Marker selector -->
                                <Select
                                    v-model="localEvent.event_data.revisit_type.marked_revisit.selected_marker"
                                    not-found-text="No markers found" 
                                    @on-change="addMarker($event)"
                                    placeholder="Select marker"
                                    filterable>
                                    <Option v-for="(marker, index) in builder.markers" 
                                        :value="marker" :key="index">{{ marker }}</Option>
                                </Select>
                                
                            </div>
                            
                        </Col>

                    </Row>

                </template>

                <Row class="mt-3 mb-3">

                    <Col :span="24">

                        <!-- Additional Responses -->
                        <div>

                            <div class="d-flex mb-2">

                                <span class="text-dark font-weight-bold mr-1">Additional Responses: </span>
                                
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
                                        :value="localEvent.event_data.general.additional_responses.code_editor_mode" 
                                        @on-change="localEvent.event_data.general.additional_responses.code_editor_mode = $event">
                                    </i-switch>

                                </Poptip>

                            </div>
                            
                            <!-- Display Instruction Input -->
                            <customEditor
                                sampleCodeTemplate="ussd_service_revisit_sample_code"
                                :content="localEvent.event_data.general.additional_responses.text"
                                @contentChange="localEvent.event_data.general.additional_responses.text = $event"
                                :codeContent="localEvent.event_data.general.additional_responses.code_editor_text"
                                :useCodeEditor="localEvent.event_data.general.additional_responses.code_editor_mode"
                                @codeChange="localEvent.event_data.general.additional_responses.code_editor_text = $event">
                            </customEditor>
                        
                        </div>

                    </Col>

                </Row>
                
            </template>

        </div>

    </div>

</template>

<script>

    import screenLinkSelector from './../../../screenLinkSelector.vue';

    import customEditor from '../../../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    export default {
        props:{
            screen: {
                type: Object,
                default: () => {}
            },
            event: {
                type: Object,
                default: null
            },
            builder: {
                type: Object,
                default: () => {}
            }
        },
        components: {
            screenLinkSelector, customEditor
        },
        data(){
            return{
                localEvent: this.event,
                revisit_types: [
                    {
                        name: 'Home Revisit',
                        value: 'home_revisit'
                    },
                    {
                        name: 'Screen Revisit',
                        value: 'screen_revisit'
                    },
                    {
                        name: 'Marked Revisit',
                        value: 'marked_revisit'
                    }
                ],
                trigger_types: [
                    {
                        name: 'Automatic',
                        value: 'automatic'
                    },
                    {
                        name: 'Manual',
                        value: 'manual'
                    }
                ]
            }
        }, 
        computed: {

        },
        methods: {
            removeArrayValue(index){
                this.localEvent.event_data.storage.array.dataset.values.splice(index, 1);
            },
            removeArrayKeyValue(index){
                this.localEvent.event_data.storage.array.dataset.key_values.splice(index, 1);
            }
        },
        created(){

        }
    }
</script>