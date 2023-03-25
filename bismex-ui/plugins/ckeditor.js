let ClassicEditor
let CKEditor

if (process.client) {
	ClassicEditor = require('@ckeditor/ckeditor5-build-classic')
	CKEditor = require('@ckeditor/ckeditor5-vue2')
} else {
	CKEditor = { component: { template: '<div></div>' } }
}
