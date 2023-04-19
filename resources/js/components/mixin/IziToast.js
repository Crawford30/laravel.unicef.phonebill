import iziToast from "izitoast";
const Izitoast = {

    methods: {
        showSuccessMessage(message, position = "bottomLeft") {
            iziToast.success({
                position: position,
                message: message
            })
        },
        showErrorMessage(message, position = "bottomLeft") {
            iziToast.error({
                position: position,
                message: message
            })
        },
        formatAjaxError(e) {
            let err = eval("(" + e.responseText + ")");
            return err.message;
        },
        showAjaxError(e) {
            let err = eval("(" + e.responseText + ")");
            this.showErrorMessage(err.message);
        }
    }
};

export default Izitoast;
