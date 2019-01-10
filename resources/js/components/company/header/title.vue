<template>

    <!-- Company Title -->
    <h5 v-if="relationType == 'client'"><Icon type="ios-happy-outline" :size="18" class="mr-2"></Icon> Client Details</h5>
    <h5 v-else-if="relationType == 'supplier'"><Icon type="ios-briefcase-outline" :size="18" class="mr-2"></Icon> Supplier Details</h5>

</template>
<script>
    export default {
        props: [
            /*  Company data  */
            'relationType'      //  client, supplier
        ],
    };
</script>