<template>
    <div>
        <div class="container mx-auto my-2 px-4">
            <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
                <i class="fi fi-rr-chart-tree-map"></i>
                <h3 class="text-start my-8">Category Component Sliders</h3>
            </div>

            <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
                <div class="block">
                    <form @submit.prevent="editOrCreateSlider()">
                        <div class="md:flex mb-3">
                            <div class="mb-5 w-full mx-2 my-1">
                                <label for="title" class="block mb-2 text-sm font-bold text-gray-900"
                                    title="The name is how it appears on your site.">Slider Title <span
                                        class="text-red-600">*</span></label>
                                <input type="text" v-model="title" id="title" class="form-input"
                                    placeholder="3 Hour Delivery" required>
                            </div>
                        </div>
                        <div class="md:flex mb-3">
                            <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                                <label for="icon" class="block mb-2 text-sm font-bold text-gray-900">
                                    Icon Class&nbsp;
                                    <a href="https://fontawesome.com/v4/icons" class="text-green-500 text-sm underline"
                                        target="_blank">FontAwesome Icons <i
                                            class="fi fi-rr-arrow-up-right !text-[12px]"></i></a>
                                </label>
                                <input type="text" v-model="icon" id="icon" class="form-input" placeholder="fa-users"
                                    required>
                            </div>
                            <div class="mb-5 md:w-1/2 w-full mx-2 my-1">
                                <label for="category_slug" class="block mb-2 text-sm font-bold text-gray-900">
                                    Category Slug <span class="text-red-600">*</span>
                                </label>
                                <select v-model="category_slug" class="form-input" id="category_slug">
                                    <option value="">Choose Category Slug</option>
                                    <option v-for="slug in category_slugs" :value="slug">{{ slug }}</option>
                                </select>
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
                                <button :disabled="!pagination.prev_page_url" @click="fetchSlider(pagination.prev_page_url)"
                                    title="Previous"
                                    class="prev-next-btn">
                                    <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                                </button>
                                <button :disabled="!pagination.next_page_url" @click="fetchSlider(pagination.next_page_url)"
                                    title="Next"
                                    class="prev-next-btn">
                                    <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="relative">
                                <select title="Status" v-model="is_active" @change="fetchSlider()" class="filter-dropdown">
                                    <option class="bg-gray-100" value="">All</option>
                                    <option class="bg-gray-100" value="1">Published</option>
                                    <option class="bg-gray-100" value="0">Un Published</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                                </div>
                            </div>
                            <label for="table-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 cursor-pointer"
                                    @click="keyword = ''; fetchSlider();" v-if="keyword">
                                    <i class="fi fi-rr-cross-small mr-1"></i>
                                </div>
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" v-else>
                                    <i class="fi fi-rr-search mr-1"></i>
                                </div>
                                <input type="text" v-model="keyword" class="search" placeholder="Search"
                                    @keydown.enter="fetchSlider()">
                            </div>
                            <div class="flex border border-gray-600 rounded-lg bg-white">
                                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                    @click="fetchSlider()">
                                    <i class="ffi fi-rr-refresh mr-1"></i>
                                </button>
                                <select
                                    class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer"
                                    @change="fetchSlider()" v-model="row_count">
                                    <option :value="count.toLowerCase()" v-for="(count, index) in $store.state.row_counts"
                                        :key="index" class="bg-white">
                                        {{ count }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <template v-if="loading">
                        <Skeleton />
                    </template>
                    <template v-else-if="sliders && sliders.length > 0">
                        <div class="clear-right overflow-x-auto">
                            <div class="table border-solid border border-gray-500 w-full">
                                <div class="table-row table-head">
                                    <div class="table-cell border-gray-500 text-center uppercase font-semibold p-1 px-2">
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded">
                                        </div>
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center font-semibold uppercase w-10 p-1">
                                        S.No.
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Title</div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Category Slug</div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Icon</div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Status</div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Last Update
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Actions</div>
                                </div>
                                <div v-for="(s, index) in sliders" v-bind:key="index"
                                    class="table-row table-body hover:bg-primary-100"
                                    :class="{ 'bg-primary-200': s.id === editId }">
                                    <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded">
                                        </div>
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">
                                        {{ pagination.from + index }}</div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{
                                        s.title }}</div>
                                    <div
                                        class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-1 text-center py-1">
                                        {{ s.category_slug }}
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">{{
                                        s.icon }}</div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                        <StatusCheckbox :status="!!s.is_active" :update="updateStatus" />
                                    </div>
                                    <div
                                        class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                                        <div class="font-normal text-gray-900" v-html="formDateTime(s.updated_at)"></div>
                                        <div class="text-sm">({{ timeAgo(s.updated_at) }})</div>
                                    </div>
                                    <div
                                        class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                                        <div class="flex gap-4 items-center justify-center">
                                            <a href="javascript:void(0)" @click="editSlider(s.id)" type="button"
                                                class="font-medium cursor-pointer text-yellow-500">
                                                <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                            </a>
                                            <a href="javascript:void(0)" @click="deleteSlider(s.id)" type="button"
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
                                <Pagination :pagination="pagination" :fetchNewData="fetchSlider" />
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div>
                            <p class="text-center text-2xl">No Category Sliders Found !</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <ImageModal :show="showModal" :hide="closeImageModal" :img="imgModal"></ImageModal>
    </div>
