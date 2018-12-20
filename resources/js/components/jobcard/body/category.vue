<template>
    
    <!-- Jobcard Category -->
    <span>
        <strong>Category: </strong>
        <categoryTag :categories="jobcard.categories" :showEditBtn="true"></categoryTag>
    </span>

</template>
<script>
    import categoryTag from './../../category/categoryTag.vue';

    export default {
        components: { categoryTag },
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>