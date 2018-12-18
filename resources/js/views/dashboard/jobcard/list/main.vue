<template>
    <Row :gutter="20">
        <Col :span="24">
            <div class="float-right mt-2 mr-4">
                <strong class="mr-2">Show:</strong>
                <Select v-model="modelType" style="width:200px">
                    <Option v-for="item in modelOptions" :value="item.value" :key="item.value">{{ item.label }}</Option>
                </Select>
            </div>
        </Col>
        <Col :span="24">
            <jobcardList :lifecycleStep="lifecycleStep" :modelType="modelType"></jobcardList>
        </Col>
    </Row>
</template>
<script type="text/javascript">
    import jobcardList from './../../../../components/jobcard/jobcard-list.vue';
    export default {
        components: { jobcardList },
        data() {
            return {
                modelType: 'company',
                modelOptions: [
                    {
                        value: 'company',
                        label: 'Company Jobcards'
                    },
                    {
                        value: 'branch',
                        label: 'Branch Jobcards'
                    },
                ],
                lifecycleStep: this.$route.query.step
            }
        },
        watch: {
            '$route.query.step'() {
                this.lifecycleStep = this.$route.query.step
            }
        }
    }
</script>
