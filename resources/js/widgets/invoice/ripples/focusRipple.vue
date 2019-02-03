<style scoped>

    .circle-ripple {
        width: 1px;
        height: 1px;
        border-radius: 50%;
        position:absolute;
        top: 50%;
        right: 50%;
        transform: translateX(-50%);
        transform: translateY(-50%);
        z-index:1;
    }

    /*   GREEN COLOR RIPPLES   */
    .green-circle-ripple {
        animation: greenRipple 3s linear infinite;
    }

    @-webkit-keyframes greenRipple {
        0% {
            box-shadow: 0 0 0 0 rgba(101, 255, 120, 0.3),
                        0 0 0 0 rgba(101, 255, 120, 0.3);
        }
        50% {
            box-shadow: 0 0 0 7em rgba(101, 255, 120, 0),
                        0 0 0 3.5em rgba(101, 255, 120, 0.3);
        }
        100% {
            box-shadow: 0 0 0 7em rgba(101, 255, 120, 0),
                        0 0 0 5em rgba(101, 255, 120, 0);
        }
    }

    @keyframes greenRipple {
        0% {
            box-shadow: 0 0 0 0 rgba(101, 255, 120, 0.3),
                        0 0 0 0 rgba(101, 255, 120, 0.3);
        }
        50% {
            box-shadow: 0 0 0 7em rgba(101, 255, 120, 0),
                        0 0 0 3.5em rgba(101, 255, 120, 0.3);
        }
        100% {
            box-shadow: 0 0 0 7em rgba(101, 255, 120, 0),
                        0 0 0 5em rgba(101, 255, 120, 0);
        }
    }

    /*   BLUE COLOR RIPPLES   */
    .blue-circle-ripple {
        animation: blueRipple 3s linear infinite;
    }

    @-webkit-keyframes blueRipple {
        0% {
            box-shadow: 0 0 0 0 rgba(101, 155, 255, 0.3),
                        0 0 0 0 rgba(101, 155, 255, 0.3);
        }
        50% {
            box-shadow: 0 0 0 7em rgba(101, 155, 255, 0),
                        0 0 0 3.5em rgba(101, 155, 255, 0.3);
        }
        100% {
            box-shadow: 0 0 0 7em rgba(101, 155, 255, 0),
                        0 0 0 5em rgba(101, 155, 255, 0);
        }
    }

    @keyframes blueRipple {
        0% {
            box-shadow: 0 0 0 0 rgba(101, 155, 255, 0.3),
                        0 0 0 0 rgba(101, 155, 255, 0.3);
        }
        50% {
            box-shadow: 0 0 0 7em rgba(101, 155, 255, 0),
                        0 0 0 3.5em rgba(101, 155, 255, 0.3);
        }
        100% {
            box-shadow: 0 0 0 7em rgba(101, 155, 255, 0),
                        0 0 0 5em rgba(101, 155, 255, 0);
        }
    }

</style>


<template>

    <!-- Ripple Switch  -->
    <div :style="{ position:'relative' }">

        <!-- Ripple Wave  -->
        <div v-if="ripple" :class="'circle-ripple'+ (color == 'green' ? ' green-circle-ripple' : ' blue-circle-ripple' )" 
            :style="waveStyle">
        </div>

        <div :style="{ position:'relative', zIndex:'2' }">
            
            <!-- Content  -->
            <slot></slot>

        </div>
    </div>
    
</template>

<script type="text/javascript">

    export default {
        props: {
            color: {
                type: String,
                default: 'green'
            },
            waveStyle: {
                type: Object,
                default: null
            },
            ripple: {
                type: Boolean,
                default: false
            }
        }
    }
    
</script>
