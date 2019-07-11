<style scoped>

    .category_widget{
        max-height: 150px;
        list-style-type: none;
    }

    .category_widget >>> .category_item{
        line-height: 30px;
        list-style-type: none;
        margin:0 !important;
    }

    .category_widget >>> .category_item label{
        margin:0 !important;
    }

    .category_widget >>> .category_item li{
        height: 25px;
        line-height: 25px;
        list-style-type: none;
    }

    .category_widget >>> .category_item ul{
        margin-left: 1rem !important;
    }

</style>

<template>
    
    <div>
        <Loader v-if="isCheckingAvailableCategories" :loading="true" type="text" class="text-left">Loading...</Loader>
        
        <template v-if="!isCheckingAvailableCategories && (availableCategories || {}).length">
            <scrollBox v-if="!isAddingCategory" class="border">
                <ul class="category_widget p-2"> 
                    
                    <li v-for="(availableCategory, parentKey) in availableCategories" class="category_item">
                        <Checkbox :value="localSelectedCategoryIds.includes(availableCategory.id)" @on-change="updateCategory($event, availableCategory.id, availableCategory.child_categories)">{{ availableCategory.name }}</Checkbox>
                        <ul v-for="(availableSubCategory, childKey) in availableCategory.child_categories">
                            <li><Checkbox :value="localSelectedCategoryIds.includes(availableSubCategory.id)" @on-change="updateCategory($event, availableSubCategory.id, [])">{{ availableSubCategory.name }}</Checkbox></li>
                        </ul>
                    </li>

                </ul>
            </scrollBox>
        </template>

        <div v-if="!isCheckingAvailableCategories && (availableCategories || {}).length && isAddingCategory" class="clearfix border p-2 mt-2">
            <div>
                <!--    Parent Selector -->
                <Select v-if="(availableCategories || {}).length" v-model="parentCategory" :style="{ width:'100%' }" placeholder="Select parent category" filterable>
                    <Option 
                        v-for="(availableCategory, parentKey) in availableCategories" :key="parentKey"
                        :value="availableCategory.id" :label="availableCategory.name">
                        <span>{{ availableCategory.name }}</span>
                    </Option>
                </Select>
                <!--    Category Name -->
                <el-form-item prop="category_name" class="mb-2">
                    <el-input v-model="title" size="small" placeholder="Category name" :style="{ maxWidth:'100%' }"></el-input>
                </el-form-item>
            </div>

            <span class="btn btn-link text-danger pl-0 float-left" @click="isAddingCategory = false">
                <Icon type="md-close-circle" class="inline-block" />
                <span style="font-size: 12px;" class="inline-block">Cancel</span>
            </span>

            <Poptip trigger="hover" content="Create category" class="float-right">
                <Button icon="ios-add" type="dashed" size="small" @click="createCategory()">Create</Button>
            </Poptip>

        </div>

        <el-button v-if="!isCheckingAvailableCategories && !isAddingCategory" class="button-new-tag" size="small" @click="isAddingCategory = true">+ New Category</el-button>
    </div>

</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    /*  scrollBox  */
    import scrollBox from './../scrollBox/scrollBox.vue'; 

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        props: {
            selectedCategories:{
                type: Array,
                default: function(){ 
                    return [] 
                }
            },
            requestData:{
                type: Object,
                default: null
            }
        },
        components: { Loader, scrollBox },
        data(){
            return {
                title: '',
                parentCategory: null,
                localSelectedCategoryIds: this.getCategoryIds( this.removeChildCategories( this.selectedCategories ) ),
                availableCategories: [],
                isAddingCategory: false,
                isCheckingAvailableCategories: false,
                renderKey: 0,
            }
        },
        watch: {

            //  Watch for changes on the selectedCategories
            selectedCategories: {
                handler: function (val, oldVal) {
                    this.localSelectedCategoryIds = this.getCategoryIds( this.removeChildCategories(val) );
                }
            }

        },
        methods:{
            getCategoryIds(categories){

                var categoryIds = categories.map( (category, i) => { 
                        
                        var ids = [];
                        ids.push(category.id);

                        if( (category.child_categories || {}).length ){
                            
                            var childCategories = [];
                            childCategories = this.getCategoryIds(category.child_categories);

                            for(var x=0; x < (childCategories|| {}).length; x++){
                                ids.push(childCategories[x]);
                            }
                            
                        }
                        
                        return ids;

                    } );

                return (categoryIds.length) ? categoryIds.flat().filter( this.onlyUnique ) : [];
            },
            onlyUnique(value, index, self) { 
                return self.indexOf(value) === index;
            },
            updateCategory(checkboxValue, categoryId, subCategories){
                var selectedCategories = [];

                if( this.localSelectedCategoryIds.includes(categoryId)){
                    
                    //  It exists so remove it
                    for(var x=0; x < (this.localSelectedCategoryIds || {}).length; x++){
                        
                        if(this.localSelectedCategoryIds[x] == categoryId){
                            //  Remove category id to indicate not selected
                            this.localSelectedCategoryIds.splice(x, 1);

                            //  Remove children to indicate not selected
                            for(var y=0; y < (subCategories || {}).length; y++){
                                if(this.localSelectedCategoryIds[x] == subCategories[y].id){
                                    //  Remove category id to indicate not selected
                                    this.localSelectedCategoryIds.splice(x, 1);
                                }
                            }
                        }
                    }

                }else{
                    //  It does not exists so add it
                    this.localSelectedCategoryIds.push(categoryId);
                    
                    for(var x=0; x < (subCategories || {}).length; x++){
                        if( !this.localSelectedCategoryIds.includes(subCategories[x].id) ){
                            this.localSelectedCategoryIds.push(subCategories[x].id);
                        }
                    }
                }

  
                for(var x=0; x < (this.availableCategories || {}).length; x++){
                    if(this.localSelectedCategoryIds.includes(this.availableCategories[x].id)){
                        selectedCategories.push(this.availableCategories[x]);
                    }
                    for(var y=0; y < (this.availableCategories[x].child_categories || {}).length; y++){
                        
                        if(this.localSelectedCategoryIds.includes(this.availableCategories[x].child_categories[y].id)){
                            selectedCategories.push(this.availableCategories[x].child_categories[y]);
                        }
                    }
                }

                this.$emit('updated', selectedCategories);
            },
            removeChildCategories(categories){
                
                var currCategories = _.cloneDeep(categories);

                for(var x=0; x < (currCategories || {}).length; x++){
                    currCategories[x].child_categories = [];
                }
                
                return currCategories;

            },
            checkAvailableCategories() {
                
                if(this.requestData){
                    
                    const self = this;

                    //  Start loader
                    self.isCheckingAvailableCategories = true;

                    console.log('Start checking available categories...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/categories?categoryType='+this.requestData.category_type)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isCheckingAvailableCategories = false;

                            self.availableCategories = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isCheckingAvailableCategories = false;

                            console.log('clientOrVendorSelector.vue - Error checking available categories...');
                            console.log(response);    
                        });
                }
            },
            createCategory(){

            }
        },
        mounted: function () {

            this.checkAvailableCategories();
        }
    };
</script>