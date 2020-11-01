<template>
  <v-row>
    <v-col cols="12" class="px-5">
      <div class="d-flex align-center mb-5">
        <!-- to="/builder/product/new" -->
        <v-btn
          @click="userNewFormDialog = true"
          class="mr-3 text--primary"
          elevation="2"
          fab
          dark
          x-small
          color="white"
        >
          <v-icon dark>mdi-plus</v-icon>
        </v-btn>
        <h3 class="font-weight-light">Users Account</h3>

         <v-spacer></v-spacer>
           
        <v-text-field
            v-model="searchData"
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
                <th class="text-left">Name</th>
                <th class="text-left">Email</th>
                <!-- <th class="text-left">Status</th> -->
                <th class="text-left">Phone</th>
                <th class="text-left">Role</th>
                <th class="text-left">Permissions</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in orgUsers" :key="item.id">
                <td>{{ item.name }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.phone ? item.phone : "not set" }}</td>
                <td :class="`${item.role < 3 ? 'primary--text': ''}`">{{ printRole(item.role) }}</td>
                <td> <div  v-for="permit in item.permissions" :key="permit.id">{{ printPermission(permit.slug) }}</div></td>
                <!-- <td
                  :class="`${item.status == 1 ? 'green--text' : 'blue--text'} text-left`"
                >{{ item.status == 1 ? 'active' : 'inactive' }}</td>-->
                <td class="text-right">
                  <div  v-if="authUser.id != item.id">
                  <v-btn  title="Edit" icon small @click="actionFunction('edit', item)" color="blue"> 
                    <v-icon small>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn
                    title="Delete"
                    icon
                    small
                    @click="actionFunction('delete', item)"
                    color="red"
                  >
                    <v-icon small>mdi-trash-can-outline</v-icon>
                  </v-btn>
                  </div>
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
     <v-dialog v-model="userNewFormDialog" max-width="600px">
      <v-card>
        <v-card-title>
          <h4 class="pb-2">User Form</h4>
        </v-card-title>
        <v-card-text>
           <ValidationObserver ref="observer" >
          <form>
            <ValidationProvider v-slot="{ errors }" name="Name" rules="required|min:3">
            <v-text-field v-model="newname" outlined label="Name" :error-messages="errors" required class="py-0" dense></v-text-field>
             </ValidationProvider>
            <ValidationProvider v-slot="{ errors }" name="Phone" rules="required|min:8">
            <v-text-field v-model="newphone" outlined label="Phone" :error-messages="errors" required class="py-0" dense></v-text-field>
            </ValidationProvider>
            <ValidationProvider v-slot="{ errors }" name="Email" rules="required|email|min:10">
            <v-text-field v-model="newemail" outlined label="Email" :error-messages="errors" required class="py-0" dense></v-text-field>
             </ValidationProvider>
            <ValidationProvider v-slot="{ errors }" name="Password" rules="required|min:8">
            <v-text-field
                v-model="password"
                :error-messages="errors"
                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" 
                :type="show1 ? 'text' : 'password'"
                outlined label="Password" required class="py-0" dense 
                @click:append="show1 = !show1"
              ></v-text-field>
               </ValidationProvider>
          <ValidationProvider v-slot="{ errors }" name="Role" rules="required">
            <v-select
              v-model="newrole"
              :items="roleItems"
              :error-messages="errors"
              item-text="role"
              item-value="value"
              label="Role"
              persistent-hint
              return-object
              outlined
              single-line
              required
              class="py-0"
              dense
            ></v-select>
             </ValidationProvider>
             
             <v-row>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                  <v-checkbox 
                   v-model="selectedPermission"
                    label="All Permission"
                    color="red"
                    value="1"
                    hide-details
                  ></v-checkbox>
                  <v-checkbox
                    v-model="selectedPermission"
                    label="Create"
                    color="red"
                    value="2"
                    hide-details
                  ></v-checkbox> 
              </v-col>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                  <v-checkbox 
                  v-model="selectedPermission"
                    label="Update"
                    color="red"
                    value="3"
                    hide-details
                  ></v-checkbox>
                  <v-checkbox 
                  v-model="selectedPermission"
                    label="Delete"
                    color="red"
                    value="5"
                    hide-details
                  ></v-checkbox>
                
                </v-col>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                   <v-checkbox  
                   v-model="selectedPermission"
                    label="Viewing Only"
                    color="red"
                    value="4"
                    hide-details
                  ></v-checkbox>
                </v-col>
          </v-row>
            <div class="d-flex justify-end">
              <v-btn class="mr-1" text color="grey" @click="userNewFormDialog = false">cancel</v-btn>
              <v-btn class="primary" @click="saveUser('save')">Update</v-btn>
            </div>
          </form>
           </ValidationObserver>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="userFormDialog" max-width="600px">
      <v-card>
        <v-card-title>
          <h4 class="pb-2">User Form</h4>
        </v-card-title>
        <v-card-text>
          <form>
            <v-text-field v-model="name" outlined label="Name" required class="py-0" dense></v-text-field>
            <v-text-field v-model="email" outlined label="Email" required class="py-0" dense></v-text-field>
            <v-text-field v-model="phone" outlined label="Phone" required class="py-0" dense></v-text-field>
            <!-- :hint="`${role.role}, ${role.value}`" -->
            <v-select
              v-model="role"
              :items="roleItems"
              item-text="role"
              item-value="value"
              label="Role"
              persistent-hint
              return-object
              outlined
              single-line
              required
              class="py-0"
              dense
            ></v-select>

              <v-row>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                  <v-checkbox 
                   v-model="updatePermission"
                    label="All Permission"
                    color="red"
                    value="1"
                    hide-details
                  ></v-checkbox>
                  <v-checkbox
                    v-model="updatePermission"
                    label="Create"
                    color="red"
                    value="2"
                    hide-details
                  ></v-checkbox> 
              </v-col>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                  <v-checkbox 
                  v-model="updatePermission"
                    label="Update"
                    color="red"
                    value="3"
                    hide-details
                  ></v-checkbox>
                  <v-checkbox 
                  v-model="updatePermission"
                    label="Delete"
                    color="red"
                    value="5"
                    hide-details
                  ></v-checkbox>
                
                </v-col>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                   <v-checkbox  
                   v-model="updatePermission"
                    label="Viewing Only"
                    color="red"
                    value="4"
                    hide-details
                  ></v-checkbox>
                </v-col>
          </v-row>
            <div class="d-flex justify-end">
              <v-btn class="mr-1" text color="grey" @click="userFormDialog = false">cancel</v-btn>
              <v-btn class="primary" @click="saveUser('update')">Update</v-btn>
            </div>
          </form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="300px">
      <v-card>
        <v-card-title class="subtitle-1">Confirm delete</v-card-title>
        <v-card-text>
          Are you sure you want to delete
          <strong>{{dialogData.name}}</strong>'s account?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="grey" text @click="deleteDialog = false">cancel</v-btn>
          <v-btn color="primary" text @click="confirmDelete">Confirm</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar
      v-model="snackbar" 
      :color="color" 
      :right="x === 'right'"
      :timeout="timeout"
      :top="y === 'top'"
      :vertical="mode === 'vertical'"
    >
      {{ text }}  
    </v-snackbar>

  </v-row>
