<template>

    <!-- Display simulator menu options -->
    <Menu :active-name="localActiveMenu" theme="light" class="w-100">

        <MenuItem name="debugger">
            <Icon type="ios-bug-outline" :size="20"/>
            <span>Debugger</span>
        </MenuItem>
        
        <MenuItem name="subscriptions">
            <Icon type="ios-contact-outline" :size="20"/>
            <span>Subscriber</span>
        </MenuItem>
        
        <MenuItem name="settings">
            <Icon type="ios-settings-outline" :size="20"/>
            <span>Settings</span>
        </MenuItem>

    </Menu>
    
</template>

<script>

    export default {
        props: { 
            activeMenu: {
                type: String,
                default: 'debugger'
            },
            builder: {
                type: Object,
                default: () => {}
            },
        },
        components: { 
            
        },
        data(){
            return {
                localActiveMenu: this.activeMenu 
            }
        }, 
        computed: {

        },
        methods: {

        }
    };
  
</script>