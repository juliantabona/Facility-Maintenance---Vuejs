<style scoped>

    svg {
        opacity:1;
        width: 80px;
        height: 80px;
        margin: 20px;
        display: inline-block;
        padding:5px;
        border-radius:50%;
        background: linear-gradient(233deg, #0af3b7, #c311bc, #d70d53);
        background-size: 600% 600%;
        -webkit-animation: pulse 5s ease infinite;
        -moz-animation: pulse 5s ease infinite;
        animation: pulse 5s ease infinite;
    }

    @-webkit-keyframes pulse {
        0%{
            background-position:0% 50%
        }
        50%{
            background-position:100% 50%
        }
        100%{
            background-position:0% 50%
        }
    }
    @-moz-keyframes pulse {
        0%{background-position:0% 50%}
        50%{background-position:100% 50%}
        100%{background-position:0% 50%}
    }
    @keyframes pulse { 
        0%{background-position:0% 50%}
        50%{background-position:100% 50%}
        100%{background-position:0% 50%}
    }

</style>

<template>

    <!-- Loader -->
    <svg version="1.1" id="L2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
        <circle fill="none" stroke="#fff" stroke-width="2" stroke-miterlimit="10" cx="50" cy="50" r="48"/>
        <line fill="none" stroke-linecap="round" stroke="#fff" stroke-width="2" stroke-miterlimit="10" x1="50" y1="50" x2="85" y2="50.5">
        <animateTransform 
            attributeName="transform" 
            dur="1s"
            type="rotate"
            from="0 50 50"
            to="360 50 50"
            repeatCount="indefinite" />
        </line>
        <line fill="none" stroke-linecap="round" stroke="#fff" stroke-width="2" stroke-miterlimit="10" x1="50" y1="50" x2="49.5" y2="74">
        <animateTransform 
            attributeName="transform" 
            dur="15s"
            type="rotate"
            from="0 50 50"
            to="360 50 50"
            repeatCount="indefinite" />
        </line>
    </svg>

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
