<template>

    <!-- Repeat Screen Events -->
    <div>

        <!-- Repeat Screen Type -->
        <div class="d-flex mb-3">
            <span class="font-weight-bold text-dark mt-1 mr-2">Repeat Type:</span>
            <Select v-model="localScreen.type.repeat.selected_type" style="width: 200px;">

                <Option 
                    v-for="(repeatType, key) in repeatTypes"
                    :key="key" :value="repeatType.value" :label="repeatType.name">
                </Option>

            </Select>
        </div>

        <Row v-if="localScreen.type.repeat.selected_type == 'repeat_on_items'" :gutter="4">

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
                                v-model="localScreen.type.repeat.repeat_on_items.total_items_reference_name"
                                maxlength="30" type="text" class="w-100 mb-3" placeholder="total_items">
                                <div slot="prepend">@</div>
                            </Input>

                        </Col>

                        <Col :span="12">

                            <!-- Item Index Reference Name -->
                            <span class="d-block font-weight-bold text-dark">Item Index</span>
                            <Input 
                                v-model="localScreen.type.repeat.repeat_on_items.item_index_reference_name"
                                maxlength="30" type="text" class="w-100 mb-3" placeholder="item_index">
                                <div slot="prepend">@</div>
                            </Input>

                        </Col>

                        <Col :span="12">

                            <!-- Item Number Reference Name -->
                            <span class="d-block font-weight-bold text-dark">Item Number</span>
                            <Input 
                                v-model="localScreen.type.repeat.repeat_on_items.item_number_reference_name"
                                maxlength="30" type="text" class="w-100 mb-3" placeholder="item_number">
                                <div slot="prepend">@</div>
                            </Input>

                        </Col>

                        <Col :span="12">

                            <!-- Is First Item Reference Name -->
                            <span class="d-block font-weight-bold text-dark">Is First Item</span>
                            <Input 
                                v-model="localScreen.type.repeat.repeat_on_items.is_first_item_reference_name"
                                maxlength="30" type="text" class="w-100 mb-3" placeholder="is_first_item">
                                <div slot="prepend">@</div>
                            </Input>

                        </Col>

                        <Col :span="12">

                            <!-- Is Last Item Reference Name -->
                            <span class="d-block font-weight-bold text-dark">Is Last Item</span>
                            <Input 
                                v-model="localScreen.type.repeat.repeat_on_items.is_last_item_reference_name"
                                maxlength="30" type="text" class="w-100 mb-3" placeholder="is_last_item">
                                <div slot="prepend">@</div>
                            </Input>

                        </Col>

                    </Row>

                </div>

                <!-- Messages / Desclaimers -->
                <div class="bg-grey-light border mt-3 mb-3 p-2">

                    <!-- Heading -->
                    <span class="d-block font-weight-bold text-dark mt-3">(No Repeat Items) Message</span>
                    
                    <Input 
                        v-model="localScreen.type.repeat.repeat_on_items.no_results_message"
                        placeholder="Enter no repeat items message"
                        type="textarea" :rows="2" class="w-100 mb-3">
                    </Input>

                </div>
            
            </Col>

        </Row>

        <Row v-if="localScreen.type.repeat.selected_type == 'repeat_on_number'" :gutter="4">

            <Col :span="12" class="mt-2 mb-3">

                <!-- Enter Number/Dynamic tags -->
                <span class="d-block font-weight-bold text-dark">Number of times to repeat</span>

                <customEditor
                    size="small" classes="px-1"
                    :useCodeEditor="false" placeholder="3"
                    :content="localScreen.type.repeat.repeat_on_number.value"
                    @contentChange="localScreen.type.repeat.repeat_on_number.value = $event">
                </customEditor>

            </Col>

        </Row>
            
    </div>
    
</template>

<script>

    import customEditor from '../../../../../../components/_common/wiziwigEditors/customEditor.vue';

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            }
        },
        components: {
            customEditor
        },
        data(){
            return {
                localScreen: this.screen,
                repeatTypes: [
                    {
                        name: 'Repeat on number',
                        value: 'repeat_on_number'
                    },
                    {
                        name: 'Repeat on items',
                        value: 'repeat_on_items'
                    },
                    {
                        name: 'Custom Repeat',
                        value: 'custom_repeat'
                    },
                ]
            }
        }
    };
  
</script>