<style scoped>

    .video-topic{
        position: relative;
    }

    .topic-sideline-connect{
        position: absolute;
        width: 2px;
        left: 22px;
        top: 0px;
        background: #19be6b;
        transition: height 0.5s linear;
    }

    .topic-sideline-dot{
        width: 10px;
        height: 10px;
        margin: 0 5px;
        border-radius: 50%;
        background: #ffffff;
        border:1px solid #a7a7a7;
        transition:all 0.1s ease;
    }

    .topic-sideline-active{
        background:#19be6b;
        border:1px solid #ffffff;
    }

    .topic-sideline-active.topic-sideline-connect{
        background:#19be6b;
    }

    .slide-enter-active {
        -moz-transition-duration: 0.5s;
        -webkit-transition-duration: 0.5s;
        -o-transition-duration: 0.5s;
        transition-duration: 0.5s;
        -moz-transition-timing-function: ease-in;
        -webkit-transition-timing-function: ease-in;
        -o-transition-timing-function: ease-in;
        transition-timing-function: ease-in;
    }

    .slide-leave-active {
        -moz-transition-duration: 0.5s;
        -webkit-transition-duration: 0.5s;
        -o-transition-duration: 0.5s;
        transition-duration: 0.5s;
        -moz-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
        -webkit-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
        -o-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
        transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
    }

    .slide-enter-to, .slide-leave {
        max-height: 100px;
        overflow: hidden;
    }

    .slide-enter, .slide-leave-to {
        overflow: hidden;
        max-height: 0;
    }

    .cursor-btn, .cursor-btn:hover{
        cursor: pointer !important;
    }

</style>

<template>
    <Row :gutter="20" class="mt-3">

        <Col :span="10" :offset="7" class="mt-0 mb-0">
            <h3 class="tool-intro-header text-center mt-2 mb-3 ml-auto mr-auto">Learn The Basics</h3>
        </Col>

        <Col :span="24" class="mt-0 mb-0 pr-0">

            <Card>

                <Row :gutter="20">

                    <Col :span="24" class="mt-0 mb-0 pr-0">
                        <span v-for="(videoPlaylist, playlistIndex) in localVideoPlaylists" :key="playlistIndex"
                                @click="updateTutorialVideos(videoPlaylist)" :class="'btn btn-link cursor-btn font-weight-bold p-0' + (videoPlaylist.heading != activeVideoTitle ? ' text-black-50' : '')">
                            {{ videoPlaylist.heading }}
                            <Divider v-if="playlistIndex != (localVideoPlaylists.length - 1)" type="vertical"/>
                        </span>
                        <Divider dashed class="mt-2 mb-0" />
                    </Col>

                    <Col :span="7" class="mt-0 mb-0 pr-0 pt-4">
                        <div v-for="(videoPlaylist, playlistIndex) in localVideoPlaylists" :key="playlistIndex">
                            <template v-if="videoPlaylist.heading == activeVideoTitle">
                                <div v-for="(topic, topicIndex) in videoPlaylist.topics" :key="topicIndex">
                                    <h5 class="mb-2" @click="setVideoPlayer(getFirstVideoTime(topic.videos))">
                                        <Icon :size="20" :type="getPlaylistIcon(playlistIndex, topicIndex, topic.videos)" />
                                        <span class="btn btn-link cursor-btn font-weight-bold m-0 p-0 text-black-50">{{ topic.heading }}</span>
                                    </h5>
                                    <transition name="slide">
                                        <div v-if="openVideoPlaylist(playlistIndex, topicIndex)" class="video-topic mb-4 mt-4">
                                            <div class="topic-sideline-connect" :style="'height:'+ getVideoTimelineProgress(topic.videos) +'%;'">
                                            </div>  
                                            <span v-for="(video, ix2) in topic.videos" :key="ix2"
                                                    class="btn btn-link cursor-btn" @click="setVideoPlayer(video.time)"
                                                    :style="(ix2 == 0 ? 'margin-top:-20px;' : '') + (ix2 == topic.videos.length - 1 ? 'margin-bottom:-20px;' : '')">  
                                                <div :class="'d-inline-block topic-sideline-dot'  
                                                    + ((video.time < activeVideoCurrentTime) ? ' topic-sideline-active' : '')">
                                                </div>
                                                <span class="d-inline">{{ video.name }}</span>
                                            
                                            </span>
                                        </div>
                                    </transition>
                                </div>
                            </template>
                        </div>
                    </Col>

                    <Col :span="17" class="mt-0 mb-0 pr-2 pl-3 pt-4" style="border-left: 1px dashed #e8eaec;">
                        <youtube 
                            :video-id="activeVideoId" 
                            player-width="100%"
                            @ready="videoReady($event)">
                        </youtube>
                    </Col>
                </Row>

            </Card>
        </Col>

    </Row>
