<template>
    
    <!-- PaymentMethod Selector -->
    <div>
        <Select v-model="localSelectedPaymentMethod" :style="{ width:'100%' }" 
                placeholder="Select payment method" 
                not-found-text="No payment methods found"
                @on-change="$emit('updated', $event)">
            <Option 
                v-for="(item, index) in paymentMethods" 
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
            selectedPaymentMethod: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                localSelectedPaymentMethod: this.selectedPaymentMethod,
                paymentMethods: ['Online Payment', 'Bank Transfer', 'Cheque', 'Cash', 'Other']
            }
        },
        watch: {
            selectedPaymentMethod: function (val) {
                this.localSelectedPaymentMethod = val;
            }
        }
    };
</script>