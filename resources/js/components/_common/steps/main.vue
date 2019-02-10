<style scoped>

    .invoice-steps {
        display: block;
        padding: 0px 10px;
        margin-bottom: 12px;
        border-radius: 8px;
        border: 1px solid #b2c2cd;
        background-color: #fff;
    }

    .invoice-steps:nth-child(1) {
        position: relative;
        z-index: 3;
    }

    .invoice-steps:nth-child(2) {
        position: relative;
        z-index: 2;
    }

    .invoice-steps:nth-child(3) {
        position: relative;
        z-index: 1;
    }

    .invoice-steps.is-highlighted {
        box-shadow: 0 8px 32px rgba(77,101,117,0.35);
        border-radius: 12px;
        border-color: transparent;
    }

    .invoice-steps.invoice-hide-step{
        margin-top: -116px;
    }

    .invoice-steps.is-highlighted:hover ~ .invoice-hide-step{
        margin-top: 0;
    }

    .invoice-steps.disabled {
        opacity:0.4;
    }

    .invoice-vertical-line{
        margin: -13px 0 0 39px;
        border: 2px solid #b2c2cd;
        height: 18px;
        width: 0;
    }

    .invoice-header {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fcfbe3;
        margin: -17px -27px 15px !important;
        border-radius: 10px 10px 0 0;
        padding: 15px;
    }

    .invoice-step-badge {
        color: #cdd1d3;
        float: left;
        font-size: 25px;
        line-height: 50px;
        margin-right: 18px;
        text-align: center;
        border-width: 2px;
    }

    .invoice-step-badge-inner {
        background: #fff;
        border: 2px solid #136acd;
        border-radius: 50%;
        color: #136acd;
        width: 50px;
        height: 50px;
        line-height: 46px;
        box-sizing: border-box;
    }

</style>

<template>

    <div>
        <!-- Card  -->
        <Card :bordered="false" :class="'invoice-steps is-highlighted' + (disabled ? ' disabled': '')">
            
            <!-- Header Content  -->
            <Row v-if="showHeader" :gutter="20" class="invoice-header">
                <Col span="24">
                    <!-- Content  -->
                    <slot name="header"></slot>
                </Col>
            </Row>

            <!-- Center Content  -->
            <Row :gutter="20">

                <!-- Left Content  -->
                <Col :span="leftWidth">
                    
                    <!-- Step Number  -->
                    <div class="invoice-step-badge">
                        <div class="invoice-step-badge-inner">{{ stageNumber }}</div>
                    </div>

                    <!-- Content  -->
                    <slot name="leftContent"></slot>

                </Col>

                <!-- Right Content  -->
                <Col :span="rightWidth">

                    <!-- Content  -->
                    <slot name="rightContent"></slot>

                </Col>
            </Row>

            <!-- Extra Content  -->
            <slot name="extraContent"></slot>

        </Card>

        <!-- Vertical Line  -->
        <div v-if="showVerticalLine" class="invoice-vertical-line"></div>

    </div>
        
</template>

<script type="text/javascript">

    export default {
        props: {
            showHeader: {
                type: Boolean,
                default: false    
            },
            showVerticalLine: {
                type: Boolean,
                default: false    
            },
            stageNumber: {
                type: Number,
                default: 1 
            },
            disabled: {
                type: Boolean,
                default: false     
            },
            leftWidth: {
                type: Number,
                default: 12 
            },
            rightWidth: {
                type: Number,
                default: 12
            }
        }
    }

</script>
