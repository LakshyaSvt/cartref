<template>
   <Transition>
      <div v-if="show"
           class="fixed right-0 left-0 top-0 bottom-0 z-50 items-center justify-center bg-gray-900 bg-opacity-60 px-4 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full">
         <div class="relative max-w-2xl max-h-full mx-auto my-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
               <!-- Modal header -->
               <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                  <h3 v-if="title" class="text-xl font-semibold text-gray-900 dark:text-white">
                     {{ title }}
                  </h3>
                  <button class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" title="Close"
                          type="button"
                          @click="hide()">
                     <svg aria-hidden="true" class="w-3 h-3" fill="none" viewBox="0 0 14 14"
                          xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"/>
                     </svg>
                     <span class="sr-only">Close modal</span>
                  </button>
               </div>
               <slot></slot>
            </div>
         </div>
      </div>
   </Transition>
</template>
<script>
   export default {
      name: "Modal",
      props: {
         show: Boolean,
         hide: Function,
         title: String,
      },
      watch: {
         show(val) {
            if (val) {
               document.body.style.overflow = 'hidden'
            } else {
               document.body.style.overflow = 'unset'
            }
         }
      },
   }
</script>
