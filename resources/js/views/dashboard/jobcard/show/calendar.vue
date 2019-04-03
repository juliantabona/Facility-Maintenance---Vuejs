<template>

    <Row :gutter="20">

        <Col>

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar 
                :showBackBtn="true"
                :fallbackRoute="{ name: 'jobcards' }">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px', fontSize:'1.5rem' }" type="ios-calendar-outline"></Icon>
                    <h1 :style="{ fontSize:'1.5rem' }" class="text-dark d-inline">Calendar</h1>
                </template>

            </pageToolbar>

            <!-- Get the filterable quotation list -->
            <jobcardCalendarWidget></jobcardCalendarWidget>

        </Col>

    </Row>

</template>

<script>

    /*  Toolbars   */
    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';

    /*  Buttons  */
    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    import jobcardCalendarWidget from './../../../../widgets/calendar/jobcardCalendarWidget.vue';


    export default {
        components: { 
          pageToolbar, basicButton, jobcardCalendarWidget
        },
        data(){
            return {
                company: null
            }
        }
    };
</script>