<template>
    
    <!-- Courier Selector -->
    <div>
        <Select v-model="localSelectedCourier" :style="{ width:'100%' }" placeholder="Select courier" not-found-text="No couriers found"
                @on-change="$emit('updated', $event)">
            <Option 
                v-for="(item, index) in couriers" 
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
            selectedCourier: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                localSelectedCourier: this.selectedCourier,
                couriers: [
                    'DHL',
                    'FedEx',
                    'Sprint Couries',
                    'Pickup by customer',
                    'Other'
                ]
            }
        },
        watch: {
            selectedCourier: function (val) {
                this.localSelectedCourier = val;
            }
        }
    };
</script>