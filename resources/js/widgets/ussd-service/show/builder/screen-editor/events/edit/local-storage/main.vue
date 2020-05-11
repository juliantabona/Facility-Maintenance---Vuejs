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
                Add <span class="font-italic text-success font-weight-bold">Validation Rules</span> to only
                allow the user to provide specific type of information e.g 
                <span class="font-italic text-success font-weight-bold">Only Numbers</span> are allowed for Age or
                <span class="font-italic text-success font-weight-bold">Only Letters</span> are allowed for First Name.
            </Alert>

            <template>

                <!-- Reference Name -->
                <Row :gutter="4" class="bg-grey-light p-2 mx-0 mb-2">
                    
                    <Col :span="24">

                        <!-- Local Storage Reference Name -->
                        <Input v-model="localEvent.event_data.reference_name" 
                            maxlength="30" type="text" class="w-100 mb-2" placeholder="Reference name">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                </Row>

                <!-- Storage Settings -->
                <Row :gutter="20" class="bg-grey-light p-2 mx-0 mb-2">
                    
                    <Col :span="12" class="mb-2">

                        <div class="d-flex">

                            <span class="font-weight-bold text-dark mt-1 mr-2">Storage:</span>
                                    
                            <!-- Storage Method -->
                            <Select v-model="localEvent.event_data.storage.selected_type">
                                <Option v-for="(storage_type, index) in storage_types" :value="storage_type.value" :key="index">
                                    {{ storage_type.name }}
                                </Option>
                            </Select>
                        
                        </div>

                    </Col>
                    
                            
                    <template v-if="localEvent.event_data.storage.selected_type == 'array'">

                        <Col :span="12" class="mb-2">

                            <div class="d-flex">

                                <span class="font-weight-bold text-dark mt-1 mr-2">Structure:</span>

                                <!-- Array Data Type Storage -->
                                <Select v-model="localEvent.event_data.storage.array.dataset.selected_type">
                                    <Option v-for="(dataset, index) in array_datasets" :value="dataset.value" :key="index">
                                        {{ dataset.name }}
                                    </Option>
                                </Select>

                            </div>

                        </Col>

                    </template>
                    
                    <Col :span="12" class="mb-2">

                        <div class="d-flex">

                            <span class="font-weight-bold text-dark mt-1 mr-2">Mode:</span>
                            
                            <template v-if="localEvent.event_data.storage.selected_type == 'array'">

                                <!-- Array Mode -->
                                <Select v-model="localEvent.event_data.storage.array.mode.selected_type">
                                    <Option v-for="(mode, index) in array_modes" :value="mode.value" :key="index">
                                        {{ mode.name }}
                                    </Option>
                                </Select>

                            </template>

                            <template v-if="localEvent.event_data.storage.selected_type == 'string'">

                                <!-- String Mode -->
                                <Select v-model="localEvent.event_data.storage.string.mode.selected_type">
                                    <Option v-for="(mode, index) in string_modes" :value="mode.value" :key="index">
                                        {{ mode.name }}
                                    </Option>
                                </Select>

                            </template>

                            <template v-if="localEvent.event_data.storage.selected_type == 'code'">

                                <!-- Code Mode -->
                                <Select v-model="localEvent.event_data.storage.code.mode.selected_type">
                                    <Option v-for="(mode, index) in code_modes" :value="mode.value" :key="index">
                                        {{ mode.name }}
                                    </Option>
                                </Select>

                            </template>

                        </div>

                    </Col>
                    
                    <Col :span="12" class="mb-2">

                        <div v-if="localEvent.event_data.storage.string.mode.selected_type == 'concatenate'" class="d-flex">

                            <span class="font-weight-bold text-dark mt-1 mr-2">Join:</span>

                            <!-- Value -->     
                            <customEditor
                                size="small" class="w-100 mb-1" classes="px-1"
                                :useCodeEditor="false" :placeholder="'{{ items }}'"
                                :content="localEvent.event_data.storage.string.mode.concatenate.value"
                                @contentChange="localEvent.event_data.storage.string.mode.concatenate.value = $event">
                            </customEditor>

                        </div>

                    </Col>

                </Row>

                <!-- Storage Values -->
                <Row :gutter="4">

                    <Col :span="24">

                        <!-- Array Storage -->
                        <template v-if="localEvent.event_data.storage.selected_type == 'array'">

                            <template v-if="localEvent.event_data.storage.array.dataset.selected_type == 'values'">
                                
                                <!-- Header -->
                                <Row class="bg-grey-light p-2 mx-0 mb-2">
                                    
                                    <Col :span="24">
                                        <span class="font-weight-bold text-dark">Values:</span>
                                    </Col>

                                </Row>

                                <template v-if="localEvent.event_data.storage.array.dataset.values.length">

                                    <Row :gutter="12" v-for="(dataset_value, index) in localEvent.event_data.storage.array.dataset.values" :key="index" 
                                         class="bg-grey-light p-2 mx-0 mb-1">
                                         
                                        <Col :span="22">

                                            <!-- Value -->     
                                            <customEditor
                                                size="small" class="w-100 mb-1" classes="px-1"
                                                :useCodeEditor="false" :placeholder="'{{ items }}'"
                                                :content="dataset_value.value"
                                                @contentChange="dataset_value.value = $event">
                                            </customEditor>

                                            <Row :gutter="12">

                                                <Col :span="4">
                                            
                                                    <!-- On Empty Handle Type -->
                                                    <Select v-model="dataset_value.on_empty_value.selected_type">
                                                        <Option v-for="(type, index) in on_empty_types" :value="type.value" :key="index">
                                                            {{ type.name }}
                                                        </Option>
                                                    </Select>

                                                </Col>

                                                <Col :span="10">

                                                    <template v-if="dataset_value.on_empty_value.selected_type == 'default'">

                                                        <!-- Default Type -->
                                                        <Select v-model="dataset_value.on_empty_value.default.selected_type">
                                                            <Option v-for="(type, index) in defaultTypes" :value="type.value" :key="index">
                                                                {{ type.name }}
                                                            </Option>
                                                        </Select>

                                                    </template>
                                                    
                                                </Col>

                                                <Col :span="10">

                                                    <template v-if="dataset_value.on_empty_value.selected_type == 'default'">
                                                        
                                                        <div class="d-flex"  
                                                             v-if="dataset_value.on_empty_value.default.selected_type == 'text_input' ||
                                                                   dataset_value.on_empty_value.default.selected_type == 'number_input'">

                                                            <span class="font-weight-bold text-dark mt-1 mr-2">Value:</span>

                                                            <!-- Value -->     
                                                            <customEditor v-if="dataset_value.on_empty_value.default.selected_type == 'text_input'"
                                                                size="small" class="w-100 mb-1" classes="px-1"
                                                                :useCodeEditor="false" :placeholder="'{{ items }}'"
                                                                :content="dataset_value.on_empty_value.default.text_input"
                                                                @contentChange="dataset_value.on_empty_value.default.text_input = $event">
                                                            </customEditor>
                                                            
                                                            <!-- Value -->     
                                                            <customEditor v-if="dataset_value.on_empty_value.default.selected_type == 'number_input'"
                                                                size="small" class="w-100 mb-1" classes="px-1"
                                                                :useCodeEditor="false" :placeholder="'{{ items }}'"
                                                                :content="dataset_value.on_empty_value.default.number_input"
                                                                @contentChange="dataset_value.on_empty_value.default.number_input = $event">
                                                            </customEditor>

                                                        </div>

                                                    </template>

                                                </Col>

                                            </Row>

                                        </Col>

                                        <Col :span="2">

                                            <Poptip confirm title="Are you sure you want to remove this value?" 
                                                    ok-text="Yes" cancel-text="No" width="300" @on-ok="removeArrayValue(index)"
                                                    placement="top-end">
                                                <Icon type="ios-trash-outline" size="20"/>
                                            </Poptip>

                                        </Col>

                                    </Row>
                                </template>

                                <!-- No values message -->
                                <Alert v-else type="info" show-icon>No Values Found</Alert>                  

                                <div class="clearfix mt-3">

                                    <!-- Add Custom Validation Rule Button  -->
                                    <Button type="primary" class="float-right" @click.native="addArrayValue()">
                                        <Icon type="ios-add" :size="20" />
                                        <span>Add Value</span>
                                    </Button>

                                </div>

                            </template>

                            <template v-if="localEvent.event_data.storage.array.dataset.selected_type == 'key_values'">
                                
                                <!-- Header -->
                                <Row class="bg-grey-light p-2 mx-0 mb-2">
                                    
                                    <Col :span="12">
                                        <span class="font-weight-bold text-dark">Key:</span>
                                    </Col>
                                    
                                    <Col :span="12">
                                        <span class="font-weight-bold text-dark">Values:</span>
                                    </Col>

                                </Row>

                                <template v-if="localEvent.event_data.storage.array.dataset.key_values.length">

                                    <Row :gutter="12" v-for="(dataset_value, index) in localEvent.event_data.storage.array.dataset.key_values" :key="index" 
                                         class="bg-grey-light p-2 mx-0 mb-1">
                                         
                                        <Col :span="22">

                                            <Row :gutter="12">

                                                <Col :span="12">

                                                    <!-- Key -->     
                                                    <customEditor
                                                        size="small" class="w-100 mb-1" classes="px-1"
                                                        :useCodeEditor="false" :placeholder="'{{ items }}'"
                                                        :content="dataset_value.key"
                                                        @contentChange="dataset_value.key = $event">
                                                    </customEditor>

                                                </Col>

                                                <Col :span="12">

                                                    <!-- Value -->     
                                                    <customEditor
                                                        size="small" class="w-100 mb-1" classes="px-1"
                                                        :useCodeEditor="false" :placeholder="'{{ items }}'"
                                                        :content="dataset_value.value"
                                                        @contentChange="dataset_value.value = $event">
                                                    </customEditor>

                                                </Col>

                                            </Row>

                                            <Row :gutter="12">

                                                <Col :span="4">
                                            
                                                    <!-- On Empty Handle Type -->
                                                    <Select v-model="dataset_value.on_empty_value.selected_type">
                                                        <Option v-for="(type, index) in on_empty_types" :value="type.value" :key="index">
                                                            {{ type.name }}
                                                        </Option>
                                                    </Select>

                                                </Col>

                                                <Col :span="10">

                                                    <template v-if="dataset_value.on_empty_value.selected_type == 'default'">

                                                        <!-- Default Type -->
                                                        <Select v-model="dataset_value.on_empty_value.default.selected_type">
                                                            <Option v-for="(type, index) in defaultTypes" :value="type.value" :key="index">
                                                                {{ type.name }}
                                                            </Option>
                                                        </Select>

                                                    </template>
                                                    
                                                </Col>

                                                <Col :span="10">

                                                    <template v-if="dataset_value.on_empty_value.selected_type == 'default'">
                                                        
                                                        <div class="d-flex"  
                                                             v-if="dataset_value.on_empty_value.default.selected_type == 'text_input' ||
                                                                   dataset_value.on_empty_value.default.selected_type == 'number_input'">

                                                            <span class="font-weight-bold text-dark mt-1 mr-2">Value:</span>

                                                            <!-- Value -->     
                                                            <customEditor v-if="dataset_value.on_empty_value.default.selected_type == 'text_input'"
                                                                size="small" class="w-100 mb-1" classes="px-1"
                                                                :useCodeEditor="false" :placeholder="'{{ items }}'"
                                                                :content="dataset_value.on_empty_value.default.text_input"
                                                                @contentChange="dataset_value.on_empty_value.default.text_input = $event">
                                                            </customEditor>
                                                            
                                                            <!-- Value -->     
                                                            <customEditor v-if="dataset_value.on_empty_value.default.selected_type == 'number_input'"
                                                                size="small" class="w-100 mb-1" classes="px-1"
                                                                :useCodeEditor="false" :placeholder="'{{ items }}'"
                                                                :content="dataset_value.on_empty_value.default.number_input"
                                                                @contentChange="dataset_value.on_empty_value.default.number_input = $event">
                                                            </customEditor>

                                                        </div>

                                                    </template>

                                                </Col>

                                            </Row>

                                        </Col>

                                        <Col :span="2">

                                            <Poptip confirm title="Are you sure you want to remove this value?" 
                                                    ok-text="Yes" cancel-text="No" width="300" @on-ok="removeArrayKeyValue(index)"
                                                    placement="top-end">
                                                <Icon type="ios-trash-outline" size="20"/>
                                            </Poptip>

                                        </Col>

                                    </Row>
                                </template>

                                <!-- No values message -->
                                <Alert v-else type="info" show-icon>No Key/Values Found</Alert>                  

                                <div class="clearfix mt-3">

                                    <!-- Add Custom Validation Rule Button  -->
                                    <Button type="primary" class="float-right" @click.native="addArrayKeyValue()">
                                        <Icon type="ios-add" :size="20" />
                                        <span>Add Key/Value</span>
                                    </Button>

                                </div>

                            </template>

                        </template>

                        <!-- String Storage -->
                        <template v-if="localEvent.event_data.storage.selected_type == 'string'">
                                
                            <!-- String Value -->
                            <Row class="bg-grey-light p-2 mx-0 mb-2">
                                
                                <Col :span="24">

                                    <div class="d-flex">

                                        <span class="font-weight-bold text-dark mt-1 mr-2">Text:</span>

                                        <!-- Value -->     
                                        <customEditor
                                            size="small" class="w-100 mb-1" classes="px-1"
                                            :useCodeEditor="false" :placeholder="'{{ items }}'"
                                            :content="localEvent.event_data.storage.string.dataset.value"
                                            @contentChange="localEvent.event_data.storage.string.dataset.value = $event">
                                        </customEditor>

                                    </div>

                                </Col>

                            </Row>

                        </template>

                        <template v-if="localEvent.event_data.storage.selected_type == 'code'">
                                
                            <span class="font-weight-bold text-dark mb-2">Code:</span>
                    
                            <!-- Code Editor --> 
                            <customEditor
                                :useCodeEditor="true"
                                size="small" class="w-100" classes="px-1"
                                sampleCodeTemplate="ussd_service_local_storage_sample_code"
                                :codeContent="localEvent.event_data.storage.code.dataset.value"
                                @codeChange="localEvent.event_data.storage.code.dataset.value = $event">
                            </customEditor>

                        </template>

                    </Col>

                </Row>
            
            </template>

        </div>

    </div>

