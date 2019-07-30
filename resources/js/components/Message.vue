<template>
  <div class="massage" v-show="message">
    {{ message }}
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    computed: {
      ...mapState({
        message: stete => state.message.content
      })
    }
  }
</script>
