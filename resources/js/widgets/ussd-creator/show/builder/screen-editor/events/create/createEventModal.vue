<style scoped>

    .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
    }

</style>

<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="false" 
            :hideModal="hideModal"
            title="Create Event"
            cancelText="Close"
            okText="" 
            @on-ok="true"
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <div style="min-height: 220px;">

                    <transition name="fade">

                        <div v-if="transition" >

                            <!-- Reset Button -->
                            <Button v-if="showResetButton" class="mb-2" @click.native="reset()">
                                <Icon type="ios-arrow-back" :size="20" />
                                <span>Back</span>
                            </Button>

                            <!-- Events -->
                            <Row :gutter="20">

                                <Col :span="8" v-for="(event, key) in displayEvents" :key="key" class="mb-2">
                                    
                                    <Card @click.native="handleSelectedEvent(event)" :padding="0">
                                        
                                        <div style="padding: 14px;">

                                            <!-- Event Icon -->
                                            <eventIcon :eventType="event.type" :size="30" class="text-center d-block"></eventIcon>
                                            
                                            <!-- Event Name -->
                                            <p class="text-center" style="padding-top:15px;">{{ event.type }}</p>

                                        </div>

                                    </Card>

                                </Col>

                            </Row>

                        </div>

                    </transition>

                </div>

            </template>
            
        </mainModal>    
        
    </div>

</template>

<script>

    /*  Main Modal   */
    import mainModal from './../../../../../../../components/_common/modals/main.vue';

    //  Get the event icon
    import eventIcon from './../eventIcon.vue';

    export default {
        components: { mainModal, eventIcon },
        data(){
            return{
                transition: true,
                hideModal: false,
                showResetButton: false,
                displayEvents: [],
                primaryEvents: [
                    {
                        type: "API's"
                    },
                    {
                        type: "Validation"
                    },
                    {
                        type: "Formatting"
                    },
                    {
                        type: "Local Storage"
                    },
                    {
                        type: "Custom Code"
                    },
                    {
                        type: "Redirect"
                    }
                ],
                apiEvents: [
                    {
                        type: "CRUD API"
                    },
                    {
                        type: "SMS API"
                    },
                    {
                        type: "Email API"
                    },
                    {
                        type: "Location API"
                    },
                    {
                        type: "Billing API"
                    },
                    {
                        type: "Subcription API"
                    }
                ],
            }
        },
        methods: {
            handleSelectedEvent(event){

                //  If we selected the API's event
                if( event.type == "API's"){

                    //  Display the list of API events
                    this.displayNestedEvents( this.apiEvents );

                }else{

                    var newEvent = this.createEvent( event.type );

                    //  Notify the parent component of the selected event
                    this.$emit('createdEvent', newEvent);

                    //  Close the modal
                    this.closeModal();

                }

            },
            displayNestedEvents( events ){

                //  Manage the transition
                this.manageTransition();

                //  Show the reset button
                this.showResetButton = true;

                //  Display the nested events
                this.displayEvents = events;

            },
            reset(){

                //  Manage the transition
                this.manageTransition();

                //  Don't show the reset button
                this.showResetButton = false;

                //  Display the primary events
                this.displayEvents = this.primaryEvents;

            },
            manageTransition(){

                //  Get the vue instanceee
                var self = this;

                //  Turn off transition
                this.transition = false;

                //  After (0.1) seconds
                setTimeout(()=>{

                    //  Turn on transition
                    self.transition = true;

                }, 100);

            },
            closeModal(){

                this.hideModal = true;

            },
            createEvent( eventType ){

                var event = null;

                if( eventType == 'CRUD API' ){

                    event = this.get_CRUD_API_Event( eventType );

                }else if( eventType == 'SMS API' ){

                    event = this.get_SMS_API_Event( eventType );

                }else if( eventType == 'Email API' ){

                    event = this.get_Email_API_Event( eventType );

                }else if( eventType == 'Location API' ){

                    event = this.get_Location_API_Event( eventType );
                    
                }else if( eventType == 'Billing API' ){

                    event = this.get_Billing_API_Event( eventType );
                    
                }else if( eventType == 'Subcription API' ){

                    event = this.get_Subcription_API_Event( eventType );
                    
                }else if( eventType == 'Validation' ){

                    event = this.get_Validation_Event( eventType );
                    
                }else if( eventType == 'Formatting' ){

                    event = this.get_Formatting_Event( eventType );
                    
                }else if( eventType == 'Local Storage' ){

                    event = this.get_Local_Storage_Event( eventType );
                    
                }else if( eventType == 'Custom Code' ){

                    event = this.get_Custom_Code_Event( eventType );
                    
                }else if( eventType == 'Redirect' ){

                    event = this.get_Redirect_Event( eventType );
                    
                }

                return event;

            },
            get_CRUD_API_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Create / Read / Update / Delete',
                    active: true,
                    event_data: {
                        url: 'http://oqcloud.local/api/test/items', // 'https://domain.com/api/items',
                        name: 'Get Items',
                        method: 'get',
                        trigger: 'on-enter',
                        query_params: [],
                        form_data: [],
                        headers: [],
                        response:{
                            general: {
                                default_success_message: 'Completed successfully',
                                default_error_message: 'Sorry, we are experiencing technical difficulties'
                            },
                            selected_type: 'automatic', //  automatic, manual
                            automatic: {
                                on_handle_success: 'use_default_success_msg',   //  do_nothing, use_default_success_msg
                                on_handle_error: 'use_default_error_msg',         //  do_nothing, use_default_error_msg
                            },
                            manual:{
                                response_status_handles: [
                                    {
                                        status: '200',
                                        attributes: [
                                            {
                                                name: '', //  e.g items_response
                                                value: '{{ response }}'     //  e.g {{ response }}
                                            }
                                        ],
                                        on_handle: {
                                            selected_type: 'use_custom_msg',   //  do_nothing, use_custom_msg
                                            use_custom_msg: {
                                                text: '',
                                                code_editor_text: '',
                                                code_editor_mode: false
                                            }
                                        }
                                    }
                                ]
                            }
                        } 
                    }
                }
            },
            get_SMS_API_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Send SMS',
                    active: true,
                }

            },
            get_Email_API_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Send Email',
                    active: true,
                }

            },
            get_Location_API_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Get Location',
                    active: true,
                }

            },
            get_Billing_API_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Handle Payment',
                    active: true,
                }

            },
            get_Subcription_API_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Handle Subcription',
                    active: true,
                }

            },
            get_Validation_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Validation',
                    active: true,
                    event_data: {
                        target: '', //  e.g "{{ product.quantity }}"
                        rules: []
                    }
                }

            },
            get_Formatting_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Formatting',
                    active: true,
                    event_data: {
                        reference_name: '',  //  e.g "product_name"
                        rules: []
                    }
                }

            },
            get_Local_Storage_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Store Data',
                    active: true,
                }

            },
            get_Custom_Code_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Custom Code',
                    active: true,
                }

            },
            get_Redirect_Event( eventType ){
                
                return {
                    type: eventType,
                    name: 'Redirect',
                    active: true,
                }

            }
        },
        created(){

            //  Display the primary events
            this.reset();

        }
    }
</script>