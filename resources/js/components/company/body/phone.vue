<template>
    
    <!-- Company Title -->
    <span class="d-inline-block cut-text"><strong>Phone: </strong>
        {{ company.phone_ext ? '+'+company.phone_ext+'-':'___-' }}
        {{ company.phone_num ? company.phone_num:'____' }}
    </span>

</template>
<script>
    export default {
        props: [
            /*  company data  */
            'company'
        ],
    };
</script>
