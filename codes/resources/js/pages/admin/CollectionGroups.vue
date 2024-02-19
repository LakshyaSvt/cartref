<template>
  <div>
    <Wait :show="loading"/>
    <div class="container mx-auto my-2 px-4">
      <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
        <i class="fi fi-rr-folders"></i>
        <h3 class="text-start my-8">Collection Groups</h3>
      </div>

      <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
        <div class="block">
          <form @submit.prevent="editOrCreateCollectionGroup()">
            <div class="md:flex mb-3">
              <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="group_name">Group Name <span class="text-red-600">*</span></label>
                <input type="text" id="group_name" v-model="group_name" placeholder="HOME ACCESSORIES & DECOR" class="form-input" required >
              </div>
              <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="category">Category <span class="text-red-600">*</span></label>
                <select id="category" v-model="category" class="form-input" required>
                  <option selected value="">Select Category</option>
                  <option v-for="(cat) in categories" :key="cat" :value="cat">{{ cat }}
                  </option>
                </select>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="background_image">Background Image</label>
                <input id="background_image" accept="image/*" class="form-input" placeholder="western-wear" type="file" @change="handleBgImageChange($event)">
              </div>
              <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="background_color">Background Color</label>
                <input type="color" id="background_color" v-model="background_color" class="form-input !h-11">
              </div>
              <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="background_opacity">Background Opacity</label>
                <input type="number" id="background_opacity" v-model="background_opacity" placeholder="40" class="form-input">
              </div>
              <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                <label class="block mb-2 text-sm font-bold text-gray-900" for="background_opacity">Font Size (px,rem,pt)</label>
                <input type="text" id="font_size" v-model="font_size" placeholder="12px" class="form-input">
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="md:flex w-1/2">
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label for="flash_sale" class="block mb-2 text-sm font-bold text-gray-900">Desktop Visibility </label>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="desktop_visiblity" id="desktop_visiblity" value="1" :checked="!!desktop_visiblity" class="sr-only peer">
                    <Checkbox/>
                  </label>
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label for="flash_sale" class="block mb-2 text-sm font-bold text-gray-900">Desktop Carousel </label>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="desktop_carousel" id="desktop_carousel" value="1" :checked="!!desktop_carousel" class="sr-only peer">
                    <Checkbox/>
                  </label>
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label class="block mb-2 text-sm font-bold text-gray-900" for="background_opacity">Desktop Gap <span class="text-red-600">*</span></label>
                  <input type="number" id="desktop_gap" v-model="desktop_gap" placeholder="1" class="form-input">
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label class="block mb-2 text-sm font-bold text-gray-900" for="background_opacity">Desktop Columns <span class="text-red-600">*</span></label>
                  <input type="number" id="desktop_columns" v-model="desktop_columns" placeholder="4" class="form-input">
                </div>
              </div>
              <div class="md:flex w-1/2">
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label for="flash_sale" class="block mb-2 text-sm font-bold text-gray-900">Mobile Visibility </label>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="mobile_visiblity" id="mobile_visiblity" value="1" :checked="!!mobile_visiblity" class="sr-only peer">
                    <Checkbox/>
                  </label>
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label for="flash_sale" class="block mb-2 text-sm font-bold text-gray-900">Mobile Carousel </label>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="mobile_carousel" id="mobile_carousel" value="1" :checked="!!mobile_carousel" class="sr-only peer">
                    <Checkbox/>
                  </label>
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label class="block mb-2 text-sm font-bold text-gray-900" for="background_opacity">Mobile Gap <span class="text-red-600">*</span></label>
                  <input type="number" id="mobile_gap" v-model="mobile_gap" placeholder="1" class="form-input">
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label class="block mb-2 text-sm font-bold text-gray-900" for="background_opacity">Mobile Columns <span class="text-red-600">*</span></label>
                  <input type="number" id="mobile_columns" v-model="mobile_columns" placeholder="4" class="form-input">
                </div>
              </div>
            </div>
            <div class="md:flex mb-3">
              <div class="md:flex w-1/2">
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label for="flash_sale" class="block mb-2 text-sm font-bold text-gray-900">Tablet Visibility </label>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="tablet_visiblity" id="tablet_visiblity" value="1" :checked="!!tablet_visiblity" class="sr-only peer">
                    <Checkbox/>
                  </label>
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label for="flash_sale" class="block mb-2 text-sm font-bold text-gray-900">Tablet Carousel</label>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="tablet_carousel" id="tablet_carousel" value="1" :checked="!!tablet_carousel" class="sr-only peer">
                    <Checkbox/>
                  </label>
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label class="block mb-2 text-sm font-bold text-gray-900" for="background_opacity">Tablet Gap <span class="text-red-600">*</span></label>
                  <input type="number" id="tablet_gap" v-model="tablet_gap" placeholder="1" class="form-input">
                </div>
                <div class="mb-5 md:w-1/4 w-full mx-2 my-1">
                  <label class="block mb-2 text-sm font-bold text-gray-900" for="background_opacity">Tablet Columns <span class="text-red-600">*</span></label>
                  <input type="number" id="tablet_columns" v-model="tablet_columns" placeholder="4" class="form-input">
                </div>
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
                <button :disabled="!pagination.prev_page_url"
                        class="prev-next-btn" title="Previous"
                        @click="fetchCollectionGroup(pagination.prev_page_url)">
                  <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                </button>
                <button :disabled="!pagination.next_page_url"
                        class="prev-next-btn" title="Next"
                        @click="fetchCollectionGroup(pagination.next_page_url)">
                  <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                </button>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <div class="relative">
                <select id="category_filter" v-model="category_filter" class="filter-dropdown !w-auto" title="Category" @change="fetchCollectionGroup()">
                  <option selected value="">All Category</option>
                  <option v-for="(cat) in categories" :key="cat" :value="cat">{{ cat }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                  <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                </div>
              </div>
              <div class="relative">
                <select v-model="status" class="filter-dropdown" title="Status" @change="fetchCollectionGroup()">
                  <option class="bg-gray-100" value="">All</option>
                  <option class="bg-gray-100" value="1">Published</option>
                  <option class="bg-gray-100" value="0">Un Published</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                  <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                </div>
              </div>
              <label class="sr-only" for="table-search">Search</label>
              <div class="relative">
                <div v-if="keyword" class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer" @click="keyword = ''; fetchCollectionGroup();">
                  <i class="fi fi-rr-cross-small mr-1"></i>
                </div>
                <div v-else class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fi fi-rr-search mr-1"></i>
                </div>
                <input v-model="keyword" class="search" placeholder="Search" type="text" @keydown.enter="fetchCollectionGroup()">
              </div>
              <div class="flex border border-gray-600 rounded-lg bg-white">
                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer" @click="fetchCollectionGroup()">
                  <i class="ffi fi-rr-refresh mr-1"></i>
                </button>
                <select v-model="row_count" class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer" @change="fetchCollectionGroup()">
                  <option v-for="(count, index) in $store.state.row_counts" :key="index" :value="count.toLowerCase()" class="bg-white">
                    {{ count }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          <template v-if="dataLoading">
            <Skeleton />
          </template>
          <template v-else-if="collection_groups && collection_groups.length > 0">
            <div class="clear-right overflow-x-auto">
              <div class="table border-solid border border-gray-500 w-full">
                <div class="table-row table-head">
                  <div class="table-cell border-gray-500 text-sm text-center font-semibold uppercase w-10 p-1">
                    S.No.
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Group Name
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Category
                  </div>
                  <div class="table-cell border-l-2 border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Background Image
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Background Color
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Background Opacity
                  </div>
                  <div class="table-cell border-l-2 border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Desktop Visiblity
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Desktop Carousel
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Desktop Gap
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Desktop Columns
                  </div>
                  <div class="table-cell border-l-2 border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Tablet Visiblity
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Tablet Carousel
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Tablet Columns
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Tablet Gap
                  </div>
                  <div class="table-cell border-l-2 border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Mobile Visiblity
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Mobile Carousel
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Mobile Columns
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Mobile Gap
                  </div>
                  <div class="table-cell border-l-2 border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Font Size
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Status
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-6">
                    Last Update
                  </div>
                  <div class="table-cell border-l border-gray-500 text-sm text-center uppercase font-semibold p-1 px-2">
                    Actions
                  </div>
                </div>
                <div v-for="(collection_group, index) in collection_groups" v-bind:key="collection_group.id"
                     :class="{ 'bg-primary-200': collection_group.id === editId }"
                     class="table-row table-body hover:bg-primary-100">
                  <div class="table-cell border-t border-gray-500 text-xs text-center w-10 p-1">
                    {{ pagination.from + index }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.group_name || '-' }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.category || '-' }}
                  </div>
                  <div class="table-cell border-t border-l-2 border-gray-500 text-xs p-1 text-center !align-middle">
                    <img v-if="collection_group.background_image"
                         :alt="collection_group.group_name"
                         :src="$store.state.storageUrl + collection_group.background_image"
                         class="w-14 h-14 border border-gray-400 mx-auto p-1 rounded-[50%]"
                         @click="imageModal($store.state.storageUrl + collection_group.background_image)"
                    >
                    <p v-else class="text-center text-gray-800">--No Image--</p>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                    <div class="text-base rounded-2xl text-white p-1 px-2 mx-auto border-[0.2px] border-gray-200"
                         :style="`background-color:${collection_group.background_color};`">
                      <span class="text-black">{{ collection_group.background_color }}</span>
                    </div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.background_opacity || '-' }}
                  </div>
                  <div class="table-cell border-t border-l-2 border-gray-500 text-xs font-semibold px-1 text-center">
                    <StatusCheckbox :id="collection_group.id" :status="!!collection_group.desktop_visiblity" :update="(id, value) => { updateStatus(id, value, 'desktop_visiblity') }"/>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    <StatusCheckbox :id="collection_group.id" :status="!!collection_group.desktop_carousel" :update="(id, value) => { updateStatus(id, value, 'desktop_carousel') }"/>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.desktop_gap || '-' }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.desktop_columns || '-' }}
                  </div>
                  <div class="table-cell border-t border-l-2 border-gray-500 text-xs font-semibold px-1 text-center">
                    <StatusCheckbox :id="collection_group.id" :status="!!collection_group.tablet_visiblity" :update="(id, value) => { updateStatus(id, value, 'tablet_visiblity') }"/>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    <StatusCheckbox :id="collection_group.id" :status="!!collection_group.tablet_carousel" :update="(id, value) => { updateStatus(id, value, 'tablet_carousel') }"/>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.tablet_columns || '-' }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.tablet_gap || '-' }}
                  </div>
                  <div class="table-cell border-t border-l-2 border-gray-500 text-xs font-semibold px-1 text-center">
                    <StatusCheckbox :id="collection_group.id" :status="!!collection_group.mobile_visiblity" :update="(id, value) => { updateStatus(id, value, 'mobile_visiblity') }"/>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    <StatusCheckbox :id="collection_group.id" :status="!!collection_group.mobile_carousel" :update="(id, value) => { updateStatus(id, value, 'mobile_carousel') }"/>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.mobile_columns || '-' }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.mobile_gap || '-' }}
                  </div>
                  <div class="table-cell border-t border-l-2 border-gray-500 text-xs font-semibold px-1 text-center">
                    {{ collection_group.font_size || '-' }}
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs px-1 text-center py-1">
                    <StatusCheckbox :id="collection_group.id" :status="!!collection_group.status" :update="(id, value) => { updateStatus(id, value, 'status') }"/>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs px-1 text-center py-1 !align-middle">
                    <div class="font-normal text-gray-900" v-html="formDateTime(collection_group.updated_at)">
                    </div>
                    <div class="text-xs">({{ timeAgo(collection_group.updated_at) }})</div>
                  </div>
                  <div class="table-cell border-t border-l border-gray-500 text-xs align-[middle!important] text-center">
                    <div class="flex gap-4 items-center justify-center">
                      <a class="font-medium cursor-pointer text-yellow-500" href="#" type="button"
                         @click="editCollectionGroup(collection_group.id)">
                        <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                      </a>
                      <a class="font-medium cursor-pointer text-red-500" href="javascript:void(0)" type="button"
                         @click="deleteCollectionGroup(collection_group.id)">
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
                <Pagination :fetchNewData="fetchCollectionGroup" :pagination="pagination"/>
              </div>
            </div>
          </template>
          <template v-else>
            <div>
              <p class="text-center text-2xl">No Collection Groups Found !</p>
            </div>
          </template>
        </div>
      </div>
    </div>
    <ImageModal :hide="closeImageModal" :img="imgModal" :show="showModal"></ImageModal>
  </div>
