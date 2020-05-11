<template>

    <Row :gutter="12">

        <!-- Ussd Service Column 1 -->
        <Col :span="7">

            <!-- Display the Ussd Service menu (Editor/Simulator menu) -->
            <ussdServiceSideMenu
                @changedMainMenu="handleChangedMainMenu($event)"
                @duplicatedScreen="handleDuplicatedScreenMenu($event)"
                @selectedScreen="handleSelectedScreenMenu($event)"
                @removedScreen="handleDeletedScreenMenu($event)"
                @addScreen="handleAddScreen()"
                :activeMenu="activeMenu"
                :builder="builder"
                :screen="screen">
            </ussdServiceSideMenu>  
            
            <div class="mt-2 clearfix">

                <!-- Loader -->
                <Loader v-if="isSaving" :loading="true" type="text" class="text-left float-right mr-3">Saving...</Loader>

                <!-- Save Button -->
                <basicButton v-if="!isSaving" class="float-right" customClass="mt-2 d-block" 
                    type="success" size="large" :disabled="!builderHasChanged"
                    :ripple="builderHasChanged" @click.native="handleSave()">
                    <span>Save Changes</span>
                </basicButton>

            </div>

        </Col>

        <!-- Ussd Service Column 2 -->
        <Col :span="17">
        
            <!-- Display the screen editor if active -->
            <screenEditor v-show="activeMenu == 'Editor'" :key="screenEditorRenderKey"
                :builder="builder"
                :screen="screen">
            </screenEditor>

            <!-- Display the simulator if active -->
            <simulator v-show="activeMenu == 'Simulator'" :ussdService="ussdService"></simulator>

        </Col>

    </Row>
    
</template>

