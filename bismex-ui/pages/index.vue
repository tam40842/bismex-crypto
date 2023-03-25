<template>
	<div class="t-block bg">
		<section
			id="introl"
			class="t-py-16 t-relative t-flex t-flex-col t-justify-center"
		>
			<v-container
				fluid
				class="xl:t-mt-0 t-mt-24 xl:t-text-left t-text-center t-flex xl:t-flex-row t-flex-col t-items-center xl:t-w-10/12 t-mx-auto"
			>
				<div class="2xl:t-w-2/5 xl:t-w-1/2">
					<p class="md:t-text-3xl t-text-lg white--text t-font-semibold">
						TRADE STOCKS, FOREX AND CRYPTO
					</p>
					<h1 class="sm:t-text-6xl t-text-4xl t-text-white t-font-bold">
						WITH BITSMEX
					</h1>
					<p class="lg:t-text-xl t-text-sm white--text t-mt-4">
						Binary options trading is based on the blockchain platform.
						<br />The price movement of cryptocurrency will decide the winner.
					</p>
					<v-btn
						rounded
						width="150"
						color="#03A593"
						:to="$auth.loggedIn ? '/trading' : '/login'"
						class="white--text"
						>Trade now</v-btn
					>
				</div>
				<div class="2xl:t-w-3/5 xl:t-w-1/2 text-center xl:t-mt-0 t-mt-8">
					<img :src="$landing.s1_laptop" class="mx-w-full" />
				</div>
			</v-container>
		</section>
		<!-- Section 2 -->
		<section class="t-py-12" id="about">
			<v-container
				fluid
				class="xl:t-w-10/12 t-mx-auto t-flex xl:t-flex-row t-flex-col t-items-center t-justify-between xl:t-min-h-screen"
			>
				<div class="xl:t-w-5/12">
					<h2 class="white--text t-text-4xl t-mb-3">What is Bitsmex?</h2>
					<p>
						BITSMEX has been helping our customers and partners earn a variety
						of sustainable and diversified income streams. BITSMEX is offering
						individual and institutional investors a wide range of trading
						products from Forex, commodities, and cryptocurrencies. The company
						is doing well all over the world and is a great place to provide
						professional and reliable services to its customers.
					</p>
				</div>
				<div class="xl:t-w-6/12">
					<v-img
						:src="$landing.s2"
						width="100%"
						max-width="100%"
						contain
					></v-img>
				</div>
			</v-container>
		</section>
		<!-- Section 3 -->
		<section class="t-py-12">
			<v-container
				fluid
				class="xl:t-w-10/12 t-mx-auto t-flex xl:t-flex-row t-flex-col t-items-center t-justify-between xl:t-min-h-screen"
				id="start"
			>
				<div class="xl:t-w-6/12 lg:t-w-2/5 sm:t-w-1/2 t-w-2/5 xl:mb-0 mb-5">
					<v-img
						:src="$landing.s3"
						width="100%"
						max-width="100%"
						contain
					></v-img>
				</div>
				<div class="xl:t-w-5/12 xl:t-order-2 t-order-1">
					<h2 class="white--text t-text-4xl t-mb-5">
						Get started easy
						<br />with Bitsmex
					</h2>
					<ul class="t-grid t-grid-flow-row t-auto-rows-max t-gap-3 t-pl-0">
						<li
							class="t-grid t-grid-flow-col t-items-center t-auto-cols-max t-gap-2"
						>
							<div
								class="t-h-10 t-w-10 t-rounded-full t-border-custom-03A593 t-border t-flex t-items-center t-justify-center t-text-xl t-font-bold white--text"
							>
								1
							</div>
							<p class="t-mb-0">Sign up for an account</p>
						</li>
						<li
							class="t-grid t-grid-flow-col t-items-center t-auto-cols-max t-gap-2"
						>
							<div
								class="t-h-10 t-w-10 t-rounded-full t-border-custom-03A593 t-border t-flex t-items-center t-justify-center t-text-xl t-font-bold white--text"
							>
								2
							</div>
							<p class="t-mb-0">Complete interactive tutorial</p>
						</li>
						<li
							class="t-grid t-grid-flow-col t-items-center t-auto-cols-max t-gap-2"
						>
							<div
								class="t-h-10 t-w-10 t-rounded-full t-border-custom-EBA900 t-border t-flex t-items-center t-justify-center t-text-xl t-font-bold white--text"
							>
								3
							</div>
							<p class="t-mb-0">Choose a cryptocurrency pair</p>
						</li>
						<li
							class="t-grid t-grid-flow-col t-items-center t-auto-cols-max t-gap-2"
						>
							<div
								class="t-h-10 t-w-10 t-rounded-full t-border-custom-EBA900 t-border t-flex t-items-center t-justify-center t-text-xl t-font-bold white--text"
							>
								4
							</div>
							<p class="t-mb-0">Practice with a demo account</p>
						</li>
						<li
							class="t-grid t-grid-flow-col t-items-center t-auto-cols-max t-gap-2"
						>
							<div
								class="t-h-10 t-w-10 t-rounded-full t-border-custom-EBA900 t-border t-flex t-items-center t-justify-center t-text-xl t-font-bold white--text"
							>
								5
							</div>
							<p class="t-mb-0">Deposit and become an expert</p>
						</li>
					</ul>
				</div>
			</v-container>
		</section>
		<!-- Section 4 -->
		<section class="t-py-12" id="profit">
			<v-container
				fluid
				class="xl:t-w-10/12 t-mx-auto t-flex xl:t-flex-row t-flex-col t-items-center t-justify-between xl:t-min-h-screen"
			>
				<div class="xl:t-w-4/12">
					<h2 class="white--text t-text-4xl t-mb-3">
						The simplest way to profit
					</h2>
					<p class="text-justify">
						practice first with a demo account, your has a lot of chances before
						you get the most profit with a real account. 90% of players have
						achieved their desired profit after a week
					</p>
				</div>
				<div class="xl:t-w-6/12">
					<v-img
						:src="$landing.s4"
						width="100%"
						max-width="100%"
						contain
					></v-img>
				</div>
			</v-container>
		</section>
		<!-- Section 5 -->
		<section class="t-py-12" id="advantages">
			<v-container
				fluid
				class="xl:t-w-10/12 t-mx-auto t-flex t-flex-col t-items-center t-justify-center xl:t-min-h-screen"
			>
				<h2 class="white--text t-text-4xl t-text-center t-mb-12 t-uppercase">
					Look at our advantages
				</h2>
				<div
					class="t-grid xl:t-grid-flow-col t-grid-flow-row t-auto-cols-fr t-auto-rows-fr t-gap-5"
				>
					<div class="t-block" v-for="i in 3" :key="i">
						<v-img :src="$landing['s5_' + i]"></v-img>
					</div>
				</div>
			</v-container>
		</section>
		<!-- Section 6 -->
		<section class="t-py-12" id="features">
			<v-container
				fluid
				class="xl:t-w-10/12 t-mx-auto t-flex t-flex-col t-items-center t-justify-center"
			>
				<h2 class="white--text t-text-4xl t-text-center t-mb-12 t-uppercase">
					FEATURES
				</h2>
				<div
					class="t-grid xl:t-grid-cols-2 x:t-grid-rows-2 t-grid-cols-1 t-grid-rows-1 t-gap-5"
				>
					<v-card
						elevation="0"
						color="#57678426"
						class="xl:t-col-span-2 t-col-span-1 t-row-span-1 t-border t-border-custom-425F99"
					>
						<v-card-text class="t-flex xl:t-flex-row t-flex-col t-items-center">
							<div class="xl:t-w-1/2">
								<v-img
									:src="$landing.s6_1"
									width="350"
									class="t-mx-auto"
								></v-img>
							</div>
							<div class="xl:t-w-1/2">
								<h3 class="white--text t-font-black t-text-4xl">
									Not just by luck
								</h3>
								<p class="white--text xl:t-w-2/3 t-mt-4">
									Trading with Bitsmex does not require the trader to do an
									in-depth analysis of the financial markets. But traders can't
									just rely on luck either. To trade more efficiently and
									understand the market movements, it is recommended that newbie
									traders learn about the opportunities offered by the broker
									and familiarize themselves with many common trading tactics.
								</p>
							</div>
						</v-card-text>
					</v-card>
					<v-card
						elevation="0"
						color="#57678426"
						class="t-col-span-1 t-row-span-1 t-border t-border-custom-425F99 t-relative"
					>
						<v-img
							:src="$landing.s6_bg_box"
							class="t-absolute t-bottom-0 t-left-0"
							max-width="100%"
							width="100%"
						></v-img>
						<v-card-text class="t-flex xl:t-flex-row t-flex-col">
							<div class="xl:t-w-2/3 t-flex t-flex-col t-justify-between">
								<h3 class="white--text t-font-black t-text-4xl">
									High Security
								</h3>
								<p class="white--text xl:t-w-4/5 t-mt-4">
									We protect your assets, personal information with blockchain
									platform and top-level data
								</p>
							</div>
							<div class="xl:t-w-1/3">
								<v-img
									:src="$landing.s6_2"
									contain
									height="180"
									class="t-mx-auto"
								></v-img>
							</div>
						</v-card-text>
					</v-card>
					<v-card
						elevation="0"
						color="#57678426"
						class="t-col-span-1 t-row-span-1 t-border t-border-custom-425F99 t-relative"
					>
						<v-img
							:src="$landing.s6_bg_box"
							class="t-absolute t-bottom-0 t-left-0"
							max-width="100%"
							width="100%"
						></v-img>
						<v-card-text class="t-flex xl:t-flex-row t-flex-col">
							<div class="xl:t-w-2/3 t-flex t-flex-col t-justify-between">
								<h3 class="white--text t-font-black t-text-4xl">
									Affiliate program
								</h3>
								<p class="white--text xl:t-w-4/5 t-mt-4">
									The best way to create passages. Income from Bitsmex affiliate
									marketing development
								</p>
							</div>
							<div class="xl:t-w-1/3">
								<v-img
									:src="$landing.s6_3"
									contain
									height="180"
									class="t-mx-auto"
								></v-img>
							</div>
						</v-card-text>
					</v-card>
				</div>
			</v-container>
		</section>
		<!-- Section 7 -->
		<section class="t-py-12">
			<v-container
				fluid
				class="xl:t-w-10/12 t-mx-auto t-flex t-flex-col t-items-center t-justify-center"
			>
				<v-card
					elevation="0"
					color="#57678426"
					class="t-border t-border-custom-425F99"
				>
					<v-card-text>
						<p class="t-mb-0">
							Trading and investing in digital options carries a significant
							degree of risk and is not appropriate and/or appropriate for all
							clients. Please ensure that you carefully consider your investment
							objectives, level of experience, and risk appetite before buying
							or selling any digital asset. You should be fully aware and
							understand all the risks involved in trading and investing in
							digital assets, you should not invest funds that you cannot afford
							to lose. You are granted limited, non-exclusive rights to use the
							intellectual property contained in this website for your personal,
							non-commercial, non-transferable use in connection with the
							services provided on the website.
						</p>
					</v-card-text>
				</v-card>
			</v-container>
		</section>
	</div>
</template>

<script>
export default {
	auth: false,
};
</script>

<style lang="scss" scoped>
#introl {
	// background-image: url("~/assets/images/landing/s1.png");
	@apply t-bg-cover t-bg-center t-bg-no-repeat;
	@screen lg {
		@apply t-min-h-screen;
	}
}
.bg {
	background-color: #102131;
	background-image: url("~/assets/images/landing/bg.svg");
	@apply t-bg-cover t-bg-center t-bg-no-repeat;
}
p {
	color: #677897;
	@apply t-text-justify t-text-lg;
}
</style>
