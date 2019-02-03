<template>

    <div>
                
        <!-- Loader for when loading the client information -->
        <Loader v-if="isLoadingClientInfo" :loading="isLoadingClientInfo" type="text" :style="{ marginTop:'40px' }">Loading client details...</Loader>

        <!-- Client Information -->
        <div v-if="localClient">
            <p v-if="!localEditMode" class="mt-3 text-dark"><strong>{{ localClient.name || '___' }}</strong></p>
            <el-input v-if="localEditMode" placeholder="Client name" v-model="localClient.name" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

            <div class="clearfix"></div>

            <p v-if="!localEditMode">{{ localClient.email || '___' }}</p>
            <el-input v-if="localEditMode" placeholder="Client email" v-model="localClient.email" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>

            <div class="clearfix"></div>

            <p v-if="!localEditMode">{{ localClient.phone || '___' }}</p>
            <el-input v-if="localEditMode" placeholder="Client tel/phone" v-model="localClient.phone" size="mini" class="mb-1" :style="{ maxWidth:'250px' }"></el-input>
            
            <div class="clearfix"> <br> </div>

            <p v-if="!localEditMode" v-for="(field, i) in localClient.additionalFields" :key="i">
                {{ field.value }}
            </p>
            <el-input v-if="localEditMode" v-for="(field, i) in localClient.additionalFields" :key="i" 
                size="mini" class="mb-1" :style="{ maxWidth:'250px' }"
                :placeholder="'Client details ' + i" 
                v-model="localClient.additionalFields[i].value">
            </el-input>
        </div>

        <!-- No client Information Alert -->
        <div v-if="!localClient && !isLoadingClientInfo">
            <Alert :style="{maxWidth: '250px'}" type="warning">
                No Client selected
            </Alert>
        </div>

    </div>

</template>


<script type="text/javascript">

    export default {
        props: {
            client: {
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
                localClient: this.client,
                localEditMode: this.editMode,
                isLoadingClientInfo: false,
            }
        },
        watch: {

            //  Watch for changes on the client
            client: {
                handler: function (val, oldVal) {

                    //  Update the client information
                    this.localClient = val;

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
