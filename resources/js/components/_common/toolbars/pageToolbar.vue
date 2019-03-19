<template>
    
    
    <Row :gutter="20" class="border-bottom mb-3 pb-3">
        <Col :span="4">
            <!-- Go Back Button -->
            <Button v-if="showBackBtn" type="primary" @click.native="handleBack()">
                <Icon type="md-arrow-back" :size="16"></Icon>
                <span>Back</span>
            </Button>
        </Col>
        <Col :span="showBackBtn ? '12': '8'" :offset="showBackBtn ? '0': '8'">
            <div v-if="$slots['title']" class="text-center" :style="{ boxShadow: 'inset 0px 0px 5px #bdc9d4', borderRadius: '30px', padding: '10px 5px', marginTop: '-5px' }">
                <!-- Main Title -->
                <slot name="title"></slot>
            </div>
        </Col>
        <Col :span="8">
            <!-- Extra e.g) Buttons -->
            <slot name="extra"></slot>
        </Col>
    </Row>

</template>
<script>
    export default {
    props:{
        showBackBtn: {
            type: Boolean,
            default: true    
        },
        fallbackRoute: {
            type: Object,
            default: null
        }
    },
    data () {
        return {
            fromRoute: null
        }
    },

    beforeRouteEnter (to, from, next) {
        next(vm => {
            vm.fromRoute = from;
        })
    },

    methods: {
        /**
        * Handle Back
        * @desc Extends default router back functionality
        * @param {string} fallback - The fallback path if there is no history to use with $router.back(). This is usually the case if the page was visited directly or from another site
        **/
        handleBack () {
            if (!(this.fromRoute || {}).name) {
                this.$router.push(this.fallbackRoute);
            } else {
                this.$router.back();
            }
        }
    }
    };
</script>