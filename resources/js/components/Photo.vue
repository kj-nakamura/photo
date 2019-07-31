<template>
  <div class="photo">
    <figure class="photo__wrapper">
      <img
        class="photo__image photo__image--portrait"
        :src="item.url"
        :alt="`Photo by ${item.owner.name}`"
      >
    </figure>
    <RouterLink
      class="photo__overlay"
      :to="`/photos/${item.id}`"
      :title="`View the photo by ${item.owner.name}`"
    >
      <div class="photo__controls">
        <button
          class="photo__action photo__action--like"
          :class="{ 'photo__action--liked': item.liked_by_user }"
          title="Like photo"
          @click.prevent="like"
        >
          <i class="icon icom-md-heart"></i>{{ item.likes_count }}
          <a
            class="photo_action"
            title="Download photo"
            @click.stop
            :href="`/photos/${item.id}/download`"
          >
            <i class="icon ion-md-arrow-round-down"></i>
          </a>
        </button>
      </div>
      <div class="photo__username">
        {{ item.owner.name }}
      </div>
    </RouterLink>
  </div>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  methods: {
    like () {
      this.$emit('like', {
        id: this.item.id,
        liked: this.item.liked_by_user
      })
    }
  }
}
</script>
