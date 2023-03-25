<template>
	<div class="t-absolute t-top-3 t-left-3 t-z-10">
		<v-menu offset-y v-if="active" transition="slide-y-transition">
			<template v-slot:activator="{ on, attrs }">
				<v-btn
					color="#8A8A8A30"
					class="white--text t-rounded"
					tile
					v-bind="attrs"
					v-on="on"
					elevation="0"
					x-small
				>
					<v-img :src="active.logo" height="14" width="14" contain left></v-img>
					{{ active.market_name }}
				</v-btn>
			</template>
			<v-list color="#8A8A8A30">
				<v-list-item
					v-for="(item, index) in list"
					:key="index"
					@click="update(item)"
					color="#8A8A8A30"
					class="t-p-2 t-min-h-0 t-flex t-items-center"
				>
					<v-list-item-icon class="t-mr-1 t-my-1">
						<v-img :src="item.logo" height="14" width="14" contain></v-img>
					</v-list-item-icon>
					<v-list-item-title class="white--text t-text-xs">{{
						item.market_name
					}}</v-list-item-title>
				</v-list-item>
			</v-list>
		</v-menu>
	</div>
</template>

<script>
export default {
	props: {
		active: {
			type: Object,
			default: null,
		},
		list: {
			type: Array,
			default: false,
		},
	},
	methods: {
		update(v) {
			this.$emit("update", v);
		},
	},
};
</script>

<style></style>
