<template>
  <div>
    <h2 class="title">Your Spotify Top 50 {{ typeLabel }} (Approximately)</h2>
    <div class="control has-icons-left">
      <div class="form-group select is-rounded is-medium">
        <select name="type" class="form-control" @change="setType">
          <option value="short_term">Short Term</option>
          <option value="medium_term">Medium Term</option>
          <option value="long_term">Long Term</option>
        </select>
      </div>
      <span class="icon is-small is-left">
        <i :class="['fas', typeIcon]"></i>
      </span>
    </div>

    <a class="button js-create-playlist" href="#" @click="createPlaylist">Create This Playlist</a>

    <a class="button" v-if="createdPlaylist" :href="createdPlaylist" target="_blank">Play This Playlist</a>

    <div class="section">
      <track-list :items="top_tracks" :type="type" />
      <!-- <transition-group name="flip-list" tag="div">
        <div class="box" v-for="(item, index) in top_tracks" :key="item.id">
          <div class="columns">
            <div class="column is-narrow has-text-grey has-text-centered">
              <div class="is-size-5" style="color: #000">
                <span>{{ index + 1 }}</span>
              </div>
              <div class="is-size-7">
                <span class="is-size-7"><i :class="['fas', getArrowIcon(index + 1, getLastTermPosition(item.id))]"></i></span> 
                {{ getLastTermPosition(item.id) }}
              </div>
            </div>
            
            <div class="column is-narrow">
              <img :src="item.album.images[2].url" :alt="item.album.name + 'Album image'">
            </div>
            <div class="column">
              <div class="title has-text-weight-light">{{ item.name }}</div>
              <div class="subtitle is-6 has-text-grey">
                {{ getArtistsName(item.artists) }}
              </div>
            </div>
          </div>
        </div>
      </transition-group> -->
    </div>
  </div>
</template>

<script>
  import Vue from 'vue'
  import lodash from 'lodash'
  import axios from 'axios'

  import TrackList from './TrackList'

  export default {
    data () {
      return {
        type: 'short_term',
        types: 
          {
            'short_term': {
              label: 'Last 4 Weeks',
              items: top_tracks_short_data.items
            },
            'medium_term': {
              label: 'Last 4 Months',
              items: top_tracks_medium_data.items
            },
            'long_term': {
              label: 'Last Couple of Years',
              items: top_tracks_long_data.items
            }
          },
        top_tracks: [],
        isCreatingPlaylist: false,
        createdPlaylist: ''        
      }
    },

    components: {
      TrackList
    },

    computed: {
      typeIcon () {
        if (this.type == 'long_term') {
          return 'fa-hourglass-start';
        }
        if (this.type == 'medium_term') {
          return 'fa-hourglass-half';
        }
        if (this.type == 'short_term') {
          return 'fa-hourglass-end';
        }
      },
      typeLabel () {
        return this.types[this.type].label;
      }
    },

    created () {
      this.setTopTracks();
    },

    methods: {
      createPlaylist () {
        if (this.isCreatingPlaylist) {
          return false;
        }

        this.isCreatingPlaylist = true;

        axios.post("{{ route('playlist.create') }}", {
          type: this.type
        })
        .then(function (response) {
          this.createdPlaylist = response.data.external_urls.spotify;
          this.isCreatingPlaylist = false;
        }.bind(this));
      },
      getArtistsName (artists) {
        if (artists.length <= 0) {
          return;
        }

        return artists.map(function(artist) {
          return artist.name;
        }).join(', ');
      },
      getArrowIcon(curr, last) {
        if (curr < last) {
          return 'has-text-success fa-arrow-up';
        } else if (curr > last) {
          return 'has-text-danger fa-arrow-down';
        } else if (curr == last) {
          return 'has-text-info fa-arrows-alt-h';
        } else {
          return 'has-text-warning fa-star';
        }
      },
      getLastTermPosition(itemId) {
        if (this.type == 'short_term') {
          let lastTermPositionIndex = _.findIndex(top_tracks_medium_data.items, function(o) { return o.id == itemId; });
          return lastTermPositionIndex >= 0 ? lastTermPositionIndex + 1 : 'NEW';
        }

        if (this.type == 'medium_term') {
          let lastTermPositionIndex = _.findIndex(top_tracks_long_data.items, function(o) { return o.id == itemId; });
          return lastTermPositionIndex >= 0 ? lastTermPositionIndex + 1 : 'NEW';
        }

        return '-';
      },
      setTopTracks () {
        this.top_tracks = this.types[this.type].items;
      },
      setType(e) {
        this.type = e.target.value;
        this.setTopTracks();

        // axios.get("{{ route('top-tracks') }}", {
        //   params: {
        //     type: e.target.value
        //   }
        // })
        // .then(function (response) {
        //   this.top_tracks = response.data.items;
        // }.bind(this));
      }
    }
  }
</script>
