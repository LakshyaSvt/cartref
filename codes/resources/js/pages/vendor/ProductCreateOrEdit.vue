<template>
   <div>
      <Wait :show="loading"/>
      <div class="container mx-auto my-2 px-4">
         <h1 v-if="editId && product.name" class="text-center text-2xl underline uppercase">{{ product.name }}</h1>
         <div class="mt-6">
            <a class="inline-flex items-center gap-2 px-4 py-2 text-base font-bold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-gray-800 hover:bg-black hover:text-white"
               @click="$router.go(-1)">
               <i class="fi fi-rr-arrow-left text-base w-4 h-5"></i>
               Back
            </a>
         </div>
         <div class="flex gap-2 items-center text-3xl text-primary-600 font-semibold">
            <i class="fi fi-rr-box-open"></i>
            <h3 v-if="editId" class="text-start my-8">Edit Product</h3>
            <h3 v-else class="text-start my-8">Add Product</h3>
         </div>

         <div class="bg-white p-4 overflow-x-auto shadow-md sm:rounded-lg my-4">
            <div class="block">
               <form @submit.prevent="submitForm">
                  <div class="flex flex-col md:flex-row gap-8 mx-2">
                     <div class="flex flex-col md:w-3/4 w-full">
                        <div class="w-full my-2">
                           <label class="block mb-2 text-sm font-bold text-gray-900" for="title"
                                  title="A concise name describing a product for easy identification and marketing.">
                              Product Title <span class="text-red-600">*</span>
                           </label>
                           <input id="title" v-model="product.name" class="form-input" placeholder="Western Wear" required type="text">
                        </div>
                        <div class="w-full my-2">
                           <label class="block mb-2 text-sm font-bold text-gray-900" for="slug"
                                  title="The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.">
                              URL / Slug <span class="text-red-600">*</span>
                           </label>
                           <input id="slug" v-model="product.slug" class="form-input" placeholder="western-wear" required type="text">
                        </div>
                        <div class="w-full my-2">
                           <label class="block mb-2 text-sm font-bold text-gray-900" for="code"
                                  title="'SKU' stands for Stock Keeping Unit, a unique code for product identification in inventory.">
                              Product Code (ERP) / SKU <span class="text-red-600">*</span>
                           </label>
                           <input id="code" v-model="product.sku" class="form-input" placeholder="12X-987XX" required type="text">
                        </div>
                        <div class="flex gap-4 w-full my-2">
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="category_id"
                                     title="Those immediately above the category in the hierarchy">Category <span class="text-red-600">*</span></label>
                              <div class="relative">
                                 <select id="category_id" v-model="product.category_id" class="form-input appearance-none" required>
                                    <option selected value="">Select Category</option>
                                    <option v-for="(category) in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                 </select>
                                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                                 </div>
                              </div>
                           </div>
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="subcategory_id" title="Those immediately below the main category in the hierarchy">Sub
                                 Category
                                 <span class="text-red-600">*</span>
                              </label>
                              <div class="relative">
                                 <select id="subcategory_id" v-model="product.subcategory_id" class="form-input appearance-none" required>
                                    <option selected value="">Select Sub Category</option>
                                    <option v-for="(sub_cat) in sub_categories" :key="sub_cat.id" :value="sub_cat.id">{{ sub_cat.name }}</option>
                                 </select>
                                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="flex gap-4 w-full my-2">
                           <div class="mb-5 md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="brand_id"
                                     title="Brand is a distinct identity and perception of a product or company in the market.">
                                 Brand <span class="text-red-600">*</span>
                              </label>
                              <div class="relative">
                                 <select id="brand_id" v-model="product.brand_id" class="form-input appearance-none" required>
                                    <option selected value="">Choose Brand</option>
                                    <option v-for="(brand) in brands" :key="brand.id" :value="brand.name">{{ brand.name }}</option>
                                 </select>
                                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="flex flex-col md:w-1/4 w-full">
                        <div class="flex flex-col gap-4" title="The primary or featured image representing a subject, often used in media and content.">
                           <div>
                              <label class="block mb-2 text-sm font-bold text-gray-900">Main Image</label>
                              <img v-if="product && product.image"
                                   :src="$store.state.storageUrl + product.image"
                                   alt="main-img"
                                   class="w-100 border h-[23rem] max:h-full w-full rounded-md shadow-md cursor-zoom-in"
                                   @click="imageModal($store.state.storageUrl + product.image)"
                                   @error="imageLoadError"/>
                           </div>
                           <div>
                              <input id="main_img" accept="image/*" class="form-input" name="main_img" type="file" @change="handleImageChange($event)">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="w-full my-1 mb-3">
                     <label class="block mb-2 text-sm font-bold text-gray-900" for="description"
                            title="A concise depiction or portrayal providing details and characteristics of a subject or object.">
                        Description <span class="text-red-600">*</span>
                     </label>
                     <textarea id="description" v-model="product.description" class="form-input h-36" placeholder="Describe your product here..."></textarea>
                  </div>
                  <div class="w-full my-1 mb-3">
                     <label class="block mb-2 text-sm font-bold text-gray-900" for="features"
                            title="Distinctive attributes or characteristics of something, often highlighting its unique qualities or functions.">
                        Features <span class="text-red-600">*</span>
                     </label>
                     <RichTextEditor :initial_value="product.features" :setVal="(val) => product.features = val" height="350"/>
                  </div>
                  <div class="flex flex-col md:flex-row gap-8 mx-2 mb-3">
                     <div class="flex flex-col md:w-3/5 w-full">
                        <div class="flex gap-4 w-full my-2">
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="gender_id">Gender <span class="text-red-600">*</span></label>
                              <div class="relative">
                                 <select id="gender_id" v-model="product.gender_id" class="form-input appearance-none" required>
                                    <option selected value="">Select Gender</option>
                                    <option v-for="(gender) in genders" :key="gender.id" :value="gender.name">{{ gender.name }}</option>
                                 </select>
                                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                                 </div>
                              </div>
                           </div>
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="style_id"
                                     title="Style is a distinctive manner or characteristic expression that defines an individual, work, or era">
                                 Style <span class="text-red-600">*</span>
                              </label>
                              <div class="relative">
                                 <select id="style_id" v-model="product.style_id" class="form-input appearance-none" required>
                                    <option selected value="">Select Style</option>
                                    <option v-for="(style) in styles" :key="style.id" :value="style.name">{{ style.name }}</option>
                                 </select>
                                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fi fi-ss-angle-small-down text-xl w-5 h-6 ml-1"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="flex gap-4 w-full my-2">
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="sizes">
                                 Available Sizes <span class="text-red-600">*</span>
                              </label>
                              <Multiselect v-model="multi_selected_sizes" :multiple="true" :options="sizeDropdown" :showLabels="false"/>
                           </div>
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="seller_id">
                                 Available Colors <span class="text-red-600">*</span>
                              </label>
                              <Multiselect v-model="multi_selected_colors" :multiple="true" :options="colorDropdown" :showLabels="false">
                                 <template v-slot:selection="{values, remove}">
                                   <span v-for="color in values" :key="color" :style="'background-color:'+color+'!important;'" class="multiselect__tag">
                                     <div class="color-backdrop">
                                       <span class="color">{{ color }}</span>
                                     </div>
                                     <i class="multiselect__tag-icon" tabindex="1" @click="remove(color)"></i>
                                   </span>
                                 </template>
                              </Multiselect>
                           </div>
                        </div>
                        <div class="flex gap-4 w-full my-2">
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="length" title="The length of the item in cms. Must be more than 0.5">
                                 Length (cms) <span class="text-red-600">*</span>
                              </label>
                              <input id="length" v-model="product.length" class="form-input" placeholder="20" required type="text">
                           </div>
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="breadth" title="The breadth of the item in cms. Must be more than 0.5">
                                 Breadth (cms) <span class="text-red-600">*</span>
                              </label>
                              <input id="breadth" v-model="product.breadth" class="form-input" placeholder="20" required type="text">
                           </div>
                        </div>
                        <div class="flex gap-4 w-full my-2">
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="height" title="The height of the item in cms. Must be more than 0.5">
                                 Height (cms) <span class="text-red-600">*</span>
                              </label>
                              <input id="height" v-model="product.height" class="form-input" placeholder="20" required type="text">
                           </div>
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="weight" title="The weight of the item in cms. Must be more than 0.5">
                                 Weight (cms) <span class="text-red-600">*</span>
                              </label>
                              <input id="weight" v-model="product.weight" class="form-input" placeholder="20" required type="text">
                           </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-4 w-full mt-2 mb-1">
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="mrp">Maximum Retail Price (MRP) <span class="text-red-600">*</span></label>
                              <input id="mrp" v-model="product.mrp" class="form-input" placeholder="1999" required step="0.01" type="number">
                           </div>
                           <div class="md:w-1/2 w-full">
                              <label class="block mb-2 text-sm font-bold text-gray-900" for="offer_price">Selling Price <span class="text-red-600">*</span></label>
                              <input id="offer_price" v-model="product.offer_price" class="form-input" placeholder="1499" required step="0.01" type="number">
                           </div>
                        </div>
                        <div class="w-full mb-2">
                           <div v-if="product.mrp > 0 && product.offer_price > 0" class="text-green-500 text-sm text-center">
                              <span class="text-gray-800">Discount :</span>
                              ₹{{ currencyFormat(discount) }} ({{ discountPercent }}%)
                           </div>
                        </div>
                        <div class="w-full my-2">
                           <label class="block mb-2 text-sm font-bold text-gray-900" for="product_tags"
                                  title="Product keywords are specific terms used to optimize search and enhance discoverability online.">
                              Product Keywords / Tags (Comma [,] Seperated)
                           </label>
                           <textarea id="product_tags" v-model="product.product_tags" class="form-input h-32"
                                     placeholder="Western, Western-style clothing, Dress, Denim, Bolo tie, Western belt">
                           </textarea>
                        </div>
                     </div>
                     <div class="block md:w-2/5 w-full">
                        <div class="w-full my-2">
                           <div>
                              <label class="block mb-1 text-sm font-bold text-gray-900" title="An image which references chart indicating measurements for selecting appropriate clothing or products.">
                                 Size Guide
                              </label>
                              <img v-if="product && product.size_guide"
                                   :src="$store.state.storageUrl + product.size_guide"
                                   alt="main-img"
                                   class="w-100 border h-[23rem] max:h-full w-full rounded-md shadow-md cursor-zoom-in"
                                   @click="imageModal($store.state.storageUrl + product.size_guide)"
                                   @error="imageLoadError"/>
                           </div>
                           <div class="my-2">
                              <input id="size_guide" accept="image/*" class="form-input" name="signature" type="file" @change="handleSizeImageChange($event)">
                           </div>
                        </div>
                        <label class="block mb-2 text-sm font-bold text-gray-900">More Images</label>
                        <div class="grid gap-2 grid-cols-2">
                           <template v-if="product.json_images && Array.isArray(product.json_images) && product.json_images.length > 0">
                              <div v-for="(image, index) in product.json_images" :key="index" class="relative bg-white border border-gray-200 rounded-lg shadow h-[18rem]">
                                 <div class="h-full w-full">
                                    <img v-if="image"
                                         :src="$store.state.storageUrl + image"
                                         alt="image"
                                         class="h-full w-full cursor-zoom-in border rounded-md object-fill"
                                         @click="imageModal($store.state.storageUrl + image)"
                                         @error="imageLoadError"
                                    />
                                 </div>
                                 <button class="absolute top-0 right-1 text-red-500 text-xl cursor-pointer" type="button" @click="deleteImage(image)">
                                    <i class="fi fi-rr-circle-xmark"></i>
                                 </button>
                              </div>
                           </template>
                           <div class="flex items-center justify-center col-span-2 h-52">
                              <label
                                  class="relative flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:rounded-xl hover:shadow-md"
                                  for="dropzone-file">
                                 <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fi fi-rr-cloud-upload-alt text-4xl h-8 mb-4 text-gray-500"></i>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF</p>
                                    <p id="dropzone-file-select" class="text-base text-gray-500"></p>
                                 </div>
                                 <input id="dropzone-file" accept="image/*" class="absolute h-full w-full z-0 m-0 p-0 opacity-0" multiple type="file" @change="handleFileChange($event)">
                              </label>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="text-center">
                     <button class="submit-btn" type="submit"> {{ this.editId ? 'Update' : 'Create' }}</button>
                     <button class="submit-btn bg-gray-600 hover:bg-gray-700" type="button" @click="clear()">Clear</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <ImageModal :hide="closeImageModal" :img="imgModal" :show="showModal"></ImageModal>
   </div>
