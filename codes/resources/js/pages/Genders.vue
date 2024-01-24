<template>
  <div>
    <div class="container mx-auto my-2 px-4">
      <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
        <i class="fi fi-rr-chart-tree-map"></i>
        <h3 class="text-start my-8">Genders</h3>
      </div>

      <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
        <div class="block">
          <form @submit.prevent="editOrCreateGender()">
            <div class="md:flex mb-3">
              <div class="mb-5 w-full mx-2 my-1">
                <label for="gender" class="block mb-2 text-sm font-bold text-gray-900">Gender <span class="text-red-600">*</span></label>
                <input type="text" v-model="name" id="gender" class="form-input" placeholder="Male" required>
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="submit-btn">
                {{ this.editId ? 'Update' : 'Create' }}
              </button>
              <button type="button" @click="clear()"
                class="clear-btn">
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
                <button :disabled="!pagination.prev_page_url" @click="fetchGender(pagination.prev_page_url)"
                  title="Previous"
                  class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50">
                  <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                </button>
                <button :disabled="!pagination.next_page_url" @click="fetchGender(pagination.next_page_url)" title="Next"
                  class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50">
                  <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                </button>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <div class="relative">
                <select title="Status" v-model="status" @change="fetchGender()" class="filter-dropdown">
                  <option class="bg-gray-100" value="">All</option>
                  <option class="bg-gray-100" value="1">Published</option>
                  <option class="bg-gray-100" value="0">Un Published</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                  <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                </div>
              </div>
              <label for="table-search" class="sr-only">Search</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer"
                  @click="keyword = ''; fetchGender();" v-if="keyword">
                  <i class="fi fi-rr-cross-small mr-1"></i>
                </div>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" v-else>
                  <i class="fi fi-rr-search mr-1"></i>
                </div>
                <input type="text" v-model="keyword" class="search" placeholder="Search" @keydown.enter="fetchGender()">
              </div>
              <div class="flex border border-gray-600 rounded-lg bg-white">
                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                  @click="fetchGender()">
                  <i class="ffi fi-rr-refresh mr-1"></i>
                </button>
                <select class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer"
                  @change="fetchGender()" v-model="row_count">
                  <option :value="count.toLowerCase()" v-for="(count, index) in $store.state.row_counts" :key="index"
                    class="bg-white">
                    {{ count }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          <template v-if="loading">
            <Skeleton />
          </template>
          <template v-else-if="genders && genders.length > 0">
            <div class="clear-right overflow-x-auto">
              <div class="table border-solid border border-gray-500 w-full">
                <div class="table-row table-head">
                  <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1 px-2">
                    <div class="flex items-center">
                      <input type="checkbox" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded">
                    </div>
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">S.No.
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Name</div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Status</div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Last Update
                  </div>
                  <div class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">Actions</div>
                </div>
                <div v-for="(gender, index) in genders" v-bind:key="index"
                  class="table-row table-body hover:bg-primary-100" :class="{ 'bg-primary-200': gender.id === editId }">
                  <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                    <div class="flex items-center">
                      <input type="checkbox" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded">
                    </div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">{{
                    pagination.from + index }}</div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{ gender.name }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                    <StatusCheckbox :id="gender.id" :status="!!gender.status" :update="updateStatus" />
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(gender.updated_at)"></div>
                    <div class="text-sm">({{ timeAgo(gender.updated_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                    <div class="flex gap-4 items-center justify-center">
                      <a href="javascript:void(0)" @click="editGender(gender.id)" type="button"
                        class="font-medium cursor-pointer text-yellow-500">
                        <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                      </a>
                      <a href="javascript:void(0)" @click="deleteGender(gender.id)" type="button"
                        class="font-medium cursor-pointer text-red-500">
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
                <Pagination :pagination="pagination" :fetchNewData="fetchGender" />
              </div>
            </div>
          </template>
          <template v-else>
            <div>
              <p class="text-center text-2xl">No Genders Found !</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Genders",
  data() {
    return {
      loading: true,
      genders: [{}],
      name: '',
      keyword: '',
      status: '',
      row_count: this.$store.state.defaultRowCount,
      pagination: {},
      editId: '',
    }
  },
  methods: {
    updateStatus(id, status) {
      axios.put('/admin/gender/' + id, { status })
        .then(res => {
          this.show_toast(res.data.status, res.data.msg);
          let index = this.genders.findIndex(gender => gender.id === id)
          this.$set(this.genders, index, res.data.data)
        })
        .catch(err => {
          this.dataLoading = false;
          err.handleGlobally && err.handleGlobally();
        })
    },
    clear() {
      this.name = '';
      this.editId = '';
    },
    editGender(id) {
      axios.get('/admin/gender/' + id)
        .then(res => {
          this.editId = res.data.data.id;
          this.name = res.data.data.name;
        })
        .catch(err => {
          err.handleGlobally && err.handleGlobally();
        })
    },
    deleteGender(id) {
      if (!confirm("Are you sure you want to delete ?")) {
        return false;
      }
      this.loading = true;
      axios.delete('/admin/gender/' + id)
        .then(res => {
          this.show_toast(res.data.status, res.data.msg);
          this.fetchGender();
        })
        .catch(err => {
          err.handleGlobally && err.handleGlobally();
          this.fetchGender();
        })
    },
    editOrCreateGender() {
      let url, data;
      if (this.editId) {
        url = '/admin/gender/' + this.editId;
        data = {
          _method: 'PUT',
          id: this.editId,
          name: this.name.trim(),
        }
      } else {
        url = '/admin/gender'
        data = {
          name: this.name.trim(),
        }
      }
      axios.post(url, data)
        .then(res => {
          this.show_toast(res.data.status, res.data.msg);
          this.clear();
          this.fetchGender();
        })
        .catch(err => {
          err.handleGlobally && err.handleGlobally();
          this.fetchGender();
        })
    },
    fetchGender(url) {
      this.loading = true;
      url = url || '/admin/gender'
      axios.get(url, {
        params: {
          rows: this.row_count,
          keyword: this.keyword.trim(),
          status: this.status,
        }
      })
        .then(res => {
          this.loading = false;
          this.genders = res.data.data || [];
          let { data, ...pagination } = res.data;
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
    this.fetchGender();
  },
}
</script>