</template>

<script>

    import customEditor from '../../../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    export default {
        props:{
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
            customEditor
        },
        data(){
            return{
                localEvent: this.event,
                storage_types:[
                    {
                        name: 'String',
                        value: 'string'
                    },
                    {
                        name: 'Array',
                        value: 'array'
                    },
                    {
                        name: 'Code Editor',
                        value: 'code'
                    }
                ],

                array_datasets:[
                    {
                        name: 'Array Values',
                        value: 'values'
                    },
                    {
                        name: 'Array Key/Values',
                        value: 'key_values'
                    }
                ],

                array_modes: [
                    {
                        name: 'Array Replace',
                        value: 'replace'
                    },
                    {
                        name: 'Array Append (Insert After)',
                        value: 'append'
                    },
                    {
                        name: 'Array Prepend (Insert Before)',
                        value: 'prepend'
                    }
                ],

                string_modes: [
                    {
                        name: 'String Replace',
                        value: 'replace'
                    },
                    {
                        name: 'String Join (Concatenate)',
                        value: 'concatenate'
                    }
                ],

                code_modes: [
                    {
                        name: 'Array Replace',
                        value: 'replace'
                    },
                    {
                        name: 'Array Append (Insert After)',
                        value: 'append'
                    },
                    {
                        name: 'Array Prepend (Insert Before)',
                        value: 'prepend'
                    },
                    {
                        name: 'String Join (Concatenate)',
                        value: 'concatenate'
                    }
                ],
                
                on_empty_types:[
                    {
                        name: 'Nullable',
                        value: 'nullable'
                    },
                    {
                        name: 'Default',
                        value: 'default'
                    }
                ],

                defaultTypes: [
                    {
                        name: 'Text Input (Also dynamic inputs)',
                        value: 'text_input'
                    },
                    {
                        name: 'Number Input (Also dynamic inputs)',
                        value: 'number_input'
                    },
                    {
                        name: 'True',
                        value: 'true'
                    },
                    {
                        name: 'False',
                        value: 'false'
                    },
                    {
                        name: 'Null',
                        value: 'null'
                    },
                    {
                        name: 'Empty Array',
                        value: 'empty_array'
                    }
                ]
            }
        }, 
        computed: {

        },
        methods: {
            updateArrayValue(index, updatedValue){
                this.$set(this.localEvent.event_data.storage.array.dataset.values, index, updatedValue);
            },
            addArrayValue(){
                this.localEvent.event_data.storage.array.dataset.values.push({
                    value: '',
                    on_empty_value: {
                        selected_type: 'nullable',            //  default, nullable
                        default: {
                            selected_type: 'text_input',      //  text_input, number_input, true, false, null, empty_array
                            text_input: '',
                            number_input: ''
                        }
                    }
                });
            },
            addArrayKeyValue(){
                
                this.localEvent.event_data.storage.array.dataset.key_values.push({
                    key: '',
                    value: '',
                    on_empty_value: {
                        selected_type: 'nullable',            //  default, nullable
                        default: {
                            selected_type: 'text_input',      //  text_input, number_input, true, false, null, empty_array
                            text_input: '',
                            number_input: ''
                        }
                    }
                });

            },
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