<template>
  <div class="row">
    <div class="col-12 pt-0 col-md-12 d-flex align-center flex-start">
      <v-card v-if="scenes.length == 0" class="text-center pa-2 mr-2" @click.stop="openMediaFiles">
        <v-icon small>mdi-plus</v-icon>
        <h5 class="font-weight-light">Add Scene</h5>
      </v-card>
      <v-skeleton-loader :loading="loading" type="list-item-avatar-two-line">
        <v-sheet v-if="scenes">
          <v-slide-group show-arrows>
            <v-slide-item v-for="item in scenes" :key="item.id">
              <div class="text-right" style="position:relative;">
                <v-btn
                  fab
                  style="position:absolute;right:0;top:0;z-index:9;height:15px;width:15px;"
                  color="red"
                  class="elevation-0"
                  x-small
                  dark
                  @click="deleteScene(item.id)"
                >
                  <v-icon x-small>mdi-close</v-icon>
                </v-btn>
                <v-card class="my-1 mx-2" active-class="primary" @click="selectedScene(item)">
                  <v-list-item dense two-line>
                    <v-list-item-avatar size="26" color="orange">
                      <v-icon dark small>mdi-panorama-horizontal</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content>
                      <v-list-item-title>{{item.media_file.title ? item.media_file.title.substring(0, 15) : 'Not Set'}}</v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-card>
              </div>
            </v-slide-item>
          </v-slide-group>
        </v-sheet>
        <v-sheet
          v-else
          class="grey lighten-4 pa-5 caption d-flex align-center justify-center"
          style="min-height:75px;"
        >
          <p class="ma-0">
            No scene found. Add your
            <a @click.stop="openMediaFiles">new scene now</a>.
          </p>
        </v-sheet>
      </v-skeleton-loader>
    </div>
    <div class="col-12 py-0 d-flex justify-space-between align-center">
      <v-toolbar dense class="elevation-1">
        <v-toolbar-title>Title</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn small color="primary" @click="saveHotspotSettings()">
          <v-icon small class="mr-1">mdi-check</v-icon>Save Hotspots
        </v-btn>
      </v-toolbar>
    </div>
    <div v-if="selectedItem.length == 0" class="col-12 pt-0">
      <v-sheet
        class="grey lighten-4 mr-auto pa-3 d-flex justify-center align-center elevation-1"
        style="min-height:480px;"
      >
        <p v-if="scenes.length == 0">
          Add your
          <a @click.stop="openMediaFiles">new scene now</a>.
        </p>
        <p v-else>Please select a scene</p>
      </v-sheet>
    </div>
    <div v-else class="col-12 pt-0">
      <div id="panorama" class="elevation-1" style="height:400px;width:100%;margin:0 auto;"></div>

      <div class="row mt-5">
       <v-text-field 
            v-model="default_hfov"
            class="col-2 pt-0"
            label="hfov"  
          ></v-text-field>

           <v-text-field 
            v-model="default_pitch"
            class="col-4 pt-0"
            label="pitch" 
          ></v-text-field>

           <v-text-field 
           v-model="default_yaw"
            class="col-4 pt-0"
            label="yaw" 
          ></v-text-field>
          <v-btn small color="primary" @click="setDefaultPosition()">
          <v-icon small class="mr-1">mdi-check</v-icon>Set Position
        </v-btn>
          </div>
    </div>
    <media-files :mediaOptions="mediaFilesSettings" @responded="mediaResponse" />
    <v-dialog v-model="deleteDialog" max-width="290">
      <v-card>
        <v-card-title class="subtitle-1">Are you sure you want to delete?</v-card-title>
        <v-card-text>Deleting this item will delete the file and hotspot information.</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="grey" text @click="deleteDialog = false">Cancel</v-btn>
          <v-btn color="error" text @click="confirmDelete(toDelete)">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
