<template>
  <v-card class="mr-auto pa-3" max-width="600">
    <v-form ref="form" @submit.prevent="saveProduct" v-model="valid" lazy-validation>
      <h3 class="font-weight-light">New Product</h3>
      <v-text-field
        v-model="title"
        :rules="titleRules"
        v-on:keydown.enter.prevent="saveProduct"
        label="Product Title"
        required
      ></v-text-field>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text color="grey" @click.prevent="close">Cancel</v-btn>
        <v-btn :disabled="!valid" color="primary" @click.prevent="saveProduct">Create</v-btn>
      </v-card-actions>
    </v-form>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
     
      valid: false, 
      title: "",
      titleRules: [(v) => !!v || "Name is required"],
    };
  },
  methods: {
    close() {
      this.$emit("close");
    },
    saveProduct() {
      this.valid = false;
      let data = {
        title: this.title,
        slug: slugify(this.title),
      }; 
      
      if(this.title == "" || this.title == null) { return false; }
      axios
        .post("/builder/product/store", data)
        .then((response) => { 
            if(response.data.product){
              this.$router.push("/builder/product/edit/" + response.data.product); 
            }else{
              $('form div.v-input').html("");
              $('form h3').html(response.data.message);
            }
        })
        .catch((error) => {
          console.log("invalid");
          console.log(error.response);
       
        });
       
    },
  },
};
</script>
