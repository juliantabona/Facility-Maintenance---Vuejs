<style scoped>

    * {
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    font-size: 12px;
    }

    #breadcrumb {
    list-style: none;
    display: inline-block;
    }
    #breadcrumb >>> .icon {
    font-size: 14px;
    }
    #breadcrumb >>> li {
    float: left;
    margin-bottom:5px;
    cursor: pointer;
    }
    #breadcrumb >>> li span {
    color: #FFF;
    display: block;
    background: #3498db;
    text-decoration: none;
    position: relative;
    height: 40px;
    line-height: 40px;
    padding: 0 10px 0 5px;
    text-align: center;
    margin-right: 23px;
    }
    /*
    #breadcrumb >>> li:nth-child(even) span {
    background-color: #19be6b;
    }
    #breadcrumb >>> li:nth-child(even) span:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li:nth-child(even) span:after {
    border-left-color: #19be6b;
    }
    */
    #breadcrumb >>> li.active span {
    background-color: #19be6b;
    }
    #breadcrumb >>> li.active span:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li.active span:after {
    border-left-color: #19be6b;
    }
    #breadcrumb >>> li:first-child span {
    padding-left: 15px;
    -moz-border-radius: 4px 0 0 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px 0 0 4px;
    }
    #breadcrumb >>> li:first-child span:before {
    border: none;
    }
    /*
    #breadcrumb >>> li:last-child span {
    padding-right: 15px;
    -moz-border-radius: 0 4px 4px 0;
    -webkit-border-radius: 0;
    border-radius: 0 4px 4px 0;
    }
    #breadcrumb >>> li:last-child span:after {
    border: none;
    }
    */
    #breadcrumb >>> li span:before, #breadcrumb li span:after {
    content: "";
    position: absolute;
    top: 0;
    border: 0 solid #3498db;
    border-width: 20px 10px;
    width: 0;
    height: 0;
    }
    #breadcrumb >>> li span:before {
    left: -20px;
    border-left-color: transparent;
    }
    #breadcrumb >>> li span:after {
    left: 100%;
    border-color: transparent;
    border-left-color: #3498db;
    }
    #breadcrumb >>> li span:hover {
    background-color: #19be6b;
    }
    #breadcrumb >>> li span:hover:before {
    border-color: #19be6b;
    border-left-color: transparent;
    }
    #breadcrumb >>> li span:hover:after {
    border-left-color: #19be6b;
    }
    #breadcrumb >>> li span:active {
    background-color: #16a085;
    }
    #breadcrumb >>> li span:active:before {
    border-color: #16a085;
    border-left-color: transparent;
    }
    #breadcrumb >>> li span:active:after {
    border-left-color: #16a085;
    }

</style>

<template>

    <Card :style="{ width: '100%' }">
        
        <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ padding: '6px 15px 6px 15px', fontSize: '14px' }">Getting Lifecycle...</Loader>

        <Alert v-if="!isLoading && !(stages || {}).length" type="warning">
            No Lifecycle Found - <span class="btn btn-link btn-sm p-0" @click="$emit('showAddLifecycleModal')">Add Lifecycle</span>
        </Alert>

        <Poptip word-wrap width="200" trigger="hover" :content="popTipTitle">
            <ul id="breadcrumb">
                <li v-for="(stage, i) in stages" :class="i < activeStep ? 'active': ''"
                    @mouseover="popTipTitle = stage.description"
                    @click="showLifecycleStageModal(stage, i)">
                    <span>{{ stage.name }}</span>
                </li>
            </ul>
        </Poptip>
        
        <!-- 
            MODAL TO UPDATE LIFECYCLE STATUS INFORMATION
        -->
        <UpdateStatusLifecycleWidget
            v-show="isOpenUpdateLifecycleDrawer" 
            :show="isOpenUpdateLifecycleDrawer"
            :nextStep="storedNextStep"
            :section="storedStage"
            :template="lifecycle.template"
            v-on:closed="closeModal"
            v-on:updated="updateChanges">
        </UpdateStatusLifecycleWidget>


    </Card>

</template>

<script>

    import Loader from './../../components/Loader.vue';
    import UpdateStatusLifecycleWidget from './update-status-lifecycle-modal.vue';

    export default {
        components: { Loader, UpdateStatusLifecycleWidget },
        data(){
            return {
                isLoading: false,
                lifecycle: {},
                popTipTitle: '',
                storedStage: {},
                storedNextStep: null,
                isOpenUpdateLifecycleDrawer: false
            }
        },
        created () {
            this.fetch();
        },
        computed: {
            stages: function () {
                return (this.lifecycle.template || {}).sections;
            },
            activeStep: {
                get() {
                    return this.lifecycle.step;
                },
                set(newValue) {
                    this.lifecycle.step = newValue;
                }
            }

        },
        methods: {
            updateChanges(nextStep){
                this.activeStep = this.storedNextStep;
                this.closeModal();
            },
            closeModal(){
                this.isOpenUpdateLifecycleDrawer = !this.isOpenUpdateLifecycleDrawer;
            },
            showLifecycleStageModal(stage, step){
                this.storedNextStep = ++step;
                this.storedStage = stage; 
                this.isOpenUpdateLifecycleDrawer = true;
            },
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting jobcard lifecycle...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/jobcards/'+this.$route.params.id+'/lifecycle')
                    .then(({data}) => {

                        //  Stop loader
                        self.isLoading = false;

                        //  Get jobcard lifecycle data
                        self.lifecycle = data;
                    })         
                    .catch(response => { 
                        console.log('status-lifecycle-widget.vue - Error getting lifecycle...');
                        console.log(response);

                        //  Stop loader
                        self.isLoading = false;     
                    });
            }
        },
    };
    
</script>