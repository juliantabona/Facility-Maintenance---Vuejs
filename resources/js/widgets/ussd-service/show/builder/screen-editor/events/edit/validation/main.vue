<template>

    <div>

        <!-- Event Name Input  -->
        <el-input type="text" v-model="localEvent.name" size="small" class="w-50"></el-input>

        <!-- Activate Event Checkbox (Marks the event as active / inactive) -->  
        <Checkbox v-model="localEvent.active" class="mt-2">Active</Checkbox>

        <!-- Event Settings -->
        <div class="mt-2">

            <!-- Validation Rules Instruction -->
            <Alert v-if="!numberOfActiveValidationRules" type="info" style="line-height: 1.4em;" class="mb-2" closable>
                Add <span class="font-italic text-success font-weight-bold">Validation Rules</span> to only
                allow the user to provide specific type of information e.g 
                <span class="font-italic text-success font-weight-bold">Only Numbers</span> are allowed for Age or
                <span class="font-italic text-success font-weight-bold">Only Letters</span> are allowed for First Name.
            </Alert>

            <template v-if="numberOfActiveValidationRules">
                
                <div class="d-flex mb-3">

                    <span class="text-dark font-weight-bold mr-2 mt-1">Target: </span>

                    <!-- Validation Target -->     
                    <customEditor
                        size="small" class="w-100" classes="px-1"
                        :useCodeEditor="false" :placeholder="'{{ items }}'"
                        :content="localEvent.event_data.target"
                        @contentChange="localEvent.event_data.target = $event">
                    </customEditor>

                </div>
                
                <!-- Header -->
                <Row :gutter="4" class="bg-grey-light m-0 p-2">
                    
                    <Col :span="12">
                        <span class="font-weight-bold text-dark">Rule</span>
                    </Col>
                    
                    <Col :span="12">
                        <span class="font-weight-bold text-dark">Error Message</span>
                    </Col>

                </Row>

                <!-- Selected Validation Rules -->
                <div class="border mb-3 p-2">

                    <!-- Rules -->
                    <Row :gutter="4" class="m-0" v-for="(validation_rule, index) in localEvent.event_data.rules" :key="index" 
                            :class="getSpecialRules().includes(validation_rule.type) ? 'bg-grey-light border p-1 mb-2' : 'mb-2'">
            
                        <Col :span="12">
                                
                            <div :class="getSpecialRules().includes(validation_rule.type) ? '' : 'ml-1'">

                                <!-- Enable / Disable Validation Checkbox -->
                                <Checkbox class="m-0" slot="prepend" v-model="validation_rule.active">

                                    <!-- Custom Validation Label -->
                                    <span v-if="validation_rule.type == 'custom_regex'">Active</span>

                                    <!-- Other Validation Label -->
                                    <span v-else>{{ validation_rule.name }}</span>

                                </Checkbox>
                                
                            </div>

                            <!-- Minimum Characters Validation -->           
                            <template v-if="validation_rule.type == 'minimum_characters'">
                            
                                <!-- Minimum Value -->
                                <div class="d-flex mt-1">

                                    <span class="text-dark font-weight-bold mr-1 mt-1">Min: </span>

                                    <customEditor size="small" class="w-100" classes="px-1"
                                        :useCodeEditor="false" :content="validation_rule.min"
                                        @contentChange="validation_rule.min = $event">
                                    </customEditor>
                                
                                </div>

                            </template>

                            <!-- Maximum Characters Validation -->           
                            <template v-if="validation_rule.type == 'maximum_characters'">
                            
                                <!-- Maximum Value -->
                                <div class="d-flex mt-1">

                                    <span class="text-dark font-weight-bold mr-1 mt-1">Max: </span>

                                    <customEditor size="small" class="w-100" classes="px-1"
                                        :useCodeEditor="false" :content="validation_rule.max"
                                        @contentChange="validation_rule.max = $event">
                                    </customEditor>
                                
                                </div>

                            </template>

                            <!-- Equal To Validation -->           
                            <template v-if="validation_rule.type == 'equal_to'">
                            
                                <!-- Equal To Value -->
                                <div class="d-flex mt-1">

                                    <span class="text-dark font-weight-bold mr-1 mt-1">Value: </span>

                                    <customEditor size="small" class="w-100" classes="px-1"
                                        :useCodeEditor="false" :content="validation_rule.value"
                                        @contentChange="validation_rule.value = $event">
                                    </customEditor>
                                
                                </div>

                            </template>

                            <!-- Not Equal To Validation -->           
                            <template v-if="validation_rule.type == 'not_equal_to'">
                            
                                <!-- Not Equal To Value -->
                                <div class="d-flex mt-1">

                                    <span class="text-dark font-weight-bold mr-1 mt-1">Value: </span>

                                    <customEditor size="small" class="w-100" classes="px-1"
                                        :useCodeEditor="false" :content="validation_rule.value"
                                        @contentChange="validation_rule.value = $event">
                                    </customEditor>
                                
                                </div>

                            </template>

                            <!-- Less Than Validation -->           
                            <template v-if="validation_rule.type == 'less_than'">
                            
                                <!-- Less Than Value -->
                                <div class="d-flex mt-1">

                                    <span class="text-dark font-weight-bold mr-1 mt-1">Value: </span>

                                    <customEditor size="small" class="w-100" classes="px-1"
                                        :useCodeEditor="false" :content="validation_rule.value"
                                        @contentChange="validation_rule.value = $event">
                                    </customEditor>
                                
                                </div>

                            </template>

                            <!-- Less Than Or Equal To Validation -->           
                            <template v-if="validation_rule.type == 'less_than_or_equal'">
                            
                                <!-- Less Than Value -->
                                <div class="d-flex mt-1">

                                    <span class="text-dark font-weight-bold mr-1 mt-1">Value: </span>

                                    <customEditor size="small" class="w-100" classes="px-1"
                                        :useCodeEditor="false" :content="validation_rule.value"
                                        @contentChange="validation_rule.value = $event">
                                    </customEditor>
                                
                                </div>

                            </template>

                            <!-- Greater Than Validation -->           
                            <template v-if="validation_rule.type == 'greater_than'">
                            
                                <!-- Greater Than Value -->
                                <div class="d-flex mt-1">

                                    <span class="text-dark font-weight-bold mr-1 mt-1">Value: </span>

                                    <customEditor size="small" class="w-100" classes="px-1"
                                        :useCodeEditor="false" :content="validation_rule.value"
                                        @contentChange="validation_rule.value = $event">
                                    </customEditor>
                                
                                </div>

                            </template>

                            <!-- Greater Than Or Equal To Validation -->           
                            <template v-if="validation_rule.type == 'greater_than_or_equal'">
                            
                                <!-- Greater Than Or Equal To Value -->
                                <div class="d-flex mt-1">

                                    <span class="text-dark font-weight-bold mr-1 mt-1">Value: </span>

                                    <customEditor size="small" class="w-100" classes="px-1"
                                        :useCodeEditor="false" :content="validation_rule.value"
                                        @contentChange="validation_rule.value = $event">
                                    </customEditor>
                                
                                </div>

                            </template>

                            <!-- In Between (Including Inputs) Validation -->           
                            <template v-if="validation_rule.type == 'in_between_including'">
                            
                                <Row :gutter="12">

                                    <!-- In Between Minimum Value -->
                                    <Col :span="12">

                                        <div class="d-flex mt-1">

                                            <span class="text-dark font-weight-bold mr-1 mt-1">Min: </span>

                                            <customEditor size="small" class="w-100" classes="px-1"
                                                :useCodeEditor="false" :content="validation_rule.min"
                                                @contentChange="validation_rule.min = $event">
                                            </customEditor>
                                        
                                        </div>

                                    </Col>

                                    <!-- In Between Maximum Value -->
                                    <Col :span="12">

                                        <div class="d-flex mt-1">

                                            <span class="text-dark font-weight-bold mr-1 mt-1">Max: </span>

                                            <customEditor size="small" class="w-100" classes="px-1"
                                                :useCodeEditor="false" :content="validation_rule.max"
                                                @contentChange="validation_rule.max = $event">
                                            </customEditor>
                                        
                                        </div>

                                    </Col>

                                </Row>

                            </template>

                            <!-- In Between (Excluding Inputs) Validation -->           
                            <template v-if="validation_rule.type == 'in_between_excluding'">
                            
                                <Row :gutter="12">

                                    <!-- In Between Minimum Value -->
                                    <Col :span="12">

                                        <div class="d-flex mt-1">

                                            <span class="text-dark font-weight-bold mr-1 mt-1">Min: </span>

                                            <customEditor size="small" class="w-100" classes="px-1"
                                                :useCodeEditor="false" :content="validation_rule.min"
                                                @contentChange="validation_rule.min = $event">
                                            </customEditor>
                                        
                                        </div>

                                    </Col>

                                    <!-- In Between Maximum Value -->
                                    <Col :span="12">

                                        <div class="d-flex mt-1">

                                            <span class="text-dark font-weight-bold mr-1 mt-1">Max: </span>

                                            <customEditor size="small" class="w-100" classes="px-1"
                                                :useCodeEditor="false" :content="validation_rule.max"
                                                @contentChange="validation_rule.max = $event">
                                            </customEditor>
                                        
                                        </div>

                                    </Col>

                                </Row>

                            </template>

                            <!-- Custom Validation Name -->           
                            <template v-else-if="validation_rule.type == 'custom_regex'">

                                <!-- Custom Validation Rule Name -->
                                <Input v-model="validation_rule.name" class="mt-1" type="text" placeholder="Custom name"></Input>
                            
                                <!-- Custom Regex Rule -->
                                <Input v-model="validation_rule.rule" class="regex-input mt-1" type="text" placeholder="/[a-zA-Z0-9]+/">
                                    <span slot="prepend">Regex Rule</span>
                                </Input>

                            </template>
                            
                        </Col>
                        
                        <!-- Validation Error Message -->    
                        <Col :span="10" :class="getSpecialRules().includes(validation_rule.type) ? 'pt-4' : ''">

                            <Input v-model="validation_rule.error_msg" type="textarea" 
                                   :rows="validation_rule.type == 'custom_regex' ? 2 : 1" 
                                   placeholder="Validation error message">
                            </Input>
                        
                        </Col>
                                
                        <Col :span="2" :class="getSpecialRules().includes(validation_rule.type) ? 'pt-4' : ''">
                        
                            <Poptip confirm title="Are you sure you want to remove this validation rule?" 
                                    ok-text="Yes" cancel-text="No" width="300" @on-ok="removeValidationRule(index)"
                                    placement="top-end">
                                <Icon type="ios-trash-outline" size="20"/>
                            </Poptip>

                        </Col>

                    </Row>
                
                </div>
            
            </template>
            
            <!-- No events message -->
            <Alert v-else type="info" show-icon>No Validation Rules Found</Alert>

            <div class="clearfix mt-3">

                <!-- Add Custom Validation Rule Button  -->
                <Button type="primary" class="float-right" @click.native="addValidationRule()">
                    <Icon type="ios-add" :size="20" />
                    <span>Add Rule</span>
                </Button>

            </div>

        </div>
    
        <!-- 
            MODAL TO ADD NEW VALIDATION RULE
        -->
        <addValidationRuleModal v-if="isOpenAddValidationRuleModal" 
            @visibility="isOpenAddValidationRuleModal = $event"
            @selected="handleSelectedRule($event)">
        </addValidationRuleModal> 

    </div>

