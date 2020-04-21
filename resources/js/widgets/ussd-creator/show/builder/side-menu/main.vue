<template>

    <!-- Display the creator menu -->
    <Card>

        <!-- Display menu options -->
        <div slot="title">

            <span v-for="(option, key) in menu" :key="key" @click="handleSelectedMenu( option )">
                
                <Button type="text" ghost>
                
                    <!-- Menu Icon -->
                    <Icon :type="option == 'Editor' ? 'ios-git-branch' : 'ios-phone-portrait'" :size="20"
                        :class="(localActiveMenu == option ? 'text-primary': 'text-dark') + ' mr-1'" />
                    
                    <!-- Menu Name -->
                    <span :class="(localActiveMenu == option ? 'text-primary font-weight-bold': 'text-dark')">
                        {{ option }}
                    </span>

                </Button>

            </span>

        </div>

        <!-- Display editor menu content if active -->
        <editorMenuContent v-if="localActiveMenu == 'Editor'"
            @duplicatedScreen="$emit('duplicatedScreen', $event)"
            @removedScreen="$emit('removedScreen', $event)"
            @selectedScreen="$emit('selectedScreen', $event)"
            @addScreen="$emit('addScreen')"
            :screens="screens"
            :screen="screen">
        </editorMenuContent>

        <!-- Display simulator menu content if active -->
        <simulatorMenuContent v-if="localActiveMenu == 'Simulator'"></simulatorMenuContent>

    </Card>
    
</template>

<script>

    /** Get the editor menu content
     * 
     *  This component is used to display the editor menu options
     *  e.g screens, settings, e.t.c
     */
    import editorMenuContent from './editor-menu-content/main.vue';
    
    /** Get the simulator menu content
     * 
     *  This component is used to display the simulator menu options
     *  e.g debugging, subscriber, e.t.c
     */
    import simulatorMenuContent from './simulator-menu-content/main.vue';

    export default {
        props: { 
            activeMenu: {
                type: String,
                default: 'Editor'
            },
            screen:{
                type: Object,
                default: () => {}
            },
            screens: {
                type: Array,
                default: []
            },
        },
        components: { 
            editorMenuContent, simulatorMenuContent
        },
        data(){
            return {
                menu: ['Editor', 'Simulator'],
                localActiveMenu: this.activeMenu
            }
        }, 
        computed: {

        },
        methods: {
            handleSelectedMenu( option ){

                //  Update to the selected menu option
                this.localActiveMenu = option;

                //  Emit the selected option e.g "Editor" or "Simulator"
                this.$emit('changedMainMenu', option);

            }
        }
    };
  
</script>