@extends('layout')

@section('styles')
  <style>
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
  <div id="top_tracks">
    <div class="form-group">
      <select name="type" class="form-control" @change="setType">
        <option value="short_term">Short Term</option>
        <option value="medium_term">Medium Term</option>
        <option value="long_term">Long Term</option>
      </select>
    </div>
    <div class="columns">
      <div class="column">
        <h2>Your Top 20 @{{ typeLabel }}</h2>
        <transition-group name="flip-list" tag="div">
          <div class="box" v-for="(item, index) in top_tracks" :key="item.id">
            <div class="columns">
              <div class="column is-narrow is-size-3">@{{ index + 1 }}</div>
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
    </div>
  </div>
@stop

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
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
              label: 'All time',
              items: top_tracks_long_data.items
            }
          },
        top_tracks: []
      },
      computed: {
        typeLabel: function() {
          return this.types[this.type].label;
        }
      },
      created: function () {
        this.setTopTracks();
      },
      methods: {
        getArtistsName: function (artists) {
          if (artists.length <= 0) {
            return;
          }

          return artists.map(function(artist) {
            return artist.name;
          }).join(', ');
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