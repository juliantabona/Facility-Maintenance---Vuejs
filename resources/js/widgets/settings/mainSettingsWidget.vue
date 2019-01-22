<style scoped>
    .el-form-item >>> .el-form-item__label{
        margin:0 !important;
        padding:0 !important;
        line-height: 24px !important;
    }

    .form-label{
        font-size:14px;
    }
</style>

<template>
    <Row id="main-settings-widget">

        <Col :span="24">
            <Row :gutter="20">
                <Col :span="6">
                    <Menu active-name="1" :style="{ width:'100%' }">
                        <MenuGroup title="User Settings">

                            <!-- User Profile Settings -->
                            <router-link :to="{name:'user-profile-settings'}">
                                <MenuItem name="1">
                                    <Icon type="ios-contact-outline" :size="24" />
                                    Profile
                                </MenuItem>
                            </router-link>
            
                            <!-- User Password Settings -->
                            <router-link :to="{name:'user-password-settings'}">
                                <MenuItem name="2">
                                    <Icon type="ios-lock-outline" :size="24" />
                                    Password
                                </MenuItem>
                            </router-link>

                            <!-- User Notifitions Settings -->
                            <router-link :to="{name:'user-notification-settings'}">
                                <MenuItem name="3">
                                    <Icon type="ios-notifications-outline" :size="24" />
                                    Notifications
                                </MenuItem>
                            </router-link>
                        </MenuGroup>
                    </Menu>
                </Col>
                <Col :span="18">

                    <slot></slot>

                </Col>
            </Row>
        </Col>

    </Row>

</template>

<script>
    import profileWidget from './profileWidget.vue'; 
    import passwordWidget from './passwordWidget.vue'; 
    import notificationsWidget from './notificationsWidget.vue'; 

    export default {
        components: { 
            profileWidget, passwordWidget, notificationsWidget
        },
        data(){
            return {
        
            }
        },
        methods: {

        }
    };
  
</script>