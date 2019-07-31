<template>
  <div class="photo-list">
    <div class="grid">
      <Photo
        class="grid__item"
        v-for="photo in photos"
        :key="photo.id"
        :item="photo"
        @like="onLikeClick"
      />
    </div>
  </div>
</template>

<script>
import { OK } from '../util'
import Photo from '../components/Photo.vue'

export default {
  components: {
    Photo
  },
  data () {
    return {
      photos: []
    }
  },
  methods: {
    async fetchPhotos () {
      const response = await axios.get('/api/photos')

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photos = response.data.data
    },
    onLikeClick ({ id, liked }) {
      if (! this.$store.getters['auth/check']) {
        alert('いいね機能を使うにはログインしてください。')
        return false
      }

      if (liked) {
        this.unlike(id)
      } else {
        this.like(id)
      }
    },
    async like (id) {
      const response = await axios.put(`/api/photos/${id}/like`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photos = this.photos.map(photo => {
        if (photo.id === response.data.photo_id) {
          photo.likes_count += 1
          photo.liked_by_user = true
        }
        return photo
      })
    },
    async unlike (id) {
      const response = await axios.delete(`/api/photos/${id}/like`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photos = this.photos.map(photo => {
        if (photo.id === response.data.photo_id) {
          photo.likes_count -= 1
          photo.liked_by_user = false
        }
        return photo
      })
    }
  },
  watch: {
    $route: {
      async handler () {
        await this.fetchPhotos()
      },
      immediate: true
    }
  }
}
</script>
