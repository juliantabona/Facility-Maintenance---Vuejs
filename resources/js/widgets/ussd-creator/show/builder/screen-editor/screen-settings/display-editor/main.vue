<template>
  
    <Row :gutter="20">

        <Col :span="24">

            <!-- Screen Menu Heading -->    
            <div class="clearfix pb-2 mb-3 border-bottom">

                <span class="d-block mt-2 font-weight-bold text-dark float-left">Displays</span>

                <!-- Create Screen Button -->
                <Button type="primary" class="p-1 float-right" @click.native="addDisplay()">
                    <Icon type="ios-add" :size="20" />
                    <span class="mr-2">Add Display</span>
                </Button>

            </div>

            <!-- Screen Display List & Dragger  -->
            <draggable v-if="displaysExist"
                :list="localScreen.displays"
                @start="drag=true" 
                @end="drag=false" 
                :options="{
                    group:'displays',
                    draggable:'.display-option', 
                    handle:'.display-dragger-handle'
                }">

                <!-- Single Screen Display  -->
                <singleDisplay v-for="(display, index) in localScreen.displays" :key="index"  
                    @duplicatedDisplay="$emit('duplicatedDisplay', $event)" 
                    @selectedDisplay="$emit('selectedDisplay', $event)"
                    @removedDisplay="$emit('removedDisplay', $event)"
                    :screen="localScreen"
                    :screens="screens"
                    :display="display"
                    :index="index">
                </singleDisplay>

            </draggable>

            <!-- No screens message -->
            <Alert v-else type="info" class="mb-2" show-icon>No Displays Found</Alert>

        </Col>

    </Row>

</template>

<script>
    
    import draggable from 'vuedraggable';

    //  Get the single screen menu component
    import singleDisplay from './single-display/main.vue';

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            },
            screens: {
                type: Array,
                default: () => []
            }
        },
        components: { 
            draggable, singleDisplay
        },
        data(){
            return {
                localScreen: this.screen
            }
        },
        computed: {

            //  Check if the screens exists
            displaysExist(){
                return (this.localScreen.displays.length) ? true : false ;
            }

        },
        methods: {
            addDisplay(){

                //  Generate the screen name
                var displayName = 'Display ' + (this.localScreen.displays.length + 1),

                /** Determine whether this must be the first display by default.
                 *  Generally if we don't already have any display assigned as the first
                 *  display for this screen, then we make this display the first display 
                 *  by default.
                 */
                firstDisplay = !this.localScreen.displays.filter( (display) => { 
                    return display.first_display == true;
                }).length ? true : false,

                //  Build the display template
                displayTemplate = { 
                    name: displayName, 
                    first_display: firstDisplay,
                    content: this.getDisplayContentTemplate()
                };

                //  Add the screen to the screen tree
                this.localScreen.displays.push( displayTemplate );

            },
            getDisplayContentTemplate(){
                
                return  {

                    //  Display description properties
                    description: {
                        text: '',
                        code_editor_text: '',
                        code_editor_mode: false
                    },

                    //  Display action properties
                    action: {
                        selected_type: 'no_action',  //  no_action, input_value, select_option
                        input_value: {
                            selected_type: 'single_value_input',    //  single_value_input, multi_value_input
                            single_value_input: {
                                reference_name: null,
                                link:{
                                    type: 'screen', //  screen, display  
                                    name: ''
                                }
                            },
                            multi_value_input: {
                                separator: 'spaces',
                                reference_names: ['first_name', 'last_name'],
                                link:{
                                    type: 'screen', //  screen, display  
                                    name: ''
                                }
                            }
                        },
                        select_option: {
                            selected_type: 'static_options',    //  static_options, dynamic_options, code_editor_options
                            static_options: {
                                options: [
                                    {
                                        name: '1. My Option ',
                                        value: {
                                            text: '',
                                            code_editor_text: '',
                                            code_editor_mode: false
                                        },
                                        input: '1',
                                        separator: {
                                            top: '',
                                            bottom: '',
                                        },
                                        link:{
                                            type: 'screen', //  screen, display  
                                            name: ''
                                        }
                                    }
                                ],
                                reference_name: null,
                                no_results_message: 'No options available',
                                incorrect_option_selected_message: 'You selected an incorrect option. Please try again'
                            }, 
                            dynamic_options: {
                                group_reference: '{{ items }}', 
                                template_reference_name: 'item',
                                template_display_name: '{{ item.name }} - {{ item.price }}',
                                template_value: {
                                    text: '',
                                    code_editor_text: '',
                                    code_editor_mode: false
                                },
                                reference_name: 'selected_item',
                                no_results_message: 'No items found',
                                incorrect_option_selected_message: 'You selected an incorrect option. Please try again',
                                link:{
                                    type: 'screen', //  screen, display  
                                    name: ''
                                },
                            },
                            code_editor_options: {
                                code_editor_text: null,
                                reference_name: 'selected_item',
                                no_results_message: 'No items found',
                                incorrect_option_selected_message: 'You selected an incorrect option. Please try again'
                            }
                        }
                    },

                    //  Repeat navigation properties
                    screen_repeat_navigation: {
                        forward_navigation: [],
                        backward_navigation: []
                    },

                    //  Event settings
                    events: {
                        before_reply: [],
                        after_reply: []
                    },
                    
                    //  Pagination settings
                    pagination: {
                        active: false,
                        content_target: {
                            selected_type: 'both'         //  instruction, action, both
                        },
                        paginate_by_line_breaks: true,
                        slice: {
                            separation_type: 'words',     //  characters, words
                            start: '0',
                            end: '160'
                        },
                        scroll_down_input: '99',
                        scroll_up_input: '88',
                        show_more: {
                            visible: true,
                            text: '99. More'
                        },
                        trailing_end: '...',
                        break_line_before_trail: false,
                        break_line_after_trail: false,
                    }

                };

            }
            
        }
    };
  
</script>