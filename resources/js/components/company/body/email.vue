<template>
    
    <!-- Company City/Town -->
    <span class="d-inline-block cut-text"><strong>Email:</strong> {{ company.email ? company.email:'____' }}</span>

</template>
<script>
    export default {
        props: [
            /*  company data  */
            'company'
        ],
    };
</script>