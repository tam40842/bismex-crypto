<template>
	<div class="t-mx-auto t-border t-border-solid t-border-custom-EFEFEF t-rounded">
		<v-card color="transparent">
			<v-card-title>
				<h3 class="t-uppercase t-text-custom-EBA900 mx-auto">shareholder POLICY</h3>
			</v-card-title>
			<v-card-text class="t-h-96 t-overflow-y-auto">
				<ul class="t-list-disc t-grid t-grid-flow-row t-auto-rows-max t-gap-2 white--text">
					<li
						v-for="i in 10"
						:key="i"
					>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Consequat duis turpis sem gravida venenatis aliquet sed ut. Pharetra adipiscing arcu in sagittis. Augue vitae accumsan turpis in volutpat turpis vestibulum. Nibh malesuada bibendum condimentum volutpat diam.</li>
				</ul>
			</v-card-text>
			<v-card-actions class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 t-items-center t-pb-5">
				<v-checkbox v-model="accept" color="#EBA900">
					<template #label>
						<span
							class="t-text-custom-EBA900"
						>I Agree to Privacy Policy checkbox gives you permission to process user data. Adding them to data collection points across your site protects you</span>
					</template>
				</v-checkbox>
				<div class="t-block t-text-center">
					<v-btn
						color="#EBA900"
						class="white--text t-mx-auto"
						:disabled="!accept"
						width="120"
						@click="next"
					>SUBMIT</v-btn>
				</div>
			</v-card-actions>
		</v-card>
	</div>
</template>

<script>
	export default {
		methods: {
			next() {
				this.$router.push("/shareholder/overview");
				this.$store.commit("shareholder/ACCEPT", this.accept);
			},
		},
		data() {
			return {
				accept: false,
			};
		},
		beforeRouteEnter(to, from, next) {
			// if (this.$store.state.shareholder.accept) next("/shareholder/overview");
			// else next();
			next((vm) => {
				if (vm.$store.state.shareholder.accept)
					next("/shareholder/overview");
			});
		},
	};
</script>

<style>
</style>