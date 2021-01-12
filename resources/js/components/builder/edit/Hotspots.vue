<template>
  <div>
    <v-btn small class="primary mb-3" @click="createHotspot">Add Hotspot</v-btn>
    <v-dialog v-model="newHotspotDialog" color="white" width="500">
      <v-card class="mr-auto pa-3" max-width="600">
        <v-form ref="form"  @submit.prevent="submitNewHotspot" lazy-validation>
          <h3 class="font-weight-light">New Hotspot</h3> 
          <v-text-field
            v-model="hotspotTitle"
            label="Hotspot Title"
            v-on:keydown.enter.prevent="submitNewHotspot"
            required
          ></v-text-field>
            <!-- v-on:keyup.enter="submitNewHotspot" -->
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text color="grey" @click.prevent="newHotspotDialog = false">Cancel</v-btn>
            <v-btn color="primary" @click.prevent="submitNewHotspot">Create</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
    <div v-if="itemHotspots.length == 0" class="pa-3 text-center mt-3">No Hotspot Found</div>
    <v-card v-else class="custom-scroll">
      <v-data-table
        :headers="headers"
        :items="itemHotspots"
        single-expand
        :expanded.sync="expanded"
        item-key="id"
        disable-pagination
        hide-default-footer
      >
        <template v-slot:[`item.title`]="{ item }">
           <span>{{item.title.length > 15 ? item.title.substring(0, 15)+'..' : item.title}}</span>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <!-- <v-icon small title="Set Hotspot" color="primary" :class="'mr-2 default-hp hp-'+item.id " @click="setHotspot(item)">mdi-plus-thick</v-icon> -->
          <v-icon small title="Edit Hotspot" @click="editItem(item)">mdi-dots-vertical</v-icon>
        </template>
      </v-data-table>
      <!-- <template v-slot:[`item.actions`]="{ item }"> -->
      <!-- && hotspotForm == hotspot.id" -->
    </v-card>
    <v-dialog v-model="editDialog" width="600px">
      <v-card>
        <v-card-title>
          <h4 class="pb-2">Manage Hotspot</h4>
        </v-card-title>
        <v-card-text>
          <form>
            <v-text-field v-model="title" outlined label="Title" required class="py-0" dense></v-text-field>
            <v-select
              v-model="type"
              :items="types"
              label="Type"
              outlined
              required
              class="py-0"
              dense
            ></v-select>
            <v-textarea v-model="description" outlined dense label="Description" class="py-0" value></v-textarea>
            <v-text-field v-model="ctaLabel" outlined dense label="CTA Label" required class="py-0"></v-text-field>
            <v-text-field v-model="ctaUrl" outlined dense label="CTA URL" required class="py-0"></v-text-field>
            <v-checkbox
              v-model="ctaNewTab"
              outlined
              dense
              label="Open in new tab?"
              class="py-0 ma-0"
            ></v-checkbox>
            <div class="d-flex">
              <v-text-field
                v-model="image"
                outlined
                dense
                label="Image URL"
                required
                class="py-0 mr-1"
              ></v-text-field>
              <v-btn class="mt-2" small icon @click="openMediaFiles">
                <v-icon small>mdi-folder-image</v-icon>
              </v-btn>
            </div>
            <div class="d-flex justify-end">
              <v-btn text color="error" @click="deleteHotspot()">Delete</v-btn>
              <v-spacer></v-spacer>
              <v-btn class="mr-1" text color="grey" @click="editDialog = false">cancel</v-btn>
              <v-btn class="primary" @click="updateHotspot()">update</v-btn>
            </div>
          </form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="deleteDialog" max-width="300">
      <v-card>
        <v-card-title class="subtitle-1">Confirm delete</v-card-title>
        <v-card-text>Are you sure you want to delete <strong class="black--text">{{title ? title : 'this hotspot'}}</strong>?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="grey" text @click="deleteDialog = false">cancel</v-btn>
          <v-btn color="primary" text @click="confirmDelete">Confirm</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <media-files :mediaOptions="mediaFilesSettings" @responded="mediaResponse" />
  </div>
