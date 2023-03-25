<template>
	<div class="container">
		<v-card
			color="#19232A"
			class="t-p-5 t-grid t-grid-flow-row t-auto-rows-max t-gap-4"
			v-if="tickets"
		>
			<v-expansion-panels>
				<v-expansion-panel
					v-for="(item, i) in tickets"
					:key="i"
					style="background-color: rgb(25, 35, 42);"
				>
					<v-expansion-panel-header
						color="#FFFFFF"
						style="background-color: #0E635D; border-radius: 8px:"
						class="t-mb-2"
						@click="getDetail({ ticketid: item.ticketid, page: 1 })"
					>
						<v-row>
							<v-col md="12">
								<div class="t-text-white">{{ item.subject }}</div>
							</v-col>
							<v-col md="12">
								<div class="t-flex t-items-center t-gap-4">
									<span class="t-text-white">{{
										item.created_at | dateYear
									}}</span>
									<span class="t-text-white">{{
										item.created_at | dateTime
									}}</span>
								</div>
							</v-col>
						</v-row>
					</v-expansion-panel-header>
					<v-expansion-panel-content
						color="#FFF7EB"
						v-if="detail"
						style="background-color: rgb(14, 99, 93); border-radius: 8px"
					>
						<div class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 pt-4">
							<div
								class="t-grid t-grid-flow-row t-auto-rows-max t-gap-3 t-max-h-52 t-overflow-y-auto"
							>
								<v-sheet
									color="#fffff"
									style="background-color: rgb(14, 99, 93);"
									class="t-p-3 t-rounded-md t-text-white"
									v-for="(m, index) in detail.data"
									:key="index"
								>
									<div class="t-flex ">
										<v-icon class="t-mr-4 t-text-white">{{
											$auth.$state.user.id === m.userid
												? "mdi-chat-processing-outline"
												: "mdi-reply-outline"
										}}</v-icon>
										<span v-html="m.message"></span>
									</div>
								</v-sheet>
							</div>
							<v-form
								class="t-block"
								@submit.prevent="reply({ data: input, id: item.ticketid })"
							>
								<v-textarea
									outlined
									height="15"
									:label="$t('supportPage.reply')"
									hide-details
									v-model="input.message"
									:readonly="$load('support')"
									style="background-color: rgb(25, 35, 42); color: white"
								>
									<template #append>
										<v-btn
											color="#03A593"
											icon
											type="submit"
											:loading="$load('support')"
										>
											<v-img :src="$svg.send" max-width="20"></v-img>
										</v-btn>
									</template>
								</v-textarea>
							</v-form>
						</div>
					</v-expansion-panel-content>
				</v-expansion-panel>
			</v-expansion-panels>
			<v-card-actions>
				<v-pagination
					:value="tickets.current_page"
					:length="tickets.last_page"
					@next="getTickets(tickets.current_page + 1)"
					@previous="getTickets(tickets.current_page - 1)"
					@input="(v) => getTickets(v)"
					color="#33B9A0"
					total-visible="7"
				></v-pagination>
			</v-card-actions>
		</v-card>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import SupportModel from "~/models/support.model";
import moment from "moment";

export default {
	data() {
		return {
			input: new SupportModel(),
		};
	},
	computed: {
		...mapState("support", ["tickets", "detail"]),
	},
	methods: {
		...mapActions("support", ["getDetail", "getTickets", "reply"]),
	},
	async mounted() {
		await this.getTickets();
	},
	filters: {
		dateYear(value) {
			return moment(value).format("YYYY-MM-DD");
		},
		dateTime(value) {
			return moment(value).format("HH:mm");
		},
	},
};
</script>

<style lang="scss">
.v-input__append-inner {
	margin: auto !important;
}

.theme--light.v-label {
	color: white !important;
}

.v-textarea textarea {
	color: white !important;
}
</style>
