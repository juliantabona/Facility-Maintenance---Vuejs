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

        <!-- Repeat On Items Settings -->
        <repeatOnItemsSettings v-if="localScreen.type.repeat.selected_type == 'repeat_on_items'" :screen="localScreen" :screens="screens"></repeatOnItemsSettings>
        
        <!-- Repeat On Number Settings -->
        <repeatOnNumberSettings v-if="localScreen.type.repeat.selected_type == 'repeat_on_number'" :screen="localScreen" :screens="screens"></repeatOnNumberSettings>

    </div>
    
</template>

<script>

    import repeatOnItemsSettings from './repeatOnItemsSettings.vue';

    import repeatOnNumberSettings from './repeatOnNumberSettings.vue'

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
            repeatOnItemsSettings, repeatOnNumberSettings
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