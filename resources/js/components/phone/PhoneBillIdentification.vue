<template>
<div>
    <div class="my-3">
        <div class="row">
            <button style="float: left" @click.prevent="goBack" type="button" class="close">
                <i class="fas fa-arrow-left"></i>
            </button>
            <!-- <h4 class="ml-3">Phone Bill Manager::{{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }} </h4> -->
            <h4 class="ml-3">Phone Bill Manager</h4>
        </div>
    </div>

    <div class="phone-view">
        <div class="row mt-3">
            <div class="col-12 ml-0 pl-0">
                <div class="unicef-card">
                    <div class="phone-card shadow-sm table-padding">
                        <form id="identification-form">
                            <div class="row justify-content-center">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>EXTENSION</th>
                                            <th>NUMBER</th>
                                            <th>ADD NAME (Optional)</th>
                                            <th>DATE</th>
                                            <th style="text-align:center;">DURATION</th>
                                            <th style="text-align:center;">COST (MWK)</th>
                                            <th style="text-align:center;">TYPE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(eachitem) in selectedPhoneBillItem" :key="'user_bill_data' + eachitem.id">
                                            <input hidden name="id" v-if="eachitem != null" :value="eachitem.id" />
                                            <input hidden name="user_id" v-if="eachitem != null" :value="eachitem.user_id" />

                                            <td>{{ eachitem.ext }}</td>
                                            <td>{{ eachitem.phone_number }}</td>
                                            <!-- <td>{{ eachitem.name }}</td> -->
                                            <td>
                                                <CustomInput @user_object="getUserObject" :input-id="eachitem.id" :value="eachitem.name" />
                                            </td>
                                            <td>{{ eachitem.date_time }}</td>
                                            <td style="text-align:center;">{{ eachitem.duration }}</td>
                                            <td style="text-align:center;">{{ eachitem.cost }}</td>
                                            <td>
                                                <div class="col-12">
                                                    <select v-if="eachitem != null" name="call_type" class="custom-select" required @change="onChangeCallType($event, eachitem)">
                                                        <option value="" selected disabled>Select Call Type</option>
                                                        <option v-for="(value, index) in ['Official', 'Personal', 'Unknown']" :key="'call_type_' + index" :value="value" :selected="eachitem.call_type == value">
                                                            {{ value }}
                                                        </option>
                                                    </select>
                                                    <select v-else :name=" 'call_type' + '_' " + index class="custom-select" required @change="onChangeCallType($event, eachitem)">
                                                        <option value="" selected disabled>Select</option>
                                                        <option v-for="(value, index) in ['Official', 'Personal', 'Unknown']" :key="'call_type_' + index" :value="value">
                                                            {{ value }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <hr />
                        <div class="my-4 mb-4">
                            <div class="form-group">
                                <label>How would you like to settle this bill?</label>
                                <div class="row">
                                    <div class="col-4 mb-2">
                                        <div class="form-group">
                                            <select name="billtype" class="custom-select" required @change="onChange($event)">
                                                <option value="" selected disabled>Select</option>
                                                <option value="Debit from Payroll">
                                                    Debit from Payroll
                                                </option>
                                                <option value="UNICEF BANK ACCOUNT">
                                                    UNICEF Bank Account
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="complete-identification-div shadow-sm">
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <div class="col-12 mx-3">
                                        <div class="d-flex justify-content-center align-items-center" style="height: 140px; float: left">
                                            <div class="col-12">

                                                <div v-if="(((defaultTotalSumOfCalls - totalOfficialCost) - allowanceValue) < 0)">
                                                    <h4>
                                                        0.00
                                                    </h4>
                                                </div>
                                                <div v-else>
                                                    <h4>
                                                        {{ ( Math.abs( (( (defaultTotalSumOfCalls - totalOfficialCost)) - allowanceValue ))).toFixed(2) }}
                                                    </h4>
                                                </div>
                                                <h6>MWK (Billable Amount, after subsidy of <b> MWK {{numberWithCommas(allowanceValue)}}</b>)</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6" v-if="selectedPhoneBillItem.status === 'Reviewed' ">
                                    <div class="d-flex justify-content-center align-items-center mx-4" style="height: 140px; float: right">
                                        <div class="col-12 mt-3 text-center">
                                            <button :disabled="true" type="button" class="unicef-btn unicef-primary" @click="completeIdentification">
                                                CALL LOGS ALREADY IDENTIFIED
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6" v-else>
                                    <div class="d-flex justify-content-center align-items-center mx-4" style="height: 140px; float: right">
                                        <div class="col-12 mt-3 text-center">
                                            <button :disabled="isProcessing" type="button" class="unicef-btn unicef-primary" @click="completeIdentification">
                                                <span><i v-if="isProcessing" id="sendlog-spinner-spinner" class="fa fa-spinner fa-spin"></i></span>
                                                COMPLETE IDENTIFICATION
                                            </button>
                                        </div>
                                    </div>
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
import Stepper from "bs-stepper";
import Swal from "sweetalert2";
import moment from "moment";
import dragAndDropHelper from "../mixin/dragAndDropHelper";
import Izitoast from "../mixin/IziToast";
import Tooltip from "../common/Tooltip.vue";
import MapLocation from "../common/MapLocation";
import CustomInput from "../common/CustomInput.vue";
import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import axios from "axios";
import ModuleAuth from "../mixin/ModuleAuth";
import ProgressBar from "vue-simple-progress";
export default {
    name: "PhoneBillIdentification",
    props: {
        phonebillid: {
            type: String,
            required: true
        }
    },
    components: {
        Tooltip,
        MapLocation,
        datePicker,
        ProgressBar,
        CustomInput
    },
    mixins: [dragAndDropHelper, Izitoast, ModuleAuth],
    data() {
        return {
            usercalldata: {
                id: 0,
                user_id: 0,
                bill_owner_id: 0,
                date_time: "",
                duration: "",
                ext: "",
                phone_number: "",
                is_call_type_accepted: "",
                status: "",
                type: "",
                call_type: "",
                name: "",
                cost: 0
            },
            callTypeArray: [],
            userNameArry: [],
            fullUserCallArray: [],
            filteredEmittedData: null,
            uniqueUsersByName: null,
            uniqueUsersByCallType: null,
            allIdentifiedCalls: [],
            userDataID: null,
            staffExtensions: [],
            bill_type: "",
            call_type: "",
            name: "",
            totalPersonalCost: 0.0,
            totalOfficialCost: 0.0,
            defaultTotalSumOfCalls: 0.0,
            totalUnknown: 0.0,
            totalPersonalAndUnknwon: 0.0,
            totalCallsIdentified: 0,
            totalCallsToBeIdentified: 0,
            personalCallsCount: 0,
            officialCallsCount: 0,
            remainingValue: 0.0,
            billableAmount: 0.0,
            allowanceValue: 0.0,
            cache: {}, //use in the edit name
            record: {}, //use in the edit name
            selectedPhoneBillItem: [],
            selectedSinglePhoneBill: null,
            otherUserAllowances: [],
            isLoadingPhoneBillData: false,
            isProcessing: false,
            isProcessingEdit: false
        };
    },
    mounted() {
        let app = this;
        app.getOtherAllowance();
        app.getPhoneBillDetails(parseInt(app.$props.phonebillid));
        console.log("RECEIVED PHONE BILL ID", parseInt(app.$props.phonebillid));
    },
    computed: {},
    methods: {
        checkIfNumberMatches() {
            return (phonenumber) => {
                let selectedPhone = this.selectedPhoneBillItem;
                selectedPhone = selectedPhone.filter((v, x) => {
                    if (phonenumber === (v.phone_number)) {
                        console.log("NAME RETURN", v.name);
                        return v.name;
                    }
                });

                if (!selectedPhone.length) {
                    return "";
                } else {
                    return selectedPhone[0]["name"];
                }
            }
        },

        numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
        sort: function (arr) {
            // Set slice() to avoid to generate an infinite loop!
            return arr.slice().sort(function (a, b) {
                return a.id - b.id;
            });
        },
        getUserObject(e) {
            var result = this.selectedPhoneBillItem.find(x => x.id == e.id);
            var findName = this.selectedPhoneBillItem.filter(x => x.phone_number == e.phone);

            this.checkIfNumberMatches(e.phone_number);

            if (result != null && result != undefined) {
                console.log("RESULT FOUND: ", result);
                result.name = e.name;
                if (result.phone_number === e.phone) {
                    result.name = e.name;
                }
                const index = this.selectedPhoneBillItem.findIndex(x => x.id === result.id);
                this.selectedPhoneBillItem.splice(index, 1, result);
                console.log("RESULT FOUND AFTER NAME: ", result);

            } else {
                //if it doesnt exist, push to array
                console.log("RESULT NOT FOUND: ", result);
                this.selectedPhoneBillItem.push(e);
            }
            console.log("FULL ARRAY: ", this.selectedPhoneBillItem);
        },

        onChange(event) {
            this.bill_type = event.target.value;
            console.log(this.bill_type);
        },
        onChangeCallType(event, item) {
            this.usercalldata = {
                id: item.id,
                call_type: event.target.value,
                name: "",
                cost: item.cost,
                phone: item.phone_number
            };
            var result = this.selectedPhoneBillItem.find(
                x => x.id === this.usercalldata.id
            );
            console.log("RESULT SEARCH: ", result);
            if (result != null && result != undefined) {
                console.log("RESULT FOUND: ", result);
                result.cost = this.usercalldata.cost;
                result.call_type = this.usercalldata.call_type;
                const index = this.selectedPhoneBillItem.findIndex(x => x.id === result.id);
                this.selectedPhoneBillItem.splice(index, 1, result);
                console.log("RESULT FOUND AFTER CALL TYPE AND COST: ", result);
            } else {
                //if it doesnt exist, push to array
                console.log("RESULT NOT FOUND: ", result);
                this.selectedPhoneBillItem.push(this.usercalldata);
            }
            console.log("FULL USER ARRAY ON CALL TYPE: ", this.selectedPhoneBillItem);
            var selectedType = this.selectedPhoneBillItem.filter(
                x => x.call_type == "Personal"
            );
            console.log("TOTAL PERSONALS", selectedType);
            var selectUnkownType = this.selectedPhoneBillItem.filter(
                x => x.call_type == "Unknown"
            );
            console.log("TOTAL UNKNOWNS", selectUnkownType);
            var selectedOfficialType = this.selectedPhoneBillItem.filter(
                x => x.call_type == "Official"
            );
            console.log("TOTAL OFFICIALS", selectedOfficialType);
            var sum = selectedType.reduce(function (total, num) {
                return total + parseFloat(num.cost);
            }, 0);
            console.log("TOTAL SUM PERSONAL", sum);
            var sumUnkown = selectUnkownType.reduce(function (total, num) {
                return total + parseFloat(num.cost);
            }, 0);
            var sumofficial = selectedOfficialType.reduce(function (total, num) {
                return total + parseFloat(num.cost);
            }, 0);
            console.log("TOTAL SUM OFFICIAL", sumofficial);
            console.log("TOTAL SUM UNKNOWNS", sumUnkown);
            this.personalCallsCount = selectedType.length + selectUnkownType.length;
            this.officialCallsCount = selectedOfficialType.length;
            this.totalPersonalCost = sum + sumUnkown;
            this.totalOfficialCost = sumofficial;
            this.remainingValue = Math.abs(
                (this.allowanceValue - (sum + sumUnkown)).toFixed(2)
            );
            this.totalCallsIdentified =
                selectedType.length +
                selectUnkownType.length +
                selectedOfficialType.length;
            this.totalCallsToBeIdentified =
                this.selectedPhoneBillItem.length - this.totalCallsIdentified;
            console.log("SUM OF ALL PERASONAL", this.totalPersonalCost);
            console.log("TOTAL PERSONAL CALL COUNT", this.personalCallsCount);
            console.log("REMAINING VALUE", this.remainingValue);
            console.log("TOTAL OFFICIAL CALL", this.officialCallsCount);
            console.log("TOTAL CALLS IDENTIFIED", this.totalCallsIdentified);
            console.log(
                "TOTAL CALLS TO BE IDENTIFIED",
                this.totalCallsToBeIdentified
            );
        },
        completeIdentification() {
            let app = this;
            let form = $("#identification-form");
            console.log("FORM: ", form);
            if (form.valid()) {
                //========Validation
                // if (this.selectedPhoneBillItem.length === 0) {
                //     this.showErrorMessage("No Data Found For Your Account");
                //     return;
                // }
                // this.selectedPhoneBillItem.filter(x => {
                //     if (x.call_type === "") {
                //         this.showErrorMessage("All Calls Should Be Identified");
                //         return;
                //     }
                // });
                console.log("DATA: ", ((this.defaultTotalSumOfCalls - this.totalOfficialCost) - this.allowanceValue).toFixed(2) > 0);
                console.log("BILL DATA", ((this.defaultTotalSumOfCalls - this.totalOfficialCost) - this.allowanceValue).toFixed(2));
                if (((app.defaultTotalSumOfCalls - app.totalOfficialCost) - app.allowanceValue) < 0) {
                    app.billableAmount = 0.00;
                } else {
                    app.billableAmount = ((app.defaultTotalSumOfCalls - app.totalOfficialCost) - app.allowanceValue).toFixed(2);
                }
                console.log("Billable Amount: ", app.billableAmount);
                if (this.bill_type === "") {
                    this.showErrorMessage("How To Settle a Bill Is Required");
                    return;
                }
                if (((this.defaultTotalSumOfCalls - this.totalOfficialCost) - this.allowanceValue).toFixed(2) > 0 && this.bill_type === "Debit from Payroll") {
                    this.debitFromPayRoll();
                } else if (
                    ((this.defaultTotalSumOfCalls - this.totalOfficialCost) - this.allowanceValue).toFixed(2) > 0 && this.bill_type === "UNICEF BANK ACCOUNT"
                ) {
                    this.debitFromBank();
                } else if (
                    ((this.defaultTotalSumOfCalls - this.totalOfficialCost) - this.allowanceValue).toFixed(2) === 0
                ) {
                    this.identificationComplete();
                } else {
                    this.identificationComplete();
                }
            }
        },
        debitFromPayRoll() {
            Swal.fire({
                title: "MWK " + this.billableAmount,
                html: "<p style='font-size: 14px;'>To be settled via payroll.<br/>This call log will be shared with Administration prior to submission<br/> to BSC for final reconcilliation, after which, you may make payment.</p>",
                imageUrl: "/images/icons/icon.attention.4.png",
                imageWidth: 100,
                imageHeight: 100,
                imageAlt: "Custom image",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: "#32CD32",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Proceed"
            }).then(result => {
                if (result.isConfirmed) {
                    this.completeIdentificationPhoneBill();
                } else if (result.isDismissed) {}
            });
        },
        debitFromBank() {
            Swal.fire({
                title: "MWK " + this.billableAmount,
                html: "<p style='font-size: 14px;'>To be settled via Bank Remittance. Send to:<br/> Bank: <b>Eco Bank Malawi</b>, Branch: <b>Hilton</b>, Acc: <b>9021998921</b> Cur: <b>MWK.</b><br/>This calllog will now be shared with Administration prior to submission to BSC for final reconcilliation, after which, you may make payment.</p>",
                imageUrl: "/images/icons/icon.attention.4.png",
                imageWidth: 100,
                imageHeight: 100,
                imageAlt: "Custom image",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: "#32CD32",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Proceed"
            }).then(result => {
                if (result.isConfirmed) {
                    this.completeIdentificationPhoneBill();
                } else if (result.isDismissed) {}
            });
        },
        identificationComplete() {
            Swal.fire({
                html: "<p class='font-size: 14px'>Identification Complete</p>",
                imageUrl: "/images/icons/icon.attention.4.png",
                imageWidth: 100,
                imageHeight: 100,
                imageAlt: "Custom image",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: "#32CD32",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Proceed"
            }).then(result => {
                if (result.isConfirmed) {
                    this.completeIdentificationPhoneBill();
                }
            });
        },
        edit(record) {
            this.record = _.cloneDeep(record);
            this.cache = record;
            $("#modal-edit-bill-data").modal("show");
            //console.log("REDCORD", record);
        },
        savePhoneBillExtentions(item) {
            let app = this;
            if (this.call_type == null) {
                app.calldata.call_type = item.call_type;
            } else {
                app.calldata.call_type = this.call_type;
            }
            app.calldata.id = item.id;
            app.calldata.name = item.name;
            // form.append('id', item.id);
            // form.append('name', item.name);
            // form.append('call_type', item.call_type);
            console.log("DATA", this.calldata);
            //$("#phone-bill-form");
            let formModal = $("#modal-edit-bill-data");
            app.isProcessingEdit = true;
            $.ajax({
                url: "/api/phonebilling/update-phone-bill-extensions",
                type: "post",
                data: this.calldata,
                success(data) {
                    app.isProcessingEdit = false;
                    formModal.modal("hide");
                    app.getPhoneBillDetails(parseInt(app.$props.phonebillid));
                    app.$refs.phoneBillRef.reset();
                    app.showSuccessMessage("Phonebill Data Saved.");
                },
                error(xhr, t, e) {
                    app.isProcessing = false;
                    formModal.modal("hide");
                    app.showErrorMessage(e);
                }
            });
        },
        editPhoneBill(item) {
            let app = this;
            app.selectedSinglePhoneBill = item;
            $("#modal-edit-bill-data").modal("show");
            console.log("SINGLE SELECTED DATA: ", app.selectedSinglePhoneBill);
        },
        goBack() {
            window.history.back();
        },
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
                        console.log("OTHER ALLOWANCE:", app.otherUserAllowances);
                        this.otherUserAllowances.forEach((value, index) => {
                            if (value.name == "UNICEF Subsidy on Staff Private Calls") {
                                this.allowanceValue = value.allowance_mwk;
                                //console.log("ALLOWANCE", this.allowanceValue);
                            }
                        });
                    })
                    .catch(error => {
                        console.log("ERRRR:: ", error.response.data);
                    });
            });
        },
        getPhoneBillDetails(id) {
            let app = this;
            app.isLoadingPhoneBillData = true;
            axios
                .get("/api/phonebilling/phone-bill-details/" + id)
                .then(response => {
                    app.selectedPhoneBillItem = response.data;
                    app.isLoadingPhoneBillData = false;
                    console.log("USER PHONE BILL DATA RESPONSE", this.selectedPhoneBillItem);
                    app.selectedPhoneBillItem.forEach((value, index) => {
                        this.userDataID = value.user_data_id;
                    });
                    this.defaultTotalSumOfCalls = app.selectedPhoneBillItem.reduce(
                        function (total, num) {
                            return total + parseFloat(num.cost);
                        },
                        0
                    );
                    console.log("USER ID", this.userDataID);
                })
                .catch(error => {
                    app.isLoadingPhoneBillData = false;
                    console.log("ERRRR:: ", error.response.data);
                });
        },
        floatToInt(value) {
            return value | 0;
        },
        completeIdentificationPhoneBill() {
            let app = this;
            app.isProcessing = true;
            let formData = new FormData();
            formData.append("personal_call_count", this.personalCallsCount);
            formData.append(
                "identified_amount",
                ((this.billableAmount))
            );
            formData.append("bill_type", this.bill_type);
            formData.append(
                "all_identified_calls",
                JSON.stringify(this.selectedPhoneBillItem)
            );
            formData.append("official_call_count", this.officialCallsCount);
            formData.append("total_calls_count", this.totalCallsIdentified);
            formData.append("user_data_id", this.userDataID);
            formData.append("allowance_amount", this.allowanceValue);
            axios({
                    method: "post",
                    url: "/api/phonebilling/complete-call-log-identification",
                    data: formData
                })
                .then(response => {
                    this.isProcessing = false;
                    //======dismiss the model
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        html: "<p class='font-size: 13px'>Identification Completed</p>",
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        showCloseButton: true,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#32CD32"
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = "/home";
                        }
                    });
                })
                .catch(error => {
                    this.isProcessing = false;
                    console.log("ERRRR:: ", error.response.data);
                    this.errors = error.response.data.errors;
                    console.log("ERRRR:: ", error.response.data.errors);
                });
        },
        updatePhoneBill() {
            let app = this;
            app.isProcessing = true;
            console.log("Update Tapped tapped");
            let formData = new FormData();
            formData.append("whole_data", JSON.stringify(this.selectedPhoneBillItem));
            formData.append("personal_call_count", this.personalCallsCount);
            formData.append(
                "remaining_value",
                Math.abs((this.allowanceValue - this.totalPersonalCost).toFixed(2))
            );
            formData.append("bill_type", this.bill_type);
            formData.append("staff_extensions", JSON.stringify(app.staffExtensions));
            axios({
                    method: "post",
                    url: "/api/phonebilling/update-phone-bill",
                    data: formData
                })
                .then(response => {
                    this.isProcessing = false;
                    //======dismiss the model
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        html: "<p class='font-size: 13px'>Identification Completed</p>",
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        showCloseButton: true,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#32CD32"
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = "/home";
                        }
                    });
                })
                .catch(error => {
                    this.isProcessing = false;
                    console.log("ERRRR:: ", error.response.data);
                    this.errors = error.response.data.errors;
                    console.log("ERRRR:: ", error.response.data.errors);
                });
        },

        getCurrentUserPhoneBill() {
            let app = this;
            app.isHidden = false;
            axios
                .get("/api/phonebilling/list-current-phone-bill")
                .then(response => {
                    app.userphonebill = response.data;
                    console.log("CURRENT USER REQUEST BILL", app.userphonebill);
                })
                .catch(error => {
                    console.log("ERRRR:: ", error.response.data);
                });
        },
        format_date(value) {
            if (value) {
                return moment(String(value)).format("DD/MMM/YYYY hh:mm");
            }
        },
        format_date_table(value) {
            if (value) {
                return moment(String(value)).format("DD/MMM/YY");
            }
        }
    }
};
</script>

<style scoped>
.complete-identification-div {
    background-color: #d3d3d3;
    min-height: 110px;
}

.phone-view,
.phone-card {
    background: #fff;
}

.phone-view {
    margin-bottom: 20px;
}

.phone-view .table-padding {
    padding: 25px 35px;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.call-type {
    text-align: center;
}
</style>
