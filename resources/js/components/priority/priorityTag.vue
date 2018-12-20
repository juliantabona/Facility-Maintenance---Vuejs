<template>
    <span>
        <span v-if="priorities.length" v-for="(priority, index) in priorities" :key="index">
            <Poptip word-wrap width="200" trigger="hover" :content="priority.description">
                <Tag class="priority_tag" :style="{ 
                    background: priority.color_code + '10 !important',
                    border: '1px solid '+priority.color_code + ' !important'}">
                    <span :style="{ color: priority.color_code }">{{ priority.name }}</span>
                </Tag>
            </Poptip>
        </span>
        <Poptip v-if="showEditBtn && !priorities.length" word-wrap width="200" trigger="hover" content="Add priority status">
            <Button icon="ios-add" type="dashed" size="small">Add</Button>
        </Poptip>
        <Poptip v-if="showEditBtn && priorities.length" word-wrap width="200" trigger="hover" content="Change the priority status">
            <Button type="dashed" size="small"><Icon type="ios-create-outline" :size="18" class="mr-2"></Icon></Button>
        </Poptip>
    </span>
</template>
<script type="text/javascript">
    export default {
        props:{
            priorities: {
                type: Array,
                default: () => []
            },
            showEditBtn: {
                type: Boolean,
                default: true
            }
        }
    }
</script>
