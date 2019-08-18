<style scoped>

    body {
        background: #C5DDEB;
        padding: 40px 0;
        color: white;
    }

    ul{
        list-style-type: none; 
    }

    .container {
        margin: 0 auto;
        width: 750px;
        background: #444753;
        border-radius: 5px;
    }

    .people-list {
        width: 260px;
        float: left;
    }
    .people-list .search {
        padding: 20px;
    }
    .people-list input {
        border-radius: 3px;
        border: none;
        padding: 14px;
        color: white;
        background: #6A6C75;
        width: 90%;
        font-size: 14px;
    }
    .people-list .fa-search {
        position: relative;
        left: -25px;
    }
    .people-list ul {
        padding: 20px;
        height: 770px;
    }
    .people-list ul li {
        padding-bottom: 20px;
    }
    .people-list img {
        float: left;
    }
    .people-list .about {
        float: left;
        margin-top: 8px;
    }
    .people-list .about {
        padding-left: 8px;
    }
    .people-list .status {
        color: #92959E;
    }

    .chat {
        width: 490px;
        float: left;
        background: #F2F5F8;
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        color: #434651;
    }
    .chat .chat-header {
        padding: 10px 15px 5px 15px;
        border-bottom: 2px solid white;
    }
    .chat .chat-header .avatar {
        float: left;
        width: 40px;
        height: 40px;
        margin-top: 5px;
    }
    .chat .chat-header .chat-about {
        float: left;
        padding-left: 10px;
        margin-top: 6px;
    }
    .chat .chat-header .chat-with {
        font-weight: bold;
        font-size: 14px;
    }
    .chat .chat-header .chat-num-messages {
        color: #92959E;
    }
    
    .chat .chat-history {
        border-bottom: 2px solid white;
        overflow-y: scroll;
        height: 575px;
    }

    .chat .chat-history ul{
        padding: 30px 30px 20px;
    }

    .chat .chat-history .message-data {
        margin-bottom: 15px;
    }
    .chat .chat-history .message-data-time {
        color: #a8aab1;
        padding-left: 6px;
    }
    .chat .chat-history .message {
        color: white;
        padding: 5px 15px;
        line-height: 18px;
        font-size: 14px;
        border-radius: 7px;
        margin-bottom: 15px;
        width: 80%;
        position: relative;
    }
    .chat .chat-history .message:after {
        bottom: 100%;
        left: 7%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
        border-bottom-color: #86cc6b;
        border-width: 10px;
        margin-left: -10px;
    }
    .chat .chat-history .my-message {
        background: #86cc6b;
    }
    .chat .chat-history .other-message {
        background: #5aaaff;
    }
    .chat .chat-history .other-message:after {
        border-bottom-color: #5aaaff;
        left: 93%;
    }
    .chat .chat-message {
        padding: 30px;
    }
    .chat .chat-message textarea {
        width: 100%;
        border: none;
        padding: 10px 20px;
        font: 14px/22px "Lato", Arial, sans-serif;
        margin-bottom: 10px;
        border-radius: 5px;
        resize: none;
    }
    .chat .chat-message .fa-file-o, .chat .chat-message .fa-file-image-o {
        font-size: 16px;
        color: gray;
        cursor: pointer;
    }
    .chat .chat-message button {
        float: right;
        color: #5aaaff;
        font-size: 16px;
        text-transform: uppercase;
        border: none;
        cursor: pointer;
        font-weight: bold;
        background: #F2F5F8;
    }
    .chat .chat-message button:hover {
        color: #75b1e8;
    }

    .online, .offline, .me {
        margin-right: 3px;
        font-size: 10px;
    }

    .online {
        color: #86cc6b;
    }

    .offline {
        color: #E38968;
    }

    .me {
        color: #5aaaff;
    }

    .align-left {
        text-align: left;
    }

    .align-right {
        text-align: right;
    }

    .float-right {
        float: right;
    }

    .clearfix:after {
        visibility: hidden;
        display: block;
        font-size: 0;
        content: " ";
        clear: both;
        height: 0;
    }

    .close-btn{
        opacity:1;
        float: right; 
        border: 1px solid #cecece; 
        padding: 5px 10px; 
        border-radius: 5px; 
        cursor: pointer;
    }

    .close-btn:active{
        opacity:0.8;
    }

</style>

