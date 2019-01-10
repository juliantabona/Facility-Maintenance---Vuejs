<template>
    
    <!-- Company City/Town -->
    <span class="d-inline-block cut-text"><strong>City/Town:</strong> {{ company.city ? company.city:'____' }}</span>

</template>
<script>
    export default {
        props: [
            /*  company data  */
            'company'
        ],
    };
</script>