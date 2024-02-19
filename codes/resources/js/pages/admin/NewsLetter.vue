<template>
  <div>
    <div class="container mx-auto my-2 px-4">
      <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
        <i class="fi fi-rs-newspaper-open"></i>
        <h3 class="text-start my-8">NewsLetter</h3>
      </div>

      <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
        <div class="block">
          <form @submit.prevent="editOrCreateContact()">
            <div class="md:flex mb-3">
              <div class="mb-5 w-full mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="email">Email <span class="text-red-600">*</span></label>
                <input id="email" v-model="email" class="form-input" placeholder="abc@gmail.com" required type="text">
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
                <button :disabled="!pagination.prev_page_url" class="prev-next-btn"
                        title="Previous"
                        @click="fetchContact(pagination.prev_page_url)">
                  <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                </button>
                <button :disabled="!pagination.next_page_url" class="prev-next-btn"
                        title="Next"
                        @click="fetchContact(pagination.next_page_url)">
                  <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                </button>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <label class="sr-only" for="table-search">Search</label>
              <div class="relative">
                <div v-if="keyword"
                     class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchContact();">
                  <i class="fi fi-rr-cross-small mr-1"></i>
                </div>
                <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fi fi-rr-search mr-1"></i>
                </div>
                <input v-model="keyword" class="search" placeholder="Search" type="text"
                       @keydown.enter="fetchContact()">
              </div>
              <div class="flex border border-gray-600 rounded-lg bg-white">
                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                        @click="fetchContact()">
                  <i class="ffi fi-rr-refresh mr-1"></i>
                </button>
                <select
                    v-model="row_count"
                    class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchContact()">
                  <option v-for="(count, index) in $store.state.row_counts" :key="index"
                          :value="count.toLowerCase()" class="bg-white">
                    {{ count }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          <template v-if="loading">
            <Skeleton/>
          </template>
          <template v-else-if="newsletters && newsletters.length > 0">
            <div class="clear-right overflow-x-auto">
              <div class="table border-solid border border-gray-500 w-full">
                <div class="table-row table-head">
                  <div
                      class="table-cell border-gray-500 text-center font-semibold uppercase w-10 p-1">
                    S.No.
                  </div>
                  <div
                      class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                    Email
                  </div>
                  <div
                      class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                    Creation Date
                  </div>
                  <div
                      class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                    Actions
                  </div>
                </div>
                <div v-for="(letter, index) in newsletters" v-bind:key="index"
                     :class="{ 'bg-primary-200': letter.id === editId }"
                     class="table-row table-body hover:bg-primary-100">
                  <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1">
                    {{ pagination.from + index }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    <i class="fi fi-rr-envelope mr-1"></i> {{ letter.email }}
                  </div>
                  <div
                      class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(letter.created_at)"></div>
                    <div class="text-sm">({{ timeAgo(letter.created_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                    <div class="flex gap-4 items-center justify-center">
                      <a class="font-medium cursor-pointer text-yellow-500" href="javascript:void(0)" type="button"
                         @click="editContact(letter.id)">
                        <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                      </a>
                      <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                         @click="deleteContact(letter.id)">
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
                <Pagination :fetchNewData="fetchContact" :pagination="pagination"/>
              </div>
            </div>
          </template>
          <template v-else>
            <div>
              <p class="text-center text-2xl">No NewsLetters Found !</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NewsLetter",
  data() {
    return {
      loading: true,
      toggleLoadingId: '',
      newsletters: [{}],
      email: '',
      keyword: '',
      row_count: this.$store.state.defaultRowCount,
      pagination: {},
      editId: '',
    }
  },
  methods: {
    clear() {
      this.email = '';
      this.editId = '';
    },
    editContact(id) {
      axios
          .get('/admin/newsletter/' + id)
          .then(res => {
            this.editId = res.data.data.id;
            this.email = res.data.data.email;
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
          })
    },
    deleteContact(id) {
      axios
          .delete('/admin/newsletter/' + id)
          .then(res => {
            this.show_toast(res.data.status, res.data.msg);
            this.fetchContact();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
            this.fetchContact();
          })
    },
    editOrCreateContact() {
      let url, data;
      data = {
        email: this.email.trim(),
      }
      if (this.editId) {
        url = '/admin/newsletter/' + this.editId;
        data._method = 'PUT';
        data.id = this.editId;
      } else {
        url = '/admin/newsletter'
      }
      axios.post(url, data)
          .then(res => {
            this.show_toast(res.data.status, res.data.msg);
            this.clear();
            this.fetchContact();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
            this.fetchContact();
          })
    },
    fetchContact(url) {
      this.loading = true;
      url = url || '/admin/newsletter'
      axios
          .get(url, {
            params: {
              rows: this.row_count,
              keyword: this.keyword.trim()
            }
          })
          .then(res => {
            this.loading = false;
            this.newsletters = res.data.data || [];
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
    this.fetchContact();
  },
}
</script>

<style lang="scss" scoped></style>
