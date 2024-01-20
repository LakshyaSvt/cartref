<template>
  <div>
    <label v-if="wait" class="inline-block  justify-center w-4 h-4">
      <Spinner/>
    </label>
    <label class="relative inline-flex items-center cursor-pointer" :title="title" v-else>
      <input type="checkbox" :id="'checkbox_'+id" value="" :checked="status" @change="toggle($event)" class="sr-only peer">
      <div
          class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer
          peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-['']
          after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5
          after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
      </div>
    </label>
  </div>
</template>

<script>
export default {
  name: "StatusCheckbox",
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
        return "Click to Unpublish"
      }
      return "Click to Publish"
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