</template>

<script>
 import { required, name } from 'vee-validate/dist/rules';
import {  ValidationObserver, ValidationProvider } from  'vee-validate/dist/vee-validate.full';
export default {
   components: {
      ValidationProvider,
      ValidationObserver,
    },
  data() {
    return {
      selectedPermission: ['1'],
      updatePermission: [],
      arr : [],
      authUser: this.$authUser,
      color: '',
      mode: 'vertical',
      snackbar: false,
      text: '',
      timeout: 5000,
      x: '',
      y: '',

      searchData: "",
      //pagination
      page: 1,
      pageCount: 0,
      itemsPerPage: 10,

      dialogData: [],
      userNewFormDialog: false,
      userFormDialog: false,
      deleteDialog: false,
      orgUsers: [], // Company Users

      roleItems: [
        { role: "Administrator", value: 2 },
        { role: "Team Editor", value: 3 },
         { role: "Free User", value: 4 },
      ],
      // permissionItems: [
      //   { role: "All Access", value: 1 },
      //   { role: "Create", value: 2 },
      //   { role: "Update", value: 3 },
      //   { role: "Viewing", value: 4 },
      //   { role: "Delete", value: 5 },
      // ],
      show1: false,
      newname: "",
      newemail: "",
      newpassword: "",
      newphone: "",
      newrole: {},

      name: "",
      email: "",
      password: "",
      phone: "",
      role: {},
    };
  },
  methods: {
    printRole(role) {
      //console.log(role);
      let roleLabel = "";
      if (role == 1) {
        roleLabel = "Super Admin";
      } else if (role == 2) {
        roleLabel = "Administrator";
      } else if (role == 3) {
        roleLabel = "Editor";
      } else {
        roleLabel = "Free";
      }
      return roleLabel;
    },
    printPermission(role) { 
      let roleLabel = "";
      if (role == 'all-permission') {
        roleLabel = "All Access";
      } else if (role == 'c-only') {
        roleLabel = "Create";
      } else if (role == 'u-only') {
        roleLabel = "Update";
      }else if (role == 'v-only') {
        roleLabel = "View only";
      } else {
        roleLabel = "Delete";
      }
      return roleLabel;
    },
    actionFunction(action, value) {
      this.arr = [];
      this.dialogData = [];
      this.dialogData = value;
      console.log( this.dialogData.permissions);
      if (action == "edit") {
        this.userFormDialog = true;
        this.name = this.dialogData.name;
        this.email = this.dialogData.email;
        this.password = this.dialogData.password;
        this.phone = this.dialogData.phone ? this.dialogData.phone : "";
        this.role = {
          role: this.dialogData.role == 2 ? "Admin" : this.dialogData.role == 2 ? "Editor" : "Free",
          value: this.dialogData.role,
        };
        this.dialogData.permissions.forEach(this.getPermissions);
         
         this.updatePermission = this.arr; 
      } else {
        this.deleteDialog = true;
      }
    },
    getPermissions(item, index) { 
         this.arr.push(''+item.id+''); 
    },
    confirmDelete() {
      axios
        .post("/settings/team/delete/" + this.dialogData.id)
        .then((response) => {
          this.deleteDialog = false;
          this.dialogData = [];
           
           var curPage = this.page;
          
          if(this.page > 1 && this.dialogData.length == 1){
             curPage = this.page-1
          }
          this.getOrgUsers(curPage);
        })
        .catch((error) => {
          console.log("Error Deleting User");
          console.log(error);
        });
    },
    saveUser(action) { 
       
       let customValidate = true;
      let data = {};
      let route = "save";
      if (action == "update") {
        route = "update/" + this.dialogData.id;

          data = {
                    name: this.name,
                    phone: this.phone,
                    role: this.role.value,
                    permissions: this.updatePermission
                  };
        if(this.email != this.dialogData.email){
          data.email = this.email;
        }
      }else{
          this.$refs.observer.validate();

         // if(this.password == null || this.password == "" || this.email == "" || this.email == null || this.newname == "" || this.newname == null) { return false; }
        
          data = {
                    name: this.newname,
                    phone: this.newphone,
                    role: this.newrole.value,
                    email: this.newemail,
                    password: this.password,
                    permissions: this.selectedPermission
                }; 

          $.each(data, function(key, value) {
              if(value === "undefined" || value == "" || value == null){ customValidate = false; return false; }
          });
      } 
       
     if(!customValidate) { return false; }

      axios
        .post("/settings/team/" + route, data)
        .then((response) => {
          if (action == "update") {
            this.userFormDialog = false;
          }else{
             this.userNewFormDialog = false;
             
          }
         
          this.dialogData = [];
           var curPage = this.page;
          
          if(this.page > 1 && this.dialogData.length == 1){
             curPage = this.page-1
          }

            // snackbar
            this.snackbar = true;
            this.color = "success";
            this.x = "right";
            this.y = "top";
            this.text = "Success";

          this.getOrgUsers(curPage);
        })
        .catch((error) => {
          
            if (error.response) {   
              // snackbar
                this.snackbar = true;
                this.color = "error";
                this.x = "right";
                this.y = "top";
                this.text = error.response.data.message; 
            }
        });
    },
    onPageChange() {
      this.getOrgUsers(this.page);
    },
    getOrgUsers(p) {
     
      axios
        .get("/settings/get-org-users/" + this.authUser.company_id+"?page=" + p)
        .then((response) => {
          console.log(response);
          this.orgUsers = response.data.data; 
          this.page = response.data.current_page;
          this.pageCount = response.data.last_page;
          // console.log(response);
        })
        .catch((error) => {
          console.log("Error Fetching Org Users");
          console.log("Error: " + error);
        });
    },
    searchButton(){ 
      if(this.searchData){
          axios
            .post("/settings/team/search_data/" + this.searchData)
            .then((response) => {
             this.orgUsers = response.data.data; 
              this.page = response.data.current_page;
              this.pageCount = response.data.last_page;
            })
            .catch((error) => {
             
              console.log("Error Deleting Items");
              console.log(error);
            });
      }else{
        this.getOrgUsers(1); 
      }
    },
  },
  created() {
    this.getOrgUsers(1);
  },
};
</script>

<style>
</style>
