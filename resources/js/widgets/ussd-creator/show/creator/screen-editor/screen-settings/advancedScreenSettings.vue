<template>

    <!-- General Screen Settings -->
    <div>

        <!-- Screen Type -->
        <div class="d-flex">
            <span class="font-weight-bold text-dark mt-1 mr-2">Screen Type:</span>
            <Select v-model="localScreen.type.selected_type" style="width: 200px;">

                <Option 
                    v-for="(screenType, key) in screenTypes"
                    :key="key" :value="screenType.value" :label="screenType.name">
                </Option>

            </Select>
        </div>

    </div>
    
</template>

<script>

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            }
        },
        data(){
            return {
                localScreen: this.screen,
                screenTypes: [
                    {
                        name: 'Default',
                        value: 'default'
                    },
                    {
                        name: 'Repeat',
                        value: 'repeat'
                    },
                ]
            }
        }
    };
  
</script>