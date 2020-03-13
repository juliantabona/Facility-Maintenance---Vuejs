<template>

    <Row :gutter="12" class="mt-4">

        <!-- Creator Column 1 -->
        <Col :span="7">

            <Card >

                <div slot="title">
                    <span v-for="(option, key) in ['Editor', 'Simulator']" :key="key" @click="activeView = option">
                        
                        <Button type="text" ghost>
                        
                            <!-- Option Icon -->
                            <Icon :type="option == 'Editor' ? 'ios-git-branch' : 'ios-phone-portrait'" :size="20"
                                :class="(activeView == option ? 'text-primary': 'text-dark') + ' mr-1'" />
                            
                            <!-- Option Name -->
                            <span :class="(activeView == option ? 'text-primary font-weight-bold': 'text-dark')">
                                {{ option }}
                            </span>

                        </Button>

                    </span>
                </div>

                <template v-if="activeView == 'Editor'">

                    <!-- Screens -->      
                    <Row :gutter="20">

                        <Col :span="24">

                            <!-- Heading -->
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

                    <!-- Live Mode Settings -->      
                    <Row :gutter="20">

                        <Col :span="24">
                        
                            <Divider orientation="left" class="mt-2 mb-2"></Divider>

                            <!-- Live Mode Switch -->  
                            <Poptip trigger="hover" width="350" placement="top-start" word-wrap
                                    content="Activate live mode">
                                
                                <Icon type="ios-flash-outline" :size="20" />
                                <span class="font-weight-bold text-dark">Live Mode: </span>
                                <i-switch 
                                    size="small" 
                                    class="ml-1"
                                    :disabled="false"
                                    true-color="#13ce66" 
                                    false-color="#ff4949"
                                    :value="localUssdCreator.live_mode" 
                                    @on-change="localUssdCreator.live_mode = $event">
                                </i-switch>
                            </Poptip>
                        
                            <Divider orientation="left" class="mt-2 mb-2"></Divider>

                        </Col>

                    </Row>

                    <!-- Components -->      
                    <Row :gutter="20">

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

                </template>

                <template v-else>

                    <Menu :active-name="activeSimulatorLink" theme="light" class="w-100">

                        <MenuItem name="debugger">
                            <Icon type="ios-bug-outline" :size="20"/>
                            <span>Debugger</span>
                        </MenuItem>
                        
                        <MenuItem name="subscriptions">
                            <Icon type="ios-contact-outline" :size="20"/>
                            <span>Subscriber</span>
                        </MenuItem>
                        
                        <MenuItem name="settings">
                            <Icon type="ios-settings-outline" :size="20"/>
                            <span>Settings</span>
                        </MenuItem>

                    </Menu>

                </template>

            </Card>

        </Col>
        
        <Col :span="17">

            <Row>

                <!-- Screen Editor -->
                <Col v-show="activeView == 'Editor'" :span="20">
                    
                    <Card>

                        <!-- Screen Settings -->      
                        <Row v-if="activeScreen" :gutter="20" class="mb-3">

                            <Col :span="24">

                                <Divider orientation="left">
                                    <span class="text-primary">Screen Settings</span>
                                </Divider>

                            </Col>

                            <Col :span="14">

                                <!-- Screen Name Input -->
                                <Input 
                                    class="w-100" :max="40"
                                    placeholder="Enter the screen name"
                                    v-model="activeScreen.title" type="text"
                                    @on-focus="storeCurrentActiveScreenTitle()"
                                    @on-blur="checkIfValidScreenTitle()">

                                    <span slot="prepend" class="font-weight-bold text-dark">Name</span>

                                </Input>

                            </Col>

                            <Col :span="10">

                                <!-- Enable / Disable First Display Screen -->
                                <Checkbox 
                                    v-model="activeScreen.first_display_screen"
                                    :disabled="activeScreen.first_display_screen" class="mt-2"
                                    @on-change="handleSelectedFirstDisplayScreen(activeScreen, $event)">
                                    First Display Screen
                                </Checkbox>

                            </Col>

                        </Row>

                        <Row v-if="activeScreen" :gutter="20">

                            <Col :span="24">
                            
                                <Poptip trigger="hover" width="350" placement="right" word-wrap class="mb-4"
                                        content="Use API's to Create, Get, Update or Delete resources stored in an external system. You can also use API's to send Emails, SMS's and process Subcriptions">
                                    
                                    <Icon type="ios-planet-outline" class="border rounded-circle p-1" :size="20" />
                                    <span class="font-weight-bold text-dark">Use APIs: </span>
                                    <i-switch 
                                        size="small" 
                                        class="ml-1"
                                        :disabled="false"
                                        true-color="#13ce66" 
                                        false-color="#ff4949"
                                        :value="activeScreen.use_apis" 
                                        @on-change="activeScreen.use_apis = $event">
                                    </i-switch>
                                </Poptip>

                            </Col>

                            <!-- Settings For Screen Not Using API's --> 
                            <Col v-if="activeScreen.use_apis" :span="24">

                                <template v-if="activeScreen">

                                    <el-tabs value="general">
                                            
                                        <!-- General Settings -->
                                        <el-tab-pane label="General" name="general" class="pt-2">

                                            <div>
                                                <!-- Heading -->
                                                <span class="d-block text-dark float-left">
                                                    <span class="font-weight-bold">Success </span>(Default Message)
                                                </span>

                                                <!-- Default API Error -->
                                                <el-input type="text" v-model="activeScreen.api.general.default_success_message" size="small" class="w-100 mb-3"></el-input>
                                            </div>

                                            <div>
                                                <!-- Heading -->
                                                <span class="d-block text-dark float-left">
                                                    <span class="font-weight-bold">Error </span>(Default Message)
                                                </span>

                                                <!-- Default API Error -->
                                                <el-input type="text" v-model="activeScreen.api.general.default_error_message" size="small" class="w-100 mb-3"></el-input>
                                            </div>

                                            <div>
                                                <!-- Heading -->
                                                <span class="d-block text-dark float-left">
                                                    <span class="font-weight-bold">No Results </span>(Default Message)
                                                </span>

                                                <!-- Default No Results Message -->
                                                <el-input type="text" v-model="activeScreen.api.general.no_results_message" size="small" class="w-100 mb-3"></el-input>
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
                <Col v-show="activeView == 'Simulator'" :span="24">

                    <Row :gutter="10">

                        <Col :span="12">

                            <Card>

                                <Divider orientation="left">
                                    <span class="text-primary">Debugger</span>
                                </Divider>
                    
                                <!-- Loader -->
                                <Loader v-if="ussdSimulatorLoading" :loading="true" type="text" class="text-left">USSD Code running</Loader>

                                <template v-if="ussdSimulatorResponse && !ussdSimulatorLoading">

                                    <div class="d-flex">
                                        <span class="font-weight-bold text-dark mt-1 mr-2">Show:</span>
                                        <Select v-model="selectedLogType" filterable placeholder="Filter logs" class="mb-4">

                                            <Option 
                                                v-for="(log, key) in logTypes"
                                                :key="key" :value="log" :label="log">
                                            </Option>

                                        </Select>
                                    </div>

                                    <div class="bg-grey-light border p-2">
                                        <span v-if="['All', 'Info'].includes(selectedLogType)" class="mr-2">
                                            {{ ussdSimulatorInfoLogsTotal }} Info
                                        </span>
                                        <span v-if="['All', 'Warnings'].includes(selectedLogType)" class="mr-2">
                                            {{ ussdSimulatorWarningLogsTotal }}
                                            {{ ussdSimulatorWarningLogsTotal == 1 ? ' Warning' : ' Warnings' }}
                                        </span>
                                        <span v-if="['All', 'Errors'].includes(selectedLogType)" class="mr-2">
                                            {{ ussdSimulatorErrorLogsTotal }}
                                            {{ ussdSimulatorErrorLogsTotal == 1 ? ' Error' : ' Errors' }}
                                        </span>
                                    </div>

                                    <Timeline style="max-height:200px; overflow-y:auto;" class="py-3 pl-1">

                                        <TimelineItem v-for="(log, index) in selectedLogsToDisplay" :key="index"
                                            :color="getLogDotColor(log.type)">

                                            <!-- Show bug icon on error log -->
                                            <Icon v-if="log.type == 'error'" type="ios-bug-outline" slot="dot" :size="20" />

                                            <span 
                                                v-html="log.description"
                                                :class="log.type == 'error' ? 'text-danger' : ''">
                                            </span>

                                        </TimelineItem>

                                    </Timeline>

                                </template>

                                <!-- No simulator response -->
                                <Alert v-if="!ussdSimulatorResponse && !ussdSimulatorLoading" type="info" show-icon>
                                    Run the simulator to test your application 
                                </Alert>
                            </Card>

                        </Col>

                        <Col :span="12">
                    
                            <ussdSimulator 
                                postURL="/api/ussd/creator"
                                :showCreatorSimulator="true"
                                :ussdInterface="localUssdCreator"
                                @loading="ussdSimulatorLoading = $event"
                                @response="ussdSimulatorResponse = $event">
                            </ussdSimulator>
                            
                        </Col>

                    </Row>

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
                ussdSimulatorLoading: false,
                ussdSimulatorResponse: null,
                activeSimulatorLink: 'debugger',
                currentActiveScreenTitle: null,
                localUssdCreator: this.ussdCreator,
                selectedLogType: 'All',
                logTypes: ['All', 'Info', 'Warnings', 'Errors'],
                screenTree: (this.ussdCreator || {}).metadata || [],
            }
        }, 
        computed: {
            ussdSimulatorInfoLogs(){
                return ((this.ussdSimulatorResponse || {}).logs || []).filter( (log) => { 
                    if(log.type == 'info'){
                        return log;
                    }
                }) || [];
            },
            ussdSimulatorInfoLogsTotal(){
                return this.ussdSimulatorInfoLogs.length;
            },
            ussdSimulatorWarningLogs(){
                return ((this.ussdSimulatorResponse || {}).logs || []).filter( (log) => { 
                    if(log.type == 'warning'){
                        return log;
                    }
                }) || [];
            },
            ussdSimulatorWarningLogsTotal(){
                return this.ussdSimulatorWarningLogs.length;
            },
            ussdSimulatorErrorLogs(){
                return ((this.ussdSimulatorResponse || {}).logs || []).filter( (log) => { 
                    if(log.type == 'error'){
                        return log;
                    }
                }) || [];
            },
            ussdSimulatorErrorLogsTotal(){
                return this.ussdSimulatorErrorLogs.length;
            },
            selectedLogsToDisplay(){

                if(this.selectedLogType == 'All'){

                    var type = ['info', 'warning', 'error'];

                }else if(this.selectedLogType == 'Info'){

                    var type = ['info'];

                }else if(this.selectedLogType == 'Warnings'){

                    var type = ['warning'];

                }else if(this.selectedLogType == 'Errors'){

                    var type = ['error'];

                }

                return ((this.ussdSimulatorResponse || {}).logs || []).filter( (log) => { 
                    if( type.includes( log.type ) ){
                        return log;
                    }
                });
            }
        },
        methods: {
            getLogDotColor(type){
                if( type == 'info' ){
                    return 'green';
                }else if( type == 'warning' ){
                    return '#ffa300';
                }else if( type == 'error' ){
                    return 'red';
                }else{
                    return '#909090';
                }
            },
            addScreen(){

                //  Generate the screen title
                var screenTitle = 'Screen ' + (this.screenTree.length + 1),

                /** Determine whether this must be the first display screen by default.
                 *  Generally if we don't already have any screen assigned as the first
                 *  display screen then make this screen the first display screen by
                 *  default.
                 */
                firstDisplayScreen = !this.screenTree.filter( (screen) => { 
                    return screen.first_display_screen == true;
                }).length ? true : false,

                //  Build the screen template
                screenTemplate = { 
                    title: screenTitle, 
                    use_apis: false, 
                    first_display_screen: firstDisplayScreen,
                    content: this.getContentTemplate(),
                    api: this.getApiTemplate()
                };

                //  Add the screen to the screen tree
                this.screenTree.push( screenTemplate );

                //  Set the new screen as the current active screen
                this.activeScreen = this.screenTree[ this.screenTree.length - 1 ];

            },
            addApiRequest(screen){

                //  Build the template
                var template = {
                        url: 'http://oqcloud.local/api/test/items', // 'https://domain.com/api/items',
                        name: 'Get Items',
                        method: 'get',
                        trigger: 'on-enter',
                        query_params: [{ key: '', value: ''}],
                        form_data: [{ key: '', value: ''}],
                        headers: [{ key: '', value: ''}],
                        response_data: [
                            {
                                status: 200,
                                attributes: [
                                    {
                                        name: 'get_items_response',
                                        value: 'response'
                                    },
                                    {
                                        name: 'items',
                                        value: 'response.items'
                                    }
                                ],
                                content: this.getContentTemplate()
                            }
                        ]
                    };

                //  Add the api request data to the screen api request dataset
                screen.api.requests.push( template );

            },
            getContentTemplate(){
                
                return  {

                    description: {
                        text: '',
                        code_editor_text: '',
                        code_editor_mode: false
                    },
                    next_screen: null,
                    reference_name: null,

                    action: {
                        selected_action_type: 'no_action',
                        input_value: {
                            selected_type: 'single_value_input',
                            single_value_input: {
                                reference_name: null,
                                next_screen: null
                            },
                            multi_value_input: {
                                separator: 'spaces',
                                reference_names: ['first_name', 'last_name'],
                                next_screen: null
                            }
                        },
                        select_option: {
                            selected_type: 'static_options',
                            static_options: {
                                options: [
                                    {
                                        name: '',
                                        value: '',
                                        input: '1',
                                        next_screen: null
                                    }
                                ],
                                reference_name: null,
                                no_results_message: 'No options available',
                                incorrect_option_selected_message: 'You selected an incorrect option. Please try again'
                            }, 
                            dynamic_options: {
                                group_reference: '{{ items }}', 
                                template_reference_name: 'item',
                                template_display_name: '{{ item.name }} - {{ item.price }}',
                                template_value: '{{ item.id }}',
                                reference_name: 'selected_item',
                                next_screen: null,
                                no_results_message: 'No items found',
                                incorrect_option_selected_message: 'You selected an incorrect option. Please try again',
                            },
                            code_editor_options: {
                                code_editor_text: null,
                                reference_name: 'selected_item',
                                no_results_message: 'No items found',
                                incorrect_option_selected_message: 'You selected an incorrect option. Please try again'
                            }
                        }
                    },


                    /*
                    select_reply: {
                        type: false, 
                        dynamic_options: {
                            group_reference: '{{ items }}', 
                            template_value: '{{ item.id }}',
                            template_reference_name: 'selected_item',
                            template_display_name: '{{ item.name }} - {{ item.price }}',
                            no_results_message: 'No items found'
                        },
                        static_options: [
                            {
                                name: '', 
                                next_screen: ''
                            }
                        ]
                    },
                    */

                    //  Validation Rules For The Input Value
                    validation:{
                        rules: []
                    },
                    //  Formatting Rules For The Input Value
                    formatting: {
                        reference_name: null,
                        rules: []
                    },
                    local_storage: [
                        {
                            type: 'single_storage', //  single_storage, multi_storage
                            global_reference_name: 'profile',
                            data: [
                                {
                                    local_reference_name: 'first_name', //  key
                                    value: '{{ first_name }}',          //  value
                                },
                                {
                                    local_reference_name: 'last_name',  //  key
                                    value: '{{ last_name }}',           //  value
                                },
                                {
                                    local_reference_name: 'age',  //  key
                                    value: '{{ age }}',           //  value
                                }
                            ]
                        }
                    ]
                };
            },
            getApiTemplate(){
                
                return  {

                    general: {
                        no_results_message: 'No results found',
                        default_success_message: 'Completed successfully',
                        default_error_message: 'Sorry, we are experiencing technical difficulties'
                    },
                    requests: []

                };
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
            storeCurrentActiveScreenTitle(){
                this.currentActiveScreenTitle = this.activeScreen.title;
            },
            checkIfValidScreenTitle(){
                
                var duplicateName = (this.screenTree.filter( (screen) => { 
                    return screen.title == this.activeScreen.title;
                }).length >= 2) ? true : false;

                if( duplicateName ){

                    this.$Notice.warning({
                        desc: 'Avoid using names of other screens'
                    });

                    this.activeScreen.title = this.currentActiveScreenTitle;

                }else if( this.activeScreen.title == '' || this.activeScreen.title == null ){

                    this.$Notice.warning({
                        desc: 'The screen name cannot be empty'
                    });

                    this.activeScreen.title = this.currentActiveScreenTitle;
                    
                }
            },
            handleSelectedFirstDisplayScreen(currentScreen, event){
                
                //  Foreach screen
                for(var x = 0; x < this.screenTree.length; x++){

                    //  Disable the first display screen attribute for each screen except the current screen
                    if( this.screenTree[x].title != currentScreen.title){

                        /** Disable first_display_screen attribute so that we only have the current screen as
                         *  the only screen with a true value
                         */
                        this.screenTree[x].first_display_screen = false;

                    }else{

                        //  Make sure that the first display screen attribute for the current screen enabled
                        this.screenTree[x].first_display_screen = true;

                    }
                }
            },
            handleDuplicateScreenMenu(index){

                //  Switch to the editor view
                this.showEditor();

                //  Duplicate the screen
                var duplicateScreen = _.cloneDeep( this.screenTree[index] );

                //  Create the duplicate screen name
                var duplicateName = 'Duplicate Screen - #' + (this.screenTree.length + 1);

                //  Set the duplicate screen name to the new screen
                duplicateScreen.title = duplicateName;

                //  Turn off the first display screen attribute
                duplicateScreen.first_display_screen = false;

                //  Add the duplicate screen to the rest of the other screens
                this.screenTree.push(duplicateScreen);

                //  Set the duplicate screen as the current active screen
                this.activeScreen = duplicateScreen;
            },
            handleDeleteScreenMenu(index){

                //  Remove screen from list
                this.screenTree.splice(index, 1);

                //  Check if we have a screen that has been set as the first display screen
                var firstDisplayScreenExists = this.screenTree.filter( (screen) => { 
                    return screen.first_display_screen == true;
                }).length ? true : false;

                //  If we don't have a screen that has been set as the first display screen
                if( !firstDisplayScreenExists ){ 

                    //  If we have any screens
                    if( this.screenTree.length ){

                        //  Set the first screen as the first display screen
                        this.screenTree[0].first_display_screen = true;

                    }

                }

                //  If we have any screens
                if( this.screenTree.length ){

                    //  Set the first screen as the default active screen
                    this.activeScreen = this.screenTree[0]

                }
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

                            this.$Notice.success({
                                desc: 'Saved successfully'
                            });

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