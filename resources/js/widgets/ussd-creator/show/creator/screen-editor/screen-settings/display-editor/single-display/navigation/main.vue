<template>

    <div>
        
        <!-- Display Navigation Explainer -->
        <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
            Use the <span class="font-italic text-success font-weight-bold">Display Navigation</span> to load the next
            repeating item. You can provide inputs for <span class="font-italic text-success font-weight-bold">Forward Navigation</span>
            which will load the next item and <span class="font-italic text-success font-weight-bold">Backward Navigation</span> to load the
            previous item.
        </Alert>

        <!-- Forward Navigation Settings -->
        <Row :gutter="10" class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">

            <Col :span="12" class="d-flex">
            
                <span class="d-block font-weight-bold text-dark mt-2 mr-2">Forward Navigation: </span>
                
                <Select v-model="localDisplay.content.screen_repeat_navigation.forward_navigation.selected_type" 
                        class="w-50 mb-2" placeholder="Type">
                    
                    <Option v-for="(action, key) in navigationTypes" :key="key" class="mb-2"
                            :value="action.type" :label="action.name">
                    </Option>

                </Select>

            </Col>

            <Col :span="24">

                <!-- Custom Navigation Settings  -->
                <template v-if="localDisplay.content.screen_repeat_navigation.forward_navigation.selected_type == 'custom'">
                    
                    <!-- Custom Navigation Inputs -->
                    <Input v-model="localDisplay.content.screen_repeat_navigation.forward_navigation.custom.inputs" 
                        type="text" class="w-100 mb-2" placeholder="Enter custom inputs">
                    </Input>

                </template>

                <!-- Regex Navigation Settings  -->
                <template v-if="localDisplay.content.screen_repeat_navigation.forward_navigation.selected_type == 'regex'">
                    
                    <!-- Regex Navigation Rule -->
                    <Input v-model="localDisplay.content.screen_repeat_navigation.forward_navigation.regex.rule" 
                        class="regex-input mt-1" type="text" placeholder="/[a-zA-Z0-9]+/">
                        <span slot="prepend">Regex Rule</span>
                    </Input>

                </template>

            </Col>

        </Row>

        <!-- Backward Navigation Settings -->
        <Row :gutter="10" class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">

            <Col :span="12" class="d-flex">
            
                <span class="d-block font-weight-bold text-dark mt-2 mr-2">Backward Navigation: </span>
                
                <Select v-model="localDisplay.content.screen_repeat_navigation.backward_navigation.selected_type" 
                        class="w-50 mb-2" placeholder="Type">
                    
                    <Option v-for="(action, key) in navigationTypes" :key="key" class="mb-2"
                            :value="action.type" :label="action.name">
                    </Option>

                </Select>

            </Col>

            <Col :span="24">

                <!-- Custom Navigation Settings  -->
                <template v-if="localDisplay.content.screen_repeat_navigation.backward_navigation.selected_type == 'custom'">
                    
                    <!-- Custom Navigation Inputs -->
                    <Input v-model="localDisplay.content.screen_repeat_navigation.backward_navigation.custom.inputs" 
                        type="text" class="w-100 mb-2" placeholder="Enter custom inputs">
                    </Input>

                </template>

                <!-- Regex Navigation Settings  -->
                <template v-if="localDisplay.content.screen_repeat_navigation.backward_navigation.selected_type == 'regex'">
                    
                    <!-- Regex Navigation Rule -->
                    <Input v-model="localDisplay.content.screen_repeat_navigation.backward_navigation.regex.rule" 
                        class="regex-input mt-1" type="text" placeholder="/[a-zA-Z0-9]+/">
                        <span slot="prepend">Regex Rule</span>
                    </Input>

                </template>

            </Col>

        </Row>

    </div>
    
</template>

<script>

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
            
        },
        data(){
            return {
                localDisplay: this.display,
                navigationTypes: [
                    {
                        name: 'Custom Inputs', type: 'custom'
                    },
                    {
                        name: 'Regex Match', type: 'regex'
                    }
                ]
            }
        }, 
        computed: {

        },
        methods: {

        }
    };
  
</script>