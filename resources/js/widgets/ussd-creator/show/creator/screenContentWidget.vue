<template>

    <!-- Screen Content --> 
    <div>

        <!-- Test API Response Data -->
        <Tabs v-model="selectedContentTab" type="card" :animated="false">

            <!-- API Response Details -->
            <TabPane v-for="(selectedTab, key) in ['Instructions', 'Action', 'Validation', 'Formatting', 'Settings']" 
                    :key="key" :label="generateTabLabel(selectedTab)" :name="selectedTab">

                <template v-if="selectedTab == 'Instructions'">

                    <!-- Screen Instructions -->
                    <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
                        Write the <span class="font-italic text-success font-weight-bold">Screen Instructions</span>.
                        This is the main information you want to display on the screen such as instructions or dynamic information.
                    </Alert>

                    <div class="clearfix mb-5">

                        <!-- Add Dynamic Content Poptip -->
                        <Poptip v-if="getDynamicContent(localScreenContent).length" word-wrap width="300" trigger="hover" placement="right-start">

                            <!-- Add Dynamic Content Button -->
                            <Button class="p-1">
                                <Icon type="ios-add" :size="20" />
                            </Button>

                            <template slot="content">
                                
                                <List size="small" style="line-height: 1;">

                                    <ListItem v-for="(dynamicContentField, key) in getDynamicContent(localScreenContent)" :key="key"
                                                @click.native="addDynamicContent(localScreenContent, dynamicContentField)">{{ dynamicContentField }}</ListItem>

                                </List>

                            </template>

                        </Poptip>

                        <div class="mt-2 mb-2">
                            <Poptip trigger="hover" width="350" placement="right" word-wrap>

                                <template slot="content">
                                    <span class="d-block">Use the Code Editor to write code in PHP and edit your screen with full control</span>
                                
                                    <span style="margin-top: -15px;" class="border-top d-block pt-2">
                                        <span class="font-weight-bold">Note:</span> Always wrap strings in <span class="text-primary">single quotes ('')</span> and use the <span class="text-primary">period (.)</span> to concatenat values. Use <span class="text-primary">double pipe (||)</span> for line-breaks. Make sure to include the <span class="text-primary">return</span> statement as soon as you want to output your result. Keep all your code within PHP Tags <span class="text-primary"><?php ?></span>
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
                                    :value="localScreenContent.description.code_editor_mode" 
                                    @on-change="localScreenContent.description.code_editor_mode = $event">
                                </i-switch>

                            </Poptip>
                        </div>

                        <!-- Screen Description Input -->
                        <customEditor
                            :content="localScreenContent.description.text"
                            sampleCodeTemplate="ussd_creator_instructions_sample_code"
                            @contentChange="localScreenContent.description.text = $event"
                            :codeContent="localScreenContent.description.code_editor_text"
                            :useCodeEditor="localScreenContent.description.code_editor_mode"
                            @codeChange="localScreenContent.description.code_editor_text = $event">
                        </customEditor>

                    </div>
                </template>

                <template v-else-if="selectedTab == 'Action'">

                    <!-- Screen Instructions -->
                    <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
                        Select the <span class="font-italic text-success font-weight-bold">Screen Action</span>.
                        The screen action is what you expect the user to do while viewing this screen e.g provide an 
                        <span class="font-italic text-success font-weight-bold">Input Value</span> such as their Full Name or
                        <span class="font-italic text-success font-weight-bold">Select Option</span> from a list of specified options.
                    </Alert>

                    <!-- Screen Action Selector -->
                    <Select v-model="localScreenContent.reply_type" filterable class="w-100 mb-2" placeholder="Reply type">
                        
                        <Option v-for="(action, key) in screenActions" :key="key" class="mb-2"
                                :value="action.name" :label="action.name">
                        </Option>

                    </Select>

                    <template v-if="localScreenContent.reply_type == 'Input Value'">

                        <!-- Input Value Name -->
                        <Input 
                            v-model="localScreenContent.reply_name" 
                            maxlength="30" type="text" class="w-100 mb-2" placeholder="Reply name">
                            <div slot="prepend">@</div>
                        </Input>
                    
                        <!-- Link To Screen Selector -->
                        <div class="d-flex mb-2">       
                            <Icon type="ios-pin-outline" size="20"  class="mt-1 ml-1 mr-2 text-muted font-weight-bold" />
                            <Select v-model="localScreenContent.next_screen" filterable placeholder="Link To Screen">

                                <Option 
                                    v-for="(screen, key) in screenTree"
                                    :key="key" :value="screen.title" :label="screen.title"
                                    :disabled="localScreenContent.title == screen.title">

                                    <!-- Screen title -->
                                    <span>{{ screen.title }}</span>

                                </Option>

                            </Select>
                        </div>
                        
                    </template>

                    <template v-if="localScreenContent.reply_type == 'Select Option'">
                                    
                        <!-- Input Value Name -->
                        <Input 
                            v-model="localScreenContent.reply_name" 
                            v-if="!localScreenContent.select_reply.is_dynamic"
                            maxlength="30" type="text" class="w-100 mb-2" placeholder="Reply name">
                            <div slot="prepend">@</div>
                        </Input>

                        <Poptip word-wrap width="350" trigger="hover" placement="top-start"
                                content="Use dynamic content inside this description field? ">

                            <template slot="content">
                                <span class="d-block">Use dynamic content inside this description field?</span>
                            
                                <span style="margin-top: -15px;" class="border-top d-block pt-2">
                                    <span class="font-weight-bold">Note: Always wrap strings in single quotes ('') and use the period (.) to concatenat values. Make sure to include <span class="text-primary">return</span> at the begining of your description.</span>
                                </span>
                            </template>

                            <Checkbox v-model="localScreenContent.select_reply.is_dynamic" class="mt-2 mb-2">Use Code Editor</Checkbox>

                        </Poptip>

                        <template v-if="localScreenContent.select_reply.is_dynamic">
                            <Row :gutter="4">
                                
                                <Col :span="4">
                                
                                    <span class="d-block text-center mt-1">Foreach</span>
                                
                                </Col>
                                
                                <Col :span="8">
                                
                                    <!-- Input Value Name -->
                                    <customEditor
                                        size="small"
                                        classes="px-1"
                                        :useCodeEditor="false"
                                        :placeholder="'{{ items }}'"
                                        :content="localScreenContent.select_reply.dynamic_options.group_reference"
                                        @contentChange="localScreenContent.select_reply.dynamic_options.group_reference = $event">
                                    </customEditor>

                                </Col>
                                
                                <Col :span="2">

                                    <span class="d-block text-center mt-1">As</span>
                                
                                </Col>
                                
                                <Col :span="10">
                                
                                    <!-- Input Value Name -->
                                    <Input 
                                        v-model="localScreenContent.reply_name" 
                                        maxlength="30" type="text" class="w-100 mb-2" :placeholder="'item'"
                                        :disabled="!localScreenContent.select_reply.dynamic_options.group_reference">
                                        <div slot="prepend">@</div>
                                    </Input>
                                
                                </Col>
                                
                                <Col :span="24">

                                    <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">

                                        <!-- Heading -->
                                        <span class="d-block font-weight-bold text-dark">Display Name</span>

                                        <customEditor
                                            classes="px-2 py-3 mb-3"
                                            :useCodeEditor="false"
                                            :placeholder="'{{ item.name }} - {{ item.price }}'"
                                            :content="localScreenContent.select_reply.dynamic_options.template_display_name"
                                            @contentChange="localScreenContent.select_reply.dynamic_options.template_display_name = $event">
                                        </customEditor>

                                        <!-- Heading -->
                                        <span class="d-block font-weight-bold text-dark">Option Value</span>

                                        <customEditor
                                            classes="px-2 py-3 mb-3"
                                            :useCodeEditor="false"
                                            :placeholder="'{{ item.id }}'"
                                            :content="localScreenContent.select_reply.dynamic_options.template_value"
                                            @contentChange="localScreenContent.select_reply.dynamic_options.template_value = $event">
                                        </customEditor>
                                        
                                        <!-- Heading -->
                                        <span class="d-block font-weight-bold text-dark">Reference Name</span>
                                        
                                        <Input 
                                            v-model="localScreenContent.select_reply.dynamic_options.template_reference_name"
                                            maxlength="30" type="text" class="w-100 mb-3" placeholder="selected_item">
                                            <div slot="prepend">@</div>
                                        </Input>
                                    
                                        <!-- Heading -->
                                        <span class="d-block font-weight-bold text-dark">Link</span>

                                        <!-- Link To Screen Selector -->
                                        <div class="d-flex mb-2">       
                                            <Icon type="ios-pin-outline" size="20"  class="mt-1 ml-1 mr-2 text-muted font-weight-bold" />
                                            <Select v-model="localScreenContent.next_screen" filterable placeholder="Link To Screen">

                                                <Option 
                                                    v-for="(screen, key) in screenTree"
                                                    :key="key" :value="screen.title" :label="screen.title"
                                                    :disabled="localScreenContent.title == screen.title">

                                                    <!-- Screen title -->
                                                    <span>{{ screen.title }}</span>

                                                </Option>

                                            </Select>
                                        </div>

                                    </div>
                                    
                                    <!-- Heading -->
                                    <span class="d-block font-weight-bold text-dark">No Results Message</span>
                                    
                                    <Input 
                                        v-model="localScreenContent.select_reply.dynamic_options.no_results_message"
                                        placeholder="Enter message for zero results found"
                                        type="textarea" :rows="2" class="w-100 mb-3">
                                    </Input>
                                
                                </Col>

                            </Row>
                        </template>

                        <template v-else>

                            <Row :gutter="4" class="bg-primary text-white mb-2">
                                
                                <Col :span="2">

                                    <!-- Number Column -->
                                    <span class="d-block text-center mt-1">#</span>
                                
                                </Col>
                                
                                <Col :span="12">

                                    <!-- Option Name Column -->
                                    <span class="d-block mt-1">Name</span>
                                
                                </Col>
                                
                                <Col :span="10">

                                    <!-- Link Column -->
                                    <span class="d-block mt-1">Link</span>

                                </Col>

                            </Row>

                            <template v-if="localScreenContent.select_reply.static_options.length">
                                <Row :gutter="4" v-for="(option, x) in localScreenContent.select_reply.static_options" :key="x">
                                    
                                    <Col :span="2">

                                        <!-- Option Number -->
                                        <span class="d-block text-center mt-1">{{ x + 1 }}</span>
                                    
                                    </Col>
                                    
                                    <Col :span="10">

                                        <!-- Option Name -->
                                        <Input 
                                            v-model="option.name" type="text"
                                            class="w-100 mb-2" placeholder="Option name"
                                            @blur="option.name = handleDynamicContent(option.name)"
                                            @focus="option.name = handleDynamicContent(option.name)">
                                        </Input>
                                    
                                    </Col>
                                    
                                    <Col :span="10">

                                        <!-- Link To Screen Selector -->
                                        <div class="d-flex mb-2">       
                                            <Icon type="ios-pin-outline" size="20"  class="mt-1 ml-1 mr-2 text-muted font-weight-bold" />
                                            <Select v-model="option.next_screen" filterable placeholder="Link To Screen">

                                                <Option 
                                                    v-for="(screen, key) in screenTree"
                                                    :key="key" :value="screen.title" :label="screen.title"
                                                    :disabled="localScreenContent.title == screen.title">

                                                    <!-- Screen title -->
                                                    <span>{{ screen.title }}</span>

                                                </Option>

                                            </Select>
                                        </div>

                                    </Col>
                                    
                                    <Col :span="2">

                                        <!-- Remove Option Button  -->
                                        <Poptip confirm title="Are you sure you want to remove this option?" 
                                                ok-text="Yes" cancel-text="No" width="300" @on-ok="removeOption(x)"
                                                placement="top-end">
                                            <Icon type="ios-trash-outline" class="screen-icon hidable mr-2" size="20"/>
                                        </Poptip>

                                    </Col>

                                </Row>
                            </template>

                            <!-- No request data message -->
                            <Alert v-else type="info" class="mb-2" show-icon>No options</Alert>

                            <div class="clearfix">

                                <!-- Create Screen Button -->
                                <Button class="float-right" @click.native="addOption()">
                                    <Icon type="ios-add" :size="20" />
                                    <span>Add Option</span>
                                </Button>

                            </div>

                        </template>
                        
                    </template>

                </template>

                <template v-if="selectedTab == 'Validation'">

                    <!-- Validation Rules -->
                    <Alert v-if="!numberOfActiveValidationRules" type="info" style="line-height: 1.4em;" class="mb-2" closable>
                        Select <span class="font-italic text-success font-weight-bold">Validation Rules</span> to only
                        allow the user to provide specific type of information e.g Only Numbers are allowed for Age or
                        only Letters are allowed for First Name.
                    </Alert>

                    <div>
                        
                        <p class="border mb-2 mt-2 p-2">
                            Using <span class="text-success font-weight-bold">{{ numberOfActiveValidationRules }}</span> 
                            Validation {{ numberOfActiveValidationRules == 1 ? 'Rule': 'Rules' }}
                        </p>

                        <Row :gutter="4" class="mb-2">
                            
                            <Col :span="12">
                                <span class="font-weight-bold text-dark">Rule</span>
                            </Col>
                            
                            <Col :span="12">
                                <span class="font-weight-bold text-dark">Error Message</span>
                            </Col>

                        </Row>

                        <Row :gutter="4" v-for="(validation_rule, index) in validation_rules" :key="index" 
                             :class="validation_rule.name == 'Custom' ? 'bg-grey-light border p-1 mb-2' : 'mb-2'">
                
                            <!-- Custom Validation Name -->           
                            <template v-if="validation_rule.name == 'Custom'">

                                <Col :span="12">
                                
                                    <!-- Custom Validation Rule Name -->
                                    <Input 
                                        v-model="validation_rule.display_name" 
                                        type="text" placeholder="Custom name"
                                        @on-focus="handleValidationRuleChanges()"
                                        @on-blur="handleValidationRuleChanges()">
                                        
                                        <!-- Enable / Disable Validation Checkbox -->
                                        <Checkbox 
                                            class="m-0"
                                            slot="prepend"
                                            v-model="validation_rule.active"
                                            @on-change="handleValidationRuleChanges()">
                                        </Checkbox>

                                    </Input>
                                
                                    <!-- Custom Regex Rule -->
                                    <Input 
                                        class="regex-input mt-1"
                                        v-model="validation_rule.rule" 
                                        type="text" placeholder="/[a-zA-Z0-9]+/"
                                        @on-focus="handleValidationRuleChanges()"
                                        @on-blur="handleValidationRuleChanges()">
                                        <span slot="prepend">Regex Rule</span>
                                    </Input>
                                
                                </Col>

                            </template>

                            <!-- Other Validation Name -->
                            <template v-else>

                                <Col :span="12">

                                    <!-- Enable / Disable Validation Checkbox -->
                                    <Checkbox 
                                        v-model="validation_rule.active"
                                        @on-change="handleValidationRuleChanges()">
                                        {{ validation_rule.name }}
                                    </Checkbox>
                                
                                </Col>

                            </template>
                            
                            <!-- Validation Error Message -->    
                            <Col :span="validation_rule.name == 'Custom' ? 10: 12">

                                <Input 
                                    v-model="validation_rule.error_msg" type="textarea" 
                                    :rows="validation_rule.name == 'Custom' ? 2 : 1" 
                                    placeholder="Validation error message"
                                    @on-focus="handleValidationRuleChanges()"
                                    @on-blur="handleValidationRuleChanges()">
                                </Input>
                            
                            </Col>

                            <!-- Remove Custom Validation Rule  -->
                            <template v-if="validation_rule.name == 'Custom'">
                                    
                                <Col :span="2">

                                    <!-- Remove Custom Validation Rule Button  -->
                                    <Poptip confirm title="Are you sure you want to remove this validation rule?" 
                                            ok-text="Yes" cancel-text="No" width="300" @on-ok="removeCustomValidationRule(index)"
                                            placement="top-end">
                                        <Icon type="ios-trash-outline" size="20"/>
                                    </Poptip>

                                </Col>

                            </template>

                        </Row>

                        <div class="clearfix">

                            <!-- Add Custom Validation Rule Button  -->
                            <Button class="float-right" @click.native="addCustomValidationRule()">
                                <Icon type="ios-add" :size="20" />
                                <span>Add Custom Rule</span>
                            </Button>

                        </div>

                    </div>

                </template>

                <template v-if="selectedTab == 'Formatting'">

                    <!-- Formatting -->
                    <Alert v-if="!numberOfActiveFormattingRules" type="info" style="line-height: 1.4em;" class="mb-2" closable>
                        Select <span class="font-italic text-success font-weight-bold">Formatting Rules</span> to make sure
                        that user information is correctly formatted upon collection e.g Capitalize the user's input 
                        (Such as their First Name) or convert the user's input into a proper Date.
                    </Alert>

                    <div>
                        
                        <p class="border mb-2 mt-2 p-2">
                            Using <span class="text-success font-weight-bold">{{ numberOfActiveFormattingRules }}</span> 
                            Formatting {{ numberOfActiveFormattingRules == 1 ? 'Rule': 'Rules' }}
                        </p>

                        <Row :gutter="4" class="mb-2">
                            
                            <Col :span="24">

                                <!-- Input Value Name -->
                                <Input 
                                    v-model="localScreenContent.formatting.reply_name" 
                                    maxlength="30" type="text" class="w-100 mb-2" placeholder="Formatted Reply name (Optional)">
                                    <div slot="prepend">@</div>
                                </Input>

                                <span class="font-weight-bold text-dark">Formatting Rules</span>

                            </Col>

                        </Row>

                        <Row :gutter="4">

                            <Col v-for="(formatting_rule, index) in formatting_rules" 
                                :key="index" :span="checkIfSpecialFormatRule(formatting_rule) ? 24 : 8"
                                :class="checkIfSpecialFormatRule(formatting_rule) ? 'bg-grey-light border p-1 mb-2' : 'mb-2'">
                    
                                <!-- Custom Structure -->
                                <template v-if="checkIfSpecialFormatRule(formatting_rule)">

                                    <!-- If Truncate (Limit Characters) -->           
                                    <template v-if="formatting_rule.type == 'truncate'">
                                             
                                        <Row :gutter="4">

                                            <Col :span="12">
                                           
                                                <!-- Enable / Disable Formatting Checkbox -->
                                                <Checkbox 
                                                    class="m-0"
                                                    v-model="formatting_rule.active"
                                                    @on-change="handleFormattingRuleChanges()">
                                                    {{ formatting_rule.name }}
                                                </Checkbox>

                                            </Col>
                                    
                                            <Col :span="12">
                                    
                                                <!-- Maximum characters -->
                                                <Input 
                                                    v-model="formatting_rule.limit" :min="1"
                                                    type="number" placeholder="Maximum characters e.g 5"
                                                    @on-focus="handleFormattingRuleChanges()"
                                                    @on-blur="handleFormattingRuleChanges()">
                                                    <span slot="prepend">Limit</span>
                                                </Input>

                                            </Col>

                                        </Row>

                                    </template>

                                    <!-- If Convert To Money -->        
                                    <template v-else-if="formatting_rule.type == 'convert_to_money'">
                                                
                                        <!-- Enable / Disable Formatting Checkbox -->
                                        <Checkbox 
                                            class="m-0"
                                            v-model="formatting_rule.active"
                                            @on-change="handleFormattingRuleChanges()">
                                            {{ formatting_rule.name }}
                                        </Checkbox>

                                        <Row :gutter="4" class="mt-1">>

                                            <Col :span="12">
                                                <!-- Currency Symbol -->
                                                <Input 
                                                    v-model="formatting_rule.currency_symbol" 
                                                    type="text" placeholder="E.g $ or USD"
                                                    @on-focus="handleFormattingRuleChanges()"
                                                    @on-blur="handleFormattingRuleChanges()">
                                                    <span slot="prepend">Symbol</span>
                                                </Input>
                                            </Col>
                                    
                                            <Col :span="12">
                                                <!-- Decimal Points -->
                                                <Input 
                                                    v-model="formatting_rule.decimal_points" 
                                                    type="number" :min="0" :max="4" placeholder="E.g 2"
                                                    @on-focus="handleFormattingRuleChanges()"
                                                    @on-blur="handleFormattingRuleChanges()">
                                                    <span slot="prepend">Decimals</span>
                                                </Input>
                                            </Col>

                                        </Row>

                                    </template>

                                    <!-- If Date Format -->        
                                    <template v-else-if="formatting_rule.type == 'date_format'">
                                                
                                        <!-- Enable / Disable Formatting Checkbox -->
                                        <Checkbox 
                                            class="m-0"
                                            v-model="formatting_rule.active"
                                            @on-change="handleFormattingRuleChanges()">
                                            {{ formatting_rule.name }}
                                        </Checkbox>

                                        <Row :gutter="4" class="mt-1">

                                            <Col :span="12">
                                                <!-- Current Format -->
                                                <Input 
                                                    v-model="formatting_rule.current_format" 
                                                    type="text" placeholder="E.g DD-MM-YYYY"
                                                    @on-focus="handleFormattingRuleChanges()"
                                                    @on-blur="handleFormattingRuleChanges()">
                                                    <span slot="prepend">Current Format</span>
                                                </Input>
                                            </Col>
                                    
                                            <Col :span="12">
                                                <!-- New Format -->
                                                <Input 
                                                    v-model="formatting_rule.new_format" 
                                                    type="text" placeholder="E.g DD/MM/YYYY"
                                                    @on-focus="handleFormattingRuleChanges()"
                                                    @on-blur="handleFormattingRuleChanges()">
                                                    <span slot="prepend">New Format</span>
                                                </Input>
                                            </Col>

                                        </Row>

                                    </template>

                                    <!-- If Custom Format -->        
                                    <template v-else-if="formatting_rule.type == 'custom_format'">
                                                
                                        <!-- Enable / Disable Formatting Checkbox -->
                                        <Checkbox 
                                            class="m-0"
                                            v-model="formatting_rule.active"
                                            @on-change="handleFormattingRuleChanges()">
                                            {{ formatting_rule.name }}
                                        </Checkbox>

                                        <Row :gutter="4" class="mt-1">

                                            <Col :span="24">

                                                <!-- Custom PHP Code Editor -->
                                                <customEditor
                                                    :useCodeEditor="true"
                                                    :codeContent="formatting_rule.custom_format"
                                                    sampleCodeTemplate="ussd_creator_custom_formatting_sample_code"
                                                    @codeChange="formatting_rule.custom_format = $event; handleFormattingRuleChanges()">
                                                </customEditor>

                                            </Col>

                                        </Row>

                                    </template>

                                </template>

                                <!-- Default Structure -->
                                <template v-else>

                                    <!-- Enable / Disable Formatting Checkbox -->
                                    <Checkbox 
                                        v-model="formatting_rule.active"
                                        @on-change="handleFormattingRuleChanges()">
                                        {{ formatting_rule.name }}
                                    </Checkbox>

                                </template>

                            </Col>

                        </Row>

                        <div class="clearfix">

                            <!-- Add Custom Formatting Rule Button  -->
                            <Button class="float-right" @click.native="addCustomFormattingRule()">
                                <Icon type="ios-add" :size="20" />
                                <span>Add Custom Rule</span>
                            </Button>

                        </div>

                    </div>

                </template>

                <template v-if="selectedTab == 'Settings'">

                    <!-- Formatting -->
                    <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
                        Manage additional <span class="font-italic text-success font-weight-bold">Screen Settings</span> by
                        adjusting optional screen options such as changing the "Go Back" to "Previous Screen" or changing
                        the "Show More" to "More Products"
                    </Alert>

                </template>

            </TabPane>

        </Tabs>

    </div>

