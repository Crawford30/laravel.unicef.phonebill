<template>
<div>
    <div class="container-fuild">
        <div class="row my-3">
            <div class="col-md-9">
                <h4>
                    Phone Bill Manager
                    <!-- <span v-if="isHidden === false">(Administrator)</span> -->
                    <span v-if="isHidden === true">(Administrator)</span>
                </h4>
            </div>
            <div class="col-md-3">
                <div class="btn-group add-new-dropdown" style="width: 100%">
                    <button @click="showUploadExcel" type="button" class="btn btn-primary">
                        Upload Man3000 Bills
                    </button>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="dropdown-menu">
                            <hr />

                            <li class="dropdown-item" style="text-align: center" @click="allBillClicked()">
                                <img src="/images/icons/phone_bill.png" class="mt-3" style="height: 30px" />
                                <div class="mt-2 mb-3">All Phone Bill Logs</div>
                                <hr />
                            </li>

                            <li @click="myBillClicked()" class="dropdown-item" style="text-align: center">
                                <img src="/images/icons/phone_gray.png" style="height: 30px" />
                                <div class="mt-2 mb-3">My Phone Bill Logs</div>
                                <hr />
                            </li>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="isHidden === false">
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
                                    <div class="row d-flex">
                                        <div class="col-md-6 text-right">
                                            <h5 class="pt-2">Click here to upload Man3000 Bills</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-primary px-4" @click="showUploadExcel()">Upload Man3000 Bills</button>
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
                <div class="row mt-3">
                    <div class="col-md-12">
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
                                                <th style="text-align:center;">COST(MWK)</th>
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
                                                        <a :href="phoneUrl(item)"> {{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }}
                                                        </a>
                                                    </div>
                                                    <!-- <div v-if="item.current_status != null && item.current_status.call_log_with === 'With ADMIN' || item.current_status.call_log_with === 'Reviewed' || item.current_status.call_log_with === 'Reconciled' ">
                                                        {{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }}
                                                    </div>
                                                    <div v-else>
                                                        <a :href="phoneUrl(item)"> {{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }}
                                                        </a>
                                                    </div> -->
                                                </td>

                                                <td style="text-align:center;">

                                                    <div v-if="item.current_status != null">
                                                        {{ item.extension_count }}
                                                    </div>
                                                    <div v-else>
                                                        <a :href="phoneUrl(item)">{{ item.extension_count }}
                                                        </a>
                                                    </div>
                                                    <!-- <div v-if="item.current_status != null && item.current_status.call_log_with === 'With ADMIN' || item.current_status.call_log_with === 'Reviewed' || item.current_status.call_log_with === 'Reconciled' ">
                                                        {{ item.extension_count }}
                                                    </div>
                                                    <div v-else>
                                                        <a :href="phoneUrl(item)">{{ item.extension_count }}
                                                        </a>
                                                    </div> -->
                                                </td>

                                                <td style="text-align:center;">
                                                    <div v-if="item.current_status != null">
                                                        {{ item.unique_mobile_count }}
                                                    </div>
                                                    <div v-else>
                                                        <a :href="phoneUrl(item)">{{ item.unique_mobile_count }}
                                                        </a>
                                                    </div>

                                                    <!-- <div v-if="item.current_status != null && item.current_status.call_log_with === 'With ADMIN' || item.current_status.call_log_with === 'Reviewed' || item.current_status.call_log_with === 'Reconciled' ">
                                                        {{ item.unique_mobile_count }}
                                                    </div>
                                                    <div v-else>
                                                        <a :href="phoneUrl(item)">{{ item.unique_mobile_count }}
                                                        </a>
                                                    </div> -->
                                                    <!-- <a :href="phoneUrl(item)">{{ item.extension_count }}</a> -->
                                                </td>
                                                <td>
                                                    <div v-if="item.current_status === null">
                                                        <span class="mr-4" style="font-size: 12px"> 0 %</span>
                                                        <span style="font-size: 12px">{{ moment(item.created_at).fromNow() }}
                                                        </span>
                                                        <div class="progressbar">
                                                            <div class="progressbar text-center" style="background-color: #dc720f; margin: 0; color: white;" :style="{width: 100  + '%'}">
                                                                {{ 0 }}
                                                            </div>

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
                                                        <span class="mr-4" style="font-size: 12px">{{parseFloat( ( (item.current_status.total_count / item.current_status.total_count) * 100 ).toFixed(2) ) }} %
                                                        </span>
                                                        <span style="font-size: 12px">{{ moment(item.created_at).fromNow() }}
                                                        </span>
                                                        <div class="progressbar">
                                                            <div class="progressbar text-center" style="background-color: green; margin: 0; color: white;" :style="{width: parseFloat( ( (item.current_status.total_count / item.current_status.total_count) * 100 ).toFixed(2) )  + '%'}">

                                                                {{ item.current_status.total_count }}

                                                            </div>

                                                        </div>
                                                    </div>
                                                </td>

                                                <td style="text-align:center;">
                                                    <div v-if="item.identified_amount === null ">
                                                        <!-- {{ floatToInt(item.total_monthly_cost) }} -->
                                                        {{ (item.total_user_monthly_cost) }}
                                                    </div>
                                                    <div v-else>
                                                        {{ parseFloat(item.identified_amount).toFixed(2) }}
                                                    </div>
                                                </td>
                                                <td style="text-align:center;">

                                                    <div v-if="item.current_status === null">
                                                        <span :style="{ color: ' #FF7B40' }">Pending</span>
                                                    </div>

                                                    <div v-else-if="item.reviewed_amount > 0  && item.current_status.call_log_with == 'Reviewed' && item.bill_type != null  && item.bill_type.bill_type === 'UNICEF BANK ACCOUNT' ">
                                                        <a href="#" @click.prevent="showUploadPayment(item)">Pay MWK {{ Math.abs(item.reviewed_amount  - allowanceValue).toFixed(2)}} </a>
                                                    </div>

                                                    <div v-else-if="item.reviewed_amount === 0  && item.current_status.call_log_with == 'Reviewed' && item.bill_type != null  && item.bill_type.bill_type === 'UNICEF BANK ACCOUNT' && ((item.reviewed_amount  - allowanceValue) < 0)">
                                                        <a href="#" @click.prevent="showUploadPayment(item)">Pay MWK 0</a>
                                                    </div>

                                                    <div v-else-if="item.reviewed_amount > 0 && item.current_status.call_log_with !== 'Reconciled' && docOne != null && docTwo != null && item.bill_type != null  && item.bill_type.bill_type == 'UNICEF BANK ACCOUNT'">
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
                                                    <div v-else-if="item.reviewed_amount > 0  &&  billType != null && billType == 'UNICEF_BANK_ACCOUNT' && item.current_status.call_log_with == 'Reviewed'  ">
                                                        <a href="#" @click.prevent="showUploadPayment(item)">Pay MWK {{ item.reviewed_amount }}</a>
                                                    </div>                                                    
                                                    <div v-else-if="item.reviewed_amount > 0 &&  billType != null && billType == 'UNICEF_BANK_ACCOUNT' && docOne != null && docTwo != null">
                                                        <span :style="{ color: ' #0CA357' }">Payment Evidence Sent </span>
                                                    </div>
                                                    <div v-else>
                                                        <span :style="{ color: ' #0CA357' }">{{ getCallLogStatus(item.current_status)["with"] }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div v-if="item.current_status != null"  @click.prevent="downloadReceipt(item); showDownloadPayment();" class="pdf-image-icon">
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
            </div>
        </div>

    </div>

    <div v-if="isHidden === true">
        <div v-if="allPhonebills.length == 0">
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
                                    <div class="row d-flex">
                                        <div class="col-md-6 text-right">
                                            <h5 class="pt-2">Click here to upload Man3000 Bills</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-primary px-4" @click="showUploadExcel()">Upload Man3000 Bills</button>
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
                                                <!-- <th style="text-align:center;">UNIQUE EXTENSIONS</th> -->
                                                <th>IDENTIFIED</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in allPhonebills" :key="'user_phone_bill' + index">
                                                <td>
                                                    {{ format_date_table(item.from_date) }} - {{ format_date_table(item.to_date) }}
                                                </td>
                                                <td style="text-align:center;">{{ item.total_records }}</td>
                                                <td style="text-align:center;">{{ item.unique_mobile_number_count }}</td>
                                                <!-- <td style="text-align:center;">{{ item.unique_extensions_count }}</td> -->

                                                <td>
                                                    <div v-if="item.current_status === null">
                                                        <span class="mr-4" style="font-size: 12px"> 0 %</span>
                                                        <span style="font-size: 12px">{{ moment(item.updated_at).fromNow() }}
                                                        </span>
                                                        <div class="progressbar">
                                                            <div class="progressbar text-center" style="background-color: #dc720f; margin: 0; color: white;" :style="{width: 100  + '%'}">
                                                                {{ 0 }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div v-else>
                                                        <span class="mr-4" style="font-size: 12px">{{parseFloat( ( (item.current_status.total_count / item.current_status.total_count) * 100 ).toFixed(2) ) }} %
                                                        </span>
                                                        <span style="font-size: 12px">{{ moment(item.updated_at).fromNow() }}
                                                        </span>
                                                        <div class="progressbar">
                                                            <div class="progressbar text-center" style="background-color: green; margin: 0; color: white;" :style="{width: parseFloat( ( (item.current_status.total_count / item.current_status.total_count) * 100 ).toFixed(2) )  + '%'}">
                                                                {{ item.current_status.total_count }}
                                                            </div>

                                                        </div>
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
        </div>
    </div>

    <!-- Upload Excel -->
    <div class="modal fade" id="modal-upload-excel">
        <div class="modal-dialog modal-lg">
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
                        Upload Call Logs
                    </h5>
                    <br />
                    <div id="workshop-stepper" class="bs-stepper">
                        <div class="justify-content-center text-center">
                            <div class="bs-stepper-header w-75" style="margin: auto">
                                <div class="step" data-target="#step1-view">
                                    <button type="button" class="btn step-trigger" :class="{ active: step >= 1 }">
                                        <span class="bs-stepper-circle">
                                            <i class="fa fa-check"></i>
                                        </span>
                                        <br />
                                        <span class="bs-stepper-label">Upload File</span>
                                    </button>
                                </div>
                                <div class="line" :class="{ active: step > 1 }"></div>
                                <div class="step" data-target="#step2-view">
                                    <button type="button" class="btn step-trigger" :class="{ active: step >= 2 }">
                                        <span v-if="step >= 2" class="bs-stepper-circle">
                                            <i class="fa fa-check"></i>
                                        </span>
                                        <span v-else class="bs-stepper-circle">2</span>
                                        <br />
                                        <span class="bs-stepper-label">Review Summary</span>
                                    </button>
                                </div>
                                <div class="line" :class="{ active: step > 2 }"></div>
                                <div class="step" data-target="#step2-view">
                                    <button type="button" class="btn step-trigger" :class="{ active: step >= 3 }">
                                        <span v-if="step >= 3" class="bs-stepper-circle">
                                            <i class="fa fa-check"></i>
                                        </span>
                                        <span v-else class="bs-stepper-circle">3</span>
                                        <br />
                                        <span class="bs-stepper-label">Dispatch</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bs-stepper-content mt-4">
                        <div id="step1-view" class="content">
                            <div class="form-group">
                                <label>Identification Deadline</label>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <div class="input-group">
                                            <date-picker ref="dp1" :config="this.dateOptions" @dp-change="onDateChangeTAD" :value="verificationDateAll" />
                                            <div class="input-group-prepend">
                                                <button class="btn btn-outline-primary" type="button" @click="$refs.dp1.dp.show()"><i class="fas fa-calendar-alt"></i></button>
                                            </div>
                                            <input name="identification_deadline" hidden class="form-control my-icon" required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="!hasFile" id="drop-area" class="drag-drop-area px-5 py-3" style="margin-left: 0; margin-right: 0">
                                <div class="text-center drop-zone">
                                    <img class="icon-img" src="/images/icons/upload_gray.png" />
                                    <h6 style="color: #bbbbbb; margin-bottom: 0.2rem">
                                        DRAG &amp; DROP
                                    </h6>
                                </div>
                                <div class="text-center">
                                    <p style="color: #bbbbbb; margin-bottom: 0; padding-bottom: 0; font-size: 12px; ">
                                        Call Logs, or
                                    </p>
                                    <div class="position-relative">
                                        <button type="button" class="btn btn-primary position-relative" style="font-size: 12px; margin-top: 5px">
                                            Browse
                                            <input @change="onBrowseFile" type="file" style="position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer;" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="border p-5">
                                <h6 class="text-center te xt-black-50">{{ file.name }}</h6>
                                <div class="text-center">
                                    <button @click.prevent="removeFile()" style="font-size: 24px; color: #666666; background: #ffffff; outline: none; border: none; cursor: pointer; ">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="step2-view" class="content"></div>
                        <div id="step3-view" class="content"></div>
                    </div>
                    <div class="text-center mt-2">
                        <button v-if="step === 1" :disabled="file === null || isProcessing" @click="uploadFile" type="button" class="unicef-btn unicef-primary">
                            <span>
                                <i v-if="isProcessing" class="fa fa-spinner fa-spin"> </i>
                                UPLOAD</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call Log Summary -->
    <div class="modal fade" id="call-log-summary">
        <div class="modal-dialog modal-lg">
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
                    <!-- <button type="button" style="position: absolute; left: 1.5rem; top: 1.5rem" class="close" @click="closeCallLogSummary">
                        <li class="fas fa-arrow-left fa-1x"></li>
                    </button> -->
                    <h5 style="text-align: center; font-weight: bold">
                        Call Log Summary
                    </h5>
                    <br />

                    <div id="workshop-stepper" class="bs-stepper">
                        <div class="justify-content-center text-center">
                            <div class="bs-stepper-header w-75" style="margin: auto">
                                <div class="step" data-target="#step1-view">
                                    <button type="button" class="btn step-trigger" :class="{ active: step >= 1 }">
                                        <span class="bs-stepper-circle"><i class="fa fa-check"></i></span>
                                        <br />
                                        <span class="bs-stepper-label">Upload File</span>
                                    </button>
                                </div>
                                <div class="line" :class="{ active: step > 1 }"></div>
                                <div class="step" data-target="#step2-view">
                                    <button type="button" class="btn step-trigger" :class="{ active: step >= 2 }">
                                        <span v-if="step >= 2" class="bs-stepper-circle"><i class="fa fa-check"></i></span>
                                        <span v-else class="bs-stepper-circle">2</span>
                                        <br />
                                        <span class="bs-stepper-label">Review Summary</span>
                                    </button>
                                </div>
                                <div class="line" :class="{ active: step > 2 }"></div>
                                <div class="step" data-target="#step2-view">
                                    <button type="button" class="btn step-trigger" :class="{ active: step >= 3 }">
                                        <span v-if="step >= 3" class="bs-stepper-circle"><i class="fa fa-check"></i></span>
                                        <span v-else class="bs-stepper-circle">3</span>
                                        <br />
                                        <span class="bs-stepper-label">Dispatch</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="Object.keys(confirmationData).length > 0">
                        <div class="bs-stepper-content mt-4">
                            <div id="step2-view" class="content">
                                <div>
                                    <div class="container justify-content-center mt-4 mb-4">
                                        <h6>
                                            Total Records in file:
                                            <strong>{{ confirmationData.total_records }}</strong>
                                        </h6>
                                        <hr />
                                        <h6>
                                            Total Unique Number Dialed:
                                            <strong>{{ confirmationData.unique_mobile_number_count }} </strong>
                                        </h6>
                                        <hr />
                                        <h6>
                                            Start Date/Time:
                                            <strong>{{ format_date(confirmationData.from_date) }}</strong>
                                        </h6>
                                        <hr />
                                        <h6>
                                            End Date/Time:
                                            <strong>{{ format_date(confirmationData.to_date) }}</strong>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div id="step3-view" class="content"></div>
                        </div>

                        <div class="row d-flex justify-content-center">
                            <div class="col-4">
                                <div class="text-center mt-2">
                                    <button v-if="step === 2" type="button" class="unicef-btn unicef-secondary" @click="closeCallLogSummary">
                                        <span> BACK</span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="text-center mt-2">
                                    <button v-if="step === 2" type="button" class="unicef-btn unicef-primary" @click="sendLogsToStaff">
                                        <i v-if="isProcessing" id="sendlog-spinner-spinner" class="fa fa-spinner fa-spin"></i>
                                        <span>SEND LOGS TO STAFF</span>
                                    </button>
                                </div>
                            </div>
                        </div>
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

    <div v-if="selectedcall != null && billdetails != null" class="d-none">
        <PaymentInvoiceForm :selectedcall="selectedcall" :billdetails="billdetails"  :usdtomwk="parseFloat(usdtomwk)" :allowanceValue="parseFloat(allowanceValue)" :billOwnerId="parseInt(billownerid)"  :phonebillDataId="parseInt(selectedcall.user_data_id)" />
    </div>

    <!-- <div v-if="selectedcall != null && staffExtensions != null" class="d-none">
        <PaymentInvoiceForm :selectedcall="selectedcall" :staffExtensions="staffExtensions" :billOwnerId="parseInt(billownerid)" />
    </div> -->

    <div class="modal fade" id="modal-download-payment">
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
    name: "PhoneBillListAdmin",

    components: {
        Tooltip,
        MapLocation,
        datePicker,
        ProgressBar,
        DocumentUpload,
        PaymentInvoiceForm
    },
    mixins: [dragAndDropHelper, Izitoast, ModuleAuth],
    props: ['docOne', 'docTwo', 'billownerid'],
    data() {
        return {
          
            moment: moment,
            phonebill_id: "",
            numberOfDaysToAdd: 0,
            isHidden: Boolean,
            verificationDate: null,
            verificationDateAll: null,
            userphonebill: [],
            allPhonebills: [],
            staffExtensions: [],
            step: 1,
            stepper: null,
            file: null,
            hasFile: false,
            isProcessing: false,
            confirmationData: {},
            importedData: {},
            documentFile: null,

            dateOptions: {
                format: "DD/MMM/YYYY",
                useCurrent: false,
                minDate: new Date(),
                icons: {
                    time: "far fa-clock",
                    date: "far fa-calendar-alt",
                    up: "fas fa-chevron-up",
                    down: "fas fa-chevron-down",
                    previous: "fas fa-chevron-left",
                    next: "fas fa-chevron-right",
                    today: "fas fa-calendar-check",
                    clear: "far fa-trash-alt",
                    close: "far fa-times-circle",
                },
            },
            bscEmail: "",
            selectedcall: null,
            allowanceValue: 0.0,
            otherUserAllowances: [],
            updated_at: "",
            usdtomwk: 0.0,
            billdetails: [],
        };
    },

    mounted() {
        let app = this;
        app.getConfig();
        app.getStaffExtensions();
        app.getCurrentUserPhoneBill();
        app.getOtherAllowance();
        app.getForexRates();

        app.dragAndDrop(e => {
            app.processSelectedFile(app.fileData(e));
        });

        console.log("BILL TYPE: ", app.$props.billType);
        if ($("#stepper1").length > 0) {
            app.stepper = new Stepper(document.querySelector("#stepper1"));
        };

        app.$on("doc-file-uploaded", data => {
            app.documentFile = data;
            console.log("RECIEVED FILE", app.documentFile);
        });

    },

    methods: {

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

        showDownloadPayment() {
            $("#modal-download-payment").modal({
                    backdrop: "static",
                    keyboard: false
                },
                "show"
            );
        },
        downloadPaymentReceipt() {
            let app = this;
           // app.closeDialog();
        //    $("#modal-download-payment").modal("hide");
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
                    orientation: "portrait",
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
        downloadReceipt(item) {
            let app = this;
            app.selectedcall = item;

            app.getExtensions(app.selectedcall.user_data_id);
            console.log("SELECTED Call", app.selectedcall);
            console.log("SELECTED Call ID", app.selectedcall.id);
            console.log("SELECTED Bill Owner ID", app.selectedcall.bill_owner_id);
            
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
        closeDialog() {
            $("#modal-upload-payment").modal("hide");
            $("#modal-upload-payment").modal("hide");
            $("#modal-upload-payment-two").modal("hide");
            $("#modal-download-payment").modal("hide");
        },
        onDateChangeTAD(e) {
            let date = moment(new Date(e.date)).format("DD/MMM/YYYY");
            $("input[name=identification_deadline]").val(date);
            this.verificationDateAll = date;
            console.log("VERIFICATION DATE: ", this.verificationDateAll);
        },

        formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        },
        getCallLogStatus(status) {
            // let now = moment(new Date());
            // let duration = moment.duration(now.diff(status["created_at"]));
            // let hoursDiff = duration.asHours();
            return {
                with: status["call_log_with"].replace(/\s*\(.*?\)\s*/g, "")
            };
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


        sendLogsToStaff() {
            let app = this;
            app.isProcessing = true;
            console.log("Dispatch email tapped");
            let formData = new FormData();
            formData.append("staff_extensions", JSON.stringify(app.staffExtensions));

            axios({
                    method: "post",
                    url: "/api/phonebilling/dispatch-email",
                    data: formData
                })
                .then(response => {
                    console.log("SEND LOG RESPONNSE: ", response.data.results);
                    this.closeAllModels();
                    $("#call-log-summary").modal("hide");
                    this.isProcessing = false;
                    //======dismiss the model

                    // window.location.href = "/home";
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        html: "<p class='font-size: 13px'>Call logs sent to staff</p>",
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
                        // app.staffExtensions = response.data.results;
                        console.log(
                            "CONFIG DATA",
                            response.data.results.identify_call_max_time
                        );
                        app.bscEmail = response.data.results.bsc_email;

                        // this.numberOfDaysToAdd = response.data.results.identify_call_max_time;

                        var someDate = new Date();
                        // var numberOfDaysToAdd = 6;
                        someDate.setDate(
                            someDate.getDate() + response.data.results.identify_call_max_time
                        );
                        var formattedDate = moment(String(someDate)).format("DD/MMM/YY");
                        this.verificationDate = moment(String(someDate)).format(
                            // "DD/MMM/YY"
                            "YYYY-MM-DD"
                        );
                        this.verificationDateAll = moment(String(someDate)).format(
                            "DD/MMM/YYYY"
                        );

                        console.log("VERIFICATION DEADLINE", this.verificationDateAll);
                    })
                    .catch(error => {
                        console.log("ERRRR:: ", error.response.data);
                    });
            });
        },

        myBillClicked() {
            let app = this;
            app.isHidden = false;
            app.getCurrentUserPhoneBill();
        },

        allBillClicked() {
            let app = this;
            app.isHidden = true;
            app.getAllUserPhoneBill();
        },

        floatToInt(value) {
            return value | 0;
        },

        format(value, event) {
            return moment(value).format('DD/MMM/YYYY')
        },

        getCurrentUserPhoneBill() {
            let app = this;
            app.userphonebill = [];
            app.isHidden = false;
            axios
                .get("/api/phonebilling/list-current-phone-bill")
                //    .get("/api/phonebilling/list-all-phone-bill")
                .then(response => {
                    app.userphonebill = response.data;
                    console.log("CURRENT USER REQUEST BILL", app.userphonebill);
                })
                .catch(error => {
                    console.log("ERRRR:: ", error.response.data);
                });
        },

        getAllUserPhoneBill() {
            let app = this;
            app.userphonebill = [];
            app.isHidden = true;
            axios
                .get("/api/phonebilling/list-all-phone-bill")
                .then(response => {
                    app.allPhonebills = response.data;
                    console.log("ALL USER REQUEST BILL", app.allPhonebills);
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
        },

        proceedToCallLogSummary() {
            let app = this;
            $("#modal-upload-excel").modal("hide");
            app.step += 1;
            $("#call-log-summary").modal({
                    backdrop: "static",
                    keyboard: false
                },
                "show"
            );
        },

        closeCallLogSummary() {
            let app = this;
            $("#call-log-summary").modal("hide");
            app.step -= 1;
            $("#modal-upload-excel").modal({
                    backdrop: "static",
                    keyboard: false
                },
                "show"
            );
        },

        closeAllModels() {
            //====Use to close all model on form insertion====
            $("call-log-summary").modal("hide");
            $("#modal-upload-excel").modal("hide");
        },

        onDateChangeIdentificationDeadline(e) {
            let date = moment(new Date(e.date)).format("YYYY-MM-DD");
            $("input[name=identication_deadline]").val(date);
        },
        showUploadExcel() {
            $("#modal-upload-excel").modal({
                    backdrop: "static",
                    keyboard: false
                },
                "show"
            );
        },

        closeDialog() {
            let app = this;
            app.file = null;
            app.hasFile = false;
            app.step = 1;
            app.isProcessing = false;
            app.confirmationData = {};
            $("#modal-upload-excel").modal("hide");
        },

        processSelectedFile(fileData) {
            let app = this;
            if (
                fileData.ext.toLowerCase() !== "xls" &&
                fileData.ext.toLowerCase() !== "xlsx"
            ) {
                app.file = null;
                app.hasFile = false;
                Swal.fire({
                    icon: "error",
                    title: "Invalid File",
                    text: "File must be in excel format"
                });
            } else {
                app.file = fileData.file;
                app.hasFile = true;
                console.log("FILE", app.file);
            }
        },

        stepTo(direction) {
            let app = this;
            app.step = direction === "next" ? (app.step += 1) : (app.step -= 1);
            this.stepper.to(app.step);
        },
        removeFile() {
            (this.file = null), (this.hasFile = false);
        },
        onBrowseFile(e) {
            let app = this;
            app.processSelectedFile(app.fileData(e, "browse"));
        },

        uploadFile() {
            let app = this;
            app.isProcessing = true;
            let formData = new FormData();
            formData.append("file", app.file);
            formData.append("identification_deadline", app.verificationDateAll);
            // formData.append("staff_extensions", JSON.stringify(app.staffExtensions));
            axios({
                    method: "post",
                    url: "/api/phonebilling/import-phone-bill-template",
                    data: formData,
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(response => {
                    this.showSuccessMessage("Done..");
                    this.isProcessing = false;
                    this.importedData = response.data;
                    this.confirmationData = response.data;

                    if (this.isHidden === true) {
                        this.getAllUserPhoneBill();
                    } else {
                        this.getCurrentUserPhoneBill();
                    }
                    this.proceedToCallLogSummary();
                })
                .catch(error => {
                    this.isProcessing = false;
                    console.log("ERRRR:: ", error.response.data);
                    this.errors = error.response.data.errors;
                    console.log("ERRRR:: ", error.response.data.errors);
                });
        }
    }
};
</script>

<style scoped>
/* input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-calendar-picker-indicator {
    display: none;
    -webkit-appearance: none;
} */

/* .input-group-prepend{
     display: inline-block;
  width: 100%;
} */
.my-icon {
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("https://use.fontawesome.com/releases/v5.8.2/svgs/regular/calendar-alt.svg");
    background-repeat: no-repeat;
    background-position: center right calc(0.375em + 0.1875rem);
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.add-new-dropdown {
    position: relative;
    width: 100%;
}

.add-new-dropdown .dropdown-menu {
    position: absolute !important;
    left: -149px !important;
    top: 0 !important;
}

li:hover {
    background-color: inherit;
}

.dropdown-item {
    cursor: pointer;
    text-align: center;
}

.dropdown-item:hover {
    color: #3b86ff;
}

.progressbar {
    width: 100%;
    height: 6px;
    /* background-color: #eee;
    margin: 1em auto; */
    transition: width 500ms;
}

/* 

  .add-new-dropdown {
        position: relative;
       
    }

     .dropdown-menu {
            position: absolute !important;
            left: -149px !important;
            top: 0 !important;
        }
       
        .dropdown-item {
            cursor: pointer;
            text-align: center;
            
           
        }

      .dropdown-item   li:hover {
          background-color: inherit;
        } */

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

/* .dropdown-menu{
    width: 80%;
} */
</style>
