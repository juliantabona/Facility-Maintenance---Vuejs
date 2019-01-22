<style scoped>

    .el-form-item >>> .el-form-item__label{
        margin:0 !important;
        padding:0 !important;
        line-height: 24px !important;
    }

    .form-label{
        font-size:14px;
    }

</style>

<template>

    <Card :bordered="false">
        <h3 slot="title">Profile Settings</h3>
        <Row>
            <Col :span="24" class="mt-3 mb-2">
                <Alert>Provide as much or as little information as you’d like. We will never share or sell individual personal information or personally identifiable details.</Alert>
            </Col>
            <Col :span="24">
                <el-form label-position="top" label-width="100px" :model="formData">
                    <Row :gutter="20" class="mb-1">

                        <Col :span="12">
                            <el-form-item label="First Name:" prop="first_name" class="mb-2">
                                <el-input v-model="formData.title" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <el-form-item label="Last Name:" prop="last_name" class="mb-2">
                                <el-input v-model="formData.title" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>
                    <Row :gutter="20" class="mb-1">

                        <Col :span="12">
                            <!-- Date Of Birth -->
                            <el-form-item label="Date Of Birth" prop="date_of_birth" class="mb-2">
                                <el-date-picker v-model="formData.date_of_birth" type="datetime" placeholder="Date Of Birth" style="width:100%" 
                                    format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss">
                                </el-date-picker>
                            </el-form-item>
                        </Col>

                        <Col :span="12">
                            <el-form-item label="Email:" prop="email" class="mb-2">
                                <el-input v-model="formData.title" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>
                    <Row :gutter="20" class="mb-1">

                        <Col :span="12">
                            <!-- Calling Codes Selector -->
                            <span class="form-label mb-1 d-block">Phone</span>
                            <phoneInput class="mb-2"
                                :localPhoneNumber="formData.phoneNumber"
                                :selectedCallingCode="formData.callingCode"
                                @updated="updatePhoneChanges($event)">
                            </phoneInput>
                        </Col>

                        <Col :span="12">
                            <!-- Country Selector -->
                            <span class="form-label mb-1 d-block">Country</span>
                            <countrySelector
                                :selectedCountry="formData.country"
                                @updated="updateCountryChanges($event)">
                            </countrySelector>
                        </Col>

                    </Row>
                    <Row :gutter="20" class="mb-1">

                        <Col :span="12">
                            <!-- Provience Selector -->
                            <span class="form-label mb-1 d-block">State/Provience/District</span>
                            <provinceSelector
                                :selectedCountry="formData.country"
                                :selectedProvience="formData.provience"
                                @updated="updateProvienceChanges($event)">
                            </provinceSelector>
                        </Col>

                        <Col :span="12">
                            <!-- Cities Selector -->
                            <span class="form-label mb-1 d-block">City/Town</span>
                            <citySelector
                                :selectedCountry="formData.country"
                                :selectedCity="formData.city"
                                @updated="updateCityChanges($event)">
                            </citySelector>
                        </Col>
                        
                    </Row>
                    <Row>
                        <Col :span="24">
                            <hr class="mt-2" />
                            <!-- Save Changes Button -->
                            <Button class="float-right mt-2" type="success" size="large">
                                <span>Save Changes</span>
                            </Button>
                        </Col>
                    </Row>
                </el-form>
            </Col>
        </Row>
    </Card>

</template>

<script>
    import Loader from './../../components/_common/loader/Loader.vue'; 
    import phoneInput from './phoneInput.vue'; 
    import provinceSelector from './provinceSelector.vue'; 
    import citySelector from './citySelector.vue'; 
    import countrySelector from './countrySelector.vue'; 

    export default {
        components: { 
            Loader, phoneInput, countrySelector, provinceSelector, citySelector
        },
        data(){
            return {
                formData: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    callingCode: '',
                    phoneNumber: '',
                    country: '',
                    provience: '',
                    city: '',
                    postal_or_zipcode: '',
                    date_of_birth: '',

                },
                fetchedCountries: [],
                fetchedStates: []
            }
        },
        methods: {
            updatePhoneChanges(newVal){
                this.formData.phone = newVal;
            },
            updateCountryChanges(newVal){
                this.formData.country = newVal;
            },
            updateProvienceChanges(newVal){
                this.formData.provience = newVal;
            },
            updateCityChanges(newVal){
                this.formData.city = newVal;
            }
        }
    };
  
</script>