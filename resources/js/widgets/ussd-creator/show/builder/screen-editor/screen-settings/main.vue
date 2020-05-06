<template>

    <!-- Screen Settings -->
    <div>

        <Tabs v-model="activeNavTab" type="card" style="overflow: visible;" :animated="false">

            <!-- Screen Settings Navigation Tabs -->
            <TabPane v-for="(currentTabName, key) in navTabs" :key="key" :label="currentTabName" :name="currentTabName"></TabPane>

        </Tabs>
            
        <!-- Screen displays -->
        <displayEditor v-show="activeNavTab == 'Screen Displays'" :screen="localScreen" :screens="screens"></displayEditor>

        <!-- Advanced Screen Settings -->
        <advancedScreenSettings v-show="activeNavTab == 'Advanced'" :screen="localScreen"></advancedScreenSettings>

        <!-- Repeat Settings -->
        <repeatScreenSettings v-show="activeNavTab == 'Repeat Settings'" :screen="localScreen" :screens="screens"></repeatScreenSettings>

        <!-- Repeat Events -->
        <repeatScreenEvents v-show="activeNavTab == 'Repeat Events'" :screen="localScreen"></repeatScreenEvents>

    </div>
    
</template>

<script>

    //  Get the general screen settings
    import advancedScreenSettings from './advancedScreenSettings.vue';

    //  Get the repeat screen settings
    import repeatScreenSettings from './repeatScreenSettings.vue';

    //  Get the repeat screen events
    import repeatScreenEvents from './repeatScreenEvents.vue';

    //  Get the display editor
    import displayEditor from './display-editor/main.vue';

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            },
            screens: {
                type: Array,
                default: []
            }
        },
        components: {
            advancedScreenSettings, repeatScreenSettings, repeatScreenEvents, displayEditor
        },
        data(){
            return {
                localScreen: this.screen,
                activeNavTab: 'Screen Displays',
            }
        }, 
        computed: {
            navTabs(){
                var tabs = ['Screen Displays', 'Advanced'];

                //  If the screen type is "repeat" then add the "Repeat Events" tab
                if( this.localScreen.type.selected_type == 'repeat' ){
                    tabs.push('Repeat Events');
                    tabs.push('Repeat Settings');
                }

                return tabs;
            }
        },
        methods: {
            
        }
    };
  
</script>