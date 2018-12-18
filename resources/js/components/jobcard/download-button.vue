<template>
    <Button type="default" @click="showOptions">
        <Icon type="ios-download-outline" :size="18" :style="{ marginTop:'-3px' }"/>
        Download

        <!-- 
            MODAL TO UPDATE LIFECYCLE STATUS INFORMATION
        -->
        <FileDownloadOptionsModal
            v-show="isOpen" 
            :show="isOpen"
            @closed="closeModal"
            @download="downloadFile">
        </FileDownloadOptionsModal>

    </Button>
</template>
<script type="text/javascript">

    import FileDownloadOptionsModal from './download-options-modal.vue';

    export default {
        components: { FileDownloadOptionsModal },
        props: {
            jobcardId: {
                type: Number
            }
        },
        data(){
            return {
                isOpen: false
            }
        },
        methods: {
            showOptions(){
                this.isOpen = true;
            },
            closeModal(){
                this.isOpen = false;
            },
            downloadFile(downlodableSections){
                let routeData = this.$router.resolve({
                        path: '/api/downloadFile', 
                        query: { 
                            id: this.jobcardId, 
                            model: 'jobcard', 
                            options: JSON.stringify(downlodableSections)  
                        }
                    });

                window.open(routeData.href.replace("#", ""), '_blank');
                
                this.closeModal();
            }
        }
    }
</script>
