<template>

    <Row :gutter="4">

        <Col :span="12" class="mt-2 mb-3">

            <!-- Enter Number/Dynamic tags -->
            <span class="d-block font-weight-bold text-dark mb-1">Number of times to repeat</span>

            <customEditor
                size="small" classes="px-1"
                :useCodeEditor="false" placeholder="3"
                :content="localScreen.type.repeat.repeat_on_number.value"
                @contentChange="localScreen.type.repeat.repeat_on_number.value = $event">
            </customEditor>

        </Col>
        
        <Col :span="24">

            <!-- Single Item Display Name -->
            <span class="d-block font-weight-bold text-dark">Additional References</span>

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">

                <Row :gutter="4">

                    <Col :span="12">

                        <!-- Loop Index Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Loop Index</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_number.loop_index_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="loop_index">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                    <Col :span="12">

                        <!-- Loop Number Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Loop Number</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_number.loop_number_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="loop_number">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                    <Col :span="12">

                        <!-- Is First Loop Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Is First Loop</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_number.is_first_loop_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="is_first_loop">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                    <Col :span="12">

                        <!-- Is Last Loop Reference Name -->
                        <span class="d-block font-weight-bold text-dark">Is Last Loop</span>
                        <Input 
                            v-model="localScreen.type.repeat.repeat_on_number.is_last_loop_reference_name"
                            maxlength="30" type="text" class="w-100 mb-3" placeholder="is_last_loop">
                            <div slot="prepend">@</div>
                        </Input>

                    </Col>

                </Row>

            </div>

            <!-- No Loops Behaviour -->
            <span class="d-block font-weight-bold text-dark mb-2">No Loops Behaviour</span>

            <Row :gutter="12" class="bg-grey-light border mb-3 p-2 clearfix">

                <Col :span="12">

                    <div class="d-flex">

                        <span class="font-weight-bold text-dark mt-1 mr-2">Behaviour:</span>
                                
                        <!-- Behaviour Method -->
                        <Select v-model="localScreen.type.repeat.repeat_on_number.on_no_loop.selected_type">
                            <Option v-for="(behaviour_type, index) in behaviour_types" :value="behaviour_type.value" :key="index">
                                {{ behaviour_type.name }}
                            </Option>
                        </Select>
                    
                    </div>

                </Col>

                <Col :span="12" v-if="localScreen.type.repeat.repeat_on_number.on_no_loop.selected_type == 'link'">
                
                    <screenLinkSelector
                        @on-change="localScreen.type.repeat.repeat_on_number.on_no_loop.link = $event"
                        :link="localScreen.type.repeat.repeat_on_number.on_no_loop.link"
                        :screens="screens"
                        :screen="screen"
                        :display="null"
                        layout="inline">
                    </screenLinkSelector>

                </Col>

            </Row>

            <!-- After Last Loop Behaviour -->
            <span class="d-block font-weight-bold text-dark mb-2">After Last Loop Behaviour</span>
            
            <Row :gutter="12" class="bg-grey-light border mb-3 p-2 clearfix">

                <Col :span="12">

                    <div class="d-flex">

                        <span class="font-weight-bold text-dark mt-1 mr-2">Behaviour:</span>
                                
                        <!-- Behaviour Method -->
                        <Select v-model="localScreen.type.repeat.repeat_on_number.after_last_loop.selected_type">
                            <Option v-for="(behaviour_type, index) in behaviour_types" :value="behaviour_type.value" :key="index">
                                {{ behaviour_type.name }}
                            </Option>
                        </Select>
                    
                    </div>

                </Col>

                <Col :span="12" v-if="localScreen.type.repeat.repeat_on_number.after_last_loop.selected_type == 'link'">
                
                    <screenLinkSelector
                        @on-change="localScreen.type.repeat.repeat_on_number.after_last_loop.link = $event"
                        :link="localScreen.type.repeat.repeat_on_number.after_last_loop.link"
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