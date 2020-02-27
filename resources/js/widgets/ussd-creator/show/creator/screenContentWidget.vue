<template>

    <!-- Screen Content --> 
    <div>

        <div class="clearfix pb-2 mb-3 border-bottom">

            <!-- Heading -->
            <span class="d-block mt-2 font-weight-bold text-dark">Description</span>

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

            <!-- Screen Description Input -->
            <customEditor
                :content="localScreenContent.description.text"
                @contentChange="localScreenContent.description.text = $event"
                :codeContent="localScreenContent.description.code_editor_text"
                :useCodeEditor="localScreenContent.description.code_editor_mode"
                @codeChange="localScreenContent.description.code_editor_text = $event">
            </customEditor>

            <Poptip word-wrap width="350" trigger="hover" placement="top-start"
                    content="Use dynamic content inside this description field? ">

                <template slot="content">
                    <span class="d-block">Use dynamic content inside this description field?</span>
                
                    <span style="margin-top: -15px;" class="border-top d-block pt-2">
                        <span class="font-weight-bold">Note: Always wrap strings in single quotes ('') and use the period (.) to concatenat values. Make sure to include <span class="text-primary">return</span> at the begining of your description.</span>
                    </span>
                </template>

                <Checkbox v-model="localScreenContent.description.code_editor_mode">Use Code Editor</Checkbox>

            </Poptip> 

        </div>

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
                maxlength="20" type="text" class="w-100 mb-2" placeholder="Reply name">
                <div slot="prepend">@</div>
            </Input>
        
            <!-- Link To Screen Selector -->
            <Select v-model="localScreenContent.next_screen" filterable class="w-100 mb-2" placeholder="Screen name">
                
                <div slot="prepend">Link</div>

                <Option v-for="(screen, key) in screenTree" :key="key" class="mb-2"
                        :value="screen.title" :label="screen.title"
                        :disabled="localScreenContent.title == screen.title">

                    <!-- Screen title -->
                    <span>{{ screen.title }}</span>

                </Option>

            </Select>
            
        </template>

        <template v-if="localScreenContent.reply_type == 'Select Option'">
                           
            <!-- Input Value Name -->
            <Input 
                v-model="localScreenContent.reply_name" 
                v-if="!localScreenContent.select_reply.is_dynamic"
                maxlength="20" type="text" class="w-100 mb-2" placeholder="Reply name">
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
                        <Input 
                            type="text" class="w-100 mb-2" :placeholder="'{{ items }}'"
                            v-model="localScreenContent.select_reply.dynamic_options.group_reference" 
                            @blur="localScreenContent.select_reply.dynamic_options.group_reference = handleDynamicContent(localScreenContent.select_reply.dynamic_options.group_reference)"
                            @focus="localScreenContent.select_reply.dynamic_options.group_reference = handleDynamicContent(localScreenContent.select_reply.dynamic_options.group_reference)">
                        </Input>

                    </Col>
                    
                    <Col :span="2">

                        <span class="d-block text-center mt-1">As</span>
                    
                    </Col>
                    
                    <Col :span="10">
                    
                        <!-- Input Value Name -->
                        <Input 
                            v-model="localScreenContent.reply_name" 
                            maxlength="20" type="text" class="w-100 mb-2" :placeholder="'item'"
                            :disabled="!localScreenContent.select_reply.dynamic_options.group_reference">
                            <div slot="prepend">@</div>
                        </Input>
                    
                    </Col>
                    
                    <Col :span="24">

                        <!-- Heading -->
                        <span class="d-block font-weight-bold text-dark float-left">Option Name</span>

                        <Input 
                            v-model="localScreenContent.select_reply.dynamic_options.template_name" 
                            type="textarea" class="w-100 mb-3" :placeholder="'{{ item.name }} - {{ item.price }}'"
                            @blur="localScreenContent.select_reply.dynamic_options.template_name = handleDynamicContent(localScreenContent.select_reply.dynamic_options.template_name)"
                            @focus="localScreenContent.select_reply.dynamic_options.template_name = handleDynamicContent(localScreenContent.select_reply.dynamic_options.template_name)">
                        </Input>

                        <!-- Heading -->
                        <span class="d-block font-weight-bold text-dark float-left">Option Value</span>

                        <Input 
                            v-model="localScreenContent.select_reply.dynamic_options.template_value" 
                            type="textarea" class="w-100 mb-3" :placeholder="'{{ item.id }}'"
                            @blur="localScreenContent.select_reply.dynamic_options.template_value = handleDynamicContent(localScreenContent.select_reply.dynamic_options.template_value)"
                            @focus="localScreenContent.select_reply.dynamic_options.template_value = handleDynamicContent(localScreenContent.select_reply.dynamic_options.template_value)">
                        </Input>
                    
                        <!-- Heading -->
                        <span class="d-block font-weight-bold text-dark float-left">Link</span>

                        <!-- Link To Screen Selector -->
                        <Select v-model="localScreenContent.next_screen" filterable class="w-100 mb-2" placeholder="Screen name">

                            <Option v-for="(screen, key) in screenTree" :key="key" class="mb-2"
                                    :value="screen.title" :label="screen.title"
                                    :disabled="localScreenContent.title == screen.title">

                                <!-- Screen title -->
                                <span>{{ screen.title }}</span>

                            </Option>

                        </Select>
                    
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

                            <!-- Option Link -->
                            <Select v-model="option.next_screen" filterable class="w-100 mb-2" placeholder="Screen name">
                                
                                <div slot="prepend">Link</div>

                                <Option v-for="(screen, y) in screenTree" 
                                        :key="y" class="mb-2"
                                        :value="screen.title" :label="screen.title"
                                        :disabled="localScreenContent.title == screen.title">
                                    
                                    <!-- Placeholder Icon -->
                                    <Icon type="ios-add" :size="20" slot="prefix" />

                                    <!-- Screen title -->
                                    <span>{{ screen.title }}</span>

                                </Option>

                            </Select>

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

        },
        methods: {
            handleDynamicContent(text = '', removeCurrentHTML = true, addHtmlToMustacheSyntax = true){

                //  Insert dynamic content inside curly braces within span tags with special styles
                function wrapInHTMLTags(match, offset, string){
                    
                    return '<span class="dynamic-content-label">' + match + '</span>';

                }

                //  Replace all matches with nothing (An empty string)
                function replaceWithNothing(match, offset, string){
                    
                    return '';

                }

                //  Get the content to format
                var content = text;

                if( removeCurrentHTML == true ){

                    //  This pattern searches for any HTML tags e.g <span ...> or </span>
                    var pattern = /([<][a-zA-Z/!][^>]*[>])/g;
                    
                    //  Replace all HTML tags within the content string with nothing
                    content = content.replace(pattern, replaceWithNothing);

                }

                if( addHtmlToMustacheSyntax == true ){

                    //  This pattern searches for anything using curly braces e.g {{ company }}
                    var pattern = /[{]{2}[\s]*[a-zA-Z_]{1}[a-zA-Z0-9_\.]{0,}[\s]*[}]{2}/g;

                    //  Wrap content with curly braces in HTML tags 
                    content = content.replace(pattern, wrapInHTMLTags);
                    
                }

                //  Return the formatted content
                return content;
                
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