</template>

<script>
import Checkbox from "@components/Checkbox.vue";

export default {
  name: "CollectionGroups",
  components: {Checkbox},
  data() {
    return {
      loading: false,
      dataLoading: true,
      categories: [
        "Home",
        "Men",
        "Women",
        "Home Décor",
        "Beauty",
        "Accessories"
      ],
      //data
      collection_groups: [],
      category: '',
      group_name: '',
      desktop_visiblity: false,
      desktop_carousel: false,
      desktop_columns: '',
      desktop_gap: '',
      mobile_visiblity: false,
      mobile_carousel: false,
      mobile_gap: '',
      mobile_columns: '',
      tablet_visiblity: false,
      tablet_carousel: false,
      tablet_columns: '',
      tablet_gap: '',
      background_image: '',
      background_opacity: '',
      background_color: '',
      font_size: '',
      //filter
      keyword: '',
      category_filter: '',
      status: '',
      row_count: this.$store.state.defaultRowCount,
      showModal: false,
      imgModal: '',
      pagination: {},
      editId: '',
    }
  },
  methods: {
    imageModal(img) {
      this.showModal = true;
      this.imgModal = img;
    },
    closeImageModal() {
      this.showModal = false;
    },
    handleBgImageChange(e) {
      let file = e.target?.files[0];
      if (file) {
        this.background_image = file;
      }
    },
    updateStatus(id, value , field) {
      let data = {};
      data[field] = value;
      axios.put('/admin/collection-group/' + id, data)
          .then(res => {
            this.show_toast(res.data.status, res.data.msg);
            let index = this.collection_groups.findIndex(image => image.id === id)
            this.$set(this.collection_groups, index, res.data.data)
          })
          .catch(err => {
            this.dataLoading = false;
            err.handleGlobally && err.handleGlobally();
          })
    },
    clear() {
      this.group_name = '';
      this.category = '';
      this.background_color = '';
      this.background_opacity = '';
      this.font_size = '';
      this.background_image = '';
      this.mb_background_image = '';
      this.editId = '';
      $('form').trigger("reset");
    },
    editCollectionGroup(id) {
      this.loading = true;
      axios.get('/admin/collection-group/' + id)
          .then(res => {
            this.loading = false;
            this.editId = res.data.data.id;
            this.category = res.data.data.category;
            this.url = res.data.data.url;
            this.group_name = res.data.data.group_name;
            // this.image = res.data.data.image;
            this.desktop_visiblity = res.data.data.desktop_visiblity;
            this.desktop_columns = res.data.data.desktop_columns;
            this.mobile_visiblity = res.data.data.mobile_visiblity;
            this.mobile_columns = res.data.data.mobile_columns;
            this.background_color = res.data.data.background_color;
            this.tablet_visiblity = res.data.data.tablet_visiblity;
            this.tablet_columns = res.data.data.tablet_columns;
            this.desktop_carousel = res.data.data.desktop_carousel;
            this.tablet_carousel = res.data.data.tablet_carousel;
            this.mobile_carousel = res.data.data.mobile_carousel;
            this.desktop_gap = res.data.data.desktop_gap;
            this.tablet_gap = res.data.data.tablet_gap;
            this.mobile_gap = res.data.data.mobile_gap;
            // this.background_image = res.data.data.background_image;
            this.background_opacity = res.data.data.background_opacity;
            this.font_size = res.data.data.font_size;
          })
          .catch(err => {
            this.loading = false;
            err.handleGlobally && err.handleGlobally();
          })
    },
    deleteCollectionGroup(id) {
      this.loading = true;
      if (!confirm("Are you sure you want to delete ?")) {
        return false;
      }
      axios
          .delete('/admin/collection-group/' + id)
          .then(res => {
            this.loading = true;
            this.show_toast(res.data.status, res.data.msg);
            this.fetchCollectionGroup();
          })
          .catch(err => {
            err.handleGlobally && err.handleGlobally();
          })
    },
    editOrCreateCollectionGroup() {
      let url = '/admin/collection-group';
      let formData = new FormData();

      // Basic fields
      formData.append('collection_id', this.collection_id);
      formData.append('category', this.category);
      formData.append('group_name', this.group_name);

      formData.append('desktop_visiblity', Number(this.desktop_visiblity));
      formData.append('desktop_carousel', Number(this.desktop_carousel));
      formData.append('desktop_columns', this.desktop_columns);
      formData.append('desktop_gap', this.desktop_gap);
      formData.append('mobile_visiblity', Number(this.mobile_visiblity));
      formData.append('mobile_carousel', Number(this.mobile_carousel));
      formData.append('mobile_columns', this.mobile_columns);
      formData.append('mobile_gap', this.mobile_gap);
      formData.append('tablet_visiblity', Number(this.tablet_visiblity));
      formData.append('tablet_columns', this.tablet_columns);
      formData.append('tablet_carousel', Number(this.tablet_carousel));
      formData.append('tablet_gap', this.tablet_gap);

      formData.append('background_color', this.background_color);
      formData.append('font_size', this.font_size);
      formData.append('background_opacity', this.background_opacity);

      //File fields
      if (this.background_image) {
        formData.append('background_image', this.background_image)
      }
      //if the action is EDIT
      if (this.editId) {
        url = '/admin/collection-group/' + this.editId;
        formData.append('_method', 'PUT');
        formData.append('id', this.editId);
      }

      const headers = {'Content-Type': 'multipart/form-data'};
      this.loading = true;
      axios
          .post(url, formData, {headers})
          .then(res => {
            this.loading = false;
            this.show_toast(res.data.status, res.data.msg);
            this.clear();
            this.fetchCollectionGroup();
          })
          .catch(err => {
            this.loading = false;
            err.handleGlobally && err.handleGlobally();
          })
    },
    fetchCollectionGroup(url) {
      this.dataLoading = true;
      url = url || '/admin/collection-group'
      axios.get(url, {
        params: {
          rows: this.row_count,
          keyword: this.keyword.trim(),
          status: this.status,
          category: this.category_filter,
        }
      })
          .then(res => {
            this.dataLoading = false;
            this.loading = false;
            this.collection_groups = res.data.data;
            let {data, ...pagination} = res.data;
            pagination.links.pop();
            pagination.links.shift();
            this.pagination = pagination;
          })
          .catch(err => {
            this.dataLoading = false;
            this.loading = false;
            err.handleGlobally && err.handleGlobally();
          })
    },
  },
  created() {
    this.fetchCollectionGroup();
  },
}
</script>

<style lang="scss" scoped>
.table-body .table-cell {
  vertical-align: middle !important;
}
</style>
