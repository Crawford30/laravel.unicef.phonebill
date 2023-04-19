<template>
<label>
    <input :value="value" @input="updateUserName" :data-input-id="inputId" type="text" placeholder="Insert Name" :id="'user_name' + inputId" class="form-control field-label-input-value" :name="'username['+inputId+'][label]'">
</label>
</template>

<script>
export default {
    name: "BaseInput",
    props: {
        label: String,
        value: String,

        inputId: {
            type: Number,
            required: true
        },
        
    },

    data() {
        return {
            username: '',

            userObject: {
                id: 0,
                name: '',
                call_type: '',
                cost: '',
                phone: '',
            }
        };

    },

    methods: {
        onKeyPress(e) {
            if (e.key < "0" || e.key > "9") {
                e.preventDefault()
            }
        },

        emitObject() {
            this.userObject.name = this.value;
            this.userObject.id = this.inputId
            this.$emit('user_object', this.userObject);
        },

        updateUserName(e) {
            this.value = e.target.value;
            console.log("NAME", this.username);
            console.log("ID", this.inputId);

            this.emitObject()

        },
    }
};
</script>
