<template>

    <Row class="border-bottom mb-3 pb-3">

        <!-- Company Reference Number  -->
        <Col span="5">
            <h2 class="text-dark">Company #{{ localCompany.id }}</h2>
        </Col>

        <!-- Company Status  -->
        <Col span="3">
            <h6 class="text-secondary">Status</h6>
            <h5><companyTag :company="company"></companyTag></h5>   
        </Col>

        <!-- Company Client  -->
        <Col span="6">
            <h6 class="text-secondary">Name</h6>
            <h5>{{ customerName }}</h5>            
        </Col>

        <!-- Company Type e.g) Private, Goverment, Parastatal  -->
        <Col span="5">
            <h6 class="text-secondary">Type</h6>
            <h5>{{ localCompany.type }}</h5>            
        </Col>

        <!-- Company Due Date  -->
        <Col span="4">
            <h6 class="text-secondary">Created</h6>
            <h5>{{ localCompany.created_at | moment("from", "now")  }}</h5>            
        </Col>

        <!-- Company Menu -->
        <Col span="1">
            <menuToggle :companyId="localCompany.id" :editMode="localEditMode" @toggleEditMode="$emit('toggleEditMode', $event)"></menuToggle>
        </Col>
    </Row>

</template>
<script type="text/javascript">

    import companyTag from './../../../components/_common/statuses/CompanyStatusTag.vue';  
    import menuToggle from './../../../components/_common/dropdowns/companyMenuDropdown.vue';

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            company: {
                type: Object,
                default: null
            }
        },
        components: { companyTag, menuToggle },
        data() {
            return {
                localCompany: this.company,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the company
            company: {
                handler: function (val, oldVal) {

                    //  Update the local company value
                    this.localCompany = val;

                },
                deep: true
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    //  Update the edit mode value
                    this.localEditMode = val;
                }
            }
        },
        computed:{
            customerName: function(){
                if(this.localCompany.model_type == 'user'){
                    return this.localCompany.full_name;
                }else if(this.localCompany.model_type == 'company'){
                    return this.localCompany.name;
                }
            }
        },
        methods: {
            
        },
        created () {
            
        }
    }
</script>