</template>

<script>
export default {
    name: "CategoryComponentSliders",
    data() {
        return {
            loading: true,
            toggleLoadingId: '',
            category_slugs: [
                'Men',
                'Women',
                'Home Décor',
                'Beauty',
                'Accessories'
            ],
            sliders: [{}],
            title: '',
            category_slug: 'Men',
            icon: '',
            keyword: '',
            is_active: '',
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
        updateStatus(id, is_active) {
            axios.put('/admin/category-component-slider/' + id, { is_active })
                .then(res => {
                    this.show_toast(res.data.status, res.data.msg);
                    let index = this.sliders.findIndex(slider => slider.id === id)
                    this.$set(this.sliders, index, res.data.data)
                })
                .catch(err => {
                    this.dataLoading = false;
                    err.handleGlobally && err.handleGlobally();
                })
        },
        clear() {
            this.title = '';
            this.category_slug = '';
            this.icon = '';
            this.editId = '';
        },
        editSlider(id) {
            axios.get('/admin/category-component-slider/' + id)
                .then(res => {
                    this.editId = res.data.data.id;
                    this.title = res.data.data.title;
                    this.category_slug = res.data.data.category_slug;
                    this.icon = res.data.data.icon;
                })
                .catch(err => {
                    err.handleGlobally && err.handleGlobally();
                })
        },
        deleteSlider(id) {
            axios.delete('/admin/category-component-slider/' + id)
                .then(res => {
                    this.show_toast(res.data.status, res.data.msg);
                    this.fetchSlider();
                })
                .catch(err => {
                    err.handleGlobally && err.handleGlobally();
                    this.fetchSlider();
                })
        },
        editOrCreateSlider() {
            let url, data;
            if (this.editId) {
                url = '/admin/category-component-slider/' + this.editId;
                data = {
                    _method: 'PUT',
                    id: this.editId,
                    title: this.title.trim(),
                    category_slug: this.category_slug.trim(),
                    icon: this.icon.trim(),
                }
            } else {
                url = '/admin/category-component-slider'
                data = {
                    title: this.title.trim(),
                    category_slug: this.category_slug.trim(),
                    icon: this.icon.trim(),
                }
            }
            axios.post(url, data)
                .then(res => {
                    this.show_toast(res.data.status, res.data.msg);
                    this.clear();
                    this.fetchSlider();
                })
                .catch(err => {
                    err.handleGlobally && err.handleGlobally();
                    this.fetchSlider();
                })
        },
        fetchSlider(url) {
            this.loading = true;
            url = url || '/admin/category-component-slider'
            axios.get(url, {
                params: {
                    rows: this.row_count,
                    keyword: this.keyword.trim(),
                    status: this.is_active,
                }
            })
                .then(res => {
                    this.loading = false;
                    this.sliders = res.data.data || [];
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
        this.fetchSlider();
    },
}
</script>

<slider lang="scss" scoped></slider>
