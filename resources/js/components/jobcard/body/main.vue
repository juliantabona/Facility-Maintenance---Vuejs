<template>

    <Row>
        <!-- Jobcard title, description, deadline -->
        <Col v-if="showTitle" span="24" class="d-block mb-2">
            <jobcardBodyTitle v-bind="$props"></jobcardBodyTitle>
        </Col>
        <Col v-if="showDescription" span="24" class="d-block mb-2">
            <jobcardBodyDescription v-bind="$props"></jobcardBodyDescription>
        </Col>
        <Col v-if="showDeadline" span="24" class="d-block mb-2">
            <jobcardBodyDeadline v-bind="$props"></jobcardBodyDeadline>
        </Col>

        <!-- Jobcard start date, end date -->
        <Col v-if="showStartDate" span="12" class="d-block mb-2">
            <jobcardBodyStartDate v-bind="$props"></jobcardBodyStartDate>
        </Col>
        <Col v-if="showEndDate" span="12" class="d-block mb-2">
            <jobcardBodyEndDate v-bind="$props"></jobcardBodyEndDate>
        </Col>

        <Col span="24" v-if="showTitle || showDescription || showDeadline  || showStartDate || showEndDate">
            <Divider dashed class="mt-3 mb-3" />
        </Col>

        <!-- Jobcard priority, category and costcenters -->
        <Col v-if="showPriority" span="12" class="d-block mb-1">
            <jobcardBodyPriority v-bind="$props"></jobcardBodyPriority>
        </Col>
        <Col v-if="showCategory" span="12" class="d-block mb-1">
            <jobcardBodyCategory v-bind="$props"></jobcardBodyCategory>
        </Col>
        <Col v-if="showCostCenters" span="24" class="d-block mb-1">
            <jobcardBodyCostCenter v-bind="$props"></jobcardBodyCostCenter>
        </Col>

        <Col span="24" v-if="showPriority || showCategory || showCostCenters">
            <Divider dashed class="mt-3 mb-3" />
        </Col>

        <!-- Jobcard created by, created date -->
        <Col v-if="showCreatedBy" span="12" class="d-block mb-1">
            <jobcardBodyCreatedBy v-bind="$props"></jobcardBodyCreatedBy>
        </Col>
        <Col v-if="showCreatedByDate" span="12" class="d-block mb-1">
            <jobcardBodyCreatedDate v-bind="$props"></jobcardBodyCreatedDate>
        </Col>

        <!-- Jobcard authourized by authourized date -->
        <Col v-if="showAuthourizedBy" span="12" class="d-block mb-1">
            <jobcardBodyAuthorizedBy v-bind="$props"></jobcardBodyAuthorizedBy>
        </Col>
        <Col v-if="showAuthourizedByDate" span="12" class="d-block mb-1">
            <jobcardBodyAuthorizedDate v-bind="$props"></jobcardBodyAuthorizedDate>
        </Col>

        <Col span="24" v-if="showCreatedBy || showCreatedByDate || showAuthourizedBy || showAuthourizedByDate">
            <Divider dashed class="mt-3 mb-3" />
        </Col>

        <Col v-if="showResourceTags" span="24" class="d-block mb-2">
            <!-- Jobcard resources -->
            <jobcardBodyAttachmentsTag v-bind="$props" class="float-right"></jobcardBodyAttachmentsTag>
            <jobcardBodyQuotationTag v-bind="$props" class="float-right"></jobcardBodyQuotationTag>
            <jobcardBodyAssetsTag v-bind="$props" class="float-right"></jobcardBodyAssetsTag>
            <jobcardBodyLabourTag v-bind="$props" class="float-right"></jobcardBodyLabourTag>
            <jobcardBodySuppliersTag v-bind="$props" class="float-right"></jobcardBodySuppliersTag>
        </Col>

        <Col span="24" v-if="showResourceTags">
            <Divider dashed class="mt-3 mb-3" />
        </Col>
        
    </Row>

</template>
<script>
    import jobcardBodyTitle from './title.vue';
    import jobcardBodyDescription from './description.vue';
    import jobcardBodyDeadline from './deadline.vue';
    import jobcardBodyStartDate from './startDate.vue';
    import jobcardBodyEndDate from './endDate.vue';
    
    import jobcardBodyPriority from './priority.vue';
    import jobcardBodyCategory from './category.vue';
    import jobcardBodyCostCenter from './costCenter.vue';

    import jobcardBodyCreatedBy from './createdBy.vue';
    import jobcardBodyCreatedDate from './createdDate.vue';

    import jobcardBodyAuthorizedBy from './authourizedBy.vue';
    import jobcardBodyAuthorizedDate from './authourizedDate.vue';

    import jobcardBodySuppliersTag from './suppliersTag.vue';
    import jobcardBodyAssetsTag from './assetsTag.vue';
    import jobcardBodyLabourTag from './labourTag.vue';
    import jobcardBodyQuotationTag from './quotationTag.vue';
    import jobcardBodyAttachmentsTag from './attachmentsTag.vue';

    export default {
        props: [
            /*  Jobcard data  */
            'jobcard',
            /*  Jobcard body related properties  */
            'showTitle', 'showDescription', 'showDeadline', 'showStartDate', 'showEndDate', 'showPriority', 'showCategory',
            'showCostCenters', 'showCreatedBy', 'showCreatedByDate', 'showAuthourizedBy', 'showAuthourizedByDate',
            'showResourceTags',
            
        ],
        components: { 
            jobcardBodyTitle, jobcardBodyDescription, jobcardBodyDeadline, jobcardBodyStartDate,
            jobcardBodyEndDate, jobcardBodyPriority, jobcardBodyCategory, jobcardBodyCostCenter,
            jobcardBodyCreatedBy, jobcardBodyCreatedDate, jobcardBodyAuthorizedBy, jobcardBodyAuthorizedDate,
            jobcardBodySuppliersTag, jobcardBodyAssetsTag, jobcardBodyLabourTag, jobcardBodyQuotationTag, 
            jobcardBodyAttachmentsTag
        }
    };
</script>