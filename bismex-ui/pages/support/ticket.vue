<template>
	<v-card color="#19232A" class="container">
		<v-form @submit.prevent="SEND_TICKET(ticket)" :readonly="$load('support')">
			<v-card-title>
				<h3 class="t-text-[#03A593] t-font-bold t-mx-auto">
					{{ $t("supportPage.createNew") }}
				</h3>
			</v-card-title>
			<v-card-text>
				<v-text-field
					solo
					v-model="ticket.subject"
					:label="$t('supportPage.subject')"
				></v-text-field>
				<ckeditor
					:editor="editor"
					v-model="ticket.message"
					:config="editorConfig"
				></ckeditor>
				<!-- <v-textarea
					color="white"
					solo
					hide-details
					v-model="ticket.message"
					label="Messages"
				></v-textarea> -->
			</v-card-text>
			<v-card-actions>
				<v-btn
					color="#03A593"
					type="submit"
					class="t-uppercase white--text t-mx-auto white--text"
					:loading="$load('support')"
					width="120"
					>Send</v-btn
				>
			</v-card-actions>
		</v-form>
	</v-card>
</template>

<script>
let ClassicEditor;
let CKEditor;

if (process.client) {
	ClassicEditor = require("@ckeditor/ckeditor5-build-classic");
	CKEditor = require("@ckeditor/ckeditor5-vue2");
} else {
	CKEditor = { component: { template: "<div></div>" } };
}
import { mapActions, mapState } from "vuex";
import SupportModel from "~/models/support.model";
export default {
	components: {
		ckeditor: CKEditor.component,
	},
	data() {
		return {
			ticket: new SupportModel(),
			editor: ClassicEditor,
			editorConfig: {
				// The configuration of the editor.
			},
		};
	},
	methods: {
		...mapActions("support", ["SEND_TICKET"]),
	},
	computed: {
		ticketChange() {
			if (this.ticket.message === null) {
				this.ticket.message = "";
				return this.ticket;
			}
		},
	},
};
</script>
