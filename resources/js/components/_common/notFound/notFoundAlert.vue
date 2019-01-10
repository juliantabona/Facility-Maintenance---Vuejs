<template>

    <!-- Jobcard not found alert -->
    <Alert type="warning" show-icon class="mt-2 mb-2">
        {{ message }}
    </Alert>

</template>
<script>
    export default {
        props: {
            message: {   //  e.g) No Jobcad Found, No Company Found, e.t.c
                type: String,
                default: 'Not Found'
            }       
        },
    };
</script>