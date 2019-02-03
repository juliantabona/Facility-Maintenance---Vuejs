<template>

    <div>
        
        <!-- Loader for when loading the company information -->
        <Loader v-if="isLoadingCompanyInfo" :loading="isLoadingCompanyInfo" type="text" class="float-right text-right" :style="{ marginTop:'40px' }">Loading Company Details...</Loader>
        
        <div v-if="localCompany">

            <div class="clearfix"></div>

            <!-- Company Name -->
            <p v-if="!localEditMode" class="mt-3 text-dark text-right"><strong>{{ localCompany.name || '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="Company name" v-model="localCompany.name" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            <div class="clearfix"></div>

            <!-- Company Email -->
            <p v-if="!localEditMode" class="text-right">{{ localCompany.email || '___' }}</p>
            <el-input v-if="localEditMode" placeholder="Company email" v-model="localCompany.email" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>

            <div class="clearfix"></div>

            <!-- Company Phone -->
            <p v-if="!localEditMode" class="text-right">{{ localCompany.phone || '___' }}</p>
            <el-input v-if="localEditMode" placeholder="Company tel/phone" v-model="localCompany.phone" size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"></el-input>
            
            <div class="clearfix"> <br> </div>

            <!-- Company Additional Fields -->
            <p v-if="!localEditMode" v-for="(field, i) in localCompany.additionalFields" :key="i" class="text-right">
                {{ field.value }}
            </p>
            <el-input v-if="localEditMode" v-for="(field, i) in localCompany.additionalFields" :key="i" 
                    size="mini" class="mb-1" :style="{ maxWidth:'250px', float:'right' }"
                    :placeholder="'Company details ' + i" v-model="localCompany.additionalFields[i].value"></el-input>
            </el-input>
        </div>


        <!-- No Company Information Alert -->
        <div v-if="!company && !isLoadingCompanyInfo">
            <Alert :style="{maxWidth: '250px'}" type="warning">
                No company details
            </Alert>
        </div>


    </div>

</template>


<script type="text/javascript">


    export default {
        props: {
            company: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                localCompany: this.company,
                localEditMode: this.editMode,
                isLoadingCompanyInfo: false,
            }
        },
        watch: {

            //  Watch for changes on the company
            company: {
                handler: function (val, oldVal) {

                    //  Update the company information
                    this.localCompany = val;

                },
                deep: true
            },
        
            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    this.localEditMode = val;
                }
            }

        }
    }

</script>