var toSaveHotspot = [];
import MediaFiles from "../../MediaFiles";
import CreateScene from "./CreateScene";
export default {
  props: {
    product: {
      type: String,
      default: "",
    },
    selectedInteriorHotspot: {
      type: Object,
      default: null,
    },
  },
  components: {
    MediaFiles,
    CreateScene,
  },
  data() {
    return {
      currentSceneId : 0,
      default_yaw : -36.30724382948786,
      default_pitch : -16.834687202204037,
      default_hfov : 50,
      authUser: this.$authUser,
      editingTitle: "",

      fetchedHotspots: [],

      toDelete: [],
      deleteDialog: false,
      loading: false,
      scenes: [],
      selectedItem: [],

      thePanorama: null,
      baseUrl: window.location.origin,

      // Media Files
      mediaFilesSettings: {
        dialog: true,
        dialogStatus: false,
        user: this.$authUser,
        action: "save",
        data: null,
        product: this.product,
        itemType: "panorama",
        watermarkOptions: false,
      },

      hotspotText: "this is the hotspot Text",
    };
  },
  watch: {
    selectedInteriorHotspot: function (v) {
     // console.log(v);
      this.addHotspot(v);
    },
  },
  methods: {
    fetchAllInteriorHotspots() {
      axios
        .get("/hotspot/all/interior/" + this.product)
        .then((response) => {
          // Add hotspots here
          this.fetchedHotspots = response.data.settings;
        })
        .catch((error) => {
          console.log("Error Fetching Interior Hotspots");
          console.log(error);
        });
    },
    setDefaultPosition(){ 
        
      var filter = {
          hfov : this.default_hfov,
          pitch : this.default_pitch,
          yaw : this.default_yaw
      }
    
       let data = {
        item_id : this.currentSceneId,
        interior_settings: JSON.stringify(filter),
      };
      
       axios
        .post("/scene/setposition", data)
        .then((response) => {
          console.log(response);
        })
        .catch((error) => {
          console.log("Error Setting Default Interior Settings");
          console.log(error);
        });
    },
    saveHotspotSettings() {
      var filter = toSaveHotspot.filter(function (el) {
        return el.hotspotsID != null;
      });
      let data = {
        hotspot_settings: JSON.stringify(filter),
      };
      axios
        .post("/hotspot/apply", data)
        .then((response) => {
          toSaveHotspot = [];
        //   console.log(response);
          // this.draggableFunc();
          // this.getHotspotSettings();
        })
        .catch((error) => {
          console.log("Error Applying Hotspots");
          console.log(error);
        });
    },
    removeHotspot() {
      // need hotspotid
      // this.thePanorama.removeHotSpot();
    },
    setHotspotFromDB(selectedHotspot) {
      let interiorSettings = JSON.parse(
        selectedHotspot.hotspot_settings[0].hotspot_settings
      );
      this.thePanorama.addHotSpot({
        draggable: true,
        pitch: interiorSettings.pitch,
        yaw: interiorSettings.yaw,
        hfov: this.thePanorama.getHfov(),
        type: "info",
        text: this.hotspotText,
        cssClass: "interior-hotspot " + selectedHotspot.id,
        createTooltipFunc: this.hotspotUi,
        createTooltipArgs: "<div>" + selectedHotspot.title + "</div>",
      });
      let dPanorama = this.thePanorama;
      this.onMouseUp(dPanorama, selectedHotspot.id);
    },
    addHotspot(selectedHotspot) {
      this.thePanorama.addHotSpot({
        draggable: true,
        pitch: this.thePanorama.getPitch(),
        yaw: this.thePanorama.getYaw(),
        hfov: this.thePanorama.getHfov(),
        type: "info",
        text: this.hotspotText,
        cssClass: "interior-hotspot " + selectedHotspot.id,
        createTooltipFunc: this.hotspotUi,
        createTooltipArgs: "<div>" + selectedHotspot.title + "</div>",
        //   sceneId: sceneTitle,
      });
      let dPanorama = this.thePanorama;
      this.onMouseUp(dPanorama, selectedHotspot.id);
    },
    hotspotUi(hotSpotDiv, args) {
      // console.log(hotSpotDiv);
      // console.log(args);
      hotSpotDiv.classList.add("custom-tooltip");
      var span = document.createElement("span");
      span.innerHTML = args;
      hotSpotDiv.appendChild(span);
      span.classList.add("hotspot-label");
      // span.style.width = span.scrollWidth - 20 + "px";
      // span.style.marginLeft =
      //   -(span.scrollWidth - hotSpotDiv.offsetWidth) / 2 + "px";
      // span.style.marginTop = -span.scrollHeight - 12 + "px";
    },
    onMouseUp(p, id) {
      //   console.log(p);
      toSaveHotspot[id] = {};
      let selectedItem = this.selectedItem.id;

      setTimeout(() => {
        $(".interior-hotspot." + id).on("mouseup", function (e) {
          let settingsToString = {
            pitch: p.mouseEventToCoords(e)[0],
            yaw: p.mouseEventToCoords(e)[1],
          };
          toSaveHotspot[id]["hotspotsID"] = id;
          toSaveHotspot[id]["itemID"] = selectedItem;
          toSaveHotspot[id]["hotspotSettings"] = settingsToString;
          // push to global variable
          // console.log(p.mouseEventToCoords(e));

          //console.log(toSaveHotspot);
        });
      }, 500);
    },
    onDebugger() {
      this.debugger = !this.debugger;
      console.log("Debugger :"+this.debugger);
    },

    deleteScene(i) {
      this.toDelete = i;
      this.deleteDialog = true;
    },
    confirmDelete(item) {
      axios
        .post("/item/delete/" + item)
        .then((response) => {
          this.getScenes();
          this.deleteDialog = false;
          this.selectedItem = [];
          //console.log(response);
        })
        .catch((error) => {
          console.log("Error: " + error);
          console.log(this.scenes);
        });
    },
    loadPanorama(i = null) {
      // console.log(i.media_file.original_name);
      let sceneTitle = "";
      sceneTitle = i !== null ? i.media_file.original_name : "not-set";
      this.thePanorama = pannellum.viewer("panorama", {
        hotSpotDebug: true,
        autoLoad: false,
        default: {
          // author: "Matthew Petroff",
          firstScene: sceneTitle,
          sceneFadeDuration: 1000,
        },
        scenes: {},
      });
      let interiorSettings = JSON.parse(
        i.media_file.interior_settings
      );
      var d_hfov, d_pitch, d_yaw;
      if(interiorSettings){
        d_hfov = interiorSettings.hfov;
        d_pitch = interiorSettings.pitch;
        d_yaw = interiorSettings.yaw;
      }else{
        d_hfov = this.default_hfov;
        d_pitch = this.default_pitch;
        d_yaw = this.default_yaw;
      }

      this.thePanorama.addScene(sceneTitle, {
        hfov: d_hfov,
        pitch: d_pitch,
        yaw: d_yaw,
        type: "equirectangular",
        panorama:
          this.baseUrl +
          "/storage/uploads/" +
          this.authUser.company_id +
          "/" +
          i.media_file.path,
        hotSpots: [],
      });

      this.currentSceneId = i.media_file_id;
      this.thePanorama.on("animatefinished",()=>{
      
        this.default_hfov = Math.round(this.thePanorama.getHfov());
        this.default_pitch = this.thePanorama.getPitch();
        this.default_yaw = this.thePanorama.getYaw();
    });

      this.thePanorama.loadScene(sceneTitle);
    },
    selectedScene(i) {
      this.selectedItem = Object.assign({}, i);
      if (this.thePanorama != null) {
        this.thePanorama.destroy();
        console.log("Destroyed Panorama");
      }

      // Fetch all saved hotspots
      this.fetchAllInteriorHotspots();
      console.log( this.selectedItem);
      setTimeout(() => {
        // To prevent "TypeError: Cannot read property 'classList' of null"
        this.loadPanorama(this.selectedItem);
      }, 300);

      setTimeout(() => {
        // To prevent empty hotspots on first load
        this.fetchedHotspots.map((hotspot) => {
          this.setHotspotFromDB(hotspot);
        });
      }, 600);

    },
    mediaResponse(v) {
      this.mediaFilesSettings.dialogStatus = false;
      if (v.status == "success") {
        // this.scenes = [];
        this.getScenes();
      }
    },
    openMediaFiles() {
      this.mediaFilesSettings.dialogStatus = !this.mediaFilesSettings
        .dialogStatus;
    },
    getScenes() {
      this.loading = true;
      axios
        .get("/item/scenes/by-product/" + this.product)
        .then((response) => {
          // this.scenes = Object.assign({}, response.data);
          this.scenes = response.data;
          this.loading = false;
          // console.log(this.scenes.length);
        })
        .catch((error) => {
          console.log("Error: " + error);
          console.log(this.scenes);
        });
    },
  },
  created() {
    this.getScenes();
  },
};
</script>
<style lang="scss">
.interior-hotspot {
  cursor: move;
  position: absolute;
  z-index: 2;
  display: block;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  background: #d95353;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
  -webkit-transition: background-color 0.2s;
  -moz-transition: background-color 0.2s;
  transition: background-color 0.2s;
  &::after {
    content: "";
    position: absolute;
    left: 50%;
    top: 50%;
    bottom: auto;
    right: auto;
    transform: translateX(-50%) translateY(-50%);
    background-color: #ffffff;
    transition-property: transform;
    transition-duration: 0.2s;
    width: 2px;
    height: 12px;
  }
  &::before {
    content: "";
    position: absolute;
    left: 50%;
    top: 50%;
    bottom: auto;
    right: auto;
    transform: translateX(-50%) translateY(-50%);
    background-color: #ffffff;
    transition-property: transform;
    transition-duration: 0.2s;
    width: 12px;
    height: 2px;
  }
  .hotspot-label {
    // overflow: hidden;
    // text-overflow: ellipsis;
    // display: -webkit-box;
    // -webkit-line-clamp: 1; /* number of lines to show */
    // -webkit-box-orient: vertical;

    line-height: 24px;
    position: absolute;
    left: 100%;
    right: auto;
    top: 0;
    bottom: auto;
    background-color: #fff;
    border-radius: 10px;
    padding: 0 8px;
    width: 120px;
    max-width: 120px;
    margin-left: 5px;
    &::before {
      content: "";
      width: 0px;
      height: 0px;
      border-top: 8px solid transparent;
      border-bottom: 8px solid transparent;
      border-right: 8px solid #fff;
      position: absolute;
      right: 98%;
      top: 0;
      bottom: auto;
      left: auto;
      margin-top: 4px;
    }
  }
}
</style>
