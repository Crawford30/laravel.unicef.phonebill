<template>
<div class="w-100 py-3">
     <div class="container-fuild">
        <div class="row my-3">
            <div class="col-md-9">
                <h4>
                    Phone Bill Manager (Administrator)
                </h4>
            </div>
            <div class="col-md-3">
                <div class="btn-group add-new-dropdown" style="width: 100%">
                    <button @click="uploadNewPhoneBill" type="button" class="btn btn-primary">
                        Upload Man3000 Bills
                    </button>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-md-9">
            <h3 style="font-weight: 500; color: #333; font-size: 1.575rem;">Phone Bill Manager (Administrator)</h3>
        </div>
        <div class="col-md-3">
            <button type="button" style="width: 100%;" class="btn btn-primary" @click="uploadNewPhoneBill()">Upload Man3000</button>
        </div>
    </div> -->

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="unicef-card unicef-dashboard-menu p-4">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-sm-12  rounded-sm mainContent ">
                            <div class="row">
                                <div class="col-lg-12 align-self-center text-center py-3">
                                    <div class="card-body">
                                        <img src="/images/icons/icon.attention.4" class="m-auto no-form-image" style="height: 140px;">
                                    </div>
                                    <h3 style="text-align: center; font-size: 18px; color: #333;" class="mt-2">No phonebills found</h3>
                                </div>
                            </div>
                            <hr>
                            <div class="row d-flex">
                                <div class="col-md-6 text-right">
                                    <h5 class="pt-2">Click here to upload Man3000</h5>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary px-4" @click="uploadNewPhoneBill()">Upload Man3000 </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>
</template>

<script>

export default {
    name: "NoPhoneBillAdmin",

    components: {
       
    },

    data() {

        return {

        }

    },

    methods: {
        uploadNewPhoneBill() {
            window.location.href = '/home';
        }

    },

}
</script>

<style scoped>
.user_left {
    float: left;
}

.user_right {
    float: right;
}

.user_tag a {
    display: inline-block;
}

.flex-container {
    display: flex;
}

.flex-child {
    flex: 1;

}

.flex-child:first-child {
    margin-right: 2px;
}
</style>
