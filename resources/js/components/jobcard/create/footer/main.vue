<template>

    <Row>

        <!-- Jobcard create button -->

        <jobcardCreateButton class="float-right mr-1" @runCreate="$emit('runCreate')"></jobcardCreateButton>

    </Row>

</template>
<script>
    import jobcardCreateButton from './../../../_common/button/create.vue';

    export default {
        props: [
            /*  Form data  */
            'rules',
            'formData'
        ],
        components: { 
            jobcardCreateButton 
        }
    };
</script>