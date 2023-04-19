<template>
  <div class="w-100 py-3">
    <div class="row">
      <div class="col-md-12">
        <h3 style="font-weight: 500; color: #333">Help Center</h3>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-12">
        <div class="unicef-card">
          <div class="card-body">
            <div :key="video.id" v-for="(video, key) in videos">
              <hr class="my-4" v-if="key > 0" />
              <div class="row">
                <div class="col-md-3">
                  <img
                    style="cursor: pointer"
                    @click="openVideo(video)"
                    width="100%"
                    min-height="140px"
                    :src="videoImage"
                    alt=""
                    srcset=""
                  />
                  <!-- <iframe  :id="'vid_show_'+video.id" autoplay="false" width="100%" height="200px" :src="video.fullUrl" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe> -->
                </div>
                <div class="col-md-9">
                  <h5 class="">{{ video.video_title }}</h5>
                  <p>{{ video.about_the_video }}</p>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center">
              <div v-if="showLoader" class="spinner-border"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="showVideo">
      <div class="modal-dialog modal-lg">
        <div v-if="selectedVideo" class="modal-content px-2 py-2">
          <p class="text-lg">{{ selectedVideo.video_title }}</p>
          <!-- {{selectedVideo}} -->
          <iframe
            :id="'vid_show_' + selectedVideo.id"
            autoplay="false"
            width="100%"
            height="400px"
            :src="selectedVideo.fullUrl"
            frameborder="0"
            gesture="media"
            allow="encrypted-media"
            allowfullscreen
          ></iframe>
          <!-- <iframe id="vid-show" autoplay="false" width="100%" height="400px" :src="(selectedVideo.video_path) ? '/storage'+selectedVideo.video_path : selectedVideo.embed_url" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import Helpers from "./../mixin/helpers";
import ModuleAuth from "../mixin/ModuleAuth";
import axios from "axios";

export default {
  name: "HelpCenter",
  mixins: [Helpers, ModuleAuth],
  props: ["usermail", "platformUrl", "videoImage"],
  data() {
    return {
      face_form_requests: [],
      email: null,
      videos: [],
      showLoader: false,
      selectedVideo: {},
    };
  },
  mounted() {
    let app = this;
    this.showLoader = true;
    app.authorize(this.platformUrl, (token) => {
      axios({
        method: "get",
        url:
          this.platformUrl +
          "/api/get-help-videos?location=Web: Phone Bill Manager",
        headers: {
          Authorization: "Bearer " + token,
        },
      }).then((res) => {
        this.showLoader = false;
        this.videos = res.data.results;
      });
    });
  },
  methods: {
    formatNumber(n) {
      return this.numerilize(n, "m", 2);
    },
    openVideo(video) {
      this.selectedVideo = video;
      $("#showVideo").modal("show");

      this.authorize(this.platformUrl, (token) => {
        axios({
          method: "post",
          url: this.platformUrl + "/api/save-view?video_id=" + video.id,
          headers: {
            Authorization: "Bearer " + token,
          },
        }).then((res) => {});
      });
    },
  },
};
</script>

<style scoped>
.unicef-faceforms-table th,
.unicef-faceforms-table td {
  font-size: 12px;
}
</style>
