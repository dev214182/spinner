<template>
  <div class="builder-container">
    <v-row class="px-3">
      <div class="col-12 pb-3 pa-0 d-flex">
        <v-btn
          class="mr-2"
          large
          :color="`${activateExterior == true ? 'yellow accent-4' : 'primary' }`"
          @click="selectPanel('exterior')"
        >Exterior</v-btn>
        <v-btn
          class="mr-2"
          large
          :color="`${activateInterior == true ? 'yellow accent-4' : 'primary' }`"
          @click="selectPanel('interior')"
        >Interior</v-btn>
        <v-btn
          class="mr-2"
          large
          :color="`${activateVideo == true ? 'yellow accent-4' : 'primary' }`"
          @click="selectPanel('video')"
        >Video</v-btn>
        <v-btn class="ml-auto" large :href="`${previewUrl}`" target="_blank">Preview</v-btn>
      </div>
    </v-row> 
    <v-divider></v-divider>
    <v-row>
      <div class="col-12 col-md-3" v-show="activateVideo == false">
        <Hotspots
          :item="selected_item"
          :auth-user="authUser"
          :product="this.$route.params.id"
          :current-panel="selected_panel_prop"
          @emitHotspot="hotspotToSet"
          @emitInteriorHotspot="hotspotToInterior"
        />
      </div>
      <div class="col-12 col-md-9" v-if="activateExterior == true" cols="9">
        <exterior-panel
          :auth-user="authUser"
          :product="this.$route.params.id"
          :product-details="product"
          :selected-hotspot-prop="selected_hotspot_prop"
          @selectedItem="theSelectedItem"
        />
      </div>
      <div class="col-12 col-md-9" v-if="activateInterior == true" cols="9">
        <interior-panel
          :auth-user="authUser"
          :product="this.$route.params.id"
          :selected-interior-hotspot="selected_interior_hotspot_prop"
        />
      </div>
      <div class="col-12 col-md-9" v-if="activateVideo == true">
        <video-panel :auth-user="authUser" :product="this.$route.params.id" />
      </div>
    </v-row>
  </div>
</template>

<script>
import Hotspots from "./edit/Hotspots";
import ExteriorPanel from "./edit/ExteriorPanel";
import InteriorPanel from "./edit/InteriorPanel";
import VideoPanel from "./edit/VideoPanel";
export default {
  components: {
    Hotspots,
    ExteriorPanel,
    InteriorPanel,
    VideoPanel,
  },
  data() {
    return {
      authUser: this.$authUser,
      selected_panel_prop: "exterior",
      activateExterior: true,
      activateInterior: false,
      activateVideo: false,
      product: {},
      selected_item: null,
      selected_hotspot_prop: null,

      selected_interior_hotspot_prop: null,

      previewUrl: "",
      baseUrl: window.location.origin,
    };
  },
  methods: {
    async fetchCurrentProduct(){
     
      await axios
        .get("/builder/products/fetch/" + this.$route.params.id)
        .then((response) => {
           
            this.product = response.data; 
           
        })
        .catch((error) => {
          console.log("Error fetching items");
          console.log(error);
        });  
         
         this.setPreviewUrl();
    },
    setPreviewUrl() { 
      
      this.previewUrl = this.baseUrl+'/product/'+this.product.slug;
        
      
    },
    theSelectedItem(v) {
      this.selected_item = v;
    },
    hotspotToInterior(v) {
      // Check if hotspot added are equal
      if (
        JSON.stringify(this.selected_interior_hotspot_prop) != JSON.stringify(v)
      ) {
        this.selected_interior_hotspot_prop = v;
      }
    },
    hotspotToSet(v) {
      // Check if hotspot added are equal
      if (JSON.stringify(this.selected_hotspot_prop) != JSON.stringify(v)) {
        this.selected_hotspot_prop = v;
      }
    },
    selectPanel(panel) {
      // Exterior
      if (panel == "exterior") {
        this.activateExterior = true;
        this.activateInterior = false;
        this.activateVideo = false;
        this.selected_panel_prop = "exterior";
      } else if (panel == "interior") {
        // Interior
        this.selected_panel_prop = "interior";
        this.activateInterior = true;
        this.activateExterior = false;
        this.activateVideo = false;
      } else {
        this.activateVideo = true;
        this.activateInterior = false;
        this.activateExterior = false;
      }
    },
  },
  created() {
    this.fetchCurrentProduct();
   
  },
};
</script>
