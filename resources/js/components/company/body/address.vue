<template>
    
    <!-- Company Address -->
    <span class="d-inline-block cut-text"><strong>Address:</strong> {{ company.address ? company.address:'____' }}</span>

</template>
<script>
    export default {
        props: [
            /*  company data  */
            'company'
        ],
    };
</script>