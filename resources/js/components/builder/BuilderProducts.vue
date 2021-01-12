<template>
  <v-row>
    <v-col cols="9" class="px-5">
      <div class="d-flex align-center">
        <!-- to="/builder/product/new""-->
        <v-btn  v-if="permitted"
              @click="newProductDialog = true"
              class="mr-3 text--primary"
              elevation="2"
              fab
              dark
              x-small
              color="white"
        >
          <v-icon dark>mdi-plus</v-icon>
        </v-btn> 
        <h3 class="font-weight-light">Products</h3>

          <v-spacer></v-spacer>
           
        <v-text-field
            v-model="searchProduct"
            v-on:keydown.enter.prevent="searchButton"
            append-icon="mdi-cloud-search-outline"  
            outlined label="Search" required class="py-0" dense
            @click:append.prevent="searchButton"
          ></v-text-field> 
          
      </div>
      <v-card>
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">Product Name</th>
                <th class="text-left">VIN</th>
                <th class="text-left">slug</th>
                <!-- <th class="text-left">Status</th> -->
                <th  v-if="authUser.role < 5" class="text-center">Embed</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in products" :key="item.name">
                <td>{{ item.title }}</td>
                <td></td>
                 <td>{{ item.slug }}</td>
                <!-- <td
                  :class="`${item.status == 1 ? 'green--text' : 'blue--text'} text-left`"
                >{{ item.status == 1 ? 'active' : 'inactive' }}</td> -->
                <td  v-if="authUser.role < 5" class="text-center">
                  <v-btn
                    title="Embed"
                    text
                    small
                    color="blue-grey darken-2"
                    @click="openCode(item.slug, item.title)"
                  >get code</v-btn>
                </td>
                <td class="text-right">
                  <v-btn
                    title="Preview"
                    icon
                    small
                    :to="`/product/${item.slug}`"
                    target="_blank"
                    color="green"
                  >
                    <v-icon small>mdi-open-in-new</v-icon>
                  </v-btn>
                 <!-- v-if="permissionAccess(['all-permission','u-only'])" v-if="permissionAccess(['all-permission','d-only'])"-->
                      <v-btn v-if="permitEdit" title="Edit" icon small @click="editProduct(item.id)" color="blue">
                        <v-icon small>mdi-pencil</v-icon>
                      </v-btn>
                     
                      <v-btn v-if="permitDelete" title="Delete" icon small @click="actionFn(item)" color="red">
                        <v-icon small>mdi-trash-can-outline</v-icon>
                      </v-btn> 
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card>
      <v-pagination
        v-if="pageCount > 1"
        class="mt-3"
        v-model="page"
        :length="pageCount"
        @input="onPageChange"
      ></v-pagination>
    </v-col>
    <v-dialog v-model="dialog" width="500">
      <v-card>
        <v-card-title class="headline">Embed code</v-card-title>
        <v-card-text>
          <v-textarea ref="code" v-model="code" @click="selectCode"></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="grey" text @click="dialog = false">Close</v-btn>
          <v-btn color="primary" text @click="selectCode">Copy Code</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="newProductDialog" width="500">
      <new-product @close="newProductDialog = false"></new-product>
    </v-dialog>

     <v-dialog v-model="actionDelete" max-width="300">
      <v-card :loading="dialogDelete">
        <v-card-title
          class="subtitle-1"
        >Are you sure you want to delete {{dialogItemDelete && dialogItemDelete.title}}?</v-card-title>
        <v-card-text>You cannot retrieve once deleted.</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn :disabled="dialogDelete" color="primary" text @click="actionDelete = false">Cancel</v-btn>
          <v-btn
            :disabled="dialogDelete"
            color="red"
            text
            @click="confirmDelete(dialogItemDelete.id)"
          >Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
