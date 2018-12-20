<template>
    
    <!-- Jobcard Deadline -->
    <span>
        <Poptip word-wrap width="200" trigger="hover" 
                content="Advertise jobcard to the general public by pushing to websites and social media. This will allow third-party suppliers to see and respond to your jobcard. You will be alerted on the platform and via email.">
            <span>
                <Icon type="md-globe mr-1" :size="18" />
                <strong>Make Public: </strong>
                <i-switch :value="jobcard.is_public ? true: false" class="ml-1" size="large">
                    <span slot="open">Yes</span>
                    <span slot="close">No</span>
                </i-switch>
            </span>
        </Poptip>
    </span>

</template>
<script>
    export default {
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>