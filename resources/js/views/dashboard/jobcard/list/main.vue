<template>
    <Row :gutter="20">
        <Col :span="24">

            <Card class="quotation-summary-widget" :style="{ width: '100%' }">

                <div slot="title">
                    <h5><Icon type="ios-copy-outline" :size="24" class="mr-2"/><span>Jobcards</span></h5>
                </div>

                <div slot="extra">

                    <Button type="primary" size="small" class="d-inline-block mr-1">
                        <span>Send</span>
                        <Icon type="ios-send-outline" :size="20" style="margin-top: -4px;"/>
                    </Button>

                    <Dropdown trigger="click" class="d-inline-block mt-1 mr-2">
                        <Button type="primary" size="small">
                            <Icon type="ios-print-outline" :size="16" style="margin-top: -4px;"/>
                            <span>Print / Download</span>
                        </Button>
                        <DropdownMenu slot="list">
                            <DropdownItem>Export As PDF</DropdownItem>
                            <DropdownItem>Export As XML</DropdownItem>
                        </DropdownMenu>
                    </Dropdown>

                    <div class="d-inline-block">
                        <strong class="mr-2">Show:</strong>
                        <Select v-model="modelType" style="width:200px">
                            <Option v-for="item in modelOptions" :value="item.value" :key="item.value">{{ item.label }}</Option>
                        </Select>
                    </div>

                </div>


                <jobcardList :lifecycleStep="lifecycleStep" :modelType="modelType"></jobcardList>


            </Card>

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