<template>
  <div class="clearfix">
    <div v-if="showContactList" class="people-list" id="people-list">
      <div class="search">
        <input type="text" placeholder="search" />
        <i class="fa fa-search"></i>
      </div>
      <ul class="list">
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Vincent Porter</div>
            <div class="status">
              <i class="fa fa-circle online"></i> online
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_02.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Aiden Chavez</div>
            <div class="status">
              <i class="fa fa-circle offline"></i> left 7 mins ago
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_03.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Mike Thomas</div>
            <div class="status">
              <i class="fa fa-circle online"></i> online
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_04.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Erica Hughes</div>
            <div class="status">
              <i class="fa fa-circle online"></i> online
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_05.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Ginger Johnston</div>
            <div class="status">
              <i class="fa fa-circle online"></i> online
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_06.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Tracy Carpenter</div>
            <div class="status">
              <i class="fa fa-circle offline"></i> left 30 mins ago
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_07.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Christian Kelly</div>
            <div class="status">
              <i class="fa fa-circle offline"></i> left 10 hours ago
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_08.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Monica Ward</div>
            <div class="status">
              <i class="fa fa-circle online"></i> online
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_09.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Dean Henry</div>
            <div class="status">
              <i class="fa fa-circle offline"></i> offline since Oct 28
            </div>
          </div>
        </li>
        
        <li class="clearfix">
          <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_10.jpg" alt="avatar" />
          <div class="about">
            <div class="name">Peyton Mckinney</div>
            <div class="status">
              <i class="fa fa-circle online"></i> online
            </div>
          </div>
        </li>
      </ul>
    </div>
    
    <div :class="'chat' + (showContactList ? '' : ' w-100')">
        <div class="chat-header clearfix">
            <Avatar class="avatar" src="images/backgrounds/review-comments-img-01.jpg" />
            
            <div class="chat-about">
            <div class="chat-with">Vincent Porter</div>
            <div class="chat-num-messages">{{ messages.length }} {{ messages.length == 1 ? ' message' : ' messages' }}</div>
            </div>

            <div v-if="!localShowMessages" class="close-btn mt-2"
                 @click="localShowMessages = true">
                <Icon type="ios-eye-outline" :size="20" />
                <span>View Messages</span>
            </div>

            <div v-if="localShowMessages" class="close-btn mt-2"
                @click="localShowMessages = false">
                <Icon type="ios-close" :size="20" />
                <span>Close</span>
            </div>
        </div> <!-- end chat-header -->
      
        <scrollBox v-if="localShowMessages">
            <div class="chat-history" :style="chatBoxStyle">
                <ul>
                    <li v-for="(message, i) in messages" class="clearfix">

                        <template v-if="(showAsStaff && message.from_staff) || (!showAsStaff && !message.from_staff)">
                            <div class="message-data align-right">
                                <span class="message-data-time" >{{ message.created_at | moment('DD MMM YYYY, H:mmA') || '___' }}</span> &nbsp; &nbsp;
                                <span class="message-data-name" >{{ (message.user).full_name }}</span> <i class="fa fa-circle me"></i>
                            </div>
                            <div class="message other-message float-right">
                                {{ message.text }}
                            </div>
                        </template>

                        <template v-else>
                            <div class="message-data">
                                <span class="message-data-name"><i class="fa fa-circle online"></i> {{ (message.user).full_name }}</span>
                                <span class="message-data-time">{{ message.created_at | moment('DD MMM YYYY, H:mmA') || '___' }}</span>
                            </div>
                            <div class="message my-message">
                                {{ message.text }}
                            </div>
                        </template>

                    </li> 
                    
                </ul>
                
            </div> <!-- end chat-history -->
        </scrollBox>

        <div v-if="localShowMessages && showReplyBox" class="chat-message clearfix">

            <commentCreator 
                :sendAsStaff="showAsStaff"
                :urlParams="urlParams"
                requiredTextError="Type something to send"
                btnText="SEND"
                btnClass="w-100 mt-2"
                placeholder="Enter your message"
                loaderText="Sending message"
                fieldType="textarea"
                :canRate="false"
                @commentSuccess="$emit('sentMessage', $event)">
            </commentCreator>

        </div> <!-- end chat-message -->
      
    </div> <!-- end chat -->
    
  </div>
</template>

<script>
    
    /*  scrollBox  */
    import scrollBox from './../../../components/_common/scrollBox/scrollBox.vue';

    /*  Form  */
    import commentCreator from './../../../components/_common/forms/comment/comment.vue'; 

    export default {
        props:{
            messages: {
                type: Array,
                default: function(){
                    return [];
                }
            },
            contactList: {
                type: Array,
                default: function(){
                    return [];
                }
            },
            showAsStaff: {
                type: Boolean,
                default: false
            },
            showReplyBox: {
                type: Boolean,
                default: true
            },
            showContactList: {
                type: Boolean,
                default: true
            },
            showMessages: {
                type: Boolean,
                default: true
            },
            chatBoxStyle:{
                type: Object,
                default: null
            },
            urlParams:{
                type: Object,
                default: null
            },
        },
        components: { 
            scrollBox, commentCreator
        },
        data(){
            return {
                localMessages: this.messages,
                localContactList: this.contactList,
                localShowMessages: this.showMessages
            }
        },
        watch: {
            messages: {
                handler: function (val, oldVal) {
                    this.localMessages = val;
                },
                deep: true
            }
        },
        computed: {
            
        },
        methods: {
            
        },
        created(){

        }
    };
  
</script>