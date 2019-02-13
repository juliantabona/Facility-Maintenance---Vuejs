<template>
    
    <!-- Gender Selector -->
    <div>
        <Select v-model="localSelectedGender" :style="{ width:'100%' }" placeholder="Select gender" not-found-text="No gender found"
                @on-change="$emit('updated', $event)">
            <Option 
                v-for="(item, index) in genders" 
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
            selectedGender: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                localSelectedGender: this.selectedGender,
                genders: ['Male', 'Female', 'Other']
            }
        },
        watch: {
            selectedGender: function (val) {
                this.localSelectedGender = val;
            }
        }
    };
</script>