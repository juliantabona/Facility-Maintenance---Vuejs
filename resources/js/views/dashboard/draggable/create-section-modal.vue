<template>
    <el-dialog
        title="Create Section"
        :visible.sync="dialogVisible"
        width="30%"
        :before-close="handleBackdropClose"
        @close="$emit('closed')">
        <b>Section Name:</b>
        <el-input type="text" 
            placeholder="Enter section name..." 
            v-model="section.name"
            clearable
            size="small"
            :maxlength="30"
            class="w-100 mb-3">
        </el-input>

        <b>Section Description:</b>
        <el-input type="textarea"
            placeholder="Enter section description..." 
            v-model="section.description"
            :maxlength="200"
            :rows="1"
            class="w-100">
        </el-input>

        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">Cancel</el-button>
            <el-button type="primary" @click="createSection(section)">Create</el-button>
        </span>
    </el-dialog>

</template>

<script>
  export default {
      props:{
          showModal: {
              default: false
          }
      },
    data() {
      return {
        dialogVisible: this.showModal,
        section: {
            id: "",
            name: "",
            description:"",
            showFields:true,
            updated: false,
            fields: []
        }
      };
    },
    watch: {
        showModal: function (val) {
            this.dialogVisible = val;
        }
    },
    methods: {
        createSection(section){
            section.id = 'section_' + Date.now();
            this.$emit('created', section);
            this.dialogVisible = false;
        },
      handleBackdropClose(done) {
        this.$confirm('Are you sure to close this dialog?')
          .then(_ => {
            console.log('Closed!!!');
            done();
          })
          .catch(_ => {
              console.log('Open!!!');
          });
      }
    }
  };
</script>