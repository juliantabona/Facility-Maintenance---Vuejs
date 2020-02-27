<template>

    <Row :gutter="12" class="mt-4">

        <!-- Creator Column 1 -->
        <Col :span="7">

            <Card >

                <div slot="title">
                    <span v-for="(option, key) in ['Editor', 'Simulator']" :key="key" @click="activeView = option"
                            :class="(activeView == option ? 'text-primary font-weight-bold': 'text-dark') + ' mr-2'">{{ option }}</span>
                </div>

                <!-- Screens -->      
                <Row :gutter="20">

                    <Col :span="24">

                        <div class="clearfix pb-2 mb-3 border-bottom">

                            <!-- Heading -->
                            <span class="d-block mt-2 font-weight-bold text-dark float-left">Screens</span>

                            <!-- Create Screen Button -->
                            <Button class="p-1 float-right" @click.native="addScreen()">
                                <Icon type="ios-add" :size="20" />
                            </Button>

                        </div>

                        <!-- Requests Dragger  -->
                        <draggable v-if="screenTree.length"
                            :list="screenTree"
                            @start="drag=true" 
                            @end="drag=false" 
                            :options="{
                                group:'screen-menu',
                                draggable:'.screen-menu-option', 
                                handle:'.screen-dragger-handle'
                            }">

                            <!-- Single Request  -->
                            <screenMenuOption v-for="(screen, index) in screenTree" :key="index"   
                                :index="index"
                                :screen="screen"
                                :activeScreen="activeScreen"
                                @selected="handleSelectScreenMenu($event)"
                                @duplicate="handleDuplicateScreenMenu($event)"
                                @remove="handleDeleteScreenMenu($event)">
                            </screenMenuOption>

                        </draggable>

                        <!-- No screens message -->
                        <Alert v-else type="info" class="mb-2" show-icon>No screens</Alert>

                    </Col>

                </Row>

                <!-- Components -->      
                <Row :gutter="20">

                    <Col :span="24">
                    
                        <Divider orientation="left">

                            <!-- Heading -->
                            <span class="font-weight-bold text-dark float-left">Components</span>
                        
                        </Divider>

                    </Col>

                    <Col :span="24">
                    
                        <Row :gutter="4">

                            <Col :span="12">

                                <Card :padding="0">
                                    <Icon type="ios-code-working" size="32" class="d-block text-center pt-2" />
                                    <span class="text-center d-block pt-1 pb-2">Text</span>
                                </Card>

                            </Col>

                            <Col :span="12">

                                <Card :padding="0">
                                    <Icon type="ios-more-outline" size="32" class="d-block text-center pt-2" />
                                    <span class="text-center d-block pt-1 pb-2">Options</span>
                                </Card>

                            </Col>

                        </Row>

                    </Col>

                </Row>

            </Card>

        </Col>
        
        <Col :span="17">

            <Row>

                <!-- Screen Editor -->
                <Col v-show="activeView == 'Editor'" :span="20">
                    
                    <Card>

                        <!-- Screen Settings -->      
                        <Row v-if="activeScreen" :gutter="20">

                            <Col :span="24">

                                <Divider orientation="left">
                                    <span class="text-primary">Screen Settings</span>
                                </Divider>

                            </Col>

                            <Col :span="24">

                                <!-- Heading -->
                                <span class="d-block font-weight-bold text-dark float-left">Screen name</span>

                                <!-- Screen Name Input -->
                                <el-input 
                                    maxlength="40" show-word-limit
                                    type="text" v-model="activeScreen.title" 
                                    size="small" class="w-100 mb-3" placeholder="Screen name">
                                </el-input>

                                <div class="mb-2">
                                    <span class="font-weight-bold text-dark">Use APIs: </span>
                                    <Poptip trigger="hover" width="350" placement="top-start" word-wrap 
                                            content="Does this screen have dynamic information you can access using an API? E.g products or users stored in an external system">
                                        <i-switch 
                                            true-color="#13ce66" 
                                            false-color="#ff4949" 
                                            class="ml-1" size="large"
                                            :disabled="false"
                                            :value="activeScreen.is_dynamic" 
                                            @on-change="activeScreen.is_dynamic = $event">
                                            <span slot="open">Yes</span>
                                            <span slot="close">No</span>
                                        </i-switch>
                                    </Poptip>
                                </div>

                            </Col>

                            <!-- Settings For Screen Not Using API's --> 
                            <Col v-if="activeScreen.is_dynamic" :span="24" class="mt-2">

                                <template v-if="activeScreen">

                                    <el-tabs value="general">
                                            
                                        <!-- General Settings -->
                                        <el-tab-pane label="General" name="general" class="pt-2">

                                            <div>
                                                <!-- Heading -->
                                                <span class="d-block text-dark float-left">
                                                    <span class="font-weight-bold">No Results </span>(Default Message)
                                                </span>

                                                <!-- Default No Results Message -->
                                                <el-input type="text" v-model="activeScreen.api.general.no_results_message" size="small" class="w-100 mb-3"></el-input>
                                            </div>

                                            <div>
                                                <!-- Heading -->
                                                <span class="d-block text-dark float-left">
                                                    <span class="font-weight-bold">Error </span>(Default Message)
                                                </span>

                                                <!-- Default API Error -->
                                                <el-input type="text" v-model="activeScreen.api.general.default_error_message" size="small" class="w-100 mb-3"></el-input>
                                            </div>

                                        </el-tab-pane>
                                            
                                        <!-- API Request Settings -->
                                        <el-tab-pane label="API Requests" name="api_requests">
                    
                                            <!-- Requests Dragger  -->
                                            <draggable v-if="activeScreen.api.requests.length"
                                                :list="activeScreen.api.requests"
                                                @start="drag=true" 
                                                @end="drag=false" 
                                                :options="{
                                                    group:'requests',
                                                    draggable:'.single-request', 
                                                    handle:'.request-dragger-handle'
                                                }">

                                                <!-- Single Request  -->
                                                <singleRequestWidget v-for="(request, index) in activeScreen.api.requests" :key="index"   
                                                    :index="index"
                                                    :request="request"
                                                    :screenTree="screenTree"
                                                    @removeRequest="handleRemoveRequest(index)">
                                                </singleRequestWidget>

                                            </draggable>

                                            <!-- No request data message -->
                                            <Alert v-else type="info" class="mb-2" show-icon>No requests</Alert>

                                            <div class="clearfix">

                                                <!-- Add Request Button -->
                                                <Button class="float-right" @click.native="addApiRequest(activeScreen)">
                                                    <Icon type="ios-add" :size="20" />
                                                    <span>Add Request</span>
                                                </Button>

                                            </div>

                                        </el-tab-pane>
                                            
                                        <!-- API Response Settings -->
                                        <el-tab-pane label="API Response" name="api_response">
                                            
                                        </el-tab-pane>

                                    </el-tabs>

                                </template>

                            </Col>

                            <!-- Settings For Screen Using API's --> 
                            <Col v-else :span="24">

                                <screenContentWidget :screenContent="activeScreen.content" :screenTree="screenTree"></screenContentWidget>

                            </Col>

                        </Row>

                    </Card>

                </Col>

                <!-- Loader And Save Changes Button -->
                <Col v-show="activeView == 'Editor'" :span="4">
                    
                    <!-- Loader -->
                    <Loader v-if="isSaving" :loading="true" type="text" class="text-left">Saving...</Loader>

                    <div v-if="!isSaving" class="clearfix">

                        <!-- Save Button -->
                        <basicButton 
                            class="float-right" customClass="pr-2 pl-2" 
                            type="success" size="default" 
                            :disabled="false"
                            :ripple="false"
                            @click.native="handleSave()">
                            <span>Save Changes</span>
                        </basicButton>

                    </div>

                </Col>
                
                <!-- Simulator -->
                <Col v-show="activeView == 'Simulator'" :span="14" :offset="3">

                    <ussdSimulator :ussdInterface="localUssdCreator" postURL="/api/ussd/creator"></ussdSimulator>

                </Col>

            </Row>

        </Col>

    </Row>

