<template>

    <div>

        <!-- Event List & Dragger  -->
        <draggable 
            :list="events"
            @start="drag=true" 
            @end="drag=false" 
            :options="{
                group:'events',
                draggable:'.event-option', 
                handle:'.event-dragger-handle'
            }"
            :style="{  minHeight:'50px' }">

            <!-- Single Event  -->
            <singleEvent v-for="(event, index) in events" :key="index" 
                :events="events" 
                :event="event"
                :index="index">
            </singleEvent>
            
            <!-- No events message -->
            <Alert v-if="!eventsExist" type="info" show-icon style="width:300px;">No Events Found</Alert>

        </draggable>

        <div class="clearfix">

            <!-- Create Event Button -->
            <Button class="p-1 float-right" @click.native="launchEventCreater()">
                <Icon type="ios-add" :size="20" />
                <span class="mr-2">Add Event</span>
            </Button>

        </div>
        
        <!-- 
            MODAL TO CREATE NEW EVENT
        -->
        <createEventModal
            v-if="isOpenCreateEventModal" 
            @visibility="isOpenCreateEventModal = $event"
            @createdEvent="addEvent($event)">
        </createEventModal>

    </div>

</template>

<script>

    import draggable from 'vuedraggable';

    //  Get the single event component
    import singleEvent from './single-event/main.vue';

    //  Get the create new event modal
    import createEventModal from './create/createEventModal.vue';

    export default {
        props: { 
            events: {
                type: Array,
                default: () => []
            }
        },
        components: { 
            draggable, singleEvent, createEventModal
        },
        data(){
            return {
                eventToEdit: null,
                isOpenCreateEventModal: false,
            }
        },
        computed: {

            //  Check if the events exist
            eventsExist(){

                return (this.events.length) ? true : false ;

            }

        },
        methods: {
            launchEventCreater(){
                this.isOpenCreateEventModal = true;
            },
            addEvent( event ){

                //  If we have an event
                if( event ){
                    
                    //  Push the new event
                    this.events.push( event );

                    this.$Notice.success({
                        title: 'Event added ('+event.type+')'
                    });

                }

            }
        }
    };
  
</script>