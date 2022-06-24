<template>

	<div class="bg-gradient-to-r from-rose-100 to-teal-100 min-h-full h-full">

		<div class="w-1/2 mx-auto bg-transparent max-h-60 fixed inset-x-0 top-7 z-10">

			<div class="flex justify-center pt-5">
				<input
					class=" placeholder:text-slate-400 block bg-white w-full border border-slate-300 rounded-md py-4 pl-9 pr-3 shadow-sm focus:outline-none focus:border-transparent focus:ring-0 text-md"
					placeholder="Search for anything..." v-model="search" />
			</div>

		</div>

		<main class="flex items-center justify-center w-full pt-36">

			<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 z-0 mb-32" v-if="images.length > 0">

				<div class="overflow-hidden relative justify-end  cursor-pointer capitalize flex flex-col rounded-md bg-gray-300 w-full lg:w-72 h-full lg:h-96 z-50 object-cover text-white transform hover:scale-105 duration-300 ease-in-out"
					v-for="(image, index) in filteredItems" :key="index">


					<img :src="`/storage/images/${image.name}`" alt="{{image.name}}" class="h-full object-contain" />

					<div class="absolute p-3 flex flex-col bg-stone-400 bg-opacity-50 w-full h-1/5">

						<span class="font-semibold capitalize" v-if="(image.original_name)">

							{{ image.original_name }}

						</span>
						<span class="font-semibold capitalize" v-else>Name not found</span>

					</div>

				</div>

			</div>

			<div class="flex justify-around items-center" v-else>

				<span class="font-semibold text-2xl tracking-wider mr-3">
					Sorry, no items were found
				</span>

				<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
					stroke="currentColor" stroke-width="2">
					<path stroke-linecap="round" stroke-linejoin="round"
						d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
				</svg>

			</div>


			<div class="bg-transparent mx-auto w-1/2 max-h-60 fixed inset-x-0 bottom-0 p-4 z-10 overflow-y-auto"
				v-if="canUpload">

				<file-pond name="image" ref="pond" label-idle="Click here to choose an image, or drag here"
					accepted-file-types="image/jpg, image/jpeg, image/png" allowFileRename="true"
					allowImagePreview="true" imagePreviewMinHeight="120" imagePreviewMaxHeight="120"
					imagePreviewTransparencyIndicator="grid" :fileRenameFunction="renameFile"
					@processfile="handleProcessedFile" @initfile="handleFilePondInitFile" />
			</div>

		</main>

	</div>

</template>
<script>

	import vueFilePond, { setOptions } from 'vue-filepond'

	import axios from 'axios'

	import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'

	import FilePondPluginImagePreview from 'filepond-plugin-image-preview';

	import FilePondPluginFileRename from 'filepond-plugin-file-rename';

	import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

	import 'filepond/dist/filepond.min.css'

	var serverMessage = {}

	setOptions({
		server: {
			process: {

				url: './collection/upload',
				onerror: (response) => {

					serverMessage = JSON.parse(response)
				},
				headers: {
					'X-CSRF-TOKEN': (document.head.querySelector('meta[name ="csrf_token"]')) ? document.head.querySelector('meta[name ="csrf_token"]').content : null
				}
			},

		},
		labelFileProcessingError: () => {

			return serverMessage.error
		}

	});

	const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginFileRename);

	export default {
		components: {
			FilePond
		},
		data() {

			return {
				images: [],
				canUpload: false,
				search: ''
			}
		},
		computed: {

			filteredItems() {


				return this.images.filter((item) => {

					return item.original_name.toLowerCase().includes(this.search.toLowerCase())

				})
			}

		},
		methods: {

			handleProcessedFile(error, file) {

				if (error) {

					console.log(error)
					return;
				}

				const createdFile = JSON.parse(file.serverId)

				if (this.images.length > 0) {

					this.images.unshift(createdFile)
				}

				this.getCollectionItems()

				setTimeout(() => {

					this.$refs.pond.removeFile()

				}, 3000);

			},
			renameFile: (file) =>

				new Promise((resolve) => {

					resolve(window.prompt('Enter new filename', file.name))

				}),
			getCollectionItems() {

				axios.get('collection/gallery')
					.then((response) => {

						this.canUpload = ((response.data[0] !== undefined && response.data[0].is_logged) || (response.data.is_logged != undefined && response.data.is_logged)) ?? false;
						this.images = response.data

					}).catch((error) => {

						console.log(error)
					})
			}
		},
		mounted() {

			this.getCollectionItems()
		}
	}
</script>