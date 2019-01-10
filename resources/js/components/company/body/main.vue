<template>

    <Row :gutter="20">
        
        <!-- Company logo -->
        <Col v-if="showLogo" span="24" class="d-block mb-1">
            <companyBodyLogo v-bind="$props"></companyBodyLogo>
        </Col>

        <!-- Company name, address, city -->
        <Col v-if="showName" span="24" class="d-block mb-1">
            <companyBodyName v-bind="$props"></companyBodyName>
        </Col>
        <Col v-if="showAddress" span="24" class="d-block mb-1">
            <companyBodyAddress v-bind="$props"></companyBodyAddress>
        </Col>
        <Col v-if="showCityOrTown" span="24" class="d-block mb-1">
            <companyBodyCityOrTown v-bind="$props"></companyBodyCityOrTown>
        </Col>

        <Col span="24" v-if="showLogo || showName || showAddress || showCityOrTown">
            <Divider dashed class="mt-2 mb-2" />
        </Col>

        <!-- Company phone, email -->
        <Col v-if="showPhone" span="24" class="d-block mb-1">
            <companyBodyPhone v-bind="$props"></companyBodyPhone>
        </Col>
        <Col v-if="showEmail" span="24" class="d-block mb-1">
            <companyBodyEmail v-bind="$props"></companyBodyEmail>
        </Col>

        <Col span="24" v-if="showPhone || showEmail">
            <Divider dashed class="mt-2 mb-2" />
        </Col>

        <!-- Company contacts -->        
        <Col v-if="showContacts" span="24">
            <companyContactsTag v-bind="$props" class="d-block float-right"></companyContactsTag>
        </Col>

        <Col span="24" v-if="showContacts">
            <Divider dashed class="mt-2 mb-2" />
        </Col>

    </Row>

</template>
<script>
    import companyBodyLogo from './logo.vue';

    import companyBodyName from './name.vue';
    import companyBodyAddress from './address.vue';
    import companyBodyCityOrTown from './cityOrTown.vue';

    import companyBodyPhone from './phone.vue';
    import companyBodyEmail from './email.vue';
    import companyContactsTag from './contactsTag.vue';

    export default {
        props: [
            /*  Company data  */
            'company',
            /*  Company body related properties  */
            'showLogo', 'showName', 'showAddress', 'showCityOrTown', 'showPhone', 'showEmail', 'showContacts'
            
        ],
        components: { 
            companyBodyLogo, companyBodyName, companyBodyAddress, companyBodyCityOrTown,
            companyBodyPhone, companyBodyEmail, companyContactsTag
        }
    };
</script>