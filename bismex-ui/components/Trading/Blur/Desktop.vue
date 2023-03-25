<template>
	<div class="t-flex t-justify-center t-items-center">
		<div class="t-flex t-justify-center t-items-center" style="width: 80%">
			<div
				class="
          t-grid t-grid-cols-5 t-gap-16 t-justify-evenly t-px-12
          2xl:t-py-2.5
          t-py-3
        "
			>
				<div
					class="t-grid t-grid-cols-5 t-gap-6"
					v-for="(item, index) in blurData"
					:key="index"
				>
					<div
						class="t-grid t-grid-rows-4 t-gap-2"
						v-for="(i, k) in item"
						:key="k"
					>
						<div
							class="
                2xl:t-h-5 2xl:t-w-5
                t-h-5 t-w-5 t-rounded-full t-mx-auto t-row-span-1
              "
							:class="color(r)"
							v-for="(r, t) in i"
							:key="t"
						></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		blurData: {
			type: Array,
			default: () => [
				[Array(4), Array(4), Array(4), Array(4), Array(4)],
				[Array(4), Array(4), Array(4), Array(4), Array(4)],
				[Array(4), Array(4), Array(4), Array(4), Array(4)],
			],
		},
	},
	methods: {
		color(data) {
			if (data === "CALL") return "t-bg-[#03A593]";
			else if (data === "PUT") return "t-bg-[#CF304A]";
			else return "t-bg-[#222F38]";
		},
	},
};
</script>

<style></style>
