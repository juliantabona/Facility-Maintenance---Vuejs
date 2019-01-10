<style scoped>
    .company-logo{
        width: 80px !important;
        height: 80px;
    }
</style>

<template>
    
    <!-- Company Logo -->
    <span>
        <a :href="company.logo_url">
            <img class="company-logo img-thumbnail mb-2 p-2 rounded rounded-circle" 
                :src="company.logo_url" />
        </a>
    </span>

</template>
<script>
    export default {
        props: [
            /*  Company data  */
            'company'
        ],
    };
</script>