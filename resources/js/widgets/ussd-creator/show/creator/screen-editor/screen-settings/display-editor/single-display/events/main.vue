<template>

    <!-- Screen Display Events -->
    <div>

        <!-- Before Display Response Events -->
        <div class="mb-2">

            <span class="d-block font-weight-bold text-dark">Before (User Reply)</span>

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">
                
                <eventsManager :events="display.content.events.before_reply"></eventsManager>

            </div>

        </div>

        <!-- After Display Response Events -->
        <div class="mb-2">

            <span class="d-block font-weight-bold text-dark">After (User Reply)</span>

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">
                
                <eventsManager :events="display.content.events.after_reply"></eventsManager>

            </div>

        </div>

    </div>
    
</template>

<script>

    //  Get the events manager
    import eventsManager from './../../../../events/main.vue';

    export default {
        props: { 
            display: {
                type: Object,
                default:() => {}
            }
        },
        components: {
            eventsManager
        },
        data(){
            return {
                
            }
        }
    };
  
</script>