<template>
    <span v-if="statusSummary" class="mr-2">
        <Poptip word-wrap width="200" trigger="hover" title="Current Status" :content="statusSummary.description">
            <Tag color="success">{{ statusSummary.name }}</Tag>
        </Poptip>
    </span>
</template>
<script type="text/javascript">
    export default {
        props:{
            statusSummary: {
                type: Object,
                default: () => {}
            }
        }
    }
</script>