</template>

<script>

    import customEditor from '../../../../components/_common/wiziwigEditors/customEditor.vue';

    export default {
        props:{
            screenContent: {
                type: Object,
                default:null
            },
            screenTree: {
                type: Array,
                default: function(){
                    return []
                }
            }
        }, 
        components: { customEditor },
        data(){
            return {
                localScreenContent: this.screenContent,
                selectedContentTab: 'Instructions',
                screenActions: [
                    {
                        name: 'No Action'
                    },
                    {
                        name: 'Input Value'
                    },
                    {
                        name: 'Select Option'
                    }
                ],
                validation_rules:[
                    {
                        active: false,
                        rule: '/[a-zA-Z]+/',
                        name: 'Only Letters',
                        error_msg: 'Please enter letters only'
                    },
                    {
                        active: false,
                        rule: '/[0-9]+/',
                        name: 'Only Numbers',
                        error_msg: 'Please enter numbers only'
                    },
                    {
                        active: false,
                        rule: '/[a-zA-Z0-9]+/',
                        name: 'Only Numbers & Letters',
                        error_msg: 'Please enter numbers and letters only'
                    },
                    {
                        min: 2,
                        active: false,
                        rule: '/[a-zA-Z0-9]+/',
                        name: 'Minimum Characters',
                        error_msg: 'Please enter 2 or more characters'
                    },
                    {
                        max: 5,
                        active: false,
                        rule: '/[a-zA-Z0-9]+/',
                        name: 'Maximum Characters',
                        error_msg: 'Please enter no more than 2 characters'
                    },
                    {
                        active: false,
                        rule: '//',
                        name: 'Validate Email',
                        error_msg: 'Please provide a valid email address'
                    },
                    {
                        active: false,
                        rule: '//',
                        name: 'Validate Phone Number',
                        error_msg: 'Please provide a valid phone number'
                    },
                    {
                        active: false,
                        rule: '/[0-9]{2}\/[0-9]{2}\/[0-9]{2}/',
                        name: 'Validate Date Format - DD/MM/YYYY',
                        error_msg: 'Please enter a valid date (DD/MM/YYYY) e.g 02/08/2020'
                    },
                    {
                        value: 3,
                        active: false,
                        rule: '//',
                        name: 'Equal To (=)',
                        error_msg: 'Please enter the number 3'
                    },
                    {
                        value: 3,
                        active: false,
                        rule: '//',
                        name: 'Not Equal To',
                        error_msg: 'Please enter any number except 3'
                    },
                    {
                        value: 3,
                        active: false,
                        rule: '//',
                        name: 'Less Than (<)',
                        error_msg: 'Please enter numbers less than 3'
                    },
                    {
                        value: 3,
                        active: false,
                        rule: '//',
                        name: 'Greater Than (>)',
                        error_msg: 'Please enter numbers greater than 3'
                    },
                    {
                        active: false,
                        rule: '//',
                        name: 'Avoid Spaces',
                        error_msg: 'Do not use spaces.'
                    },
                    {
                        active: false,
                        rule: '//',
                        name: 'Avoid Special Characters e.g ($ % & *)',
                        error_msg: 'Do not use special characters e.g ($ % & *)'
                    }
                ],
                formatting_rules: [
                    {
                        active: false,
                        type: 'capitalize',
                        name: 'Capitalize',
                    },
                    {
                        active: false,
                        type: 'uppercase',
                        name: 'Uppercase',
                    },
                    {
                        active: false,
                        type: 'lowercase',
                        name: 'Lowercase',
                    },
                    {
                        active: false,
                        type: 'trim_left_spaces',
                        name: 'Trim Left Spaces',
                    },
                    {
                        active: false,
                        type: 'trim_right_spaces',
                        name: 'Trim Right Spaces',
                    },
                    {
                        active: false,
                        type: 'truncate',
                        name: 'Limit Characters',
                        limit: 10,
                    },
                    {
                        active: false,
                        type: 'convert_to_money',
                        name: 'Convert To Money',
                        currency_symbol: 'P',
                        decimal_points: 2,
                    },
                    {
                        active: false,
                        type: 'date_format',
                        name: 'Date Format',
                        current_format: 'DD-MM-YYYY',
                        new_format: 'DD/MM/YYYY',
                    },
                    {
                        active: false,
                        type: 'custom_format',
                        name: 'Custom Format',
                        custom_format: ''
                    },
                ]
            }
        },
        watch: {

            //  Watch for changes on the screenContent
            screenContent: {
                handler: function (val, oldVal) {

                    this.localScreenContent = val;

                },
                deep: true
            }
        },
        computed: {
            
            activeValidationRules(){
                
                //  Get all active validation rules
                return this.validation_rules.filter( (validation_rule) => { 
                        return validation_rule.active == true;
                    }) || [];

            },
            
            numberOfActiveValidationRules(){
                
                //  Count all active validation rules
                return this.activeValidationRules.length || 0;

            },

            activeFormattingRules(){
                
                //  Get all active formatting rules
                return this.formatting_rules.filter( (formatting_rule) => { 
                        return formatting_rule.active == true;
                    }) || [];

            },
            
            numberOfActiveFormattingRules(){
                
                //  Count all active formatting rules
                return this.activeFormattingRules.length || 0;

            },
            
        },
        methods: {
            generateTabLabel(selectedTab){
                if(selectedTab == 'Validation'){

                    var countRules = this.numberOfActiveValidationRules;
                    return selectedTab + (countRules ?  ' ('+countRules+')': '');

                }else if(selectedTab == 'Formatting'){

                    var countRules = this.numberOfActiveFormattingRules;
                    return selectedTab + (countRules ?  ' ('+countRules+')': '');

                }else{
                    return selectedTab
                }
            },
            addCustomValidationRule(){

                //  Build the custom validation rule template
                var template = {
                        active: false,
                        rule: '/[a-zA-Z0-9]+/',
                        name: 'Custom',
                        display_name: 'Custom Rule',
                        error_msg: 'Custom validation error'
                    };

                //  Add the screen to the screen tree
                this.validation_rules.push( template );

            },
            removeCustomValidationRule(index){
                //  Remove custom validation rule
                this.validation_rules.splice(index, 1);

                //  Update the screen content with active validation rules
                this.handleValidationRuleChanges();
            },
            handleValidationRuleChanges(){
                //  Update the screen content with active validation rules
                this.localScreenContent.validation.rules = this.activeValidationRules;
            },
            addCustomFormattingRule(){

            },
            handleFormattingRuleChanges(){
                //  Update the screen content with active formatting rules
                this.localScreenContent.formatting.rules = this.activeFormattingRules;
            },
            checkIfSpecialFormatRule(formatting_rule){
                return ['truncate', 'convert_to_money', 'date_format', 'custom_format'].includes(formatting_rule.type) 
                       && formatting_rule.active
            },
            addOption(){

                //  Build the option template
                var optionTemplate = {
                        name: '', 
                        next_screen: ''
                    };

                //  Add the screen to the screen tree
                this.localScreenContent.select_reply.static_options.push( optionTemplate );

            },
            removeOption(index){
                //  Remove screen from list
                this.localScreenContent.select_reply.static_options.splice(index, 1);
            },
            getDynamicContent(currentScreen){

                var currentScreenTitle = currentScreen.title;
                
                var dynamicContent = [];

                for(var x = 0; x < this.screenTree.length; x++){

                    //  If the screen allows the user to respond with a reply
                    if( this.screenTree[x].reply_type != 'No Action'){

                        //  If the screen links the user to this current screen
                        if( this.screenTree[x].content.next_screen == currentScreenTitle ){

                            //  Get the reply field name
                            let replyName = this.screenTree[x].reply_name;

                            //  If the reply field is not empty
                            if( replyName != '' && replyName != null ){

                                //  Add the reply field name to the dynamic content fields
                                dynamicContent.push( replyName );

                            }

                            var additionalDynamicContent = this.getDynamicContent(this.screenTree[x]);

                            if( additionalDynamicContent.length ){

                                for(var x = 0; x < additionalDynamicContent.length; x++){

                                    dynamicContent.push( additionalDynamicContent[x] );

                                }

                            }

                        }

                    }

                }

                //  Return all the dynamic content fields
                return dynamicContent;

            },
            addDynamicContent(screen, dynamicContentField){

                if( screen.description.text.trim() == '' ){

                    //  Update the screen description with the new dynamic content
                    screen.description.text = screen.description.text.trim() + '['+dynamicContentField+']';

                }else{

                    //  Update the screen description with the new dynamic content
                    screen.description.text = screen.description.text.trim() + ' ['+dynamicContentField+']';
                }

            }
        },
        created(){

        }
    }

</script>