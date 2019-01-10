<template>

    <div class="d-inline-block float-right mr-2 mt-2">
        <Select v-model="modelType" :style="{ width:'200px' }" placeholder="Select company/branch" @on-change="updateChanges()" not-found-text="No record found">
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
                modelType: store.resourceType,
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
                console.log('resourceTypeButton.vue - Update server and other components on updatedResourceType to "'+this.modelType+'"');
                //  Notify all elements that the resourceType have been updated
                Event.$emit('updatedResourceType', this.modelType);

                //  Also update on the server side
                store.updateResourceType(this.modelType);
            }
        }
    };
</script>