</template>
<script>


    /*  Loaders  */
    import Loader from './../loaders/Loader.vue';

    export default {
        components: { Loader },
        props:{
           videoPlaylists: {
               type: Array,
               default: function(){
                   return [];
               }
           }
        },
        data () {
            return {

                videoPlayer: null,
                videoAutoPlay: 0,
                activeVideoId: null,
                activeVideoTitle: null,
                videoCheckEverySecond: null,
                activeVideoCurrentTime: 0,
                activeVideoDurationTime: 0,
                localVideoPlaylists: this.videoPlaylists
            }
        },
        methods: {
            videoReady (event) {
                this.videoPlayer = event.target;

                //  Set a time interval to run every one second
                this.videoCheckEverySecond = setInterval(() => {
                    //  Update the current video time
                    this.activeVideoCurrentTime = this.videoPlayer.getCurrentTime();
                    this.activeVideoDurationTime =  this.videoPlayer.getDuration();
                }, 1000);

            },
            setVideoPlayer(time=null){
                //  Check if we have the time specified otherwise default to zero
                var specifiedtTime = time ? time : 0;

                //  Go to the specified time in video
                this.videoPlayer.seekTo(specifiedtTime);

                //  Play the video
                this.videoPlayer.playVideo();
            },
            updateTutorialVideos(videoPlaylist){
                //  Update the video id and title
                this.activeVideoTitle = videoPlaylist.heading;
                this.activeVideoId = videoPlaylist.videoId;
                //  Start th video from the begining
                this.setVideoPlayer(0);
            },
            getVideoTimelineProgress(videos){
                //  Calculate percentage progress of the timeline line
                if( this.activeVideoCurrentTime >= this.getFirstVideoTime(videos) &&
                    this.getLastVideoTime(videos) >= this.getFirstVideoTime(videos) ){

                    var progressPercentage = (this.activeVideoCurrentTime - this.getFirstVideoTime(videos)) 
                                                / (this.getLastVideoTime(videos) - this.getFirstVideoTime(videos)) 
                                                    * 100;
                    //  return a value strictly between 0 - 100 since its a percentage
                    return (progressPercentage <= 100 ? progressPercentage : 100);

                }

                //  Otherwise return percentage as 0
                return 0;
            },
            getFirstVideoTime(videos=null){
                if(videos){
                    return ((videos[0] || {}).time || 0);
                }

                return 0;
            },
            getLastVideoTime(videos){
                var finalTime = 0;
                for(var x=0; x < (videos || {}).length; x++){
                    finalTime = videos[x].time;
                }

                return finalTime;
            },
            openVideoPlaylist(playlistIndex, topicIndex){
                /*
                    How the data is organised
                    - playlist
                     - topics
                      -videos
                       - time
                */

                //  Get the playlist
                var playlist = this.localVideoPlaylists[playlistIndex];

                //  Get the topics
                var playlistTopics = playlist.topics;

                //  As long as this is not the last topic of the playlist
                if(topicIndex != (playlistTopics.length - 1)){
                    //  Get the next topic videos on the playlist
                    var topicVideos = playlistTopics[topicIndex + 1].videos;
                    //  Get the time of the first video
                    var firstVideoTime = ((topicVideos[0] || {}).time || 0);
                    //  If the first video time of the next topic is greater than the current time
                    //  then return true to keep the current playlist open
                    if(firstVideoTime >= this.activeVideoCurrentTime){
                        //  Open the playlist
                        return true;
                    }
                //  If this is the last topic in the playlist
                }else{
                    //  Get the current topic videos on the playlist
                    var topicVideos = playlistTopics[topicIndex].videos;

                    //  Get the time of the first video
                    var firstVideoTime = ((topicVideos[0] || {}).time || 0);
                    
                    //  If the first video time of the topic is greater than the current time
                    //  then return false to keep the current playlist closed
                    if(firstVideoTime > this.activeVideoCurrentTime){
                        //  Close the playlist
                        return false;
                    }else{
                        //  Open the playlist
                        return true;
                    }
                }
            },
            getPlaylistIcon(playlistIndex, topicIndex, topicVideos){
                var isPlaylistOpen = this.openVideoPlaylist(playlistIndex, topicIndex);
                var isCurrentTimeAfterLastVideo = (this.activeVideoCurrentTime >= this.getLastVideoTime(topicVideos));
                return (isPlaylistOpen ? 'ios-play-outline' : (isCurrentTimeAfterLastVideo ? 'ios-checkmark' : 'md-arrow-dropright'));
            },
        },
        watch: {
            videoPlaylists: {
                handler: function (val, oldVal) {
                    this.localVideoPlaylists = val ? val : [];
                },
                deep: true
            }
        },
        beforeDestroy () {
            clearInterval(this.videoCheckEverySecond);
        },
        mounted () {
            //  Use the details of the first video in playlist as default
            for(var x = 0; x < (this.videoPlaylists || {}).length; x++){
                //  If this is the first playlist
                if(x == 0){
                    //  Set the active video id and title
                    this.activeVideoId = this.videoPlaylists[x].videoId;
                    this.activeVideoTitle = this.videoPlaylists[x].heading;
                }
            }
        }
    }
</script>