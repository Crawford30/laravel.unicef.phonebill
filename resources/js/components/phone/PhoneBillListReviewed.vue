<template>
<div>
    <div class="container-fuild">
        <div class="row my-3">
            <div class="col-md-9">
                <h4>Phone Bill Manager</h4>
            </div>
        </div>
    </div>
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
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in currentuserdata" :key="'user_phone_bill' + index">
                                        <td>

                                            <div v-if="item.status ===  'Reviewed' || item.status ===  'Reconciled'">
                                                {{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }}
                                            </div>
                                            <div v-else>
                                                <a :href="phoneUrl(item)"> {{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }}
                                                </a>
                                            </div>

                                        </td>
                                        <td style="text-align:center;">
                                            <!-- <a :href="phoneUrl(item)">{{ item.extension_count }}</a> -->
                                            <div v-if="item.status ===  'Reviewed' || item.status ===  'Reconciled'">
                                                {{ item.extension_count }}
                                            </div>
                                            <div v-else>
                                                <a :href="phoneUrl(item)">{{ item.extension_count }}
                                                </a>
                                            </div>
                                        </td>
                                        <td style="text-align:center;">
                                            <!-- <a :href="phoneUrl(item)">{{ item.unique_mobile_count }}
                                            </a> -->

                                            <div v-if="item.status ===  'Reviewed' || item.status ===  'Reconciled'">
                                                {{ item.unique_mobile_count }}
                                            </div>
                                            <div v-else>
                                                <a :href="phoneUrl(item)">{{ item.unique_mobile_count }}
                                                </a>
                                            </div>
                                        </td>

                                        <td>
                                            <div>
                                                <span style="font-size: 12px">{{ (1 * 100  ) }} %
                                                </span>
                                                <div class="progressbar">
                                                    <div class="progressbar text-center" style="background-color: green; margin: 0; color: white;" :style="{width: parseFloat( ( ((item.official_calls_count + item.personal_calls_count) / (item.official_calls_count + item.personal_calls_count)) * 100 ).toFixed(2) )  + '%'}">
                                                        {{ (item.official_calls_count + item.personal_calls_count)}}
                                                    </div>
                                                    <span style="font-size: 12px">{{ moment(item.updated_at).fromNow() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td style="text-align:center;">{{ item.reviewed_amount }}</td>
                                        <td>
                                            <span v-if="item.document_one === null && item.reviewed_amount !== 0 && isUploadPaymentSet == true">
                                                <a href="#" @click.prevent="showUploadPayment(item)">Pay MWK {{ item.reviewed_amount }}</a>
                                            </span>
                                            <span v-else-if=" item.document_one !==  null && item.document_two === null && item.reviewed_amount !== 0">
                                                <a href="#" @click.prevent="showUploadPaymentTwo(item)">Payment Evidence Sent</a>
                                            </span>

                                            <div v-else>
                                                <span :style="{ color: ' #0CA357' }">{{ getCallLogStatus(item.current_status)["with"] }}
                                                </span>
                                            </div>

                                            <div @click.prevent="downloadReceipt(item); showDownloadPayment();" class="pdf-image-icon">
                                                <img src="/images/icons/icon.pdf.png" alt="" />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="selectedcall != null && staffExtensions != null" class="d-none">
        <PaymentInvoiceForm :selectedcall="selectedcall" :staffExtensions="staffExtensions" />
    </div>
    <!-- </div> -->

    <!-- Upload Payment -->
    <div class="modal fade" id="modal-upload-payment">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body py-4">
                    <button type="button" style="position: absolute; right: 1.5rem; top: 1.5rem" class="close" @click="closeDialog">
                        &times;
                    </button>
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
                        <button :disabled="documentFile === null || isProcessing" @click="uploadFile" type="button" class="unicef-btn unicef-primary">
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
                <!-- Modal body -->
                <div class="modal-body py-4">
                    <button type="button" style="position: absolute; right: 1.5rem; top: 1.5rem" class="close" @click="closeDialog">
                        &times;
                    </button>
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

    <div class="modal fade" id="modal-download-payment">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body py-4">
                    <button type="button" style="position: absolute; right: 1.5rem; top: 1.5rem" class="close" @click="closeDialog">
                        &times;
                    </button>
                    <h5 style="text-align: center; font-weight: bold">
                        Download Receipt
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
                        <button @click="downloadPaymentReceipt" type="button" class="unicef-btn unicef-primary">
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
import DocumentUpload from "../DocumentUpload";
import PaymentInvoiceForm from "../PaymentInvoiceForm";

export default {
    name: "PhoneBillListReviewed",
    props: ["currentuserdata"],
    components: {
        Tooltip,
        Swal,
        MapLocation,
        datePicker,
        ProgressBar,
        DocumentUpload,
        PaymentInvoiceForm
    },
    mixins: [dragAndDropHelper, Izitoast, ModuleAuth],
    data() {
        return {
            bscEmail: "",
            updated_at: "",
            selectedcall: null,
            callid: null,
            billdetails: null,
            documentFile: null,
            userdatabankselected: [],
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
            allowanceValue: 0.0,

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

        app.userdatabankselected = app.currentuserdata;
        console.log("USER DATA", app.currentuserdata);
        app.verificationDeadline();
        // app.getStaffExtensions();
        app.getConfig();
        app.$on("doc-file-uploaded", data => {
            app.documentFile = data;
            console.log("RECIEVED FILE", app.documentFile);
        });
        app.getOtherAllowance();

        app.$on("doc-file-removed", () => {
            app.documentFile = null;
        });
    },

    methods: {
        downloadReceipt(item) {
            let app = this;
            app.selectedcall = item;
            console.log("SELECTED Call", app.selectedcall);
            console.log("SELECTED Call ID", app.selectedcall.id);
            console.log("SELECTED Bill Owner ID", app.selectedcall.bill_owner_id);
        },

        downloadPaymentReceipt() {
            let app = this;
            app.closeDialog();
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
        },

        getCallLogStatus(status) {
            let now = moment(new Date());
            let duration = moment.duration(now.diff(status["created_at"]));
            let hoursDiff = duration.asHours();

            return {
                with: status["call_log_with"].replace(/\s*\(.*?\)\s*/g, "")
            };
        },

        uploadFileTwo() {
            let app = this;
            app.isProcessing = true;
            let data = new FormData();
            data.append("id", app.selectedcall.id);
            data.append("user_data_id", app.selectedcall.user_data_id);
            data.append("bill_owner_id", app.selectedcall.bill_owner_id);
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

        uploadFile() {
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

        showDownloadPayment() {
            $("#modal-download-payment").modal({
                    backdrop: "static",
                    keyboard: false
                },
                "show"
            );
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

        getConfig() {
            let app = this;
            let platform_url = $("#platform-url").val();
            app.authorize(platform_url, token => {
                axios
                    .get(platform_url + "/api/config/details", {
                        headers: {
                            Authorization: "Bearer " + token
                        }
                    })
                    .then(response => {
                        console.log("CONFIG", response.data.results);
                        this.bscEmail = response.data.results.bsc_email;
                        console.log("BSC EMAIL", this.bscEmail);
                    })
                    .catch(error => {
                        console.log("ERRRR:: ", error.response.data);
                    });
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
            return "call-log-identification/" + phone.user_data_id;
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

.pdf-image-icon {
    cursor: pointer;
    width: 30px;
    height: 30px;
    border: 0.5px solid #dddddd;
    border-radius: 50%;
}

.pdf-image-icon img {
    width: 30px;
    height: 30px;
}

.progressbar {
    width: 100%;
    height: 6px;
    /* background-color: #eee;
    margin: 1em auto; */
    transition: width 500ms;
}
</style>
