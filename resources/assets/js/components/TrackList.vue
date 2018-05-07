<template>
  <transition-group name="flip-list" tag="div">
    <div class="box" v-for="(item, index) in items" :key="item.id">
      <track-item :index="index" :item="item" :type="type" />
    </div>
    <!-- <div class="box" v-for="(item, index) in items" :key="item.id">
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
    </div> -->
  </transition-group>
</template>

<script>
import TrackItem from './TrackItem'

export default {
  props: ['items', "type"],
  components: {
    TrackItem
  }
}
</script>
