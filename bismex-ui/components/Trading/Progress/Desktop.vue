<template>
	<div :class="classOrientation" :style="!this.$store.state.settings.orientation?'max-width: 20px;':''">
		<span class="t-inline-block t-text-custom-2BB99F">{{progress}}%</span>
		<div class="t-flex-grow">
			<div
				class="t-h-full t-relative t-rounded-full t-w-2 t-transition-all t-duration-500 t-bg-custom-CF304A"
			>
				<div
					class="t-absolute t-top-0 t-left-0 t-w-2 t-bg-custom-03A593 t-transition-all t-duration-500"
					:style="{height: progress + '%'}"
				></div>
				<div
					class="t-absolute t-w-3 t-h-3 t-transform t--translate-x-1/2 t--translate-y-1/2 t-left-1/2 t-transition-all t-duration-500"
					:style="{'top': progress + '%'}"
				>
					<div class="t-relative t-w-3 t-h-3">
						<span
							class="t-absolute t-animate-ping t-inline-flex t-h-3 t-w-3 t-rounded-full t-opacity-20 t-top-1/2"
							:class="color"
						></span>
						<span class="t-relative t-inline-flex t-rounded-full t-h-3 t-w-3 t-bg-white"></span>
					</div>
				</div>
			</div>
		</div>
		<span class="t-inline-block t-text-custom-CF304A">{{100 - progress}}%</span>
	</div>
</template>

<script>
	export default {
		props: {
			progress: {
				type: Number,
				default: 50,
			},
		},
		computed: {
			color() {
				if (this.progress <= 50) return "t-bg-custom-2BB99F";
				else return "t-bg-custom-CF304A";
			},
      classOrientation(){
        if(this.$store.state.settings.orientation){
          return "t-h-full t-flex t-flex-col t-items-center"
        } else{
          return "t-flex-grow t-flex t-flex-col t-items-center"
        }
      }
		},
	};
</script>
