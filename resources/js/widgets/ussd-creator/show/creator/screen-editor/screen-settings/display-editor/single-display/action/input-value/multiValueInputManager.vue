<template>

    <Row>

        <Col :span="24" class="bg-grey-light border mt-3 p-2">

            <Row>  

                <Col :span="24">
                
                    <!-- Multi Value Separator -->
                    <div class="d-flex">
                        <span class="d-block font-weight-bold text-dark mt-2 mr-2">Separator: </span>
                            
                        <Select v-model="localDisplay.content.action.input_value.multi_value_input.separator" 
                                class="mb-2" placeholder="Select separator" style="width: 200px;">
                            
                            <Option v-for="(separator, key) in separatorTypes" :key="key" class="mb-2"
                                    :value="separator.type" :label="separator.name">
                            </Option>

                        </Select>
                    </div>

                    <template v-if="localDisplay.content.action.input_value.multi_value_input.reference_names.length">
                    
                        <!-- Foreach Multi Value Input Reference Name -->
                        <Row :gutter="4" v-for="(reference_name, x) in localDisplay.content.action.input_value.multi_value_input.reference_names" :key="x">

                            <Col :span="22">

                                <!-- Multi Value Input Reference Name -->
                                <Input v-model="localDisplay.content.action.input_value.multi_value_input.reference_names[x]" 
                                    maxlength="30" type="text" class="w-100 mb-2" placeholder="Reference name">
                                    <div slot="prepend">@</div>
                                </Input>

                            </Col>
                            
                            <Col :span="2">

                                <!-- Remove Option Button  -->
                                <Poptip confirm title="Are you sure you want to remove this option?" 
                                        ok-text="Yes" cancel-text="No" width="300" @on-ok="removeMultiInputReference(x)"
                                        placement="top-end">
                                    <Icon type="ios-trash-outline" class="screen-icon hidable mr-2" size="20"/>
                                </Poptip>

                            </Col>

                        </Row>

                    </template>

                    <Alert v-else type="info" class="mb-2" show-icon>No references</Alert>

                </Col>

                <Col :span="22">

                    <!-- Add Static Option -->
                    <div class="clearfix">

                        <!-- Add Static Option Button -->
                        <Button class="float-right" @click.native="addMultiInputReference()">
                            <Icon type="ios-add" :size="20" />
                            <span>Add Reference</span>
                        </Button>

                    </div>

                </Col>

            </Row>

        </Col>

        <Col :span="24" class="bg-grey-light border mt-2 p-2">
            
            <!-- Next Screen Selector -->
            <screenLinkSelector
                @on-change="localDisplay.content.action.input_value.multi_value_input.link = $event"
                :link="localDisplay.content.action.input_value.multi_value_input.link"
                :display="localDisplay"
                :screens="screens"
                :screen="screen">
            </screenLinkSelector>

        </Col>

    </Row>
    
</template>

<script>

    //  Get the screen settings
    import screenLinkSelector from './../../../../../screenLinkSelector.vue';

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
            screenLinkSelector
        },
        data(){
            return {
                localDisplay: this.display,
                separatorTypes: [
                    {
                        name: 'Single spaces ( )', type: 'spaces'
                    },
                    {
                        name: 'Period symbol (.)', type: '.'
                    },
                    {
                        name: 'Comma symbol (,)', type: ','
                    },
                    {
                        name: 'Hyphen symbol (-)', type: '-'
                    },
                    {
                        name: 'Plus symbol (+)', type: '+'
                    },
                    {
                        name: 'Hash symbol (#)', type: ' '
                    },
                    {
                        name: 'Forward slash symbol (/)', type: '/'
                    }
                ]
            }
        }, 
        computed: {

        },
        methods: {
            addMultiInputReference(){

                //  Build the multi-input reference name template
                var template = '';

                //  Add to existing reference names
                this.localDisplay.content.action.input_value.multi_value_input.reference_names.push( template );

            },
            removeMultiInputReference(index){

                //  Remove current reference name
                this.localDisplay.content.action.input_value.multi_value_input.reference_names.splice(index, 1);

            }
        }
    };
  
</script>