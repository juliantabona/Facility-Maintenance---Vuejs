<template>

    <!-- Repeat Screen Events -->
    <div>

        <!-- Before Repeat Events -->
        <div class="mb-2">

            <span class="d-block font-weight-bold text-dark">Before Repeat</span>

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">
                
                <eventsManager :events="localScreen.type.repeat.events.before_repeat"></eventsManager>

            </div>

        </div>

        <!-- After Repeat Events -->
        <div class="mb-2">

            <span class="d-block font-weight-bold text-dark">After Repeat</span>

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">
                
                <eventsManager :events="localScreen.type.repeat.events.after_repeat"></eventsManager>

            </div>

        </div>

    </div>
    
</template>

<script>

    //  Get the events manager
    import eventsManager from './../events/main.vue';

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            }
        },
        components: {
            eventsManager
        },
        data(){
            return {
                localScreen: this.screen
            }
        }
    };
  
</script>