</template>

<script>
    
    import draggable from 'vuedraggable';

    /*  Loaders  */
    import Loader from './../../../../components/_common/loaders/Loader.vue';  

    /*  Buttons  */
    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    /*  Buttons  */
    import ussdSimulator from './../../../../components/_common/simulators/ussdSimulator.vue';

    import screenMenuOption from './screenMenuOption.vue';
    import screenContentWidget from './screenContentWidget.vue';
    import singleRequestWidget from './singleRequestWidget.vue';

    export default {
        props: { 
            ussdCreator: {
                type: Object,
                default: null
            }
        },
        components: { draggable, Loader, basicButton, ussdSimulator, 
                      screenMenuOption, screenContentWidget, singleRequestWidget
                    },
        data(){
            return {
                isSaving: false,
                activeScreen: null,
                activeView: 'Editor',
                localUssdCreator: this.ussdCreator,
                screenTree: (this.ussdCreator || {}).metadata || []
            }
        }, 
        computed: {
            
        },
        methods: {
            addScreen(){

                //  Generate the screen title
                var screenTitle = 'Screen ' + (this.screenTree.length + 1),

                //  Build the screen template
                screenTemplate = { 
                    title: screenTitle, 
                    is_dynamic: false, 
                    content: {
                        description: {
                            text: '',
                            code_editor_text: '',
                            code_editor_mode: false
                        },
                        next_screen: null,
                        reply_type: 'No Action',
                        reply_name: null,
                        select_reply: {
                            is_dynamic: false, 
                            dynamic_options: {
                                    group_reference: '', 
                                    template_value: '',
                                    template_name: ''
                                },
                            static_options: [
                                {
                                    name: '', 
                                    next_screen: ''
                                }
                            ]
                            
                        },
                    },
                    api: {
                        general: {
                            no_results_message: 'No results found',
                            default_error_message: 'Sorry, we are experiencing technical difficulties'
                        },
                        requests: []
                    }
                };

                //  Add the screen to the screen tree
                this.screenTree.push( screenTemplate );

            },
            addApiRequest(screen){

                //  Build the template
                var template = {
                        url: 'https://domain.com/api/items',
                        name: 'Get Items',
                        method: 'get',
                        trigger: 'on-enter',
                        payload: [],
                        response_data: [
                            {
                                status: 200,
                                attributes: [
                                    {
                                        name: 'Items',
                                        value: 'response'
                                    }
                                ],
                                content: {
                                    description: {
                                        text: '',
                                        code_editor_mode: false
                                    },
                                    reply_type: 'No Action',
                                    reply_name: null,
                                    select_reply: [
                                        {
                                            name: '', 
                                            next_screen: ''
                                        }
                                    ],
                                }
                            }
                        ]
                    };

                //  Add the api request data to the screen api request dataset
                screen.api.requests.push( template );

            },
            showEditor(){
                //  Switch to the editor view
                this.activeView = 'Editor';
            },
            handleSelectScreenMenu(index){

                //  Switch to the editor view
                this.showEditor();

                //  Set the selected screen as the active screen
                this.activeScreen = this.screenTree[index];
            },
            handleDuplicateScreenMenu(index){

                //  Switch to the editor view
                this.showEditor();

                //  Duplicate the screen
                var duplicateScreen = _.cloneDeep( this.screenTree[index] );

                var duplicateName = 'Screen Copy';

                var num_similar_names = this.screenTree.filter( (screen) => { 
                        return screen.title.substring(0, 11) == duplicateName;
                    }).length;

                duplicateScreen.title = duplicateName + ' - '+ (num_similar_names + 1);

                this.screenTree.push(duplicateScreen);
                this.activeScreen = duplicateScreen;
            },
            handleDeleteScreenMenu(index){
                //  Remove screen from list
                this.screenTree.splice(index, 1);
            },
            handleSave(){

                const self = this;

                //  Start loader
                this.isSaving = true;

                this.localUssdCreator.metadata = this.screenTree;

                var updateData = this.localUssdCreator;

                if(self.localUssdCreator['_links'].self.href){

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('post', self.localUssdCreator['_links'].self.href, updateData)
                        .then(({data}) => {

                            //  Stop loader
                            this.isSaving = false;
                            
                            console.log(data);
                            
                        })         
                        .catch(response => { 
                            
                            //  Stop loader
                            this.isSaving = false;
                        
                            console.log(response);  
            
                        });

                }

            }
        }
    };
  
</script>