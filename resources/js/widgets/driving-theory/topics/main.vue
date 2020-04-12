<style scoped>

    .topic-card:hover{
        cursor:pointer;
        box-shadow: 0px 5px 10px #b9b9b9;
    }

</style>

<template>
    <Row :gutter="20">

        <Col :span="20" :offset="2">
        
            <template v-if="!isLoadingTopics">

                <div class="border-bottom clearfix pb-3 mb-3">

                    <template v-if="activeTopic">

                        <h2 class="float-left">{{ activeTopic.name }}</h2>

                        <Button type="default" class="p-1 float-right" @click.native="goBack()">
                            <Icon type="md-arrow-back" :size="20" />
                            <span class="mr-2">Go Back</span>
                        </Button>

                    </template>

                    <template v-else>

                        <h2 class="float-left">Topics</h2>

                        <Button type="default" class="p-1 float-right" @click.native="refreshTopics()">
                            <Icon type="ios-refresh" :size="20" />
                            <span class="mr-2">Refresh</span>
                        </Button>

                    </template>

                </div>

                <!-- Topic Questions -->
                <Row v-if="activeTopic">
                    
                    <Col :span="24">
                        
                        <topicQuestions :topic="activeTopic"></topicQuestions>

                    </Col>

                </Row>

                <!-- List Of Topics -->
                <Row v-else>
                    
                    <Col :span="24">

                        <template v-if="topics.length">

                            <Card v-for="(topic, index) in topics" class="topic-card mb-2" :key="index" @click.native="selectTopic(topic)">

                                <div>

                                    <span>{{ topic.name }}</span>
                                    <Icon type="md-arrow-forward" :size="15" class="float-right mt-1"/>

                                </div>

                            </Card>

                        </template>

                        <Card v-else>No Topics Found</Card>

                    </Col>

                </Row>

            </template>
        
            <!-- Show loader -->
            <Loader v-else :loading="true" type="text" class="mt-5 text-left" theme="white">Loading topics...</Loader>

        </Col>


    </Row>
</template>
<script type="text/javascript">

    import topicQuestions from './../questions/main.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    export default {
        components: { Loader, topicQuestions },
        data(){
            return {
                topics: [],
                activeTopic: null,
                isLoadingTopics: true
            }
        },
        methods: {
            goBack(){
                //  Remove the current active topic
                this.activeTopic = null;
            },
            refreshTopics(){
                //  Remove the current active topic
                this.activeTopic = null;

                //  Get topics
                this.fetchTopics();
            },
            selectTopic(topic){

                this.activeTopic = topic;

            },
            fetchTopics() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoadingTopics = true;
                
                api.call('get', 'http://driving-theory.local/api/sub-topics')
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isLoadingTopics = false;

                        //  Store the stores data
                        self.topics = data || [];

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingTopics = false;

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){
            
            //  Fetch the store
            this.fetchTopics();

        }
    }
</script>