<script>

    //  Buttons
    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    //  Loaders
    import Loader from './../../../../components/_common/loaders/Loader.vue';  

    //  Get the side menu
    import ussdServiceSideMenu from './side-menu/main.vue';

    //  Get the screen editor
    import screenEditor from './screen-editor/main.vue';
    
    //  Get the simulator
    import simulator from './simulator/main.vue';

    export default {
        props: { 
            ussdService: {
                type: Object,
                default: null
            }
        },
        components: { 
            basicButton, Loader, ussdServiceSideMenu, screenEditor, simulator
        },
        data(){
            return {
                builder: this.ussdService.builder,
                builderBeforeChange: null,
                screenEditorRenderKey: 1,
                activeMenu: 'Editor',
                isSaving: false,
                screen: null,
            }
        }, 
        computed: {

            builderHasChanged(){
                var now = _.cloneDeep(this.builder);
                var before = (this.builderBeforeChange);

                var isNotEqual = !_.isEqual(now, before);

                return isNotEqual;
            },

        },
        methods: {
            getFirstScreenToShow(){
                
                //  If we have screens
                if( this.builder.screens.length ){

                    //  Get the screen marked as the first screen
                    var markedScreens = this.builder.screens.filter( (screen) => { 
                        return screen.first_display_screen == true;
                    });

                    if( markedScreens.length ){
                     
                        //  Get the first marked screen
                        var firstDisplayScreen = markedScreens[0];

                        //  Return the first display screen
                        return firstDisplayScreen;

                    }

                    //  Otherwise get the first listed screen
                    return this.builder.screens[0];

                }

                return null;

            },
            storeOriginalBuilderData(){
                //  Store the original Ussd Service data before any changes
                this.builderBeforeChange = _.cloneDeep(this.builder);
            },
            handleChangedMainMenu(activeMenu){
                this.activeMenu = activeMenu;
            },
            handleSelectedScreenMenu(index){

                //  Switch to the editor view
                this.showEditor();

                //  Set the selected screen as the active screen
                this.screen = this.builder.screens[index];

                //  Re-render the screen editor
                this.forceRenderOnScreenEditor();
                
            },
            forceRenderOnScreenEditor(){

                //  Increment the screen editor render key to force rerender
                this.screenEditorRenderKey = ++this.screenEditorRenderKey; 

            },
            handleDuplicatedScreenMenu(index){

                //  Switch to the editor view
                this.showEditor();

                var totalScreens = this.builder.screens.length;

                //  Duplicate the screen
                var duplicateScreen = _.cloneDeep( this.builder.screens[index] );

                //  Create the duplicate screen name
                var duplicateName = 'Duplicate Screen - #' + (totalScreens + 1);

                //  Set the duplicate screen name to the new screen
                duplicateScreen.name = duplicateName;

                //  Turn off the first display screen attribute
                duplicateScreen.first_display_screen = false;

                //  Add the duplicate screen to the rest of the other screens
                this.builder.screens.push(duplicateScreen);

                var duplicateScreenIndex = totalScreens;

                //  Set the duplicate screen as the current active screen
                this.handleSelectedScreenMenu(duplicateScreenIndex);

            },
            handleDeletedScreenMenu(index){

                var deletingActiveScreen = false;

                //  If the screen being deleted is the current active screen
                if( (this.screen || {}).name == this.builder.screens[index].name ){

                    deletingActiveScreen = true;

                }

                //  Remove screen from list
                this.builder.screens.splice(index, 1);

                //  Check if we have a screen that has been set as the first display screen
                var firstDisplayScreenExists = this.builder.screens.filter( (screen) => { 
                    return screen.first_display_screen == true;
                }).length ? true : false;

                //  If we don't have a screen that has been set as the first display screen
                if( !firstDisplayScreenExists ){ 

                    //  If we have any screens
                    if( this.builder.screens.length ){

                        //  Set the first screen as the first display screen
                        this.builder.screens[0].first_display_screen = true;

                    }

                }

                //  If we have any screens
                if( this.builder.screens.length ){
                    
                    //  If we ar deeleting the current active screen
                    if( deletingActiveScreen ){

                        //  Set the first screen as the default active screen
                        this.handleSelectedScreenMenu(0);

                    }

                }else{

                    this.screen = null;

                }
            },
            showEditor(){

                //  Switch to the editor view
                this.activeMenu = 'Editor';

            },
            handleAddScreen(){

                //  Generate the screen name
                var screenName = 'Screen ' + ((this.builder.screens || []).length + 1);

                /** Determine whether this must be the first display screen by default.
                 *  Generally if we don't already have any screen assigned as the first
                 *  display screen, then we make this screen the first display screen by
                 *  default.
                 */
                
                var firstDisplayScreen = !((this.builder.screens || []).filter( (screen) => { 
                    return screen.first_display_screen == true;
                }).length ? true : false);

                //  Build the screen template
                var screenTemplate = { 
                    name: screenName, 
                    type: {
                        selected_type: 'default',   //  default, repeat
                        repeat: {
                            selected_type: 'repeat_on_number',  //  repeat_on_number, repeat_on_items, custom_repeat
                            repeat_on_number: {
                                value: '3',
                                loop_index_reference_name: 'loop_index',
                                loop_number_reference_name: 'loop_number',
                                is_first_loop_reference_name: 'is_first_loop',
                                is_last_loop_reference_name: 'is_last_loop',
                                on_no_loop: {
                                    selected_type: 'do_nothing',            //  do_nothing, link
                                    link:{
                                        type: 'screen',                     //  screen, display  
                                        name: ''
                                    }
                                },
                                after_last_loop: {
                                    selected_type: 'do_nothing',            //  do_nothing, link
                                    link:{
                                        type: 'screen',                     //  screen, display  
                                        name: ''
                                    }
                                }
                            },
                            repeat_on_items: {
                                group_reference: '{{ items }}', 
                                item_reference_name: 'item',
                                total_loops_reference_name: 'total_items',
                                loop_index_reference_name: 'item_index',
                                loop_number_reference_name: 'item_number',
                                is_first_loop_reference_name: 'is_first_item',
                                is_last_loop_reference_name: 'is_last_item',
                                on_no_loop: {
                                    selected_type: 'do_nothing',            //  do_nothing, link
                                    link:{
                                        type: 'screen',                     //  screen, display  
                                        name: ''
                                    }
                                },
                                after_last_loop: {
                                    selected_type: 'do_nothing',            //  do_nothing, link
                                    link:{
                                        type: 'screen',                     //  screen, display  
                                        name: ''
                                    }
                                }
                            },
                            events: {
                                before_repeat: [],
                                after_repeat: []
                            }
                        }
                    },
                    first_display_screen: firstDisplayScreen,
                    displays: [],
                    markers: []
                };

                //  Add the screen to the screen tree
                this.builder.screens.push( screenTemplate );

                //  Set the new screen as the current active screen
                this.handleSelectedScreenMenu( this.builder.screens.length - 1 );

            },
            handleSave(){

                const self = this;

                //  Start loader
                this.isSaving = true;

                //  Get the Ussd Service
                var updateData = this.ussdService;

                //  Update the Ussd Service Builder
                updateData['builder'] = this.builder;

                if(self.ussdService['_links'].self.href){

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('put', self.ussdService['_links'].self.href, updateData)
                        .then(({data}) => {

                            self.$Notice.success({
                                desc: 'Saved successfully'
                            });
                            
                            self.storeOriginalBuilderData();

                            //  Stop loader
                            self.isSaving = false;
                            
                            console.log(data);
                            
                        })         
                        .catch(response => { 
                            
                            //  Stop loader
                            self.isSaving = false;
                        
                            console.log(response);  
            
                        });

                }

            }
        },
        created(){

            this.storeOriginalBuilderData();

            this.screen = this.getFirstScreenToShow();

        }
    };
  
</script>