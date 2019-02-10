<style scoped>

    .animate-opacity {
        animation: opacityAnimation 2s linear infinite;
    }

    @keyframes opacityAnimation {
        50% {
            opacity: 0;
        }
    }

</style>

<template>

    <!-- Loader -->
    <div v-if="loading" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">{{ msg }}</div>

</template>

<script type="text/javascript">

    export default {
        props: {
            loading: {
                type: Boolean,
                default: false
            },
            msg: {
                type: String,
                default: 'Loading, please wait...'
            }
        }
    }

</script>
