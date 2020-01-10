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

    <List>

        <!-- Iterate over the first level statistics -->
        <ListItem v-for="(main_statistic, stat_type) in stats" :key="stat_type">

            <Row class="w-100">
                
                <!-- If the details are statistics about session time --> 
                <template v-if="isSessionTimeStats(stat_type)">

                    <Col :span="24">     
                    
                        <List size="small">
                            
                            <ListItem v-for="(session_time, session_time_type) in main_statistic" :key="session_time_type">

                                <Row class="w-100">

                                    <!-- Main Title -->
                                    <Col span="16">
                                    
                                        <span class="main-heading font-weight-bold mb-2">{{ session_time.name }}</span>

                                    </Col>

                                    <Col span="8">
                                    
                                        <!-- Time -->
                                        <span class="text-muted">
                                            <span v-if="session_time.minutes">{{ session_time.minutes }} mins </span>
                                            <span v-if="session_time.seconds">{{ session_time.seconds }} sec</span>
                                        </span>

                                    </Col>

                                </Row>

                            </ListItem>

                        </List>

                    </Col>

                </template>

                <template v-else>

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
                            (stat_type == 'abandoned_cart' ? 'text-danger' : '') +
                            (stat_type == 'payment_success' ? 'text-success' : '')" />

                        <!-- Percentage Amount -->
                        <span :class="'percentage'
                                +(stat_type == 'abandoned_cart' ? ' text-danger' : '')
                                +(stat_type == 'payment_success' ? ' text-success' : '')">
                                
                            {{ main_statistic.percentage }}%

                        </span>

                    </Col>

                    <!-- Percentage -->
                    <Col v-if="main_statistic.details" span="24">

                        <detailedStats :stats="main_statistic.details"></detailedStats>

                    </Col>

                </template>

            </Row>


        </ListItem>

    </List>

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
        },
        methods: {

            //  Check if the given statistic is data about session time
            isSessionTimeStats(type){
                return ['session_time'].includes(type)
            }

        }
    };
  
</script>