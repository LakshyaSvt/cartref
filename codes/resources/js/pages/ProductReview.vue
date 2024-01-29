<template>
  <div>
    <div class="container mx-auto my-2 px-4">
      <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
        <i class="fi fi-rr-chart-tree-map"></i>
        <h3 class="text-start my-8">Product Reviews</h3>
      </div>

      <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
        <div class="block">
          <form @submit.prevent="editOrCreateProductReview()">
            <div class="md:flex mb-3">
              <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="rate">Rate <span class="text-red-600">*</span></label>
                <input id="rate" v-model="rate" class="form-input" placeholder="2" required step="0.1" type="number">
              </div>
              <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="meta_title">User <span class="text-red-600">*</span></label>
                <select v-model="user_id" class="form-input" required>
                  <option selected value="">Select User</option>
                  <option v-for="(user, index) in users" :key="index" :value="user.id">{{ user.name }}
                  </option>
                </select>
              </div>
              <div class="mb-5 md:w-1/3 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="image">Product <span class="text-red-600">*</span></label>
                <select v-model="product_id" class="form-input" required>
                  <option selected value="">Select Product</option>
                  <option v-for="(product, index) in products" :key="index" :value="product.id">{{ product.name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 w-full md:mx-2">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="comment">Comment <span class="text-red-600">*</span></label>
                <textarea id="comment" v-model="comment" class="form-input" required rows="5"></textarea>
              </div>
            </div>
            <div class="text-center">
              <button class="submit-btn" type="submit">
                {{ this.editId ? 'Update' : 'Create' }}
              </button>
              <button class="clear-btn" type="button" @click="clear()">
                Clear
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg">
        <div class="block">
          <div class="flex flex-wrap items-center justify-between py-4">
            <div class="flex flex-wrap text-base text-gray-700 gap-2">
              <div>
                {{ pagination.from || '0' }} - {{ pagination.to || '0' }} of {{ pagination.total || '0' }}
              </div>
              <div>
                <button :disabled="!pagination.prev_page_url" class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50"
                        title="Previous"
                        @click="fetchProductReview(pagination.prev_page_url)">
                  <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                </button>
                <button :disabled="!pagination.next_page_url" class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50"
                        title="Next"
                        @click="fetchProductReview(pagination.next_page_url)">
                  <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                </button>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <label class="sr-only" for="table-search">Search</label>
              <div class="relative">
                <div v-if="keyword"
                     class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchProductReview();">
                  <i class="fi fi-rr-cross-small mr-1"></i>
                </div>
                <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fi fi-rr-search mr-1"></i>
                </div>
                <input v-model="keyword" class="search" placeholder="Search" type="text"
                       @keydown.enter="fetchProductReview()">
              </div>
              <div class="flex border border-gray-600 rounded-lg bg-white">
                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                        @click="fetchProductReview()">
                  <i class="ffi fi-rr-refresh mr-1"></i>
                </button>
                <select v-model="row_count" class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchProductReview()">
                  <option v-for="(count, index) in $store.state.row_counts" :key="index" :value="count.toLowerCase()" class="bg-white">
                    {{ count }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          <template v-if="loading">
            <Skeleton/>
          </template>
          <template v-else-if="product_reviews && product_reviews.length > 0">
            <div class="clear-right overflow-x-auto">
              <div class="table border-solid border border-gray-500 w-full">
                <div class="table-row table-head">
                  <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1 px-2">
                    <div class="flex items-center">
                      <input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded" type="checkbox">
                    </div>
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">
                    S.No.
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                    Rate
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">
                    User
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">
                    Product
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-40">
                    Comment
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                    Creation Date
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                    Last Update
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                    Actions
                  </div>
                </div>
                <div v-for="(product_review, index) in product_reviews" v-bind:key="index"
                     :class="{ 'bg-primary-200': product_review.id === editId }"
                     class="table-row table-body hover:bg-primary-100">
                  <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                    <div class="flex items-center">
                      <input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded"
                             type="checkbox">
                    </div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">
                    {{ pagination.from + index }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                    {{ product_review.rate }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    {{ product_review.user ? product_review.user.name : '-' }} <br>
                    <i class="fi fi-sr-envelope mr-1"></i>{{ product_review.user ? product_review.user.email : '-' }}<br>
                    <i class="fi fi-sr-phone-flip mr-1"></i>+91 {{ product_review.user ? product_review.user.mobile : '-' }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    <a v-if="product_review.product" :href="'/product/' + product_review.product.slug" class="cursor-pointer" target="_blank">
                      <i class="fi fi-rr-arrow-up-right-from-square text-primary-500 text-sm mr-1"></i>
                    </a>
                    {{ product_review.product ? product_review.product.name : '-' }}
                  </div>
                  <div :title="product_review?.comment" class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    {{ product_review.comment.length > 100 ? (product_review.comment.substr(0, 100) + '...') : product_review.comment }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(product_review.created_at)"></div>
                    <div class="text-sm">({{ timeAgo(product_review.created_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(product_review.updated_at)"></div>
                    <div class="text-sm">({{ timeAgo(product_review.updated_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                    <div class="flex gap-4 items-center justify-center">
                      <a class="font-medium cursor-pointer text-yellow-500" href="javascript:void(0)" type="button"
                         @click="editProductReview(product_review.id)">
                        <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                      </a>
                      <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                         @click="deleteProductReview(product_review.id)">
                        <i class="fi fi-rr-trash w-5 h-5 text-xl"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between py-4">
                <div>
                  <p class="text-base text-gray-700">
                    Showing
                    <span class="font-medium">{{ pagination.from || '0' }}</span>
                    to
                    <span class="font-medium">{{ pagination.to || '0' }}</span>
                    of
                    <span class="font-medium">{{ pagination.total || '0' }}</span>
                    results
                  </p>
                </div>
                <Pagination :fetchNewData="fetchProductReview" :pagination="pagination"/>
              </div>
            </div>
          </template>
          <template v-else>
            <div>
              <p class="text-center text-2xl">No Product Reviews Found !</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProductReview",
  data() {
    return {
      loading: true,
      product_reviews: [{}],
      products: [],
      users: [],
      rate: '',
      user_id: '',
      product_id: '',
      comment: '',
      keyword: '',
      row_count: this.$store.state.defaultRowCount,
      pagination: {},
      editId: '',
    }
  },
  methods: {
    clear() {
      this.url = '';
      this.rate = '';
      this.user_id = '';
      this.product_id = '';
      this.comment = '';
      this.editId = '';
    },
    editProductReview(id) {
      axios
          .get('/admin/product-review/' + id)
          .then(res => {
            this.editId = res.data.data.id;
            this.rate = res.data.data.rate;
            this.user_id = res.data.data.user_id;
            this.product_id = res.data.data.product_id;
            this.comment = res.data.data.comment;
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
          })
    },
    deleteProductReview(id) {
       if (!confirm("Are you sure you want to delete ?")) {
          return false;
       }
      axios
          .delete('/admin/product-review/' + id)
          .then(res => {
            this.show_toast(res.data.status, res.data.msg);
            this.fetchProductReview();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
            this.fetchProductReview();
          })
    },
    editOrCreateProductReview() {
      let url, data;
      data = {
        rate: this.rate,
        user_id: this.user_id,
        product_id: this.product_id,
        comment: this.comment,
      }
      if (this.editId) {
        url = '/admin/product-review/' + this.editId;
        data._method = 'PUT';
        data.id = this.editId;
      } else {
        url = '/admin/product-review'
      }
      axios.post(url, data)
          .then(res => {
            this.show_toast(res.data.status, res.data.msg);
            this.clear();
            this.fetchProductReview();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
            this.fetchProductReview();
          })
    },
    fetchProductReview(url) {
      this.loading = true;
      url = url || '/admin/product-review'
      axios
          .get(url, {
            params: {
              rows: this.row_count,
              keyword: this.keyword.trim()
            }
          })
          .then(res => {
            this.loading = false;
            this.product_reviews = res.data.data || [];
            let {data, ...pagination} = res.data;
            pagination.links.pop();
            pagination.links.shift();
            this.pagination = pagination;
          })
          .catch(err => {
            this.loading = false;
            err.handleGlobally && err.handleGlobally();
          })
    },
    fetchProducts(url) {
      this.dataLoading = true;
      url = url || '/admin/product'
      axios.get(url, {
        params: {
          row_count: 'all',
          status: 'Accepted',
        }
      })
          .then(res => {
            this.loading = false;
            this.products = res.data.data;
          })
          .catch(err => {
            this.dataLoading = false;
            this.loading = false;
            err.handleGlobally && err.handleGlobally();
          })
    },
    fetchUsers(url) {
      this.dataLoading = true;
      url = url || '/admin/user'
      axios.get(url, {
        params: {
          rows: 'all',
          status: 1,
          roles: JSON.stringify(["user"])
        }
      })
          .then(res => {
            this.users = res.data.data;
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
          })
    }
  },
  created() {
    this.fetchProductReview();
    this.fetchUsers();
    this.fetchProducts();
  },
}
</script>

<style lang="scss" scoped></style>
