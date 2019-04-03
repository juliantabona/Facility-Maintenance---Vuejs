<template>
    <Row :gutter="20">

        <Col :span="24">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="false">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <i :style="{ marginTop:'-10px', fontSize:'1.5rem' }" class="icon-organization"></i>
                    <h1 :style="{ fontSize:'1.5rem' }" class="text-dark d-inline">{{ titleName }}</h1>
                </template>

                <!-- Slot Extra functionality -->
                <template slot="extra">
                    <!-- Get the resource type button to allow user to toggle between getting jobcard/branch specific data -->
                    <allocationTypeButton></allocationTypeButton>
                </template>


            </pageToolbar>

            <Row :gutter="20">

                <Col :span="24" :style="{ padding: '0 20px' }">

                    <!-- Get the client activity cards -->
                    <activityCardWidget 
                        url="/jobcards/stats" 
                        routeName="jobcards"
                        :renameTitleList="[{search: 'Client', replace: 'Clients'}, {search: 'Supplier', replace: 'Suppliers'}]">
                    </activityCardWidget>

                </Col>
                
            </Row>

            <!-- Get the filterable jobcard client list -->
            <jobcardListWidget></jobcardListWidget>

        </Col>
    </Row>
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import allocationTypeButton from './../../../../components/_common/buttons/allocationTypeButton.vue';
    import activityCardWidget from './../../../../widgets/activity/activityCardWidget.vue';
    import jobcardListWidget from './../../../../widgets/jobcard/list/jobcardListWidget.vue';

    export default {
        components: { pageToolbar, allocationTypeButton, activityCardWidget, jobcardListWidget },
        computed:{
            titleName(){
                
                var title;

                if( this.$route.query.status == 'Supplier' ){
                    title = 'Company Suppliers';
                }else if( this.$route.query.status == 'Client' ){
                    title = 'Company Clients';
                }else{
                    title = 'Jobcards';
                }

                return title;
            }
        }
    }
</script>
