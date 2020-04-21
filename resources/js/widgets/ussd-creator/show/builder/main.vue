<template>

    <Row :gutter="12">

        <!-- Creator Column 1 -->
        <Col :span="7">

            <!-- Display the creator menu (Editor/Simulator menu) -->
            <creatorSideMenu
                @changedMainMenu="handleChangedMainMenu($event)"
                @duplicatedScreen="handleDuplicatedScreenMenu($event)"
                @selectedScreen="handleSelectedScreenMenu($event)"
                @removedScreen="handleDeletedScreenMenu($event)"
                @addScreen="handleAddScreen()"
                :activeMenu="activeCreatorMenu"
                :screens="screens"
                :screen="screen">
            </creatorSideMenu>       
            
            <div class="mt-2 clearfix">

                <!-- Loader -->
                <Loader v-if="isSaving" :loading="true" type="text" class="text-left float-right mr-3">Saving...</Loader>

                <!-- Save Button -->
                <basicButton v-if="!isSaving" class="float-right" customClass="mt-2 d-block" 
                    type="success" size="large" :disabled="!creatorHasChanged"
                    :ripple="creatorHasChanged" @click.native="handleSave()">
                    <span>Save Changes</span>
                </basicButton>

            </div>

        </Col>

        <!-- Creator Column 2 -->
        <Col :span="17">
        
            <!-- Display the screen editor if active -->
            <screenEditor v-show="activeCreatorMenu == 'Editor'"
                :key="screenEditorRenderKey"
                :screens="screens"
                :screen="screen">
            </screenEditor>

            <!-- Display the simulator if active -->
            <simulator v-show="activeCreatorMenu == 'Simulator'" :ussdCreator="ussdCreator"></simulator>

        </Col>

    </Row>
    
</template>

<script>

    //  Buttons
    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    //  Loaders
    import Loader from './../../../../components/_common/loaders/Loader.vue';  
    
    //  Get the side menu
    import creatorSideMenu from './side-menu/main.vue';

    //  Get the screen editor
    import screenEditor from './screen-editor/main.vue';
    
    //  Get the simulator
    import simulator from './simulator/main.vue';

    export default {
        props: { 
            ussdCreator: {
                type: Object,
                default: null
            }
        },
        components: { 
            basicButton, Loader, creatorSideMenu, screenEditor, simulator
        },
        data(){
            return {
                localUssdCreator: this.ussdCreator,
                localUssdCreatorBeforeChange: null,
                activeCreatorMenu: 'Editor',
                screens: this.getScreens(),
                screenEditorRenderKey: 1,
                isSaving: false,
                screen: null,
            }
        }, 
        watch: {
            /*  Keep track of changes on the screens  */
            screens: {

                handler: function (val, oldVal) {

                    //  If we have screens update the metadata otherwise set to null
                    this.ussdCreator.metadata = (val.length != 0) ? val : null;

                },
                deep: true

            }
        },
        computed: {

            creatorHasChanged(){
                var now = _.cloneDeep(this.localUssdCreator);
                var before = (this.localUssdCreatorBeforeChange);

                var isNotEqual = !_.isEqual(now, before);

                return isNotEqual;
            },

        },
        methods: {
            getScreens(){
                
                return this.ussdCreator.metadata || [];

            },
            storeOriginalUssdCreatorData(){
                //  Store the original ussd creator data before any changes
                this.localUssdCreatorBeforeChange = _.cloneDeep(this.localUssdCreator);
            },
            handleChangedMainMenu(activeMenu){
                this.activeCreatorMenu = activeMenu;
            },
            handleSelectedScreenMenu(index){

                //  Switch to the editor view
                this.showEditor();

                //  Set the selected screen as the active screen
                this.screen = this.screens[index];

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

                //  Duplicate the screen
                var duplicateScreen = _.cloneDeep( this.screens[index] );

                //  Create the duplicate screen name
                var duplicateName = 'Duplicate Screen - #' + (this.screens.length + 1);

                //  Set the duplicate screen name to the new screen
                duplicateScreen.name = duplicateName;

                //  Turn off the first display screen attribute
                duplicateScreen.first_display_screen = false;

                //  Add the duplicate screen to the rest of the other screens
                this.screens.push(duplicateScreen);

                //  Set the duplicate screen as the current active screen
                this.screen = duplicateScreen;

            },
            handleDeletedScreenMenu(index){

                //  Remove screen from list
                this.screens.splice(index, 1);

                //  Check if we have a screen that has been set as the first display screen
                var firstDisplayScreenExists = this.screens.filter( (screen) => { 
                    return screen.first_display_screen == true;
                }).length ? true : false;

                //  If we don't have a screen that has been set as the first display screen
                if( !firstDisplayScreenExists ){ 

                    //  If we have any screens
                    if( this.screens.length ){

                        //  Set the first screen as the first display screen
                        this.screens[0].first_display_screen = true;

                    }

                }

                //  If we have any screens
                if( this.screens.length ){

                    //  Set the first screen as the default active screen
                    this.screen = this.screens[0];

                }else{

                    this.screen = null;

                }
            },
            showEditor(){

                //  Switch to the editor view
                this.activeCreatorMenu = 'Editor';

            },
            handleAddScreen(){

                //  Generate the screen name
                var screenName = 'Screen ' + ((this.screens || []).length + 1);

                /** Determine whether this must be the first display screen by default.
                 *  Generally if we don't already have any screen assigned as the first
                 *  display screen, then we make this screen the first display screen by
                 *  default.
                 */
                
                var firstDisplayScreen = !((this.screens || []).filter( (screen) => { 
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
                                value: '3'
                            },
                            repeat_on_items: {
                                group_reference: '{{ items }}', 
                                item_reference_name: 'item',
                                total_items_reference_name: 'total_items',
                                item_index_reference_name: 'item_index',
                                item_number_reference_name: 'item_number',
                                is_first_item_reference_name: 'is_first_item',
                                is_last_item_reference_name: 'is_last_item',
                                no_results_message: 'No items found',
                                next_screen: null
                            },
                            events: {
                                before_repeat: [],
                                after_repeat: []
                            }
                        }
                    },
                    first_display_screen: firstDisplayScreen,
                    displays: []
                };

                //  Add the screen to the screen tree
                this.screens.push( screenTemplate );

                //  Set the new screen as the current active screen
                this.handleSelectedScreenMenu( this.screens.length - 1 );

            },
            handleSave(){

                const self = this;

                //  Start loader
                this.isSaving = true;

                this.localUssdCreator.metadata = this.screens;

                var updateData = this.localUssdCreator;

                if(self.localUssdCreator['_links'].self.href){

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('put', self.localUssdCreator['_links'].self.href, updateData)
                        .then(({data}) => {

                            self.$Notice.success({
                                desc: 'Saved successfully'
                            });
                            
                            self.storeOriginalUssdCreatorData();

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

            this.storeOriginalUssdCreatorData();

        }
    };
  
</script>