<template>
  <div>
    <v-navigation-drawer v-model="drawer" app clipped width="200px">
      <v-list dense>
        <v-list-item
          v-for="item in items"
          :key="item.text"
          link
          :to="item.location"
        >
          <v-list-item-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>{{ item.text }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-spacer></v-spacer>
        <!-- Client Settings -->
        <v-subheader
          v-if="authUser.role < 5"
          class="mt-4 mt-auto grey--text text--darken-1"
          >Settings</v-subheader
        >
        <v-list-item
          link
          v-for="setting in clientSettings"
          :key="setting.text"
          :to="setting.location"
        >
          <v-list-item-action>
            <v-icon color="grey darken-1">{{ setting.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-title class="grey--text text--darken-1">{{
            setting.text
          }}</v-list-item-title>
        </v-list-item>
        <v-subheader
          v-if="authUser.role == 1"
          class="mt-4 mt-auto grey--text text--darken-1"
          >Admin</v-subheader
        >
        <div v-if="authUser.role == 1">
          <v-list-item
            link
            v-for="adminSetting in adminSettings"
            :key="adminSetting.text"
            :to="adminSetting.location"
          >
            <v-list-item-action tion>
              <v-icon color="grey darken-1">{{ adminSetting.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-title class="grey--text text--darken-1">{{
              adminSetting.text
            }}</v-list-item-title>
          </v-list-item>
        </div>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar app clipped-left color="indigo darken-4" dense>
      <v-app-bar-nav-icon
        @click.stop="drawer = !drawer"
        color="white"
      ></v-app-bar-nav-icon>
      <v-toolbar-title class="mr-12 align-center">
        <span class="title white--text">Gallega Spinner</span>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-menu
        v-model="menu"
        :close-on-content-click="false"
        :nudge-width="150"
        transition="slide-y-transition"
        offset-y
        :nudge-bottom="3"
      >
        <template v-slot:activator="{ on }">
          <v-btn text icon v-on="on">
            <v-avatar size="30">
              <img
                :src="`${baseUrl + '/images/placeholder-user.png'}`"
                :alt="authUser.name"
              />
            </v-avatar>
          </v-btn>
        </template>
        <v-card>
          <v-list>
            <v-list-item>
              <v-list-item-avatar>
                <img
                  :src="`${baseUrl + '/images/placeholder-user.png'}`"
                  :alt="authUser.name"
                />
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title>{{ authUser.name }}</v-list-item-title>
                <v-list-item-subtitle>{{
                  authUser.email
                }}</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn depressed v-on:click="logout" width="100%">Logout</v-btn>
          </v-card-actions>
        </v-card>
      </v-menu>
    </v-app-bar>
  </div>
</template>

<script>
export default {
  data() {
    return {
      authUser: this.$authUser,
      baseUrl: window.location.origin,
      drawer: null,
      menu: false,
      items: [
        {
          icon: "mdi-view-dashboard",
          text: "Dashboard",
          location: "/dashboard",
        },
        {
          icon: "mdi-diamond-stone",
          text: "Products",
          location: "/builder/products",
        },
      ],
      clientSettings: [ {
            icon: "mdi-account",
            text: "Account",
            location: "/settings/account",
          }],

      adminSettings: [
                      {
                        icon: "mdi-account-group",
                        text: "Companies",
                        location: "/settings/companies",
                      }
                    ],
    };
  },
  methods: {
    logout: function (event) {
      event.preventDefault();
      document.getElementById("logout-form").submit();
    },

    settings: function () {
      
      
      if (this.authUser.role < 4) {
        let clSet = [{
                        icon: "mdi-watermark",
                        text: "Watermark",
                        location: "/settings/watermarks",
                      },
                      {
                        icon: "mdi-star",
                        text: "Organization",
                        location: "/settings/organization",
                      },
                      {
                        icon: "mdi-account-group",
                        text: "Teams",
                        location: "/settings/teams",
                      }];
        clSet.push(this.clientSettings[0])
        this.clientSettings = clSet;
      }else  if (this.authUser.role == 4) {
        let clSet = [{
                        icon: "mdi-watermark",
                        text: "Watermark",
                        location: "/settings/watermarks",
                      }];
        clSet.push(this.clientSettings[0])
        this.clientSettings = clSet;
      }
      
    },
  },
  created: function () {
    this.settings();
  },
};
</script>
