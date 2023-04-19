<template>
<div>
    <div id="receipt-form">
        <div class="unicef-hact-container">
            <div class="hact-header">
                <div class="row" style="mb-3">
                    <div class="col-6" style="float: left">
                        <img src="/images/unicef.logo.blue.png" alt="" />
                    </div>
                    <div class="col-6" style="text-align: right">
                        <h4 class="pr-2">
                            <b>Invoice</b>
                        </h4>
                        <p class="mb-0 pb-0">
                            <b>Reference #: </b> {{ refNumber() }}-001
                        </p>
                    </div>
                </div>
            </div>

            <div class="hact-content">
                <div class="hact-content-data">
                    <table class="table mx-0 mb-3 mt-3">
                        <tbody>
                            <tr>
                                <td colspan="3">
                                    <h6>From</h6>
                                    <h6>
                                        <b>UNICEF MALAWI</b>
                                    </h6>
                                    <h6>P.O.Box 30375 Lilongwe 3</h6>
                                    <h6>Phone: +265 1770778</h6>
                                    <h6>Email: lilongwe@unicef.org</h6>
                                </td>
                                <td colspan="3">
                                    <div v-for="userdetail in userdetails" :key="'user_id_' + userdetail.id">
                                        <div style="text-align: right">
                                            <h6>To</h6>
                                            <h6>
                                                <b> {{ userdetail.name }}</b>
                                            </h6>
                                            <h6>P.O.Box 30375 Lilongwe 3</h6>
                                            <!-- <h6>Phone: +265 1770778</h6> -->
                                            <h6>Email: {{ userdetail.email }}</h6>
                                        </div>
                                    </div>
                                    <br />
                                    <div style="text-align: right">
                                        <h6>
                                            <b>Invoice Date: </b>{{ nowDate() }}</h6>
                                        <h6>
                                            <b>Invoice Status: </b>
                                            <div v-if="selectedcall.invoice_status === 'NotSettled' ||  selectedcall.current_status.call_log_with === 'Reviewed' || selectedcall.current_status.call_log_with === 'With ADMIN' ||  selectedcall.current_status.call_log_with === 'Payment Made' || selectedcall.current_status.call_log_with === 'Payment Evidence Sent' ">
                                                <button style="
                              background-color: #ffbf00;
                              padding: 2px;
                              color: #fff;
                            ">
                                                    Pending {{selectedcall.status}}
                                                </button>
                                            </div>
                                            <div v-else>
                                                <button style="background-color: #009d0c; color: #fff">
                                                    Settled
                                                </button>
                                            </div>
                                        </h6>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-striped mx-0 mb-3 mt-4">
                        <thead>
                            <tr>
                                <td>Extension</td>
                                <td>Number</td>
                                <td>Date</td>
                                <td>Duration</td>
                                <td>Cost(MWK)</td>
                                <td>Type</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="billdetail in billdetails" :key="'call_id' + billdetail.id">
                                <td>{{ billdetail.ext }}</td>
                                <td>{{ billdetail.phone_number }}</td>
                                <td>{{ billdetail.date_time }}</td>
                                <td>{{ billdetail.duration }}</td>
                                <td>{{ billdetail.cost }}</td>
                                <td>{{ billdetail.call_type }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-12 mx-auto mt-5">

                    <div style="text-align: right">
                        <h6>
                            <b>Sub-Total: </b>MWK {{ selectedcall.reviewed_amount }}</h6>
                        <h6>
                            <b>Discount: </b>USD {{ Math.abs(parseFloat(allowanceValue / parseFloat(usdtomwk) )).toFixed(4)}} (MWK {{ Intl.NumberFormat('en-US').format(allowanceValue ) }})</h6>
                        <h2>
                            <div v-if="( (selectedcall.reviewed_amount - allowanceValue)) < 0">
                                <b>MWK 0</b>
                            </div>
                            <div v-else>
                                <b>MWK {{ Intl.NumberFormat('en-US').format( Math.abs( parseFloat(selectedcall.reviewed_amount - allowanceValue)))   }}</b>
                            </div>
                        </h2>
                    </div>
                </div>
            </div>

            <hr style="border-color: #ddd; margin: 2.5rem 3.5rem" />
            <p class="text-center" style="font-style: italic">
                Invoice generated by UNICEF Project Operations Platform on {{ now() }}
            </p>
        </div>
    </div>
</div>
</template>

<script>
import moment from "moment";
import Helpers from "./mixin/helpers";
import ModuleAuth from "./mixin/ModuleAuth";

export default {
    name: "PaymentInvoiceForm",
    mixins: [Helpers, ModuleAuth],
    props: {
        selectedcall: {
            type: Object,
            required: true,
        },

        billdetails: {
            type: Array,
            required: true,
        },
        billOwnerId: {
            type: Number,
            required: false
        },

        phonebillDataId: {
            type: Number,
            required: false
        },
        usdtomwk: {
            type: Number,
            required: true
        },
        allowanceValue: {
            type: Number,
            required: true
        }

    },
    data() {
        return {
            usdtomwk: 0.0,
            allowanceValue: 0.0,
           // billdetails: [],
            otherUserAllowances: [],
            identified_amount: 0,
            reviewed_amount: 0,
            userdetails: null,
            rides: [],
            rates: [],
            districts: [],
            attendees: [],
        };
    },

    mounted() {
        let app = this;

        console.log("USD TO MWK", app.$props.usdtomwk);
        console.log("ALLOWANCE VALUE", app.$props.allowanceValue);

        console.log("USER DATA ID", app.$props.selectedcall.user_data_id);

        console.log("BILL EXTENSIONS PROP", app.$props.billdetails);

        

        // app.getForexRates();
        //app.getOtherAllowance();

        if (app.selectedcall.user_data_id != null && app.selectedcall.bill_owner_id != null) {
           // app.getExtensions(app.selectedcall.user_data_id);
            app.getUserDetails(app.selectedcall.bill_owner_id);
        } else {
            //app.getExtensions(app.phonebillDataId);
            app.getUserDetails(app.billOwnerId);
        }
        // console.log("STAFF EXTENSIONS", app.staffExtensions);
        // console.log("SELECTED CALL: ", app.selectedcall);
        // console.log("BILL OWNER ID: ", app.billOwnerId);

    },

    computed: {
        currency() {
            return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "MWK"
            }).format(this.lowPrice);
        }
    },

    methods: {

        getOtherAllowance() {
            let app = this;
            let bsc_url = $("#bsc-url").val();
            app.authorize(bsc_url, token => {
                axios
                    .get(bsc_url + "/api/settings/others/list", {
                        headers: {
                            Authorization: "Bearer " + token
                        }
                    })
                    .then(response => {
                        app.otherUserAllowances = response.data.results;
                        // console.log("OTHER ALLOWANCE:", app.otherUserAllowances);
                        this.otherUserAllowances.forEach((value, index) => {
                            if (value.name == "UNICEF Subsidy on Staff Private Calls") {
                                this.allowanceValue = value.allowance_mwk;
                                console.log("ALLOWANCE", this.allowanceValue);
                            }
                        });
                    })
                    .catch(error => {
                        console.log("ERRRR:: ", error.response.data);
                    });
            });
        },
        getExtensions(id) {
            let app = this;
            axios
                .get("/api/phonebilling/phone-bill-extensions-details/" + id)
                .then((response) => {
                    app.billdetails = response.data;
                    console.log("BILLING EXTENSIONS: ", app.billdetails);
                })
                .catch((error) => {
                    console.log("ERROR:: ", error.response.data);
                });
        },

        getForexRates() {
            let app = this;
            let platform_url = $("#platform-url").val();
            app.authorize(platform_url, token => {
                axios
                    .get(platform_url + "/api/forex-rates/list", {
                        headers: {
                            Authorization: "Bearer " + token
                        }
                    })
                    .then(response => {
                        app.rates = response.data.results;
                        var valData = Object.values(response.data.results); //convert to array
                        var selectedType = valData.filter(x => x.currency == "MWK");
                        var amount = selectedType.filter((v, x) => {
                            if (v.currency === "MWK") {
                                console.log("AMOUNT USD TO MWK (USD-MWK)", v.rate);
                                this.usdtomwk = v.rate;
                                return v.rate;
                            }
                        });
                    })
                    .catch(error => {
                        console.log("ERRRR:: ", error.response.data);
                    });
            });
        },

        getUserDetails(id) {
            let app = this;
            axios
                .get("/api/phonebilling/user-details/" + id)
                .then((response) => {
                    app.userdetails = response.data;
                    // console.log("USER DETAILS: ", app.userdetails);

                    app.userdetails.array.forEach((element) => {
                        this.reviewed_amount = element.reviewed_amount;
                    });
                })
                .catch((error) => {
                    console.log("ERROR:: ", error.response.data);
                });
        },

        numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        now() {
            return moment().format("D/MMM/YYYY [at] HH:mm");
        },

        nowDate() {
            return moment().format("D/MMM/YYYY");
        },

        refNumber() {
            return moment().format("YYYY/MM").replace(/\//g, "");
        },
        formatNumber(n) {
            return this.numerilize(n, "m", 2);
        },
        timeAgo(t) {
            return moment(t, "YYYY-MM-DD").format("MMMM Do YYYY [at] h:mm a");
        },
        formatDate(date) {
            return moment(date, "YYYY-MM-DD").format("D/MMM/YYYY");
        },
    },
};
</script>

<style scoped>
.table-bordered tr th,
.table-bordered tr td {
    font-size: 12px !important;
}

.unicef-table-header tr th {
    background-color: #f5f6fa;
}

.table-bordered .custom-control-label {
    font-weight: normal !important;
    font-size: 12px !important;
}
</style>
