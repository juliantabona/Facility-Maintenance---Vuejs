<template>

    <Card :bordered="false">
        <h3 slot="title">Notification Settings</h3>
        <Row>
            <Col :span="24" class="mt-3 mb-2">
                <Alert>Control which notifications you want to receive. Turn on/off what you need or don't need</Alert>
            </Col>
            <Col :span="24">
                <Row :gutter="20" class="mb-1">

                    <Col :span="24">

                        <h5 class="mb-3">General</h5>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Action</th>
                                    <th scope="col"><span class="font-weight-bold">Notify me when I:</span></th>
                                    <th scope="col"><span class="font-weight-bold">Notify me when others:</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Create Resources</th>
                                    <td>                            
                                        <i-switch class="ml-1" size="large">
                                            <span slot="open">On</span>
                                            <span slot="close">Off</span>
                                        </i-switch>
                                    </td>
                                    <td>
                                        <i-switch class="ml-1" size="large">
                                            <span slot="open">On</span>
                                            <span slot="close">Off</span>
                                        </i-switch>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Update Resources</th>
                                    <td>                            
                                        <i-switch class="ml-1" size="large">
                                            <span slot="open">On</span>
                                            <span slot="close">Off</span>
                                        </i-switch>
                                    </td>
                                    <td>
                                        <i-switch class="ml-1" size="large">
                                            <span slot="open">On</span>
                                            <span slot="close">Off</span>
                                        </i-switch>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Delete Resources</th>
                                    <td>                            
                                        <i-switch class="ml-1" size="large">
                                            <span slot="open">On</span>
                                            <span slot="close">Off</span>
                                        </i-switch>
                                    </td>
                                    <td>
                                        <i-switch class="ml-1" size="large">
                                            <span slot="open">On</span>
                                            <span slot="close">Off</span>
                                        </i-switch>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </Col>

                </Row>
                <Row>
                    <Col :span="24">
                        <hr class="mt-2" />
                        <!-- Save Changes Button -->
                        <Button class="float-right mt-2" type="success" size="large">
                            <span>Save Changes</span>
                        </Button>
                    </Col>
                </Row>
            </Col>
        </Row>
    </Card>

</template>

<script>
    import Loader from './../../components/_common/loader/Loader.vue';

    export default {
        components: { 
            Loader,
        },
        data(){
            return {
               
            }
        }
    };
  
</script>