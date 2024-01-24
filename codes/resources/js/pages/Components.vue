<template>
    <div>
        <Wait :show="loading" />
        <div class="container mx-auto my-2 px-4">
            <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
                <i class="fi fi-rr-folder-tree"></i>
                <h3 class="text-start my-8">Components</h3>
            </div>

            <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
                <div class="block">
                    <form @submit.prevent="editOrCreateComponent()">
                        <div class="md:flex mb-3">
                            <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                                <label for="page_name" class="block mb-2 text-sm font-bold text-gray-900"
                                    title="The name is how it appears on your site.">Page
                                    <span class="text-red-600">*</span>
                                </label>
                                <select v-model="page_name" id="page_name" class="form-input" required>
                                    <option selected disabled>Choose page</option>
                                    <option v-for="page in pages" :key="page">{{ page }}</option>
                                </select>
                            </div>
                            <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                                <label for="title_1" class="block mb-2 text-sm font-bold text-gray-900"
                                    title="The name is how it appears on your site.">Title 1
                                    <span class="text-red-600">*</span>
                                </label>
                                <input type="text" v-model="title_1" id="title_1" class="form-input" placeholder="Step 1"
                                    required>
                            </div>
                            <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                                <label for="title_2" class="block mb-2 text-sm font-bold text-gray-900">Title 2
                                    <span class="text-red-600">*</span></label>
                                <input type="text" v-model="title_2" id="title_2" class="form-input"
                                    placeholder="Activate your area pincode" required>
                            </div>
                        </div>
                        <div class="md:flex mb-3">
                            <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                                <label for="image" class="block mb-2 text-sm font-bold text-gray-900"
                                    title="An image belonging to a specific type, often distinguished by shared characteristics or features.">
                                    Image</label>
                                <input type="file" @change="handleImageChange($event)" id="image" class="form-input"
                                    placeholder="western-wear" accept="image/*">
                            </div>
                            <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                                <label for="url" class="block mb-2 text-sm font-bold text-gray-900">Url <span
                                        class="text-red-600">*</span></label>
                                <input type="text" v-model="url" id="url" class="form-input"
                                    placeholder="/showcase-at-home/get-started" required>
                            </div>
                            <div class="mb-5 md:w-1/3 w-full mx-2 my-1">
                                <label for="button" class="block mb-2 text-sm font-bold text-gray-900">Button Text <span
                                        class="text-red-600">*</span></label>
                                <input type="text" v-model="button" id="button" class="form-input" placeholder="Get Started"
                                    required>
                            </div>
                        </div>
                        <div class="md:flex mb-3">
                            <div class="mb-5 w-full mx-2 my-1">
                                <label for="description"
                                    class="block mb-2 text-sm font-bold text-gray-900">Description</label>
                                <textarea type="text" v-model="description" id="description" class="form-input h-28"
                                    placeholder="Hello Users! Try Showroom at Home"></textarea>
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
                                <button :disabled="!pagination.prev_page_url"
                                    @click="fetchComponent(pagination.prev_page_url)" title="Previous"
                                    class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50">
                                    <i class="fi fi-rr-angle-small-left text-xl px-1 py-2"></i>
                                </button>
                                <button :disabled="!pagination.next_page_url"
                                    @click="fetchComponent(pagination.next_page_url)" title="Next"
                                    class="border border-transparent rounded-full hover:bg-primary-400 disabled:opacity-50">
                                    <i class="fi fi-rr-angle-small-right text-xl px-1 py-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="relative">
                                <select title="Status" v-model="status" @change="fetchComponent()"
                                    class="block appearance-none w-32 leading-tight h-full cursor-pointer text-black bg-white border border-gray-400 focus:outline-none hover:shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-none font-medium rounded-lg text-sm px-3 py-2">
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
                                    @click="keyword = ''; fetchComponent();" v-if="keyword">
                                    <i class="fi fi-rr-cross-small mr-1"></i>
                                </div>
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" v-else>
                                    <i class="fi fi-rr-search mr-1"></i>
                                </div>
                                <input type="text" v-model="keyword" class="search" placeholder="Search"
                                    @keydown.enter="fetchComponent()">
                            </div>
                            <div class="flex border border-gray-600 rounded-lg bg-white">
                                <button class="px-2 py-1 m-[2px] hover:bg-primary-100 border-r border-solid cursor-pointer"
                                    @click="fetchComponent()">
                                    <i class="ffi fi-rr-refresh mr-1"></i>
                                </button>
                                <select
                                    class="w-14 block px-1 m-[2px] text-base text-center text-gray-900 bg-white cursor-pointer"
                                    @change="fetchComponent()" v-model="row_count">
                                    <option :value="count.toLowerCase()" v-for="(count, index) in $store.state.row_counts"
                                        :key="index" class="bg-white">
                                        {{ count }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <template v-if="dataLoading">
                        <Skeleton />
                    </template>
                    <template v-else-if="components && components.length > 0">
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
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-20">
                                        Image
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Page
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Title 1
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold w-52 p-1">
                                        Title 2
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold w-52 p-1">
                                        Description
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Button
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Url
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Status
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1 w-24">
                                        Last Update
                                    </div>
                                    <div
                                        class="table-cell border-l border-gray-500 text-center uppercase font-semibold p-1">
                                        Actions
                                    </div>
                                </div>
                                <div v-for="(component, index) in components" v-bind:key="index"
                                    class="table-row table-body hover:bg-primary-100"
                                    :class="{ 'bg-primary-200': component.id === editId }">
                                    <div class="table-cell border-t border-gray-500 text-sm text-center w-10 p-1 px-2">
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded">
                                        </div>
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm text-center w-10 p-1">
                                        {{ pagination.from + index }}
                                    </div>
                                    <div
                                        class="table-cell border-t border-l border-gray-500 text-sm p-1 text-center !align-middle">
                                        <img @click="imageModal($store.state.storageUrl + component.image)"
                                            v-if="component.image"
                                            class="w-20 h-16 border border-gray-400 mx-auto p-1 rounded-[50%]"
                                            :src="$store.state.storageUrl + component.image" :alt="component.name">
                                        <p class="text-center text-gray-800" v-else>--No Image--</p>
                                    </div>
                                    <div
                                        class="table-cell border-t border-l border-gray-500 text-sm font-semibold px-1 text-center">
                                        {{ component.page_name }}
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center">
                                        {{ component.title_1 || '-' }}
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                        {{ component.title_2 || '-' }}
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1"
                                        :title="component?.description">
                                        {{ component.description.length > 100 ? (component.description.substr(0, 100) +
                                            '...')
                                            : component.description }}
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                        {{ component.button || '-' }}
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                        {{ component.url || '-' }}
                                    </div>
                                    <div class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1">
                                        <StatusCheckbox :id="component.id" :status="!!component.status"
                                            :update="updateStatus" />
                                    </div>
                                    <div
                                        class="table-cell border-t border-l border-gray-500 text-sm px-1 text-center py-1 !align-middle">
                                        <div class="font-normal text-gray-900" v-html="formDateTime(component.updated_at)">
                                        </div>
                                        <div class="text-sm">({{ timeAgo(component.updated_at) }})</div>
                                    </div>
                                    <div
                                        class="table-cell border-t border-l border-gray-500 text-sm align-[middle!important] text-center">
                                        <div class="flex gap-4 items-center justify-center">
                                            <a href="#" @click="editComponent(component.id)" type="button"
                                                class="font-medium cursor-pointer text-yellow-500">
                                                <i class="fi fi-rr-pencil w-5 h-5 text-xl"></i>
                                            </a>
                                            <a href="javascript:void(0)" @click="deleteComponent(component.id)"
                                                type="button" class="font-medium cursor-pointer text-red-500">
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
                                <Pagination :pagination="pagination" :fetchNewData="fetchComponent" />
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div>
                            <p class="text-center text-2xl">No Components Found !</p>
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
    name: "Components",
    data() {
        return {
            loading: false,
            dataLoading: true,
            toggleLoadingId: '',
            //data
            pages: [
                "About Us",
                "Showcase At Home"
            ],
            components: [{ description: '...' }],
            title_1: '',
            title_2: '',
            description: '',
            button: '',
            url: '',
            page_name: 'Showcase At Home',
            //filter
            keyword: '',
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
        handleImageChange(e) {
            let file = e.target?.files[0];
            if (file) {
                this.image = file;
            }
        },
        updateStatus(id, status) {
            axios.put('/admin/component/' + id, { status })
                .then(res => {
                    this.show_toast(res.data.status, res.data.msg);
                    let index = this.components.findIndex(component => component.id === id)
                    this.$set(this.components, index, res.data.data)
                })
                .catch(err => {
                    this.dataLoading = false;
                    err.handleGlobally && err.handleGlobally();
                })
        },
        clear() {
            this.editId = '';
            this.title_1 = '';
            this.title_2 = '';
            this.description = '';
            this.button = '';
            this.url = '';
            this.page_name = '';
            $('form').trigger("reset");
        },
        editComponent(id) {
            this.loading = true;
            axios.get('/admin/component/' + id)
                .then(res => {
                    this.editId = res.data.data.id;
                    this.title_1 = res.data.data.title_1;
                    this.title_2 = res.data.data.title_2;
                    this.description = res.data.data.description;
                    this.button = res.data.data.button;
                    this.url = res.data.data.url;
                    this.page_name = res.data.data.page_name;
                    this.loading = false;
                })
                .catch(err => {
                    this.loading = false;
                    err.handleGlobally && err.handleGlobally();
                })
        },
        deleteComponent(id) {
            this.loading = true;
            if (!confirm("Are you sure you want to delete ?")) {
                return false;
            }
            axios.delete('/admin/component/' + id)
                .then(res => {
                    this.loading = true;
                    this.show_toast(res.data.status, res.data.msg);
                    this.fetchComponent();
                })
                .catch(err => {
                    err.handleGlobally && err.handleGlobally();
                })
        },
        editOrCreateComponent() {
            let url = '/admin/component';
            let formData = new FormData();

            // Basic fields
            formData.append('title_1', this.title_1.trim());
            formData.append('title_2', this.title_2);
            formData.append('description', this.description);
            formData.append('button', this.button);
            formData.append('url', this.url);
            formData.append('page_name', this.page_name);

            //Image field
            if (this.image) {
                formData.append('image', this.image)
            }
            //if the action is EDIT
            if (this.editId) {
                url = '/admin/component/' + this.editId;
                formData.append('_method', 'PUT');
                formData.append('id', this.editId);
            }

            const headers = { 'Content-Type': 'multipart/form-data' };
            this.loading = true;
            axios.post(url, formData, { headers })
                .then(res => {
                    this.loading = false;
                    this.show_toast(res.data.status, res.data.msg);
                    this.clear();
                    this.fetchComponent();
                })
                .catch(err => {
                    this.loading = false;
                    err.handleGlobally && err.handleGlobally();
                })
        },
        fetchComponent(url) {
            this.dataLoading = true;
            url = url || '/admin/component'
            axios.get(url, {
                params: {
                    rows: this.row_count,
                    keyword: this.keyword.trim(),
                    status: this.status,
                }
            })
                .then(res => {
                    this.dataLoading = false;
                    this.loading = false;
                    this.components = res.data.data;
                    let { data, ...pagination } = res.data;
                    pagination.links.pop();
                    pagination.links.shift();
                    this.pagination = pagination;
                })
                .catch(err => {
                    this.dataLoading = false;
                    this.loading = false;
                    err.handleGlobally && err.handleGlobally();
                })
        }
    },
    created() {
        this.fetchComponent();
    },
}
</script>

<style lang="scss" scoped></style>
