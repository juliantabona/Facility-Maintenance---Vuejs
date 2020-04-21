<template>

    <div>

        <!-- Event Name Input  -->
        <el-input type="text" v-model="localEvent.name" size="small" class="w-50"></el-input>

        <!-- Activate Event Checkbox (Marks the event as active / inactive) -->  
        <Checkbox v-model="localEvent.active" class="mt-2">Active</Checkbox>

        <!-- Event Settings -->
        <div class="mt-2">

            <Tabs v-model="activeNavTab" type="card" style="overflow: visible;" :animated="false">

                <!-- Screen Settings Navigation Tabs -->
                <TabPane v-for="(currentTabName, key) in navTabs" :key="key" :label="currentTabName" :name="currentTabName"></TabPane>

            </Tabs>

            <!-- Request Url -->
            <requestUrl v-show="activeNavTab == 'Request Url'" :event="localEvent"></requestUrl>

            <!-- Query Params -->
            <requestQueryParams v-show="activeNavTab == 'Query Params'" :event="localEvent"></requestQueryParams>

            <!-- Form Data -->
            <requestFormData v-show="activeNavTab == 'Form Data'" :event="localEvent"></requestFormData>
                
            <!-- Headers -->
            <requestHeaders v-show="activeNavTab == 'Headers'" :event="localEvent"></requestHeaders>
                
            <!-- Responses -->
            <requestResponses v-show="activeNavTab == 'Responses'" :event="localEvent"></requestResponses>

        </div>
    </div>

</template>

<script>
    
    //  Get the Request URL Settings
    import requestUrl from './requestUrl.vue';

    //  Get the Request Query Params Settings
    import requestQueryParams from './requestQueryParams.vue';

    //  Get the Request Form Data Settings
    import requestFormData from './requestFormData.vue';

    //  Get the Request Form Data Settings
    import requestHeaders from './requestHeaders.vue';

    //  Get the display editor
    import requestResponses from './request-response/main.vue';

    export default {
        props:{
            event: {
                type: Object,
                default: null
            }
        },
        components: {
            requestUrl, requestQueryParams, requestFormData, requestHeaders, requestResponses
        },
        data(){
            return{
                localEvent: this.event,
                activeNavTab: 'Request Url'
            }
        }, 
        computed: {
            navTabs(){
                var tabs = ['Request Url', 'Query Params', 'Form Data', 'Headers', 'Responses'];

                return tabs;
            }
        },
        created(){

        }
    }
</script>