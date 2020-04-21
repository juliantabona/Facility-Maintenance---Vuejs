<template>

    <div>

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

    </div>
    
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