<style scoped>
    .ivu-select >>> .ivu-select-dropdown {
        min-width: 150px;
    }
</style>
<template>

    <!-- Tag Selector -->
    <div>
        <Loader v-if="isLoading" :loading="isLoading" type="text" class="text-left"></Loader>
        <Select v-if="localfetchedTags.length" 
                v-model="localSelectedTag" 
                placeholder="Select tag" 
                not-found-text="No tags found" 
                @on-change="selectTag($event)"
                :key="renderKey"
                filterable>
            <Option 
                v-for="(tag, index) in localfetchedTags" 
                :value="JSON.stringify(tag)" 
                :key="index">{{ tag.name }}</Option>
        </Select>
        
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    export default {
        props: {
            selectedTag:{
                type: Object,
                default: null
            },
            tagType: {
                type: String,
                default: ''   
            },
            clearable: {
                type: Boolean,
                default: false   
            },
        },
        components: { Loader },
        data(){
            return {
                localSelectedTag: (this.selectedTag ? JSON.stringify(this.selectedTag) : null),
                localTagType: this.tagType,
                localClearable: this.clearable,
                localfetchedTags: [],
                renderKey: 1,
                isLoading: false
            }
        },
        watch: {

            //  Watch for changes on the selectedTag
            selectedTag: {
                handler: function (val, oldVal) {
                    console.log('selectedTag changed!');
                    this.localSelectedTag = (val ? JSON.stringify(val) : null);
                }
            },
            tagType: {
                handler: function (val, oldVal) {
                    this.localTagType = val;
                }
            },
            clearable: {
                handler: function (val, oldVal) {
                    this.localClearable = val;
                }
            }

        },
        methods: {
            selectTag(selectedTag) {
                var tag = selectedTag ? JSON.parse(selectedTag) : null;
                this.$emit('updated', tag);

                if(this.localClearable){
                    this.localSelectedTag = null;
                    this.renderKey = this.renderKey + 1;
                }
            },
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting tags...');

                //  Get the status e.g) client, supplier, e.t.c
                var tagType = this.localTagType ? 'tagType='+this.localTagType : '';

                //  Additional data to eager load along with each tag found
                var connections = '';

                //  Settings to prevent pagination
                var pagination = ( (tagType) ? '&' : '' ) + 'paginate=0';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/tags?'+tagType+pagination)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Get tags
                        self.localfetchedTags = data;

                        console.log('New fetched tags');

                        console.log(self.localfetchedTags);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('tagSelector.vue - Error getting tags...');
                        console.log(response);    
                    });
            }
        },
        created(){
            console.log('tagSelector Ready');
            this.fetch();
        }
    };
</script>