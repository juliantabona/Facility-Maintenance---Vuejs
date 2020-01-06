<style scoped>
  
  /*  Makes rounded status tags - (Makes the Iview tag look like a Pill shaped tag e.g statuses in orders)  */
  .rounded-status-tag{
      padding: 0 10px 0 5px;
      border-radius: 15px;
      line-height: 25px;
      min-width: 90px;
      height: 25px;
  }

  /*  Fixes the colour dot by reducing size and provides proper alignment  */
  .rounded-status-tag >>> .ivu-tag-dot-inner {
      width: 8px;
      height: 8px;
      margin-right: 3px;
      margin-left: 2px;
      top: -1px;
  }

</style>

<template>

    <Poptip word-wrap width="350" trigger="hover" placement="top-start"
            :content="localStatus.description">
        <Tag type="dot" :color="localStatus.color" class="rounded-status-tag">
            <span class="cut-text text-capitalize">{{ localStatus.name }}</span>
        </Tag>
    </Poptip>

</template>

<script type="text/javascript">

    export default {
        props:{
            status: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localStatus: this.getStatusInfo(this.status)
            }
        },
        watch: {
            status: {
                handler: function (val, oldVal) {
                    this.localStatus = this.getStatusInfo(val)
                },
                deep: true
            }
        },
        methods: {
            getStatusInfo(status){
                return {
                    name: status.name || '...',
                    description: status.description || '...',
                    color: status.color
                }
            }
        }
    }
</script>
