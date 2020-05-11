<template>

    <!-- Enable / Disable First Display -->
    <Checkbox 
        v-model="localDisplay.first_display"
        :disabled="localDisplay.first_display" class="mt-2"
        @on-change="handleSelectedFirstDisplay(localDisplay, $event)">
        First Display
    </Checkbox>
    
</template>

<script>

    export default {
        props: { 
            display:{
                type: Object,
                default: () => {}
            },
            displays: {
                type: Array,
                default: []
            }
        },
        data(){
            return {
                localDisplay: this.display
            }
        }, 
        methods: {
            
            handleSelectedFirstDisplay(currentDisplay, event){
                
                //  Foreach scrdisplayeen
                for(var x = 0; x < this.displays.length; x++){

                    //  Disable the first display attribute for each display except the current display
                    if( this.displays[x].name != currentDisplay.name){

                        /** Disable first_display attribute so that we only have the current display as
                         *  the only display with a true value
                         */
                        this.displays[x].first_display = false;

                    }else{

                        //  Make sure that the first display attribute for the current display enabled
                        this.displays[x].first_display = true;

                    }
                }
            }
        }
    };
  
</script>