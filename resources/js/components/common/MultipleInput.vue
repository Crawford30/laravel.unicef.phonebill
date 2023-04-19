<template>
    <div>
        <input v-model="values[key]" v-for="(item, key) in inputsCount" :key="key" 
            type="text" class="form-control mb-2" :name="inputName + '[]'" :placeholder="placeholder" autocomplete="off">
        <button @click="addMore"  type="button" class="btn btn-success text-white add-more-btn addMore"> 
            <span class="fa fa-plus text-blue"></span>
        </button>
    </div>
</template>
<script>

    export default {
        name: "multiple-input",
        components:{},
        props:{
            inputName:{
                type: String,
                required:true,
            },
            placeholder:{
                type: String,
                required:false
            },
            inputsValues:{
                type: Array,
                required: false
            }
        },
        data() {
            return {
                inputsCount: 1,
                values: [],
            }
        },
        mounted() {
            if (this.inputsValues != null) {
                this.inputsCount = this.inputsValues.length;
                if (this.inputsCount != 0 ) {
                    this.values = this.inputsValues;
                } else {
                    this.inputsCount = 1;
                }
            }
        },
        methods: {
            addMore(){
                this.inputsCount += 1;
            }
        },
        watch: {
            inputsValues: function(newVal, oldVal) {
                if (newVal != null) {
                    this.inputsCount = newVal.length;
                    if (this.inputsCount != 0 ) {
                        this.values = newVal;
                    } else {
                        this.inputsCount = 1;
                    }
                }
            }
        }
    }
</script>

