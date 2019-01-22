<template>

    <Row :gutter="20">

        <Col span="22" offset="1">

            <mainSettingsWidget>
                
                <router-view />
            
            </mainSettingsWidget>
        
        </Col>

    </Row>

</template>
<script>

    import mainSettingsWidget from './../../../../widgets/settings/mainSettingsWidget.vue';

    export default {
        components: { 
          mainSettingsWidget
        }
    };

</script>