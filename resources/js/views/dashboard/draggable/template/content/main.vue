<style>

</style>

<template>

    <div>
        
        <oq-Template-Header :sections="sections"></oq-Template-Header>

        <oq-Template-Sections :sections="sections"></oq-Template-Sections>

    </div>

</template>

<script>
    export default {
        props:{
            sections: {
                default:() => {}
            }
        }
    }
</script>