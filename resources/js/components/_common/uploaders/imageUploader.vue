<style scoped>
    .image_upload_poptip,
    .image_upload_poptip >>> .ivu-poptip-rel{
        width: 100% !important;
    }

    .image_upload_poptip >>> .ivu-upload{
        width: 100% !important;
    }

    .image-uploader{
        height: auto !important;
        min-height:150px;
        text-align: center;
        line-height: 60px;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        position: relative;
        margin-right: 4px;

        /* background-image: url(/images/assets/icons/star_loader.svg); */
        background-repeat: no-repeat;
        background-position: center;
        background-size: 65px;
    }

    .highlight-line{
        border: 1px solid transparent;
        box-shadow: 0px 1px 4px rgba(0,0,0,.2);
    }

    .image-uploader img{
        width: 100%;
        height: 100%;
    }
    .image-uploader-cover{
        height: 0px;
        overflow:hidden;
        background: #ffffff;
        transition:all 0.5s ease;
    }
    .image-uploader:hover .image-uploader-cover{
        height: 50px;
        transition:all 0.5s ease;
    }
    .image-uploader-cover i{
        color: #a7a7a7;
        font-size: 30px;
        cursor: pointer;
        margin: 0;
    }

    .image-preview{
        max-width:180px;
        margin:auto;
    }

    .image-preview img{
        width:100% !important;
        height:auto !important;
        margin: auto;
        display: block;
    }

    .no-image-box{
        line-height: 135px;
        border: 1px dashed #e8eaec;
        text-align: center;
    }

</style>

<template>
    <div>
        
        <!-- Loader for when loading the docs -->
        <Loader v-if="isLoadingDocs" :loading="true" type="text" class="mt-2 mb-2">Loading...</Loader>
        <Loader v-if="isUploadingDocs" :loading="true" type="text" class="mt-2 mb-2">Uploading...</Loader>
        <Loader v-if="isRemovingDocs" :loading="true" type="text" class="mt-2 mb-2">Removing...</Loader>
    
        <div v-if="previewImage && !isLoadingDocs && !isUploadingDocs && !isRemovingDocs" class="image-preview">
            <img :src="previewImage" :style="{ width:'150px',  }">
        </div>
        
        <Row v-if="!isLoadingDocs && !isUploadingDocs && !isRemovingDocs && uploadList.length" :gutter="20"
             style="margin-left: 0;margin-right: 0;">

            <Col v-for="(item, i) in uploadList" :key="i" :span="thumbnailColSpan">
                
                <div v-if="(item.response || {}).url"
                    :style="thumbnailStyle" :class="'mb-3 image-uploader' + ( allowUpload ? ' highlight-line' : '')">
                    <img :src="(item.response || {}).url">
                    <div class="image-uploader-cover">
                        <Icon type="ios-eye-outline" @click.native="handleView(item)"></Icon>
                        <Icon type="ios-trash-outline" @click.native="handleRemove(item, i)"></Icon>
                    </div>
                </div>

                <template v-else>
                    <Progress v-if="item.showProgress" :percent="item.percentage" hide-info></Progress>
                </template>

            </Col>
        </Row>

        <Poptip v-show="allowUpload && !isLoadingDocs && !isUploadingDocs && !isRemovingDocs" 
                word-wrap width="200" trigger="hover" :content="uploadMsg" class="image_upload_poptip">
            
            <Upload
                ref="upload"
                :show-upload-list="false"
                :on-success="handleSuccess"
                :format="['jpg','jpeg','png']"
                :max-size="2048"
                :on-format-error="handleFormatError"
                :on-exceeded-size="handleMaxSize"
                :before-upload="handleBeforeUpload"
                :multiple="multiple"
                type="drag"
                :data="postData"
                action="/api/upload"
                style="display: inline-block;width:58px;">
                
                <div v-if="uploadList.length || previewImage">
                    <span class="btn btn-link">{{ uploadBtnText }}</span>
                </div>

                <div v-else style="padding: 30px;line-height: 3em;">
                    <Icon type="ios-image-outline" size="50"></Icon>
                    <span class="d-block">{{ changeUplodBtnText }}</span>
                </div>
                
            </Upload>
        </Poptip>
        <div v-show="!allowUpload && !isLoadingDocs && !isUploadingDocs && !uploadList.length" class="no-image-box">
            <Icon type="ios-image-outline" size="70"></Icon>
            <span class="">{{ noUplodFoundText }}</span>
        </div>
        <Modal v-model="visible">
            <img :src="imgUrl" v-if="visible" style="width: 100%">
        </Modal>
    </div>
