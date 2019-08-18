<template>
    
    <!-- Type Selector -->
    <div>
        <Select v-model="localSelectedType" :style="{ width:'100%' }" placeholder="Select type" not-found-text="No types found"
                @on-change="$emit('updated', $event)">
            <Option 
                v-for="(item, index) in types" 
                :value="item" 
                :label="item" 
                :key="index">
                {{ item }}
            </Option>
        </Select>
    </div>
</template>

<script>

    export default {
        props: {
            selectedType: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                localSelectedType: this.selectedType,
                types: [
                    'Basic',
                    'Advanced'
                ]
            }
        },
        watch: {
            selectedType: function (val) {
                this.localSelectedType = val;
            }
        }
    };
</script>