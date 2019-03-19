<template>
    <Row :gutter="20">

        <Col :span="24">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="false">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon type="ios-contact-outline":style="{ marginTop:'-10px', fontSize:'1.5rem' }" />
                    <h1 :style="{ fontSize:'1.5rem' }" class="text-dark d-inline">{{ titleName }}</h1>
                </template>

                <!-- Slot Extra functionality -->
                <template slot="extra">
                    <!-- Get the resource type button to allow user to toggle between getting company/branch specific data -->
                    <allocationTypeButton></allocationTypeButton>
                </template>


            </pageToolbar>

            <Row :gutter="20">

                <Col :span="24" :style="{ padding: '0 20px' }">

                    <!-- Get the client activity cards -->
                    <activityCardWidget 
                        url="/users/stats" 
                        routeName="users"
                        :renameTitleList="[{search: 'Client', replace: 'Clients'}, {search: 'Supplier', replace: 'Suppliers'}]">
                    </activityCardWidget>

                </Col>
                
            </Row>

            <!-- Get the filterable user client list -->
            <userListWidget></userListWidget>

        </Col>
    </Row>
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import allocationTypeButton from './../../../../components/_common/buttons/allocationTypeButton.vue';
    import activityCardWidget from './../../../../widgets/activity/activityCardWidget.vue';
    import userListWidget from './../../../../widgets/user/list/userListWidget.vue';

    export default {
        components: { pageToolbar, allocationTypeButton, activityCardWidget, userListWidget },
        computed:{
            titleName(){
                
                var title;

                if( this.$route.query.status == 'Supplier' ){
                    title = 'Individual Suppliers';
                }else if( this.$route.query.status == 'Client' ){
                    title = 'Individual Clients';
                }else if( this.$route.query.status == 'Staff' ){
                    title = 'Staff Members';
                }else{
                    title = 'Individuals';
                }

                return title;
            }
        }
    }
</script>
