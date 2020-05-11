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
                Use <span class="font-italic text-success font-weight-bold">Redirect</span> to go back to a previous
                screen or to another <span class="font-italic text-success font-weight-bold">Service Code</span>
            </Alert>

            <template>

                <!-- Redirect Type -->
                <Row :gutter="20" class="bg-grey-light p-2 mx-0 mb-2">  

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
                         :span="12" class="mb-2">
                    
                        <div class="d-flex mb-2">

                            <span class="text-dark font-weight-bold mr-1 mt-1">Input: </span>

                            <customEditor size="small" class="w-100" classes="px-1"
                                :useCodeEditor="false" :content="localEvent.event_data.general.trigger.manual.input"
                                @contentChange="localEvent.event_data.general.trigger.manual.input = $event">
                            </customEditor>
                        
                        </div>

                    </Col>

                </Row>

                <!-- Redirect Settings -->
                <Row :gutter="20" class="bg-grey-light p-2 mx-0 mb-2">
                    
                    <Col :span="24" class="mb-2">
                    
                        <span class="text-dark font-weight-bold mr-1 mt-1" style="width: 120px;">Service Code: </span>

                        <customEditor size="small" class="w-100" classes="px-1"
                            :useCodeEditor="false" :content="localEvent.event_data.service_code"
                            @contentChange="localEvent.event_data.service_code = $event">
                        </customEditor>
                    
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