import NewProduct from "./NewProduct";
export default {
  components: {
    NewProduct,
  },
  name: "Products",
  data() {
    return {   
      permitted: false,
      permitEdit: false,
      permitDelete: false,
      permitValidation : {
        add : ['all-permission','c-only'],
        edit:  ['all-permission','u-only'],
        delete:  ['all-permission', 'd-only'],
      },
      authUser: this.$authUser,
      searchProduct: "",
      newProductDialog: false,
      dialogDelete: false,
      dialogItemDelete: null,
      actionDelete: false,
      dialog: false,
      page: 1,
      pageCount: 0,
      itemsPerPage: 10,
      products: [],
      code: "",
    };
  },
  methods: {
    openCode(slug, title) {
      this.dialog = true;
      this.code =
        '<div id="body-spin" style="padding-top:70%;position:relative;"><iframe src="' +
        window.location.origin +
        "/product/" +
        slug +
        '" style="border:none; position:absolute; top:0; left:0;height: 100%;" width="100%" title="' +
        title +
        '" scrolling="no" allowfullscreen="allowfullscreen"></iframe></div>';
    },
    selectCode() {
      let theCode = this.$refs.code.$el.querySelector("textarea");
      theCode.select();
      document.execCommand("copy");
    },
    actionFn(item) {
      
      this.actionDelete = true;
      this.dialogItemDelete = Object.assign({}, item);
      
    },
    confirmDelete(item) {
       
      this.dialogDelete = true;
      axios
        .post("/product/delete/" + item)
        .then((response) => {
          this.actionDelete = false; 
          this.dialogDelete = false;

          var curPage = this.page;

          if(this.page > 1 && this.products.length == 1){
             curPage = this.page-1
          }
          this.getProducts(curPage);
        })
        .catch((error) => {
          this.dialogDelete = false; 
          console.log("Error Deleting Items");
          console.log(error);
        });
    },
    editProduct(i) {
      this.$router.push("/builder/product/edit/" + i);
    },
    getProducts(p) {  
    
      axios
        .get("/builder/products/all/?page=" + p)
        .then((response) => {
          this.products = response.data.data;
          this.page = response.data.current_page;
          this.pageCount = response.data.last_page; 
        })
        .catch((error) => {
          console.log("Error: " + error);
        });
    },
    onPageChange() {
      this.getProducts(this.page);
    },
    saveProduct() {
      let data = {
        title: "auto draft",
        slug: "auto-draft",
      };
      axios
        .post("/builder/product/store", data)
        .then((response) => {
          this.valid = true;
          this.$router.push("/builder/product/edit/" + data.slug);
        })
        .catch((error) => {
          console.log(error.response);
        });
    },
    searchButton(){ 
      if(this.searchProduct){
          axios 
            .post("/builder/products/searchProduct/" + this.searchProduct)
            .then((response) => {
              this.products = response.data.data;
              this.page = response.data.current_page;
              this.pageCount = response.data.last_page;
            })
            .catch((error) => {
              
              console.log("Error Deleting Items");
              console.log(error);
            });
      }else{
        this.getProducts(1);  
      }
    }, 
    permissionAccess(allowed){  
          
          //this.authUser.permissions.forEach(element => allowed['add'].includes(element.slug) ? this.permitted = true : this.permitted = false );  
          this.authUser.permissions.forEach((element) => {
                                                            if(this.permitted != true){ 
                                                              allowed['add'].includes(element.slug) ? this.permitted = true : this.permitted = false;
                                                            }
                                                            if(this.permitEdit != true){ 
                                                              allowed['edit'].includes(element.slug) ? this.permitEdit = true : this.permitEdit = false;
                                                            }
                                                            if(this.permitDelete != true){ 
                                                              allowed['delete'].includes(element.slug) ? this.permitDelete = true : this.permitDelete = false;
                                                            } 
                                                        } );   
      }

  }, // end method
  
  mounted() {
    // Product New Button - Permission
    this.permissionAccess(this.permitValidation);
    this.getProducts(1); 
  },
};
</script>

<style>
</style>
