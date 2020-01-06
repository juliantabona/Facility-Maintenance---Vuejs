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

    .detailed-stats{
        margin-top: 10px;
        position: relative;
        background: #f4faff;
        padding: 20px 10px 0px 15px;
        border-top: 2px solid #3c86d0;
    }

</style>

<template>
    
    <div>

        <Row>

            <Col :span="24" class="clearfix">

                <!-- Show More Details Button -->
                <Button type="text" style="font-size: 12px;"
                        ghost @click.native="toggleVisibility()"
                        class="text-primary pr-1 pl-1 mr-3 float-right">
                    <span>{{ visibility ? 'Hide Details' : 'View Details' }}</span>
                </Button>

            </Col>

        </Row>

        <!-- Iterate over the detailed statistics -->
        <List v-if="visibility" size="small" class="detailed-stats">
            
            <ListItem v-for="(detailed_statistic, stat_type) in stats" :key="stat_type">

                <Row class="w-100">
            
                    <!-- If the details are statistics about payment methods --> 
                    <template v-if="isPaymentMethodStats(stat_type)">

                        <Col :span="24">     
                        
                            <List size="small">
                                
                                <ListItem v-for="(payment_details, payment_name) in detailed_statistic" :key="payment_name">

                                    <Row class="w-100">

                                        <!-- Main Title -->
                                        <Col :span="24">
                                        
                                            <span class="main-heading font-weight-bold mb-2">{{ payment_name }}</span>

                                        </Col>

                                        <!-- Primary Counting Number -->
                                        <Col :span="payment_details.percentage ? 18 : 24">

                                            <span class="main-figure">{{ payment_details.count }}</span>

                                        </Col>

                                        <!-- Percentage -->
                                        <Col v-if="payment_details.percentage" span="6">

                                            <!-- Percentage Arrow -->
                                            <Icon type="md-arrow-forward" />

                                            <!-- Percentage Amount -->
                                            <span class="percentage">{{ payment_details.percentage }}%</span>

                                        </Col>

                                    </Row>

                                </ListItem>

                            </List>

                        </Col>

                    </template>

                    <template v-else>

                        <!-- Main Title -->
                        <Col :span="24">

                            <span class="main-heading font-weight-bold mb-2">{{ detailed_statistic.name }}</span>

                        </Col>

                        <!-- Primary Counting Number -->
                        <Col :span="detailed_statistic.percentage ? 18 : 24">

                            <span class="main-figure">{{ detailed_statistic.count }}</span>

                        </Col>

                        <!-- Percentage -->
                        <Col v-if="detailed_statistic.percentage" span="6">

                            <!-- Percentage Arrow -->
                            <Icon type="md-arrow-forward" :class="
                                (stat_type == 'abandoned_cart' ? 'text-danger' : '') +
                                (stat_type == 'payment_success' ? 'text-success' : '')" />

                            <!-- Percentage Amount -->
                            <span :class="'percentage'
                                    +(stat_type == 'abandoned_cart' ? ' text-danger' : '')
                                    +(stat_type == 'payment_success' ? ' text-success' : '')">
                                    
                                {{ detailed_statistic.percentage }}%

                            </span>

                        </Col>

                    </template>

                </Row>

            </ListItem>

        </List>

    </div>

</template>

<script>

    export default {
        props:{
            stats: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                visibility: false
            }
        },
        methods: {

            //  Check if the given statistic is data about payment methods
            isPaymentMethodStats(type){
                return ['popular_payment_methods', 'popular_successful_payment_methods', 'popular_failed_payment_methods'].includes(type)
            },

            //  Toggle the visibility to show or hide the details
            toggleVisibility(){
                
                this.visibility = !this.visibility

            }

        }
    };
  
</script>