</template>
<script>


    /*  Loaders  */
    import Loader from './../loaders/Loader.vue';

    export default {
        components: { Loader },
        props:{
            docUrl: {
                type: String,
                default: null
            },
            postData: {
                type: Object,
                default: null
            },
            allowUpload: {
                type: Boolean,
                default: true
            },
            uploadMsg:{
                type: String,
                default: 'Upload'
            },
            uploadBtnText:{
                type: String,
                default: 'Click To Attach'
            },
            changeUplodBtnText:{
                type: String,
                default: 'Click To Change'     
            },
            noUplodFoundText:{
                type: String,
                default: 'Document not found'  
            },
            multiple: {
                type: Boolean,
                default: false
            },
            thumbnailStyle: {
                type: Object,
                default: {}
            },
            thumbnailColSpan: {
                type: Number,
                default: 24  
            }
        },
        data () {
            return {
                imgName: '',
                imgUrl: '',
                visible: false,
                uploadList: [],
                isLoadingDocs: false,
                isUploadingDocs: false,
                isRemovingDocs: false,
                previewImage: null,
            }
        },
        methods: {
            handleView (item) {
                this.imgName = (item.response || {}).name;
                this.imgUrl = (item.response || {}).url;
                this.visible = true;
            },
            handleRemove (file, index) {
                
                if((file.response).id){
                    
                    const self = this;

                    this.removeFile(file.response.id).then((data) => {
                        const fileList = self.$refs.upload.fileList;

                        self.uploadList.splice(index, 1);

                    });
                }
            },
            handleSuccess (res, file, fileList) {
                //  Stop loader
                this.isUploadingDocs = false;

                //  If we are not allowing multiple files
                if( !this.multiple ){
                    //  Empty the uploadList to only list one file
                    this.uploadList = [];
                }

                //  Add file to uplaod list
                this.uploadList.push( file );
                
                //  Remove preview
                this.removePreview();

                //  Notify success message
                this.$Notice.success({
                    title: 'Upload completed!'
                });

                this.$emit('updated', res);
                
                //  Notify the parent on whether the upload is done or not
                this.handleIsCompletedAllUploadsStatus();

            },
            handleFormatError (file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
                });

                //  Remove preview
                this.removePreview();
            },
            handleMaxSize (file) {
                this.$Notice.warning({
                    title: 'Exceeding file size limit',
                    desc: 'File  ' + file.name + ' is too large, no more than 2M.'
                });

                //  Remove preview
                this.removePreview();
            },
            handleBeforeUpload (file) {

                if( (this.postData || {}).modelId && (this.postData || {}).modelType ){

                    //  Start loader
                    this.isUploadingDocs = true;

                    //  Notify the parent on whether the upload is done or not
                    this.handleIsCompletedAllUploadsStatus();

                    //  Handle Validation
                    const check = this.uploadList.length < 5;

                    if (!check) {
                        this.$Notice.warning({
                            title: 'Up to five pictures can be uploaded.'
                        });
                    }

                    return check;

                }else{

                    //  Create a preview of the file
                    this.createPreview(file);

                    //  Emit the file
                    this.$emit('fileBeforeUpload', file);

                    //  Do not allow the upload to proceed
                    return false;

                }
            },
            createPreview(file){
                
                //  Make a preview image
                var self = this;
                var reader  = new FileReader();

                reader.addEventListener("load", function () {
                    self.previewImage = reader.result;
                }, false);

                reader.readAsDataURL(file);

            },
            removePreview(){

                //  Remove preview image
                this.previewImage = null;

            },
            getDocs() {
                
                if(this.docUrl){
                    
                    const self = this;

                    //  Start loader
                    self.isLoadingDocs = true;

                    console.log('Start getting docs...');

                    //  Additional data to eager load along with docs found
                    var connections = '';
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.docUrl + connections)
                            .then(({data}) => {
                                
                                console.log(data);

                                //  Stop loader
                                self.isLoadingDocs = false;

                                if(data){
                                    self.uploadList = data.map( (doc) => { return { response: doc } });
                                    console.log('self.uploadList');
                                    console.log(self.uploadList);
                                }
                                
                            })         
                            .catch(response => { 

                                //  Stop loader
                                self.isLoadingDocs = false;

                                console.log('imageUploader.vue - Error getting docs...');
                                console.log(response);    
                            });
                }
            },
            removeFile(fileId) {
                
                const self = this;

                //  Start loader
                self.isRemovingDocs = true;

                //  Notify the parent on whether the upload is done or not
                self.handleIsCompletedAllUploadsStatus();

                console.log('Start removing doc...');
                
                //  Use the api call() function located in resources/js/api.js
                return api.call('delete', '/api/upload/'+fileId)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isRemovingDocs = false;

                            //  Notify the parent on whether the upload is done or not
                            self.handleIsCompletedAllUploadsStatus();
                            
                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isRemovingDocs = false;

                            console.log('imageUploader.vue - Error removing doc...');
                            console.log(response);    
                        });
            },
            handleIsCompletedAllUploadsStatus(){
                //  If we are not uploading any documents and we already have some in the upload list
                if(!this.isLoadingDocs && !this.isUploadingDocs && !this.isRemovingDocs && this.uploadList.length){
                    //  Notify the parent on completed uploading
                    this.$emit('completedAllUploads', true);
                }else{
                    // Notify the parent that we are not done uploading
                    this.$emit('completedAllUploads', false);
                }
            }
        },
        mounted () {
            this.getDocs();
            this.uploadList = this.$refs.upload.fileList;
            
        }
    }
</script>