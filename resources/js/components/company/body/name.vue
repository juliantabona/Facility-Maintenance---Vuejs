<template>
    
    <!-- Company Name -->
    <span class="d-inline-block cut-text"><strong>Client:</strong> {{ company.name ? company.name:'____' }}</span>

</template>
<script>
    export default {
        props: [
            /*  company data  */
            'company'
        ],
    };
</script>