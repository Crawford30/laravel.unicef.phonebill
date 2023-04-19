<template>
	<div>
		<money :class="[booststrapInput, inputClasses]" v-model="price" v-bind="money"></money> 
		<input hidden type="text" :name="name" :value="price" :id="inputId" required>
	</div>
</template>

<script>
	import { Money } from 'v-money'

	export default {
		name:'input-money',
		components: { Money },
		props:{
			inputName:{
				type: String,
				required: true,
			},
			inputPrice:{
				type: Number,
				required: false
			},
			inputId:{
				type: String,
				required: true
			},
			inputClasses:{
				type: Array,
				required: false
			},
		},
		mounted(){
			let app = this;
			app.name = app.inputName;
			app.price = app.inputPrice;
		},
		data () {
			return {
				name: 'amount',
				price: 0.0,
				booststrapInput:'form-control',
				id: '',
				money: {
					decimal: '.',
					thousands: ',',
					prefix: '',
					suffix: '',
					precision: 0,
					masked: false
				}
			}
		},
		methods:{}
	}
</script> 