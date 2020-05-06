<template>

    <Row :gutter="12">

        <Col :span="24">

            <!-- Static Option List & Dragger  -->
            <draggable 
                :list="localOptions"
                @start="drag=true" 
                @end="drag=false" 
                :options="{
                    group:'static-options',
                    draggable:'.draggable-option', 
                    handle:'.dragger-handle'
                }"
                :style="{  minHeight:'50px' }">

                <!-- Single Static Option  -->
                <singleStaticOption v-for="(option, index) in localOptions" :key="index"  
                    :options="localOptions"
                    :screens="screens"
                    :display="display"
                    :screen="screen"
                    :option="option"
                    :index="index">
                </singleStaticOption>
                
                <!-- No options message -->
                <Alert v-if="!staticOptionsExist" type="info" show-icon style="width:300px;">No Options Found</Alert>

            </draggable>

            <div class="clearfix">

                <!-- Add Static Option Button -->
                <Button class="float-right" @click.native="addSelectStaticOption()">
                    <Icon type="ios-add" :size="20" />
                    <span>Add Option</span>
                </Button>

            </div>

        </Col>
        
        <Col :span="24">

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">

                <!-- Selected Item Reference Name -->
                <span class="d-block font-weight-bold text-dark">Reference Name</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.static_options.reference_name"
                    maxlength="30" type="text" class="w-100 mb-3" placeholder="selected_item">
                    <div slot="prepend">@</div>
                </Input>

            </div>

            <!-- Messages / Desclaimers -->
            <div class="bg-grey-light border mt-3 mb-3 p-2">

                <!-- Heading -->
                <span class="d-block font-weight-bold text-dark mt-3">No Options Message</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.static_options.no_results_message"
                    placeholder="Enter no options message"
                    type="textarea" :rows="2" class="w-100 mb-3">
                </Input>

                <!-- Heading -->
                <span class="d-block font-weight-bold text-dark">Incorrect Option Selected Message</span>
                
                <Input 
                    v-model="localDisplay.content.action.select_option.static_options.incorrect_option_selected_message"
                    placeholder="Enter incorrect option selected message"
                    type="textarea" :rows="2" class="w-100 mb-3">
                </Input>

            </div>
        
        </Col>

    </Row>
    
</template>

<script>

    import draggable from 'vuedraggable';

    //  Get the single option
    import singleStaticOption from './single-option/main.vue';

    export default {
        props: { 
            display: {
                type: Object,
                default:() => {}
            },
            screen: {
                type: Object,
                default:() => {}
            },
            screens: {
                type: Array,
                default: () => []
            }
        },
        components: {
            draggable, singleStaticOption
        },
        data(){
            return {
                localDisplay: this.display,
                localOptions: this.display.content.action.select_option.static_options.options
            }
        }, 
        computed: {

            //  Count the static options
            numberOfstaticOptions(){

                return this.localOptions.length;

            },

            //  Check if the static options exist
            staticOptionsExist(){

                return (this.numberOfstaticOptions) ? true : false ;

            }

        },
        methods: {

            addSelectStaticOption(){

                var optionNumber = (this.numberOfstaticOptions + 1);

                //  Build the option template
                var optionTemplate = {
                        name: optionNumber + '. My Option ',
                        value: {
                            text: '',
                            code_editor_text: '',
                            code_editor_mode: false
                        },
                        input: optionNumber.toString(),
                        separator: {
                            top: '',
                            bottom: '',
                        },
                        link: {
                            type: 'screen',
                            name: ''
                        }
                    };

                //  Add the screen to the screen tree
                this.localOptions.push( optionTemplate );

            }

        }
    };
  
</script>