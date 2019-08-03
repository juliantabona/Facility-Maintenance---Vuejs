<template>
    
    <!-- Reason Selector -->
    <div>
        <Select v-model="localSelectedReason" :style="{ width:'100%' }" placeholder="Select reason" not-found-text="No reasons found"
                @on-change="$emit('updated', $event)">
            <Option 
                v-for="(item, index) in reasons" 
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
            selectedReason: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                localSelectedReason: this.selectedReason,
                reasons: [
                    'Order does not require delivery',
                    'Other'
                ]
            }
        },
        watch: {
            selectedReason: function (val) {
                this.localSelectedReason = val;
            }
        }
    };
</script>