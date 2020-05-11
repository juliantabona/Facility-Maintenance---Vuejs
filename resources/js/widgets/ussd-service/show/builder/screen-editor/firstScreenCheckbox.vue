<template>

    <!-- Enable / Disable First Display Screen -->
    <Checkbox 
        v-model="localScreen.first_display_screen"
        :disabled="localScreen.first_display_screen" class="mt-2"
        @on-change="handleSelectedFirstDisplayScreen(localScreen, $event)">
        First Screen
    </Checkbox>
    
</template>

<script>

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            },
            builder: {
                type: Object,
                default: () => {}
            }
        },
        data(){
            return {
                localScreen: this.screen
            }
        }, 
        methods: {
            
            handleSelectedFirstDisplayScreen(currentScreen, event){
                
                //  Foreach screen
                for(var x = 0; x < this.builder.screens.length; x++){

                    //  Disable the first display screen attribute for each screen except the current screen
                    if( this.builder.screens[x].name != currentScreen.name){

                        /** Disable first_display_screen attribute so that we only have the current screen as
                         *  the only screen with a true value
                         */
                        this.builder.screens[x].first_display_screen = false;

                    }else{

                        //  Make sure that the first display screen attribute for the current screen enabled
                        this.builder.screens[x].first_display_screen = true;

                    }
                }
            }
        }
    };
  
</script>