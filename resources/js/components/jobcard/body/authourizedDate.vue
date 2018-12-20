<template>
    
    <!-- Jobcard Authorized Date -->
    <span>
        <strong>Authorized Date: </strong>
        <Poptip word-wrap width="200" trigger="hover" :content="getAuthourizedDate() | moment('DD MMM YYYY, H:mmA')">
            <span v-if="getAuthourizedDate()">{{ getAuthourizedDate() | moment("DD MMM YYYY") }}</span>
        </Poptip>
    </span>

</template>
<script>
    export default {
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
        methods: {
            getAuthourizedDate(){
                var x, recentActivity = this.jobcard.recent_activities || {};

                for(x=0; x < _.size(recentActivity); x++){
                    if(recentActivity[x].activity.type == 'authourized'){
                        return recentActivity[x].created_at; 
                    }
                }
            }
        }
    };
</script>