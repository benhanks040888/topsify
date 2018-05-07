@extends('layout')

@section('styles')
  <style>
    [v-cloak] * { display:none }
    [v-cloak]::before { content: "loading…" }

    .box {
      transition: all 1s;
      display: block;
      width: 100%;
    }
    .box {
      padding: 10px;
      box-shadow: 0 1px 0 rgba(10, 10, 10, .1);
      border-radius: 0;
    }
    .box:not(:last-child) {
      margin-bottom: .5rem;
    }
    .flip-list-enter, .flip-list-leave-to {
      opacity: 0;
      transform: translateY(30px);
    }
    .flip-list-leave-active {
      opacity: 0;
      transition: opacity .2s, transform 1s;
      position: absolute;
    }
  </style>
@stop

@section('content')
  <div id="top_tracks" v-cloak>
    <h2 class="title">Your Spotify Top 50 @{{ typeLabel }} (Approximately)</h2>
    <div class="level">
      <div class="level-left">
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
      </div>
      <div class="level-right">
        <a class="button is-link js-create-playlist" href="#" v-if="!createdPlaylist" @click="createPlaylist">Create This Playlist</a>
    
        <a class="button is-primary" v-if="createdPlaylist" :href="createdPlaylist" target="_blank">Play This Playlist</a>
      </div>
    </div>


    <transition-group name="flip-list" tag="div" class="a">
      <div class="box" v-for="(item, index) in top_tracks" :key="item.id">
        <div class="columns is-mobile">
          <div class="column is-narrow has-text-grey has-text-centered" style="min-width: 80px;">
            <div class="is-size-3" style="color: #000">
              <span>@{{ index + 1 }}</span>
            </div>
            <div class="is-size-7">
              <span class="is-size-7"><i :class="['fas', getArrowIcon(index + 1, getLastTermPosition(item.id))]"></i></span> @{{ getLastTermPositionLabel(getLastTermPosition(item.id)) }}
            </div>
          </div>
          
          <div class="column is-narrow">
            <img :src="item.album.images[2].url" :alt="item.album.name + 'Album image'">
          </div>
          <div class="column">
            <div class="title has-text-weight-light">@{{ item.name }}</div>
            <div class="subtitle is-6 has-text-grey">
              @{{ getArtistsName(item.artists) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
  {{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
  <script src="https://unpkg.com/sweetalert2@7.19.3/dist/sweetalert2.all.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
  <script>
    var top_tracks_short_data = {!! json_encode($top_tracks_short) !!};
    var top_tracks_medium_data = {!! json_encode($top_tracks_medium) !!};
    var top_tracks_long_data = {!! json_encode($top_tracks_long) !!};

    var topTracksApp = new Vue({
      el: '#top_tracks',
      data: {
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
      },
      computed: {
        typeIcon: function() {
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
        typeLabel: function() {
          return this.types[this.type].label;
        }
      },
      created: function () {
        this.setTopTracks();
      },
      methods: {
        createPlaylist: function() {
          if (this.isCreatingPlaylist) {
            return false;
          }

          this.isCreatingPlaylist = true;

          var self = this;

          swal({
            title: 'Name your playlist',
            input: 'text',
            inputValue: 'Your Top Songs ' + self.typeLabel,
            showCancelButton: true,
            confirmButtonText: 'Create Playlist',
            showLoaderOnConfirm: true,
            preConfirm: function (title) {
              axios.post("{{ route('playlist.create') }}", {
                type: self.type,
                title: title
              })
              .then(function (response) {
                self.createdPlaylist = response.data.external_urls.spotify;
                self.isCreatingPlaylist = false;
              })
              .catch(function(error) {
                swal.showValidationError(
                  'Request failed: ' + error
                );
              })
            },
            allowOutsideClick: function () {
              return !swal.isLoading();
            }
          }).then(function(result) {
            if (result.value) {
              swal({
                title: 'Playlist created! Enjoy!'
              })
            }
          });
        },
        getArtistsName: function (artists) {
          if (artists.length <= 0) {
            return;
          }

          return artists.map(function(artist) {
            return artist.name;
          }).join(', ');
        },
        getArrowIcon: function(curr, last) {
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
        getLastTermPositionLabel: function(position) {
          return position != 'NEW' ? '(' + position + ')' : position;
        },
        getLastTermPosition: function(itemId) {
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
        setTopTracks: function () {
          this.top_tracks = this.types[this.type].items;
        },
        setType: function(e) {
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
    })
  </script>
@stop