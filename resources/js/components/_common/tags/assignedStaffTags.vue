<style scoped>

    .user-profile-tag >>> .ivu-tag{
        padding: 2px 10px;
        height: 30px;
    }

</style>

<template>

    <span v-if="localStaff" class="user-profile-tag">
        <Poptip v-for="(staff, i) in localStaff" :key="i"
                word-wrap width="250" trigger="hover" :content="'Position: ' + staff.position">
            <Tag type="border" color="primary">
                <img src="https://content-static.upwork.com/uploads/2014/10/02123010/profilephoto_goodcrop.jpg" 
                    :style="{ width:'25px', height:'25px', borderRadius:'50%', marginRight:'5px' }">
                <span>{{ staff.full_name }}</span>
            </Tag>
        </Poptip>
    </span>

</template>
<script type="text/javascript">

    export default {
        props:{
            staffMembers: {
                type: Array,
                default: null
            }
        },
        data(){
            return {
                localStaff: this.staffMembers
            }
        },
        watch: {
            staffMembers: {
                handler: function (val, oldVal) {
                    this.localStaff = val;
                },
                deep: true
            }
        }
    }
</script>
