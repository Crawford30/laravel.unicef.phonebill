<template>
<div>
    <div class="container-fuild">
        <div class="row my-3">
            <div class="col-md-9">
                <h4>Phone Bill Manager</h4>
            </div>
        </div>
    </div>
    <div v-if="userphonebill.length == 0">

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="unicef-card unicef-dashboard-menu p-4">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12  rounded-sm mainContent ">
                                <div class="row">
                                    <div class="col-lg-12 align-self-center text-center py-3">
                                        <div class="card-body">
                                            <img src="/images/icons/icon.no-data.png" class="m-auto no-form-image" style="height: 200px;">
                                        </div>
                                        <h3 style="text-align: center; font-size: 18px; color: #333;" class="mt-2">No phonebill has been found</h3>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 m-0 text-center">
                                        <h5 class="pt-2 text-center">When you have a phonebill, it will appear here</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else>

        <div class="phone-view">
            <!-- <div class="container"> -->
            <div class="row mt-3">
                <div class="col-12 ml-0 pl-0">
                    <div class="unicef-card">
                        <div class="phone-card shadow-sm table-padding">
                            <div class="row justify-content-center">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>CALL LOGS</th>
                                            <th style="text-align:center;">TOTAL RECORDS</th>
                                            <th style="text-align:center;">UNIQUE MOBILE NUMBERS</th>
                                            <th>IDENTIFIED</th>
                                            <th style="text-align:center;">COST (MWK)</th>
                                            <th style="text-align:center;">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in userphonebill" :key="'user_phone_bill' + index">
                                            <td>

                                                <div v-if="item.current_status != null">
                                                    {{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }}
                                                </div>
                                                <div v-else>
                                                    <a :href="phoneUrl(item)">{{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }}
                                                    </a>
                                                </div>
                                            </td>

                                            <td style="text-align:center;">
                                                <div v-if="item.current_status != null">
                                                    {{ item.extension_count }}
                                                </div>
                                                <div v-else>
                                                    <a :href="phoneUrl(item)">{{ item.extension_count }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="text-align:center;">

                                                <div v-if="item.current_status != null">
                                                    {{ item.unique_mobile_count }}
                                                </div>
                                                <div v-else>
                                                    <a :href="phoneUrl(item)">{{ item.unique_mobile_count }}
                                                    </a>
                                                </div>
                                            </td>

                                            <td>
                                                <div v-if="item.current_status === null">
                                                    <span style="font-size: 12px"> 0 %</span>
                                                    <div class="progressbar">
                                                        <div class="progressbar text-center" style="background-color: #dc720f; margin: 0; color: white;" :style="{width: 100  + '%'}">
                                                            {{ 0 }}
                                                        </div>
                                                        <span style="font-size: 12px">{{ moment(item.created_at).fromNow() }}
                                                        </span>
                                                    </div>

                                                </div>

                                                <div v-else-if="item.current_status.total_count === 0 ">
                                                    <span style="font-size: 12px"> 100 %</span>
                                                    <div class="progressbar">
                                                        <div class="progressbar text-center" style="background-color: green; margin: 0; color: white;" :style="{width: 100  + '%'}">
                                                            {{ 100 }}
                                                        </div>
                                                        <span style="font-size: 12px">{{ moment(item.created_at).fromNow() }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div v-else>
                                                    <span style="font-size: 12px">{{ parseFloat( ( (item.current_status.total_count / item.current_status.total_count) * 100 ).toFixed(2) ) }} %
                                                    </span>
                                                    <div class="progressbar">
                                                        <div class="progressbar text-center" style="background-color: green; margin: 0; color: white;" :style="{width: parseFloat( ( (item.current_status.total_count / item.current_status.total_count) * 100 ).toFixed(2) )  + '%'}">
                                                            {{ item.current_status.total_count }}
                                                        </div>
                                                        <span style="font-size: 12px">{{ moment(item.created_at).fromNow() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td style="text-align:center;">
                                                <div v-if="item.identified_amount === null ">
                                                    <!-- {{ floatToInt(item.total_monthly_cost) }} -->
                                                    {{ (item.total_user_monthly_cost) }}
                                                </div>
                                                <div v-else>
                                                    <!-- {{ floatToInt(item.identified_amount) }} -->
                                                    {{ (item.identified_amount) }}
                                                </div>
                                            </td>

                                            <td style="text-align:center;">

                                                <div v-if="item.current_status === null">
                                                    <span :style="{ color: ' #FF7B40' }">Pending</span>
                                                </div>

                                                <div v-else-if="item.reviewed_amount > 0  && item.current_status.call_log_with == 'Reviewed' && item.bill_type != null  && item.bill_type.bill_type === 'UNICEF BANK ACCOUNT' && (Math.sign((item.reviewed_amount  - allowanceValue)) === 1)">
                                                    <a href="#" @click.prevent="showUploadPayment(item)">Pay MWK {{ Math.abs(item.reviewed_amount  - allowanceValue).toFixed(2)}} </a>
                                                </div>

                                                <div v-else-if="item.reviewed_amount === 0  && item.current_status.call_log_with == 'Reviewed' && item.bill_type != null  && item.bill_type.bill_type === 'UNICEF BANK ACCOUNT' && (Math.sign((item.reviewed_amount  - allowanceValue)) === -1)">
                                                    <a href="#" @click.prevent="showUploadPayment(item)">Pay MWK 0</a>
                                                </div>

                                                <div v-else-if="item.reviewed_amount > 0 && item.current_status.call_log_with !== 'Reconciled'  && docOne != null && docTwo != null && item.bill_type != null  && item.bill_type.bill_type == 'UNICEF BANK ACCOUNT'">
                                                    <span :style="{ color: ' #0CA357' }">Payment Evidence Sent </span>
                                                </div>

                                                <div v-else-if="item.reviewed_amount > 0 && item.current_status.call_log_with == 'Reconciled' && docOne != null  && item.bill_type != null  && item.bill_type.bill_type == 'UNICEF BANK ACCOUNT'">
                                                    <span :style="{ color: ' #0CA357' }">Reconciled</span>
                                                </div>

                                                <div v-else>
                                                    <span :style="{ color: ' #0CA357' }">{{ getCallLogStatus(item.current_status)["with"] }}
                                                    </span>
                                                </div>

                                                <div v-if="item.current_status != null &&  item.current_status.call_log_with !=  'With ADMIN' " @click.prevent="downloadReceipt(item); showDownloadPayment();" class="pdf-image-icon">
                                                    <img width="30" height="30" src="/images/icons/icon.pdf.png" alt="" />
                                                </div>
                                                <!-- <div v-if="item.current_status === null">
                                                    <span :style="{ color: ' #FF7B40' }">Pending</span>
                                                </div>
                                                <div v-else-if="item.reviewed_amount > 0  && item.current_status.call_log_with == 'Reviewed' && item.bill_type != null  && item.bill_type.bill_type === 'UNICEF BANK ACCOUNT' ">
                                                    <a href="#" @click.prevent="showUploadPayment(item)">Pay MWK {{ item.reviewed_amount }} </a>
                                                </div>
                                                <div v-else-if="item.reviewed_amount > 0  && docOne != null && docTwo != null && item.bill_type != null  && item.bill_type.bill_type == 'UNICEF BANK ACCOUNT'">
                                                    <span :style="{ color: ' #0CA357' }">Payment Evidence Sent </span>
                                                </div>

                                                  <div v-else-if="item.reviewed_amount > 0 && item.current_status.call_log_with == 'Reconciled' && docOne != null && docTwo != null && item.bill_type != null  && item.bill_type.bill_type == 'UNICEF BANK ACCOUNT'">
                                                        <span :style="{ color: ' #0CA357' }">Reconciled</span>
                                                    </div>
                                                <div v-else>
                                                    <span :style="{ color: ' #0CA357' }">{{ getCallLogStatus(item.current_status)["with"] }}
                                                    </span>
                                                </div>
                                                <div v-if="item.current_status != null" @click.prevent="downloadReceipt(item); showDownloadPayment();" class="pdf-image-icon">
                                                    <img width="30" height="30" src="/images/icons/icon.pdf.png" alt="" />
                                                </div> -->

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="modal fade" id="modal-upload-payment">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal header -->
                        <div class="modal-header">
                            <div class="modal-title col-12">
                                <button type="button" class="close" data-dismiss="modal" aria-label="close" style="">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body py-4">
                            <!-- <button type="button" data-dismiss="modal" style="position: absolute; right: 1.5rem; top: 1.5rem" class="close" @click="closeDialog">
                        &times;
                    </button> -->
                            <h5 style="text-align: center; font-weight: bold">
                                Upload Payment Evidence
                            </h5>
                            <br />

                            <div class="col-md-12">
                                <div class="form-group">
                                    <DocumentUpload />
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <button :disabled="documentFile === null || isProcessing" @click="uploadPaymentEvidence" type="button" class="unicef-btn unicef-primary">
                                    <span>
                                        <i v-if="isProcessing" class="fa fa-spinner fa-spin"> </i>
                                        COMPLETE</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Payment Two-->
            <div class="modal fade" id="modal-upload-payment-two">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal header -->
                        <div class="modal-header">
                            <div class="modal-title col-12">
                                <button type="button" class="close" data-dismiss="modal" aria-label="close" style="">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body py-4">
                            <!-- <button type="button" style="position: absolute; right: 1.5rem; top: 1.5rem" class="close" @click="closeDialog">
                        &times;
                    </button> -->
                            <h5 style="text-align: center; font-weight: bold">
                                Upload Payment Evidence
                            </h5>
                            <br />
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h6>
                                        Payment Evidence uploaded {{ moment(updated_at).fromNow() }}. Upload an alternative below.
                                    </h6>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <DocumentUpload />
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <button :disabled="documentFile === null || isProcessing" @click="uploadFileTwo" type="button" class="unicef-btn unicef-primary">
                                    <span>
                                        <i v-if="isProcessing" class="fa fa-spinner fa-spin"> </i>
                                        COMPLETE</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- </div> -->
            <!-- <div v-if="selectedcall != null && staffExtensions != null" class="d-none">
                <PaymentInvoiceForm :selectedcall="selectedcall" :staffExtensions="staffExtensions" :billOwnerId="parseInt(billownerid)" :usdtomwk="parseFloat(usdtomwk)" :allowanceValue="parseFloat(allowanceValue)"  :phonebillDataId="parseInt(selectedcall.user_data_id)" />
            </div> -->

            <div v-if="selectedcall != null && billdetails != null" class="d-none">
                <PaymentInvoiceForm :selectedcall="selectedcall" :billdetails="billdetails" :usdtomwk="parseFloat(usdtomwk)" :allowanceValue="parseFloat(allowanceValue)" :billOwnerId="parseInt(billownerid)" :phonebillDataId="parseInt(selectedcall.user_data_id)" />
            </div>
        </div>

    </div>

    <div class="modal fade" id="modal-download-payment">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body py-4">
                    <button type="button" style="position: absolute; right: 1.5rem; top: 1.5rem" class="close" @click="closeDialog">
                        &times;
                    </button>
                    <h5 style="text-align: center; font-weight: bold">
                        Download Invoice
                    </h5>
                    <br />

                    <div class="col-md-12">
                        <div class="form-group">
                            <div style="text-align: center">
                                <img style="width: 100px; height: 100px" src="/images/icons/icon.pdf.png" alt="" />
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <button @click="downloadPaymentReceipt" :disabled="otherUserAllowances === null" type="button" class="unicef-btn unicef-primary">
                            <span> DOWNLOAD</span>
                        </button>
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
import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import axios from "axios";
import ModuleAuth from "../mixin/ModuleAuth";
import ProgressBar from "vue-simple-progress";
import PaymentInvoiceForm from "../PaymentInvoiceForm";
import DocumentUpload from "../DocumentUpload";

export default {
    name: "PhoneBillListUser",
    props: ['docOne', 'docTwo', 'billownerid'],

    components: {
        Tooltip,
        MapLocation,
        datePicker,
        ProgressBar,
        PaymentInvoiceForm,
        DocumentUpload
    },
    mixins: [dragAndDropHelper, Izitoast, ModuleAuth],
    data() {
        return {
            usdtomwk: 0.0,
            billdetails: [],
            updated_at: "",
            otherUserAllowances: [],
            allowanceValue: 0.0,
            documentFile: null,
            billownerId: null,
            status: [],
            moment: moment,
            phonebill_id: "",
            isHidden: Boolean,
            verificationDate: null,
            verificationDateAll: null,
            userphonebill: [],
            staffExtensions: [],
            step: 1,
            stepper: null,
            file: null,
            hasFile: false,
            isProcessing: false,
            confirmationData: {},
            importedData: {},
            selectedcall: null,

            dateOptions: {
                format: "YYYY-MM-DD",
                useCurrent: false,
                icons: {
                    time: "far fa-clock",
                    date: "far fa-calendar-alt",
                    up: "fas fa-chevron-up",
                    down: "fas fa-chevron-down",
                    previous: "fas fa-chevron-left",
                    next: "fas fa-chevron-right",
                    today: "fas fa-calendar-check",
                    clear: "far fa-trash-alt",
                    close: "far fa-times-circle"
                }
            }
        };
    },

    mounted() {
        let app = this;
        app.billownerId = app.$props.billownerid;
        app.getOtherAllowance();
        app.verificationDeadline();
        app.getStaffExtensions();
        app.getAllUserPhoneBill();
        app.getForexRates();

        app.getPhoneBillStatus(app.billownerId);

        app.$on("doc-file-uploaded", data => {
            app.documentFile = data;
            console.log("RECIEVED FILE", app.documentFile);
        });
    },

    methods: {
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
                        console.log("otherUserAllowances: ", app.otherUserAllowances);
                        app.otherUserAllowances.forEach((value, index) => {
                            if (value.name == "UNICEF Subsidy on Staff Private Calls") {
                                app.allowanceValue = value.allowance_mwk;
                                //console.log("ALLOWANCE", this.allowanceValue);
                            }
                        });
                    })
                    .catch(error => {
                        console.log("ERRRR:: ", error.response.data);
                    });
            });
        },

        uploadPaymentEvidence() {
            let app = this;
            app.isProcessing = true;
            let data = new FormData();

            console.log("ID: ", app.selectedcall.id);
            console.log("BSC MAIL: ", app.bscEmail);
            console.log("USER DATA ID: ", app.selectedcall.user_data_id);
            console.log("DOC FILE: ", app.documentFile.file);

            data.append("id", app.selectedcall.id);
            data.append("bsc_email", app.bscEmail);
            data.append("user_data_id", app.selectedcall.user_data_id);
            data.append("document_one", app.documentFile.file);
            data.append("allowance_amount", app.allowanceValue);
            data.append("to_date", app.selectedcall.to_date);
            data.append("from_date", app.selectedcall.from_date);
            data.append("reviewed_amount", app.selectedcall.reviewed_amount);
            data.append("personal_count", app.selectedcall.current_status.personal_count);
            data.append("official_count", app.selectedcall.current_status_official_count);

            console.log("FORM DATA ON UPLOAD: ", data);

            axios({
                    method: "post",
                    url: "/api/phonebilling/upload-receipt",
                    data: data,
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(response => {
                    app.isProcessing = false;
                    console.log("RESPONSE: ", response.data);
                    //======dismiss the model
                    app.closeDialog();
                    app.documentFile = null;
                    app.selectedcall = null;

                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        html: "<p class='font-size: 13px'>Payment Evidence Uploaded</p>",
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
                    app.isProcessing = false;
                    console.log("Error:: ", error.response.data);
                });
        },

        downloadReceipt(item) {
            let app = this;
            app.selectedcall = item;
            app.getExtensions(app.selectedcall.user_data_id);
            console.log("SELECTED Call", app.selectedcall);
            console.log("SELECTED Call ID", app.selectedcall.id);
            console.log("SELECTED Bill Owner ID", app.selectedcall.bill_type.bill_owner_id);
        },

        showDownloadPayment() {
            $("#modal-download-payment").modal({
                    backdrop: "static",
                    keyboard: false
                },
                "show"
            );
        },

        uploadFileTwo() {
            let app = this;
            app.isProcessing = true;
            let data = new FormData();
            data.append("id", app.selectedcall.id);
            data.append("user_data_id", app.selectedcall.user_data_id);
            data.append("bill_owner_id", app.selectedcall.bill_type.bill_owner_id);
            data.append("bsc_email", app.bscEmail);
            data.append("document_two", app.documentFile.file);
            data.append("allowance_amount", app.allowanceValue);

            axios({
                    method: "post",
                    url: "/api/phonebilling/upload-receipt-two",
                    data: data,
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(response => {
                    app.isProcessing = false;
                    //======dismiss the model
                    app.closeDialog();
                    app.documentFile = null;
                    app.selectedcall = null;

                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        html: "<p class='font-size: 13px'>Payment Evidence Uploaded</p>",
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
                    app.isProcessing = false;
                    console.log("Error:: ", error.response.data);
                });
        },

        downloadPaymentReceipt() {
            let app = this;
            // $("#modal-download-payment").modal("hide");
            var element = document.getElementById("receipt-form");
            Swal.fire({
                title: "Downloading...",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });
            var options = {
                margin: 0.5,
                filename: "payment_receipt.pdf",
                pagebreak: {
                    avoid: "tr"
                },
                image: {
                    type: "jpeg",
                    quality: 1.0
                },
                html2canvas: {
                    scale: 6
                },
                jsPDF: {
                    unit: "in",
                    format: "a4",
                    orientation: "landscape",
                    compress: true
                }
            };
            html2pdf()
                .from(element)
                .set(options)
                .save()
                .then(() => {
                    Swal.close();
                });

                app.closeDialog();
        },

        getPhoneBillStatus(id) {
            let app = this;
            app.isHidden = true;
            axios
                .get("/api/phonebilling/phone-bill-status/" + id)
                .then(response => {
                    // app.userphonebill = response.data;
                    console.log("Phone Bill Status", response.data);
                    app.status = response.data;
                })
                .catch(error => {
                    console.log("ERROR:: ", error.response.data);
                });
        },

        getCallLogStatus(status) {
            let now = moment(new Date());
            let duration = moment.duration(now.diff(status["created_at"]));
            let hoursDiff = duration.asHours();

            return {
                with: status["call_log_with"].replace(/\s*\(.*?\)\s*/g, "")
            };
        },

        closeDialog() {
            $("#modal-upload-payment").modal("hide");
            $("#modal-upload-payment").modal("hide");
            $("#modal-upload-payment-two").modal("hide");
            $("#modal-download-payment").modal("hide");
        },

        showUploadPayment(item) {
            let app = this;
            app.selectedcall = item;
            console.log("SELECTED Call", app.selectedcall);
            console.log("SELECTED Call ID", app.selectedcall.id);
            $("#modal-upload-payment").modal({
                    backdrop: "static",
                    keyboard: false
                },
                "show"
            );
        },

        showUploadPaymentTwo(item) {
            let app = this;
            app.selectedcall = item;
            console.log("SELECTED Call", app.selectedcall);
            app.updated_at = app.selectedcall.updated_at;
            console.log("SELECTED Bill Owner ID", app.selectedcall.updated_at);
            console.log("SELECTED Call ID", app.selectedcall.id);

            $("#modal-upload-payment-two").modal({
                    backdrop: "static",
                    keyboard: false
                },
                "show"
            );
        },

        getExtensions(id) {
            let app = this;
            axios
                .get("/api/phonebilling/phone-bill-extensions-details/" + id)
                .then((response) => {
                    app.billdetails = response.data;
                    console.log("BILLING EXTENSIONS LIST ADMIN: ", app.billdetails);
                })
                .catch((error) => {
                    console.log("ERROR:: ", error.response.data);
                });
        },

        getStaffExtensions() {
            let app = this;
            let platform_url = $("#platform-url").val();
            app.authorize(platform_url, token => {
                axios
                    .get(platform_url + "/api/staff/extensions/", {
                        headers: {
                            Authorization: "Bearer " + token
                        }
                    })
                    .then(response => {
                        app.staffExtensions = response.data.results;
                        console.log("STAFF EXTENSIONS", app.staffExtensions);
                    })
                    .catch(error => {
                        console.log("ERRRR:: ", error.response.data);
                    });
            });
        },

        phoneUrl(phone) {
            return "call-log-identification/" + phone.id;
        },

        verificationDeadline() {
            var someDate = new Date();
            var numberOfDaysToAdd = 6;
            someDate.setDate(someDate.getDate() + numberOfDaysToAdd);
            var formattedDate = moment(String(someDate)).format("DD/MMM/YY");
            this.verificationDate = formattedDate;
            this.verificationDateAll = moment(String(someDate)).format("DD/MMM/YYYY");
        },

        floatToInt(value) {
            return value | 0;
        },

        getAllUserPhoneBill() {
            let app = this;
            app.isHidden = true;
            axios
                .get("/api/phonebilling/list-current-phone-bill")
                .then(response => {
                    app.userphonebill = response.data;
                    console.log("ALL  PHONE BILLS", app.userphonebill);
                })
                .catch(error => {
                    console.log("ERROR:: ", error.response.data);
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
        },

        onDateChangeIdentificationDeadline(e) {
            let date = moment(new Date(e.date)).format("YYYY-MM-DD");
            $("input[name=identication_deadline]").val(date);
        }
    }
};
</script>

<style scoped>
.phone-view,
.phone-card {
    background: #fff;
}

.phone-view .table-padding {
    padding: 25px 35px;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.progressbar {
    width: 100%;
    height: 6px;
    /* background-color: #eee;
    margin: 1em auto; */
    transition: width 500ms;
}
</style>
