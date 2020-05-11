<style scoped>

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        width: 85% !important;
        white-space: nowrap !important;
        display: block;
    }

    /*  Event Toolbox */

    .event-option >>> .event-toolbox{
        z-index: 1;
        position: relative;
        margin: -2px 0 0 0;
        background: #fff;
    }

    .event-option >>> .event-toolbox,
    .event-option >>> .event-toolbox .hidable{
        opacity:0;
    }

    .event-option:hover >>> .event-toolbox,
    .event-option:hover >>> .event-toolbox .hidable{
        opacity:1;
    }

    .event-option >>> .event-toolbox .event-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .event-option >>> .event-toolbox .event-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .event-option >>> .ivu-card-body{
        padding:0 !important;
    }

    .event-details{
        padding:16px;
    }

</style>

<template>

    <Card v-if="localEvent" class="event-option mb-2">

        <!-- Event Name -->
        <div slot="title">

            <!-- Event Name Label  -->
            <span class="event-name font-weight-bold cut-text">
                {{ getEventNumber ? getEventNumber +'. ' : '' }}
                {{ localEvent.name }}
            </span>
            
        </div>

        <!-- Event Toolbar (Edit, Move, Delete Buttons) -->
        <div slot="extra" class="d-flex" style="margin-top: -2px;">

            <span class="d-flex blue-highlighter mr-2" style="border-radius: 5px;">

                <!-- Event Icon -->
                <eventIcon :eventType="localEvent.type" :size="20" class="text-primary mr-1"></eventIcon>

                <!-- Event Type -->
                <span>{{ localEvent.type }}</span>

            </span>

            <div class="event-toolbox">

                <!-- Remove Event Button  -->
                <Poptip confirm title="Are you sure you want to remove this event?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemoveEvent(index)"
                        placement="left">
                    <Icon type="ios-trash-outline" class="event-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Edit Event Button  -->
                <Icon type="ios-create-outline" class="event-icon hidable mr-2" size="20" @click="editEvent()" />

                <!-- Copy Event Button  -->
                <Icon type="ios-copy-outline" class="event-icon hidable mr-2" size="20" @click="handleDuplicateEvent(index)"/>

                <!-- Move Event Button  -->
                <Icon type="ios-move" class="event-icon event-dragger-handle hidable mr-2" size="20" />
            
            </div>

        </div> 
    
        <!-- 
            MODAL TO EDIT EXISTING EVENT
        -->
        <editEventModal v-if="isOpenEditEventModal" 
            @visibility="isOpenEditEventModal = $event"
            :event="localEvent"
            :builder="builder"
            :screen="screen">
        </editEventModal> 

    </Card>

</template>

<script>

    //  Get the event icon
    import eventIcon from './../eventIcon.vue';

    //  Get the create new event modal
    import editEventModal from './../edit/editEventModal.vue';

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            screen: {
                type: Object,
                default: () => {}
            },
            events: {
                type: Array,
                default:() => []
            },
            event: {
                type: Object,
                default:() => {}
            },
            builder: {
                type: Object,
                default: () => {}
            }
        }, 
        components: { eventIcon, editEventModal },
        data(){
            return {
                localEvent: this.event,
                isOpenEditEventModal: false
            }
        },
        watch: {
            //  Keep track of changes on the event
            event: {

                handler: function (val, oldVal) {
                    
                    /*  Update the localScreen  */
                    this.localEvent = val;

                },
                deep: true

            },
        },
        computed: {
            getEventNumber(){
                /**
                 *  Returns the event number. We use this as we list the events.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            }
        },
        methods: {
            editEvent(){
                this.isOpenEditEventModal = true;
            },
            handleDuplicateEvent(index) {

                //  Duplicate the event
                var duplicateEvent = _.cloneDeep( this.events[index] );

                //  Create the duplicate event name
                var duplicateName = 'Event - #' + (this.events.length + 1);

                //  Set the duplicate event name
                duplicateEvent.name = duplicateName;

                //  Add the duplicate event to the rest of the other events
                this.events.push(duplicateEvent);

            },
            handleRemoveEvent(index) {
                this.events.splice(index, 1);
            }
        }
    }

</script>