<style scoped>

    .main-heading {
        display: block;
        font-size: 11px;
        margin-bottom: 16px;
        text-transform: uppercase;
        color: #6c7781;
    }

    .main-figure {
        font-size: 18px;
        font-weight: 500;
        color: #191e23;
        margin-bottom: 4px;
    }

    .sub-heading, 
    .sub-figure {
        font-size: 13px;
        color: #555d66;
        display: block;
    }

</style>

<template>
    
    <div>

        <!-- Iterate over the first level statistics -->
        <Row v-for="(main_statistic, key_1) in stats" :key="key_1"
                class="border-bottom pb-2 mb-3">
            
            <!-- Main Title -->
            <Col :span="24">
            
                <span class="main-heading font-weight-bold mb-2">{{ main_statistic.name }}</span>

            </Col>

            <!-- Primary Counting Number -->
            <Col :span="main_statistic.percentage ? 18 : 24">

                <span class="main-figure">{{ main_statistic.count }}</span>

            </Col>

            <!-- Percentage -->
            <Col v-if="main_statistic.percentage" span="6">

                <!-- Percentage Arrow -->
                <Icon type="md-arrow-forward" :class="
                    (key_1 == 'abandoned_cart' ? 'text-danger' : '') +
                    (key_1 == 'payment_success' ? 'text-success' : '')" />

                <!-- Percentage Amount -->
                <span :class="'percentage'
                        +(key_1 == 'abandoned_cart' ? ' text-danger' : '')
                        +(key_1 == 'payment_success' ? ' text-success' : '')">
                        
                    {{ main_statistic.percentage }}%

                </span>

            </Col>

            <!-- Percentage -->
            <Col v-if="main_statistic.details" span="24">

                <detailedStats :stats="main_statistic.details"></detailedStats>

            </Col>

        </Row>

    </div>

</template>

<script>

    import detailedStats from './detailedStats.vue'

    export default {
        props:{
            stats: {
                type: Object,
                default: null
            }
        },
        components: { detailedStats },
        data(){
            return {
                
            }
        }
    };
  
</script>