</template>

<script>

    //  Get the modal used to add a new validation rule
    import addValidationRuleModal from './addValidationRuleModal.vue';

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
            addValidationRuleModal, customEditor
        },
        data(){
            return{
                localEvent: this.event,
                isOpenAddValidationRuleModal: false
            }
        }, 
        computed: {
            
            activeValidationRules(){
                
                //  Get all active validation rules
                return this.localEvent.event_data.rules.filter( (validation_rule) => { 
                        return validation_rule.active == true;
                    }) || [];

            },

            numberOfActiveValidationRules(){
                
                //  Count all validation rules
                return this.localEvent.event_data.rules.length || 0;

            }

        },
        methods: {
            getSpecialRules(){
                return [
                    'minimum_characters', 'maximum_characters', 'equal_to', 'not_equal_to', 'less_than', 'less_than_or_equal',
                    'greater_than', 'greater_than_or_equal', 'custom_regex', 'in_between_including', 'in_between_excluding'
                ];
            },
            addValidationRule(){

                this.isOpenAddValidationRuleModal = true;

            },

            handleSelectedRule( validation_rule ){

                //  Run through all our current validation rules
                for( var x=0; x < this.localEvent.event_data.rules.length; x++ ){

                    //  If the current validation rule matches the validation rule we want to add
                    if( this.localEvent.event_data.rules[x].type == validation_rule.type ){

                        //  Stop - Don't add this validation rule
                        return null;

                    }

                }

                //  Add validation rule
                this.localEvent.event_data.rules.push(validation_rule);

            },

            removeValidationRule(index){

                //  Remove validation rule
                this.localEvent.event_data.rules.splice(index, 1);

            }
            
        },
        created(){

        }
    }
</script>