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
            No Lifecycle Found - <span class="btn btn-link btn-sm p-0" @click="isOpenAddLifecycleModal = true">Add Lifecycle</span>
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
        <updateLifecycleModal
            v-show="isOpenUpdateLifecycleModal" 
            :show="isOpenUpdateLifecycleModal"
            :nextStep="storedNextStep"
            :section="storedStage"
            :template="lifecycle.template"
            v-on:closed="closeModal"
            v-on:updated="updateChanges">
        </updateLifecycleModal>

        <!-- 
            MODAL TO ADD LIFECYCLE IF DOES NOT EXIST
        -->
        <addLifecycleModal
            v-show="isOpenAddLifecycleModal" 
            :show="isOpenAddLifecycleModal"
            :jobcard="jobcard"
            v-on:closed="closeAddLifecycleModal"
            v-on:updated="updateAddLifecycleChanges">
        </addLifecycleModal>


    </Card>

</template>

<script>

    import Loader from './../../_common/loader/Loader.vue';
    import updateLifecycleModal from './updateLifecycleModal.vue';
    import addLifecycleModal from './addLifecycleModal.vue';

    export default {
        props: {
            jobcard: {
                type: Object,
                default: null
            },
        },
        components: { Loader, updateLifecycleModal, addLifecycleModal },
        data(){
            return {
                isLoading: false,
                lifecycle: {},
                popTipTitle: '',
                storedStage: {},
                storedNextStep: null,
                isOpenUpdateLifecycleModal: false,
                isOpenAddLifecycleModal: false,
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
            updateAddLifecycleChanges(lifecycle){
                //  Update the local lifecycle data with our new data
                this.lifecycle = lifecycle;

                //  Select the first step
                this.activeStep = 1;

                //  Close modal
                this.closeAddLifecycleModal();
            },
            closeModal(){
                this.isOpenUpdateLifecycleModal = !this.isOpenUpdateLifecycleModal;
            },
            closeAddLifecycleModal(){
                this.isOpenAddLifecycleModal = !this.isOpenAddLifecycleModal;
            },
            showLifecycleStageModal(stage, step){
                this.storedNextStep = ++step;
                this.storedStage = stage; 
                this.isOpenUpdateLifecycleModal = true;
            },
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting jobcard lifecycle...');

                var jobcardId = this.jobcard.id || this.$route.params.id;

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/jobcards/'+jobcardId+'/lifecycle')
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