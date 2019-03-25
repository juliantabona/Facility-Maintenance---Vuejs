<template>

    <span v-if="localPriority">
        <Poptip word-wrap width="250" trigger="hover" :content="localPriority.description">
            <Tag :style="{ 
                maxWidth: '70px',
                background: localPriority.color_code + '10 !important',
                border: '1px solid '+localPriority.color_code + ' !important'}">
                <span :style="{ color: localPriority.color_code }">{{ localPriority.name }}</span>
            </Tag>
        </Poptip>
    </span>

</template>
<script type="text/javascript">

    export default {
        props:{
            priority: {
                type: Array,
                default: null
            }
        },
        data(){
            return {
                localPriority: (this.priority || [] )[0]
            }
        },
        watch: {
            priority: {
                handler: function (val, oldVal) {
                    this.localPriority = (val || [] )[0];
                },
                deep: true
            }
        }
    }
</script>
