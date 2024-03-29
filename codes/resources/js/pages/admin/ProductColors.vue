<template>
  <div>
    <Wait :show="loading"/>
    <div class="container mx-auto my-2 px-4">
      <h1 class="text-center text-2xl underline uppercase">{{ product.name }}</h1>
      <div class="mt-6">
        <a @click="$router.go(-1)"
           class="inline-flex items-center gap-2 px-4 py-2 text-base font-bold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-gray-800 hover:bg-black hover:text-white">
          <i class="fi fi-rr-arrow-left text-base w-4 h-5"></i>
          Back
        </a>
      </div>
      <div class="flex flex-wrap justify-between items-center">
        <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
          <i class="fi fi-rr-palette"></i>
          <h3 class="text-start my-8">Colors</h3>
        </div>
        <div>
          <router-link :to="{ name: 'product-edit', params: { id: product.id } }"
                       class="flex items-center gap-2 px-4 py-2 text-base font-bold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-primary-500 hover:bg-primary-600">
            Edit Product
            <i class="fi fi-rr-arrow-up-right-from-square text-base w-6 h-6"></i>
          </router-link>
        </div>
      </div>

      <div class="block">
        <div class="grid w-full grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" v-if="product_colors && product_colors.length > 0">
          <div v-for="(color, index) in product_colors" :key="color.id"
               class="relative flex flex-col mt-6 mx-2 text-gray-700 bg-white shadow-lg bg-clip-border rounded-xl w-100">
            <div class="p-4">
              <img :src="$store.state.storageUrl + color.main_image" class="w-full h-64 mx-auto mb-2 rounded">
              <h5 class="block mb-2 font-sans text-xl antialiased font-semibold leading-snug tracking-normal text-blue-gray-900">
                {{ color.color }}
              </h5>
              <div class="flex items-center justify-between">
                <router-link :to="{name:'product-sizes', params: { product_id: product.id, color_id: color.id }}" class="inline-block">
                  <button
                      class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-center text-white bg-primary-500 uppercase align-middle transition-all rounded-lg select-none  hover:bg-primary-600"
                      type="button">
                    Sizes
                    <i class="fi fi-rr-arrow-right text-sm w-4 h-4"></i>
                  </button>
                </router-link>
                <div class="h-6">
                  <StatusCheckbox :id="color.id" :status="!!color.status" :update="updateStatus"/>
                </div>
              </div>
            </div>
            <div class="absolute top-1 right-0">
              <router-link :to="{name:'product-color-edit', params: { product_id: product.id, color_id: color.id }}"
                           class="bg-white text-primary-500 hover:bg-primary-500 hover:text-white cursor-pointer px-2 py-1 rounded-md">
                <i class="fi fi-rr-pencil text-sm w-4 h-4"></i>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProductColors",
  data() {
    return {
      loading: true,
      dataLoading: true,
      product_id: this.$route.params.id,
      product: {},
      product_colors: [],
    }
  },
  methods: {
    fetchProductColors(url) {
      axios.get(`/admin/product/${this.product_id}/colors`)
          .then(res => {
            this.product_colors = res.data.data.colors;
            this.product = res.data.data.product;
            this.dataLoading = false;
            this.loading = false;
          })
          .catch(err => {
            this.dataLoading = false;
            this.loading = false;
            err.handleGlobally && err.handleGlobally();
          })
    },
    updateStatus(id, status) {
      axios.put('/admin/product/color/' + id, {status})
          .then(res => {
            this.show_toast(res.data.status, res.data.msg);
            let index = this.product_colors.findIndex(color => color.id === id)
            this.$set(this.product_colors, index, res.data.data)
          })
          .catch(err => {
            this.dataLoading = false;
            err.handleGlobally && err.handleGlobally();
          })
    },
  },
  created() {
    this.fetchProductColors();
  },
}
</script>

<style lang="scss" scoped></style>
