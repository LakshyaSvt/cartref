<template>
  <div>
    <label v-if="wait" class="inline-block  justify-center w-4 h-4">
      <Spinner/>
    </label>
    <label class="relative inline-flex items-center cursor-pointer" :title="title" v-else>
      <input type="checkbox" value="" :checked="status" @change="toggle($event)" class="sr-only peer">
      <Checkbox/>
    </label>
  </div>
</template>

<script>
import Checkbox from "./Checkbox.vue";

export default {
  name: "StatusCheckbox",
  components: {Checkbox},
  data() {
    return {
      wait: false,
    }
  },
  props: {
    id: {
      type: Number,
      default: 0,
    },
    status: {
      type: Boolean,
      default: false
    },
    update: {
      type: Function,
    },
    onTitle:{
      type: String,
      default:"Unpublish"
    },
    offTitle:{
      type: String,
      default:"Publish"
    }
  },
  watch: {
    status: function (newVal) {
      this.wait = false;
    },
  },
  computed: {
    title() {
      if (this.status) {
        return "Click to "+this.onTitle;
      }
      return "Click to "+this.offTitle;
    }
  },
  methods: {
    toggle(e) {
      this.wait = true;
      this.update(this.id, e.target.checked)
    }
  },
}
</script>