<template>
  <div class="columns">
    <div class="column is-narrow has-text-grey has-text-centered">
      <div class="is-size-5" style="color: #000">
        <span>{{ index + 1 }}</span>
      </div>
      <div class="is-size-7">
        <span class="is-size-7"><i :class="['fas', arrowIcon]"></i></span> 
        {{ lastTermPosition }}
      </div>
    </div>
    
    <div class="column is-narrow">
      <img :src="item.album.images[2].url" :alt="item.album.name + 'Album image'">
    </div>
    <div class="column">
      <div class="title has-text-weight-light">{{ item.name }}</div>
      <div class="subtitle is-6 has-text-grey">
        {{ artistsName }}
        <!-- {{ getArtistsName(item.artists) }} -->
      </div>
    </div>
  </div>  
</template>

<script>
export default {
  props: ['index', 'item', 'type'],
  computed: {
    artistsName () {
      if (this.item.artists.length <= 0) {
        return
      }

      return this.item.artists.map(function(artist) {
        return artist.name;
      }).join(', ')
    },
    lastTermPosition () {
      let self = this
      if (this.type == 'short_term') {
        let lastTermPositionIndex = _.findIndex(top_tracks_medium_data.items, function(o) { return o.id == self.item.id; })
        return lastTermPositionIndex >= 0 ? lastTermPositionIndex + 1 : 'NEW'
      }

      if (this.type == 'medium_term') {
        let lastTermPositionIndex = _.findIndex(top_tracks_long_data.items, function(o) { return o.id == self.item.id; })
        return lastTermPositionIndex >= 0 ? lastTermPositionIndex + 1 : 'NEW'
      }

      return '-'
    },
    arrowIcon () {
      let curr = this.index + 1
      let last = this.lastTermPosition

      if (curr < last) {
        return 'has-text-success fa-arrow-up';
      } else if (curr > last) {
        return 'has-text-danger fa-arrow-down';
      } else if (curr == last) {
        return 'has-text-info fa-arrows-alt-h';
      } else {
        return 'has-text-warning fa-star';
      }
    }
  },
  methods: {
    // getArtistsName (artists) {
    //   if (artists.length <= 0) {
    //     return;
    //   }

    //   return artists.map(function(artist) {
    //     return artist.name;
    //   }).join(', ');
    // },
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
  }
}
</script>
