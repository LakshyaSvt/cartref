<template>
   <div class="container mx-auto my-2 px-4 py-2">
      <Wait :show="loading"/>
      <div class="flex flex-wrap justify-between items-center">
         <div>
            <a class="inline-flex items-center gap-2 px-4 py-2 text-base font-bold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-gray-800 hover:bg-black hover:text-white"
               @click="$router.go(-1)">
               <i class="fi fi-rr-arrow-left text-base w-4 h-5"></i>
               Back
            </a>
         </div>
      </div>
      <div class="flex gap-2 items-center text-3xl text-primary-500 font-semibold">
         <i class="fi fi-rr-folder-upload"></i>
         <h3 class="text-start my-8">Upload</h3>
      </div>

      <div class="bg-white p-4 mx-auto my-2 overflow-x-auto shadow-md rounded-lg">
         <div class="block">
            <form @submit.prevent="uploadFile()">
               <div class="flex flex-col md:flex-row gap-4 my-2 py-1">
                  <div class="md:w-1/3 w-full md:mx-2 my-1">
                     <label class="block mb-2 text-sm font-bold text-gray-900" title="Those immediately above the category in the hierarchy">Parent Category
                        <span class="text-red-600">*</span></label>
                     <select v-model="parent_category_id" class="form-input" required @change="fetchSubCategory">
                        <option selected value="">Select Parent Category</option>
                        <option v-for="(parent, index) in parent_category" :key="index" :value="parent.id">{{ parent.name }}
                        </option>
                     </select>
                  </div>
                  <div class="md:w-1/3 w-full md:mx-2 my-1">
                     <label class="block mb-2 text-sm font-bold text-gray-900" title="Those immediately above the category in the hierarchy">Sub Category
                        <span class="text-red-600">*</span></label>
                     <select v-model="sub_category_id" class="form-input" required>
                        <option selected value="">Select Sub Category</option>
                        <option v-for="(sub, index) in sub_category" :key="index" :value="sub.id">{{ sub.name }}
                        </option>
                     </select>
                  </div>
                  <div class="md:w-1/3 w-full md:mx-2 my-1">
                     <label class="block mb-2 text-sm font-bold text-gray-900" for="file">Excel File <span class="text-red-600">*</span></label>
                     <input id="file" accept=".csv,.xls,.xlsx" class="form-input" required type="file" @change="handleFileChange($event)">
                  </div>
               </div>
               <div class="w-full">
                  <button :disabled="uploading" class="submit-btn" type="submit">
                     {{ progress ? progress : '' }}
                     {{ uploading ? 'Uploading...' : 'Upload' }}
                  </button>
               </div>
            </form>
         </div>
         <!--<a class=" btn btn-warning btn-add-new" download href="{{ route('products.download-dummy') }}">-->
         <!--  <i class="voyager-cloud-download"></i> <span>Download Dummy File</span>-->
         <!--</a>-->
      </div>
      <a :href="$store.state.url+'/seller/products/download-dummy'"
         class="inline-flex items-center float-right gap-2 px-4 py-2 text-sm font-semibold text-center text-white align-middle transition-all rounded-lg cursor-pointer bg-green-500 hover:bg-green-600"
         download>
         <i class="fi fi-rr-download text-base w-4 h-5"></i> Download Dummy File
      </a>
      <div v-if="responseData" class="bg-white p-4 mx-auto my-2 overflow-x-auto shadow-md rounded-lg">
         <h1 class="text-3xl mb-4">Response :- </h1>
         <div class="block">
            <p class="text-base" v-html="responseData"></p>
         </div>
      </div>
   </div>
</template>

<script setup>
   import {onMounted, ref} from "vue";
   import toast from "../../plugins/toast";

   const loading = ref(false);
   const parent_category = ref([]);
   const sub_category = ref([]);
   const parent_category_id = ref('');
   const sub_category_id = ref('');
   //file
   const uploading = ref(false);
   const file = ref('');
   const responseData = ref('');
   const progress = ref('');

   const handleFileChange = (e) => {
      if (e?.target?.files.length > 0) {
         const targetFile = e.target.files[0];
         if (targetFile) {
            const fileName = targetFile.name.toLowerCase();
            if (fileName.endsWith('.xls') || fileName.endsWith('.xlsx') || fileName.endsWith('.csv')) {
               file.value = targetFile //updating ref value
            } else {
               document.getElementById('file').value = '';
               alert("Please choose valid file!");
            }
         }
      }
   }

   const uploadFile = () => {
      if (!file.value) return;

      //uploading progress
      const config = {
         onUploadProgress: (progressEvent) => {
            const {loaded, total} = progressEvent;
            let percent = Math.floor((loaded * 100) / total)

            if (percent > 0 && percent < 100) {
               progress.value = percent + '%'
            } else {
               progress.value = '';
            }
         },
      }
      let formData = new FormData();
      formData.append('file', file.value);
      formData.append('category_id', parent_category_id.value);
      formData.append('subcategory_id', sub_category_id.value);

      uploading.value = true;
      responseData.value = '';

      axios.post('/admin/product/bulk-upload', formData, config)
          .then(res => {
             console.log(res.data);
             uploading.value = false;
             progress.value = '';
             responseData.value = (res.data.data);
             toast.show_toast(res.data.status, res.data.msg);
          })
          .catch(err => {
             err.handleGlobally && err.handleGlobally();
             uploading.value = false;
          })
   }

   const fetchParentCategory = () => {
      loading.value = true;
      axios.get('/admin/category', {
         params: {
            rows: 'all',
            status: 1,
         }
      })
          .then(res => {
             loading.value = false;
             parent_category.value = res.data.data;
          })
          .catch(err => {
             loading.value = false;
             err.handleGlobally && err.handleGlobally();
          })
   }
   const fetchSubCategory = () => {
      sub_category_id.value = '';
      sub_category.value = [];
      axios.get('/admin/sub-category', {
         params: {
            rows: 'all',
            status: 1,
            category_id: parent_category_id.value,
         }
      })
          .then(res => {
             sub_category.value = res.data.data;
          })
          .catch(err => {
             err.handleGlobally && err.handleGlobally();
          })
   }

   onMounted(() => {
      fetchParentCategory();
   })

</script>