</template>
<script>
   export default {
      name: "ProductCreateOrEdit",
      data() {
         return {
            loading: false,
            // dataLoading: false,
            showModal: false,
            imgModal: '',
            editId: this.$route.params.id,
            //dropdown-lists
            categories: [],
            sub_categories: [],
            brands: [],
            sellers: [],
            sizes: [],
            colors: [],
            genders: [],
            styles: [],
            multi_selected_sizes: [],
            multi_selected_colors: [],
            admin_statuses: [
               {id: 1, name: 'Pending For Verification'},
               {id: 2, name: 'Accepted'},
               {id: 3, name: 'Rejected'},
               {id: 4, name: 'Updated'},
            ],
            product: {
               //basic-text-fields
               name: '',
               sku: '',
               slug: '',
               //dropdown-list
               category_id: '',
               subcategory_id: '',
               sizes: [],
               colors: [],
               brand_id: '',
               gender_id: '',
               style_id: '',
               //dimensions
               length: '',
               breadth: '',
               height: '',
               weight: '',
               //price fields
               offer_price: 0,
               mrp: 0,
               //image
               image: "",
               images: [],
               json_images: [],
               size_guide: "",
               //textarea-fields
               description: "",
               features: "<p>Describe your product's features here...</p>",
               product_tags: "",
            },
         }
      },
      watch: {
         "product.category_id": function (newValue, oldValue) {
            this.fetchSubCategory();
         }
      },
      computed: {
         discount() {
            return (this.product.mrp - this.product.offer_price).toFixed(2);
         },
         discountPercent() {
            return ((this.discount / this.product.mrp) * 100).toFixed(2);
         },
         sizeDropdown() {
            return this.sizes.map(size => size.name);
         },
         colorDropdown() {
            return this.colors.map(color => color.name);
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
         clear() {

         },
         deleteImage(img) {
            if (!confirm("Are you sure want to delete this image ? ")) {
               return false;
            }
            this.loading = true;
            axios
                .post(`/vendor/product/delete-image`, {
                   image: img,
                })
                .then(res => {
                   this.loading = false;
                   const imgIndex = this.product.json_images.findIndex(jsonImage => jsonImage === img)
                   this.product.json_images.splice(imgIndex, 1);
                   this.show_toast(res.data.status, res.data.msg);
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         handleSizeImageChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            const formData = new FormData();
            formData.append('images[0]', files[0]);
            this.uploadImageToServer(formData).then(res => {
               this.product.size_guide = res[0];
               $('#size_guide').val('')
            });
         },
         handleImageChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }
            const formData = new FormData();
            formData.append('images[0]', files[0]);
            this.uploadImageToServer(formData).then(res => {
               this.product.image = res[0];
               $('#main_img').val('')
            });
         },
         handleFileChange(e) {
            let files = e.target.files;
            if (files.length <= 0) {
               return false;
            }

            //4 files selected txt
            $('#dropzone-file-select').html(files.length + ' file(s) selected')

            //creating new object of files
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
               let file = files[i];
               formData.append('images[' + i + ']', file);
            }
            this.uploadImageToServer(formData).then(res => {
               this.product.json_images = [...this.product.json_images, ...res];
            });

         },
         uploadImageToServer(formData) {
            return new Promise((resolve, reject) => {
               //uploading to db
               const config = {
                  headers: {'Content-Type': 'multipart/form-data'}
               }
               this.loading = true;
               axios.post(`/vendor/product/upload-images`, formData, config)
                   .then((res) => {
                      this.loading = false;
                      this.show_toast(res.data.status, res.data.msg);
                      resolve(res.data.data)
                      $('#dropzone-file-select').html('');
                   })
                   .catch(err => {
                      $('#dropzone-file-select').html('');
                      this.loading = false;
                      reject(err);
                      err.handleGlobally && err.handleGlobally();
                   })
            })

         },

         submitForm() {
            this.loading = true;
            const formData = new FormData();
            if (this.editId) {
               formData.append('id', this.editId);
            }
            formData.append('multi_selected_sizes', JSON.stringify(this.multi_selected_sizes));
            formData.append('multi_selected_colors', JSON.stringify(this.multi_selected_colors));
            //appned all keys of product object
            Object.keys(this.product).forEach(function (key) {
               const value = this.product[key];
               if (value) {
                  if (Array.isArray(value)) {
                     formData.append(key, JSON.stringify(value));
                  } else {
                     formData.append(key, value);
                  }
               }
            }, this);

            axios
                .post('/vendor/product/edit-or-create', formData)
                .then(res => {
                   this.loading = false;
                   this.show_toast(res.data.status, res.data.msg);
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchProduct() {
            this.loading = true;
            axios
                .get('vendor/product/' + this.editId)
                .then(res => {
                   this.product = res.data.data;
                   this.multi_selected_sizes = this.product.sizes.map(size => size.name)
                   this.multi_selected_colors = this.product.colors.map(color => color.name)
                   this.loading = false;
                })
                .catch(err => {
                   this.loading = false;
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchParentCategory() {
            this.loading = true;
            axios
                .get('/vendor/category', {
                   params: {
                      rows: 'all',
                      status: 1,
                   }
                })
                .then(res => {
                   this.loading = false;
                   this.categories = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchSubCategory() {
            axios
                .get('/vendor/sub-category', {
                   params: {
                      rows: 'all',
                      status: 1,
                      category_id: this.product.category_id,
                   }
                })
                .then(res => {
                   this.sub_categories = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchBrand() {
            axios
                .get('/vendor/brand', {
                   params: {
                      rows: 'all',
                      status: 1,
                   }
                })
                .then(res => {
                   this.brands = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchSizes() {
            axios
                .get('/vendor/size', {
                   params: {
                      rows: 'all',
                      status: 1,
                   }
                })
                .then(res => {
                   this.sizes = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchColors() {
            axios
                .get('/vendor/color', {
                   params: {
                      rows: 'all',
                      status: 1,
                   }
                })
                .then(res => {
                   this.colors = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchStyle() {
            axios
                .get('/vendor/style', {
                   params: {
                      rows: 'all',
                      status: 1,
                   }
                })
                .then(res => {
                   this.styles = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         },
         fetchGender() {
            axios
                .get('/vendor/gender', {
                   params: {
                      rows: 'all',
                      status: 1,
                   }
                })
                .then(res => {
                   this.genders = res.data.data;
                })
                .catch(err => {
                   err.handleGlobally && err.handleGlobally();
                })
         }
      },
      created() {
         this.editId && this.fetchProduct();
         this.fetchParentCategory();
         this.fetchSizes();
         this.fetchColors();
         this.fetchBrand();
         this.fetchStyle();
         this.fetchGender();
      },
   }
</script>