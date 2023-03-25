<template>
	<v-app-bar height="64" fixed color="#19232A50" elevate-on-scroll>
		<v-container fluid class="xl:t-w-10/12 t-flex t-items-center t-mx-auto">
			<v-toolbar-title>
				<v-btn
					color="transparent"
					class="t-px-0"
					text
					@click="$vuetify.goTo('#introl')"
					v-if="$route.name === 'index'"
					height="64"
				>
					<!-- <v-img :src="$image.logo" height="40" alt="Bitsmex" class="md:t-inline-block t-hidden" contain></v-img>
					<v-img :src="$image.logo" height="40" contain alt="Bitsmex" class="md:t-hidden"></v-img>-->
					<img
						:src="$image.logo"
						alt="Bitsmex"
						class="md:t-inline-block t-hidden t-h-10"
					/>
					<img :src="$image.logo" alt="Bitsmex" class="md:t-hidden t-h-10" />
				</v-btn>
				<v-btn
					color="transparent"
					class="t-px-0 t-text-right"
					text
					to="/"
					v-else
					height="64"
				>
					<img
						:src="$image.logo"
						alt="Bitsmex"
						class="md:t-inline-block t-hidden t-h-10"
					/>
					<img :src="$image.logo" alt="Bitsmex" class="md:t-hidden t-h-10" />
				</v-btn>
			</v-toolbar-title>
			<v-spacer></v-spacer>
			<div class="t-grid t-grid-flow-col t-auto-cols-max">
				<MenuLanding />
				<div class="xl:t-hidden">
					<v-btn color="#03A593" @click="open">
						<v-icon color="white">mdi-menu</v-icon>
					</v-btn>
				</div>
			</div>
		</v-container>
	</v-app-bar>
</template>

<script>
export default {
	methods: {
		open() {
			this.$emit("open", true);
		},
	},
};
</script>

<style></style>
