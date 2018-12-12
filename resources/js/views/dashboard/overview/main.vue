<style scoped>
  .ivu-card:hover{
    cursor: pointer;
  }
</style>
<template>

    <Row :gutter="20" class="pt-3 pl-2 pr-2">
        <Col :span="24">
          <Card padding="5px" class="mt-2 mb-4">
            <h6 class="ml-2"><strong>Jobcard Lifecycle</strong></h6>
          </Card>
        </Col>
        <Col :span="6" v-for="stage in lifecycleStages" :key="stage.name" class="mb-2">
    
            <Card  @click.native="viewJobcards(stage)" :style="{ padding:0, minHeight:'135px' }">
                
                <div style="padding: 0px 15px;">
                    <Icon :type="stage.icon" size="45" class="text-center" style="display: block;"/>
                    <p class="text-center" style="padding-top:5px;">{{ stage.name }}</p>
                    <h6 class="text-center" style="padding-top:10px;"><strong>{{ getCount(stage) }}</strong></h6>
                </div>

            </Card>

        </Col>

    </Row>

</template>

<script>
    export default {
        data(){
            return {
                isLoading: false,
                template: {},
                allocations: {}
            }
        },
        created () {
            this.fetch();
        },
        computed: {
          lifecycleStages: function () {
            return this.template.sections;
          }
        },
        methods: {
            viewJobcards(){

            },
            getCount(stage){
              
              var count = 0;
              var x;

              for(x = 0; x < this.allocations.length; x++){
                if(this.allocations[x].step == stage.step){
                  count = this.allocations[x].count;
                }
              }

              return count;
            },
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting jobcard lifecycle stages...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/jobcards/allocations')
                    .then(({data}) => {

                        //  Stop loader
                        self.isLoading = false;

                        //  Get jobcard lifecycle stages
                        self.template = data.template;
                        self.allocations = data.allocations;

                        console.log('response');
                        console.log(self.allocations);
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