</template>

<script>
/**
 * Hotspot Process
 * 1. Select Item/Scene
 * 2. Add Hotpots
 *    a. Multiple Hotspots in 1 Item
 *    b. Save on stop
 * 3. Save Hotspot Settings in current Item
 * repeat
 */
import MediaFiles from "../../MediaFiles";
export default {
  props: {
    product: {
      type: String,
      default: "",
    },
    item: {
      type: Number,
      default: null,
    },
    currentPanel: {
      type: String,
      default: ""
    }
  },
  components: {
    MediaFiles,
  },
  data() {
    return {
      authUser: this.$authUser,
      deleteDialog: false,
      editDialog: false,
      dialogHotspot: [],

      expanded: [],
      singleExpand: false,
      headers: [
        {
          text: "Hotspots",
          align: "start",
          sortable: false,
          value: "title",
        },
        { text: "Actions", align: "end", value: "actions", sortable: false },
      ],

      disableSet: false,
      toDisableHotspot: [],

      setButton: {
        status: true,
        msg: "",
      },

      // Form
      dialogHotspotId: [],
      title: "",
      description: "",
      ctaLabel: "",
      ctaUrl: "",
      ctaNewTab: "",
      type: "",
      image: "",
      hotspotContent: "",
      types: ["info", "scene"],

      editing: false,
      hotspotData: null,

      newHotspotDialog: false,
      hotspotTitle: "",
      itemHotspots: [],

      selectedItem: [],

      // Media Files
      mediaFilesSettings: {
        dialog: true,
        dialogStatus: false,
        user: this.$authUser,
        action: "save",
        data: null,
        product: this.product,
        itemType: "image",
        returnUrl: true,
      },
    };
  },
  watch: {
    currentPanel: function (val) {
      this.currentPanel = val;
      this.getAllHotspots();
    },
    item: function (val) {
      this.selectedItem = val;
      // console.log(val)

      // Set Button
      this.setButton.status = true;
      // console.log(this.selectedItem.id);
    },
  },
  methods: {
    deleteHotspot() {
      this.deleteDialog = true;
    },
    confirmDelete() {
      // Delete hotspot
      // Delete relationship to item?
       axios
        .post("/hotspot/delete/"+this.dialogHotspotId)
        .then((response) => {
          $("#"+this.dialogHotspotId).remove();
          this.deleteDialog = false;
          setTimeout(() => {
            this.editDialog = false;
          }, 100);
          this.getAllHotspots();
        })
        .catch((error) => {
          console.log("Error fetching items");
          console.log(error);
        });
    },
    editItem(i) {
      this.editDialog = true;

      // Set up dialog value
      let theContent = i.content !== null ? JSON.parse(i.content) : null;
      this.dialogHotspotId = i.id;
      this.title = i.title ? i.title : "Title is not set";
      this.type = i.hotspot_type ? i.hotspot_type : "select";
      this.description =
        theContent !== null && theContent.description !== null
          ? theContent.description
          : "";
      this.ctaLabel =
        theContent !== null && theContent.cta_label !== undefined
          ? theContent.cta_label
          : "";
      this.ctaUrl =
        theContent !== null && theContent.cta_url !== undefined
          ? theContent.cta_url
          : "";
      this.ctaNewTab =
        theContent !== null && theContent.cta_new_tab !== undefined
          ? theContent.cta_new_tab
          : "";
      this.image =
        theContent !== null && theContent.image !== undefined
          ? theContent.image
          : "";
    },
    createHotspot() {
      this.newHotspotDialog = true;
    },
    submitNewHotspot() {
      let data = {
        title: this.hotspotTitle,
        product_id: this.product,
        hotspot_for: this.currentPanel && this.currentPanel
      };
      axios
        .post("/hotspot/new", data)
        .then((response) => {
          // console.log(response);
          this.newHotspotDialog = false;
          this.hotspotTitle = "";
          this.getAllHotspots();
        })
        .catch((error) => {
          console.log("Error fetching items");
          console.log(error);
        });
    },
    mediaResponse(v) {
      this.image = v;
      this.mediaFilesSettings.dialogStatus = !this.mediaFilesSettings
        .dialogStatus;
    },
    openMediaFiles() {
      this.mediaFilesSettings.dialogStatus = !this.mediaFilesSettings
        .dialogStatus;
    },
    updateHotspot() {
      let c = {
        description: this.description,
        cta_label: this.ctaLabel,
        cta_url: this.ctaUrl,
        cta_new_tab: this.ctaNewTab,
        image: this.image,
      };
      axios
        .post("/hotspot/update/" + this.dialogHotspotId, {
          title: this.title,
          hotspot_type: this.type,
          product_id: this.product,
          content: JSON.stringify(c),
        })
        .then((response) => {
          this.getAllHotspots();
          //console.log(response);
          this.editDialog = false;
        })
        .catch((error) => {
          // this.editDialog = false;
          console.log("Error updating hotspot");
          console.log(error);
        });
    },
    editHotspot(h) {
      // Toggle form
      this.editing = !this.editing;
      this.hotspotData = h.id;
      // Setup Form Data
      let theContent = h.content !== null ? JSON.parse(h.content) : null;
      this.title = h.title ? h.title : "Title is not set";
      this.description =
        theContent !== null && theContent.description !== null
          ? theContent.description
          : "";
      this.ctaLabel =
        theContent !== null && theContent.ctaLabel !== undefined
          ? theContent.ctaLabel
          : "";
      this.ctaUrl =
        theContent !== null && theContent.ctaUrl !== undefined
          ? theContent.ctaUrl
          : "";
      this.ctaNewTab =
        theContent !== null && theContent.ctaNewTab !== undefined
          ? theContent.ctaNewTab
          : "";
      this.image =
        theContent !== null && theContent.image !== undefined
          ? theContent.image
          : "";
      this.hotspotContent = {
        description: this.description,
        cta_label: this.ctaLabel,
        cta_url: this.ctaUrl,
        cta_new_tab: this.ctaNewTab,
        image: this.image,
      };
    },
    setHotspot(h) {
    $(".hp-"+h.id).hide();
    if(this.currentPanel == 'interior'){
      this.$emit("emitInteriorHotspot", h);
    }else{
      let hotspotToEmit = {
        id: h.id,
        item_id: this.selectedItem,
        hotspot_settings: [{
            top: '5%',
            left: '5%',
            display: 'block',
            hotspot_id: h.id,
            item_id: this.selectedItem,
            hotspot_settings: '{"top":"5%","left":"5%","display":"block"}',
          }],
        title: h.title

      };


      if (this.selectedItem.length != 0) {

        if($("div").hasClass("hotspot-id-"+h.id)){

            $(".hotspot-id-"+h.id).css({
              left:"5%",
              top: "5%",
              display: "block"
            });
        }else{
          this.$emit("emitHotspot", hotspotToEmit);
        }
        this.toDisableHotspot.push(h.id);
      } else {
        this.setButton.status = false;
        this.setButton.msg = "Please select an item first";
      }
    }
    },
    getAllHotspots() {
      let panel = this.currentPanel;
      axios
        .get("/hotspot/by-product/" + this.product + "/" + panel)
        .then((response) => {
          this.itemHotspots = response.data;
          // console.log(this.itemHotspots);
          // console.log("sdfds")
        //   console.log(this.itemHotspots)
        })
        .catch((error) => {
          console.log("Error fetching items");
          console.log(error);
        });
    },
  },
  created() {
    this.getAllHotspots();
  },
};
</script>
<style lang="scss" scoped>
.custom-scroll {
  overflow-y: scroll;
  max-height: 550px;
}
.custom-scroll::-webkit-scrollbar {
  width: 10px;
}
.custom-scroll::-webkit-scrollbar-track {
  background-color: #f9f9f9;
}
.custom-scroll::-webkit-scrollbar-thumb {
  border-radius: 10px;
  -webkit-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.15);
  box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.15);
}
</style>
