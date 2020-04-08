<style scoped>

    .topic-card:hover{
        cursor:pointer;
        box-shadow: 0px 5px 10px #b9b9b9;
    }

    .topic-card >>> .topic-heading{
        display:block;
        font-size: 20px;
        font-weight: bold;
    }

</style>

<template>
    <Row :gutter="20">

        <Col :span="20" :offset="2">
        
            <template v-if="isLoadingTopics">

                <div class="pb-3 border-bottom">

                    <h2 v-if="activeTopic">{{ activeTopic.name }}</h2>
                    <h2 v-else>Topics</h2>

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

                                    <span class="topic-heading">{{ topic.name }}</span>

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
            selectTopic(topic){

                this.activeTopic = topic;

            },
            fetchTopics() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoadingTopics = true;
                
                api.call('get', 'http://localhost:8000/api/sub-topics')
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
