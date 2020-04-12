<style scoped>

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        width: 85% !important;
        white-space: nowrap !important;
        display: block;
    }

    /*  Screen Toolbox */

    .display-option >>> .display-toolbox{
        z-index: 1;
        position: relative;
        margin: -2px 0 0 0;
        background: #fff;
    }

    .display-option >>> .display-toolbox,
    .display-option >>> .display-toolbox .hidable{
        opacity:0;
    }

    .display-option:hover >>> .display-toolbox,
    .display-option:hover >>> .display-toolbox .hidable{
        opacity:1;
    }

    .display-option >>> .display-toolbox .display-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .display-option >>> .display-toolbox .display-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .display-option >>> .ivu-card-body{
        padding:0 !important;
    }

    .display-details{
        padding:16px;
    }

</style>

<template>

    <Card v-if="localDisplay" class="display-option mb-2">

        <!-- Display Name -->
        <div slot="title">

            <template v-if="showContent">

                <!-- Display Name Input  -->
                <el-input type="text" v-model="localDisplay.name" size="small" class="w-50"></el-input>

                <!-- First display checkbox (Marks the display as the first display) -->  
                <firstDisplayCheckbox :display="localDisplay" :displays="screen.displays"></firstDisplayCheckbox>

            </template>

            <!-- Display Name Label  -->
            <span v-else class="display-name font-weight-bold cut-text">
                {{ getDisplayNumber ? getDisplayNumber +'. ' : '' }}
                {{ localDisplay.name }}
            </span>
            
        </div>

        <!-- Display Toolbar (Edit, Move, Delete Buttons) -->
        <div slot="extra">

            <div class="display-toolbox float-right d-block">

                <!-- Remove Display Button  -->
                <Poptip confirm title="Are you sure you want to remove this display?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemoveDisplay(index)"
                        placement="left">
                    <Icon type="ios-trash-outline" class="display-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Edit Display Button  -->
                <Icon type="ios-create-outline" class="display-icon hidable mr-2" size="20" @click="showContent = !showContent" />

                <!-- Copy Screen Button  -->
                <Icon type="ios-copy-outline" class="display-icon hidable mr-2" size="20" @click="handleDuplicateDisplay(index)"/>

                <!-- Move Display Button  -->
                <Icon type="ios-move" class="display-icon display-dragger-handle hidable mr-2" size="20" />
            
            </div>

            <!-- First Display Pointer -->
            <Icon v-if="localDisplay.first_display" type="ios-pin-outline" size="20"  
                class="text-success font-weight-bold" :style="{ position: 'absolute', top: 0, right: 0 }"/>

        </div>    

        <!-- Display Content -->
        <Row v-if="showContent" class="display-details">

            <Col :span="24">
                                    
                <div class="clearfix mb-3">

                    <!-- Navigation Tabs -->
                    <Tabs v-model="activeNavTab" type="card" :animated="false">

                        <TabPane v-for="(navTab, key) in navTabs" 
                                :key="key" :label="navTab" :name="navTab">
                        </TabPane>

                    </Tabs>

                    <!-- Display Instruction -->
                    <displayInstruction v-show="activeNavTab == 'Instruction'" :display="localDisplay"></displayInstruction>

                    <!-- Display Action -->
                    <displayAction v-show="activeNavTab == 'Action'" :display="localDisplay" :screen="screen" :screens="screens"></displayAction>

                    <!-- Display Events -->
                    <displayEvents v-show="activeNavTab == 'Events'" :display="localDisplay"></displayEvents>
                        
                    <!-- Display navigation -->
                    <displayNavigation v-show="activeNavTab == 'Navigation'" :display="localDisplay"></displayNavigation>
                        
                    <!-- Display pagination -->
                    <displayPagination v-show="activeNavTab == 'Pagination'" :display="localDisplay"></displayPagination>
                        
                    <!-- Display Settings -->
                    <displaySettings v-show="activeNavTab == 'Settings'" :display="localDisplay"></displaySettings>

                </div>
            
            </Col>

        </Row>

    </Card>

</template>

<script>

    //  Get the first display checkbox
    import firstDisplayCheckbox from './firstDisplayCheckbox.vue';
    
    //  Get the display instruction
    import displayInstruction from './instruction/main.vue';
    
    //  Get the display action
    import displayAction from './action/main.vue';
    
    //  Get the display events
    import displayEvents from './events/main.vue';
    
    //  Get the display navigation
    import displayNavigation from './navigation/main.vue';
    
    //  Get the display pagination
    import displayPagination from './pagination/main.vue';

    //  Get the display settings
    import displaySettings from './settings/main.vue';

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            display: {
                type: Object,
                default:() => {}
            },
            screen: {
                type: Object,
                default:() => {}
            },
            screens: {
                type: Array,
                default: () => []
            }
        }, 
        components: {
            firstDisplayCheckbox, displayInstruction, displayAction, displayEvents, 
            displayPagination, displayNavigation, displaySettings
        },
        data(){
            return {
                showContent: false,
                localDisplay: this.display,
                activeNavTab: 'Instruction'
            }
        },
        watch: {
            //  Keep track of changes on the display
            display: {

                handler: function (val, oldVal) {
                    
                    //  Update the current display
                    this.localDisplay = val;

                },
                deep: true

            },
        },
        computed: {
            getDisplayNumber(){
                /**
                 *  Returns the display number. We use this as we list the displays.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            },
            navTabs(){
                var tabs = ['Instruction', 'Action', 'Events'];

                //  If the screen type is "repeat" then add the "Navigation" tab
                if( this.screen.type.selected_type == 'repeat' ){
                    tabs.push('Navigation');
                }
                
                tabs.push('Pagination');

                tabs.push('Settings');

                return tabs;
            }
        },
        methods: {
            handleDuplicateDisplay(index) {

                //  Duplicate the display
                var duplicateDisplay = _.cloneDeep( this.screen.displays[index] );

                //  Create the duplicate display name
                var duplicateName = 'Display - #' + (this.screen.displays.length + 1);

                //  Disable the first display indicator
                duplicateDisplay.first_display = false;

                //  Set the duplicate display name
                duplicateDisplay.name = duplicateName;

                //  Add the duplicate display to the rest of the other displays
                this.screen.displays.push(duplicateDisplay);

            },
            handleRemoveDisplay(index) {
                this.screen.displays.splice(index, 1);

            }
        }
    }

</script>