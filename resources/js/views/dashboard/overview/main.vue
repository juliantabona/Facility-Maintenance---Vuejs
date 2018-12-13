<style scoped>
  .ivu-card:hover{
    cursor: pointer;
  }
  
  .ivu-badge >>> .ivu-badge-count {
    font-family: inherit !important;
    font-weight: 600 !important;
    min-width: 35px !important;
    height: 20px !important;
    z-index: 1 !important;
  }

</style>
<template>

    <Row :gutter="20" class="pt-3 pl-2 pr-2">
        <Col :span="24">
          <Divider orientation="left"><Badge><h5>Jobcard Status</h5></Badge></Divider>
        </Col>
        
        <Col :span="24">

          <Row :gutter="20" class="pl-3 pr-3">

            <Col :span="6" v-for="(stage, index) in lifecycleStages" :key="index" class="mb-2">

                <Card  @click.native="viewJobcards(stage.step)" :style="{ padding:0, minHeight:'100px' }">
                  
                    <div style="padding: 0px 15px;">
                      <Badge :show-zero="onFirstAndLast(index)" :count="getCount(stage)" type="primary" style="width:100%;">
                          <Icon :type="stage.icon" size="45" class="text-center" style="display: block;"/>
                          <p class="text-center" style="padding-top:5px;">{{ stage.name }}</p>
                        </Badge>
                    </div>
                    

                </Card>

            </Col>

          </Row>   

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
            viewJobcards(step){
              this.$router.push({ name: 'jobcards', query: { step: step } });
            },
            onFirstAndLast(index){
                if(index == 0 || index == ( (this.lifecycleStages || {}).length - 1) ){
                    return true;
                }

                return false;
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
                api.call('get', '/api/jobcards/lifecycle/stages')
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