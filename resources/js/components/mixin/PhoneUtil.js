import intlTelInput from 'intl-tel-input';
const PhoneUtil = {
    data() {
        return {
            errorMap: ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"]
        }
    },
    methods: {
        initPhone(id) {

            let errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];

            let input = document.querySelector("#"+id);
            let errorMsg = document.querySelector("#error-msg-"+id),
                validMsg = document.querySelector("#valid-msg-"+id);

            let iti = intlTelInput(input, {
                utilsScript: "//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.3.0/js/utils.js"
            });
            iti.setCountry("mw");

            let reset = function() {
                input.classList.remove("error");
                errorMsg.innerHTML = "";
                errorMsg.classList.add("error-hide");
                validMsg.classList.add("error-hide");
            };

            // on blur: validate
            input.addEventListener('blur', function() {
                reset();
                if (input.value.trim()) {
                    if (iti.isValidNumber()) {
                        validMsg.classList.remove("error-hide");
                    } else {
                        input.classList.add("error");
                        let errorCode = iti.getValidationError();
                        errorMsg.innerHTML = errorMap[errorCode];
                        errorMsg.classList.remove("error-hide");
                    }
                }
            });

            // on keyup / change flag: reset
            input.addEventListener('change', reset);
            input.addEventListener('keyup', reset);
        }
    }
};

export default PhoneUtil;
