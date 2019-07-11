<style scoped>

    .vb >>> .vb-dragger {
        z-index: 1000;
        width: 10px;
        right: 0;
    }

    .vb >>> .vb-dragger > .vb-dragger-styler {
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: rotate3d(0,0,0,0);
        transform: rotate3d(0,0,0,0);
        -webkit-transition:
            background-color 100ms ease-out,
            margin 100ms ease-out,
            height 100ms ease-out;
        transition:
            background-color 100ms ease-out,
            margin 100ms ease-out,
            height 100ms ease-out;
        background-color: rgba(48, 121, 244,.1);
        margin: 5px 5px 5px 0;
        border-radius: 20px;
        height: calc(100% - 10px);
        display: block;
    }

    .vb.vb-scrolling-phantom >>> .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.3);
    }

    .vb >>> .vb-dragger:hover > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
        margin: 0px;
        height: 100%;
    }

    .vb.vb-dragging >>> .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
        margin: 0px;
        height: 100%;
    }

    .vb.vb-dragging-phantom >>> .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
    }

</style>

<template>

    <div v-bar>
        <slot></slot>
    </div>

</template>

<script>

    export default {
        props: {
            customClass:{
                type: Object,
                default: function(){ 
                    return {} 
                }
            }
        },
        data(){
            return { 

            }
        }
    };
</script>