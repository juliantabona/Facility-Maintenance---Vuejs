<template>
       
    <Row :gutter="4">

        <Col :span="24">

            <Row :gutter="4" class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">
        
                <!-- [Foreach] Label -->
                <Col :span="4">
                
                    <span class="d-block text-center mt-1">Foreach</span>
                
                </Col>
                
                <!-- Items Group Reference -->
                <Col :span="8">
                
                    <customEditor
                        size="small" classes="px-1"
                        :useCodeEditor="false" :placeholder="'{{ items }}'"
                        :content="localScreen.type.repeat.repeat_on_items.group_reference"
                        @contentChange="localScreen.type.repeat.repeat_on_items.group_reference = $event">
                    </customEditor>

                </Col>
                
                <!-- [As] Label -->
                <Col :span="2">

                    <span class="d-block text-center mt-1">As</span>
                
                </Col>
                
                <!-- Template Reference Name -->
                <Col :span="10">
                
                    <Input maxlength="30" type="text" class="w-100 mb-2" :placeholder="'item'" 
                        :disabled="!localScreen.type.repeat.repeat_on_items.group_reference"
                        v-model="localScreen.type.repeat.repeat_on_items.item_reference_name">
                        <div slot="prepend">@</div>
                    </Input>
                
                </Col>

            </Row>

        </Col>
        
        <Col :span="24">

            <!-- Single Item Display Name -->
            <span class="d-block font-weight-bold text-dark">Additional References</span>

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">

                <Row :gutter="4">

                    <Col :span="12">

                        <!-- Total Items Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Total Items</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_items.total_loops_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="total_items">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                    <Col :span="12">

                        <!-- Item Index Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Item Index</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_items.loop_index_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="item_index">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                    <Col :span="12">

                        <!-- Item Number Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Item Number</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_items.loop_number_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="item_number">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                    <Col :span="12">

                        <!-- Is First Item Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Is First Item</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_items.is_first_loop_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="is_first_item">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                    <Col :span="12">

                        <!-- Is Last Item Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Is Last Item</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_items.is_last_loop_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="is_last_item">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                </Row>

            </div>

            <!-- No Items Behaviour -->
            <span class="d-block font-weight-bold text-dark mb-2">No Items Behaviour</span>

            <Row :gutter="12" class="bg-grey-light border mb-3 p-2 clearfix">

                <Col :span="12">

                    <div class="d-flex">

                        <span class="font-weight-bold text-dark mt-1 mr-2">Behaviour:</span>
                                
                        <!-- Behaviour Method -->
                        <Select v-model="localScreen.type.repeat.repeat_on_items.on_no_loop.selected_type">
                            <Option v-for="(behaviour_type, index) in behaviour_types" :value="behaviour_type.value" :key="index">
                                {{ behaviour_type.name }}
                            </Option>
                        </Select>
                    
                    </div>

                </Col>

                <Col :span="12" v-if="localScreen.type.repeat.repeat_on_items.on_no_loop.selected_type == 'link'">
                
                    <screenLinkSelector
                        @on-change="localScreen.type.repeat.repeat_on_items.on_no_loop.link = $event"
                        :link="localScreen.type.repeat.repeat_on_items.on_no_loop.link"
                        :screens="screens"
                        :screen="screen"
                        :display="null"
                        layout="inline">
                    </screenLinkSelector>

                </Col>

            </Row>

            <!-- After Last Item Behaviour -->
            <span class="d-block font-weight-bold text-dark mb-2">After Last Item Behaviour</span>
            
            <Row :gutter="12" class="bg-grey-light border mb-3 p-2 clearfix">

                <Col :span="12">

                    <div class="d-flex">

                        <span class="font-weight-bold text-dark mt-1 mr-2">Behaviour:</span>
                                
                        <!-- Behaviour Method -->
                        <Select v-model="localScreen.type.repeat.repeat_on_items.after_last_loop.selected_type">
                            <Option v-for="(behaviour_type, index) in behaviour_types" :value="behaviour_type.value" :key="index">
                                {{ behaviour_type.name }}
                            </Option>
                        </Select>
                    
                    </div>

                </Col>

                <Col :span="12" v-if="localScreen.type.repeat.repeat_on_items.after_last_loop.selected_type == 'link'">
                
                    <screenLinkSelector
                        @on-change="localScreen.type.repeat.repeat_on_items.after_last_loop.link = $event"
                        :link="localScreen.type.repeat.repeat_on_items.after_last_loop.link"
                        :screens="screens"
                        :screen="screen"
                        :display="null"
                        layout="inline">
                    </screenLinkSelector>

                </Col>

            </Row>
        
        </Col>

    </Row>

</template>

<script>

    import customEditor from '../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    //  Get the link selector
    import screenLinkSelector from './../screenLinkSelector.vue';

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            },
            screens: {
                type: Array,
                default: () => []
            },
        },
        components: {
            customEditor, screenLinkSelector
        },
        data(){
            return {
                localScreen: this.screen,
                behaviour_types: [
                    {
                        name: 'Do Nothing',
                        value: 'do_nothing'
                    },
                    {
                        name: 'Link To Screen',
                        value: 'link'
                    }
                ]
            }
        }
    };
  
</script>