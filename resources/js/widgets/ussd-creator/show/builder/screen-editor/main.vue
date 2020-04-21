<template>
                    
    <!-- Screen Settings -->  
    <Card>
    
        <Row v-if="localScreen" :gutter="20" class="mb-3">
            
            <Col :span="24">

                <!-- Main Heading -->  
                <Divider orientation="left">
                    <span class="text-primary">Screen Settings</span>
                </Divider>

            </Col>

            <Col :span="14">

                <!-- Screen name input (Changes the screen name) -->  
                <screenNameInput :screen="localScreen" :screens="screens"></screenNameInput>

            </Col>

            <Col :span="8">

                <!-- First screen checkbox (Marks the screen as the first screen) -->  
                <firstScreenCheckbox :screen="localScreen" :screens="screens"></firstScreenCheckbox>

            </Col>

            <Col :span="24">

                <!-- Screen settings (Change screen type, events, e.t.c) -->  
                <screenSettings :screen="localScreen" :screens="screens" class="mt-3"></screenSettings>

            </Col>

        </Row>

        <!-- No active screen selected message -->
        <Alert v-else type="info" class="mb-2" show-icon>No screen selected</Alert>
            
    </Card>
    
</template>

<script>

    //  Get the screen name input
    import screenNameInput from './screenNameInput.vue';

    //  Get the first screen checkbox
    import firstScreenCheckbox from './firstScreenCheckbox.vue';

    //  Get the screen settings
    import screenSettings from './screen-settings/main.vue';
    
    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            },
            screens: {
                type: Array,
                default: []
            }
        },
        components: { 
            screenNameInput, firstScreenCheckbox, screenSettings
        },
        data(){
            return {
                localScreen: this.screen
            }
        }, 
        watch: {
            /*  Keep track of changes on the localScreen.  */
            screen: {

                handler: function (val, oldVal) {
                    
                    /*  Update the localScreen  */
                    this.localScreen = val;

                },
                deep: true

            },
        },
        computed: {

        },
        methods: {

        }
    };
  
</script>