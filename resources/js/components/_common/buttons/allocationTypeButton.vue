<template>

    <div class="d-inline-block float-right mr-2 mt-2">
        <Select v-model="allocationType" :style="{ width:'200px' }" placeholder="Select company/branch" @on-change="updateChanges()" not-found-text="No record found">
            <Option v-for="item in modelOptions" :value="item.value" :key="item.value">{{ item.label }}</Option>
        </Select>
    </div>

</template>
<script>
    export default {
        data(){
            return {
                //  store is a global custom class found in store.js
                //  We use it to access data accessible globally
                //  In this case we need to access the data
                //  allocationType to know whether the user
                //  wants company/branch related data
                allocationType: store.allocationType,
                modelOptions: [
                     {
                         value: 'company',
                         label: 'Show Company Records'
                     },
                     {
                         value: 'branch',
                         label: 'Show Branch Records'
                     },
                ],
            }
        },
        methods: {
            updateChanges(){
                console.log('allocationTypeButton.vue - Update server and other components on updatedAllocationType to "'+this.allocationType+'"');
                //  Notify all elements that the allocationType have been updated
                Event.$emit('updatedAllocationType', this.allocationType);

                //  Also update on the server side
                store.updateAllocationType(this.allocationType);
            }
        }
    };
</script>