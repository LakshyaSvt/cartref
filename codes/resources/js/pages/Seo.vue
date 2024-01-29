<template>
  <div>
    <div class="container mx-auto my-2 px-4">
      <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
        <i class="fi fi-rr-chart-tree-map"></i>
        <h3 class="text-start my-8">Seo</h3>
      </div>

      <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
        <div class="block">
          <form @submit.prevent="editOrCreateSeo()">
            <div class="md:flex mb-3">
              <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="url">Url <span class="text-red-600">*</span></label>
                <input id="url" v-model="url" class="form-input" placeholder="John Doe" required type="url">
              </div>
              <div class="mb-5 md:w-1/2 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="meta_title">Meta Title <span class="text-red-600">*</span></label>
                <input id="meta_title" v-model="meta_title" class="form-input" placeholder="Showcase At Home" required type="text">
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="meta_description">Meta Description <span class="text-red-600">*</span></label>
                <textarea id="meta_description" v-model="meta_description" class="form-input" required></textarea>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="meta_keywords">Meta Keywords <span class="text-red-600">*</span></label>
                <textarea id="meta_keywords" v-model="meta_keywords" class="form-input" required></textarea>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="header_script">Header Script <span class="text-red-600">*</span></label>
                <textarea id="header_script" v-model="header_script" class="form-input" required></textarea>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 w-full md:mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="footer_script">Footer Script <span class="text-red-600">*</span></label>
                <textarea id="footer_script" v-model="footer_script" class="form-input" required></textarea>
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
                        @click="fetchSeo(pagination.prev_page_url)">
                  <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                </button>
                <button :disabled="!pagination.next_page_url" class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50"
                        title="Next"
                        @click="fetchSeo(pagination.next_page_url)">
                  <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                </button>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <label class="sr-only" for="table-search">Search</label>
              <div class="relative">
                <div v-if="keyword"
                     class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchSeo();">
                  <i class="fi fi-rr-cross-small mr-1"></i>
                </div>
                <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fi fi-rr-search mr-1"></i>
                </div>
                <input v-model="keyword" class="search" placeholder="Search" type="text"
                       @keydown.enter="fetchSeo()">
              </div>
              <div class="flex border border-gray-600 rounded-lg bg-white">
                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                        @click="fetchSeo()">
                  <i class="ffi fi-rr-refresh mr-1"></i>
                </button>
                <select v-model="row_count" class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchSeo()">
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
          <template v-else-if="seos && seos.length > 0">
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
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-40">
                    Url
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">
                    Meta Title
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-64">
                    Meta Description
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-40">
                    Meta Keywords
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                    Creation Date
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-44">
                    Last Date
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                    Actions
                  </div>
                </div>
                <div v-for="(seo, index) in seos" v-bind:key="index"
                     :class="{ 'bg-primary-200': seo.id === editId }"
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
                    {{ seo.url }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    {{ seo.meta_title }}
                  </div>
                  <div :title="seo?.meta_description" class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    {{ seo.meta_description.length > 100 ? (seo.meta_description.substr(0, 100) + '...') : seo.meta_description }}
                  </div>
                  <div :title="seo?.meta_keywords" class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    {{ seo.meta_keywords.length > 100 ? (seo.meta_keywords.substr(0, 100) + '...') : seo.meta_keywords }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(seo.created_at)"></div>
                    <div class="text-sm">({{ timeAgo(seo.created_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(seo.updated_at)"></div>
                    <div class="text-sm">({{ timeAgo(seo.updated_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                    <div class="flex gap-4 items-center justify-center">
                      <a class="font-medium cursor-pointer text-yellow-500" href="javascript:void(0)" type="button"
                         @click="editSeo(seo.id)">
                        <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                      </a>
                      <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                         @click="deleteSeo(seo.id)">
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
                <Pagination :fetchNewData="fetchSeo" :pagination="pagination"/>
              </div>
            </div>
          </template>
          <template v-else>
            <div>
              <p class="text-center text-2xl">No Seo Found !</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Seo",
  data() {
    return {
      loading: true,
      seos: [{}],
      url: '',
      meta_title: '',
      meta_description: '',
      meta_keywords: '',
      header_script: '',
      footer_script: '',
      keyword: '',
      row_count: this.$store.state.defaultRowCount,
      pagination: {},
      editId: '',
    }
  },
  methods: {
    clear() {
      this.url = '';
      this.meta_title = '';
      this.meta_description = '';
      this.meta_keywords = '';
      this.header_script = '';
      this.footer_script = '';
      this.editId = '';
    },
    editSeo(id) {
      axios
          .get('/admin/seo/' + id)
          .then(res => {
            this.editId = res.data.data.id;
            this.url = res.data.data.url;
            this.meta_title = res.data.data.meta_title;
            this.meta_description = res.data.data.meta_description;
            this.meta_keywords = res.data.data.meta_keywords;
            this.header_script = res.data.data.header_script;
            this.footer_script = res.data.data.footer_script;
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
          })
    },
    deleteSeo(id) {
      axios
          .delete('/admin/seo/' + id)
          .then(res => {
            this.show_toast(res.data.status, res.data.msg);
            this.fetchSeo();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
            this.fetchSeo();
          })
    },
    editOrCreateSeo() {
      let url, data;
      data = {
        url: this.url,
        meta_title: this.meta_title,
        meta_description: this.meta_description,
        meta_keywords: this.meta_keywords,
        header_script: this.header_script,
        footer_script: this.footer_script,
      }
      if (this.editId) {
        url = '/admin/seo/' + this.editId;
        data._method = 'PUT';
        data.id = this.editId;
      } else {
        url = '/admin/seo'
      }
      axios.post(url, data)
          .then(res => {
            this.show_toast(res.data.status, res.data.msg);
            this.clear();
            this.fetchSeo();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
            this.fetchSeo();
          })
    },
    fetchSeo(url) {
      this.loading = true;
      url = url || '/admin/seo'
      axios
          .get(url, {
            params: {
              rows: this.row_count,
              keyword: this.keyword.trim()
            }
          })
          .then(res => {
            this.loading = false;
            this.seos = res.data.data || [];
            let {data, ...pagination} = res.data;
            pagination.links.pop();
            pagination.links.shift();
            this.pagination = pagination;
          })
          .catch(err => {
            this.loading = false;
            err.handleGlobally && err.handleGlobally();
          })
    }
  },
  created() {
    this.fetchSeo();
  },
}
</script>

<style lang="scss" scoped></style>
