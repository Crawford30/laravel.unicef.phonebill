const ModuleAuth = {

    methods: {
        authorize(moduleUrl, onComplete) {
            $.ajax({
                url: "/api/user",
                success(data) {
                    let loginUrl = moduleUrl + "/api/auth/login-token";
                    $.ajax({
                        url: loginUrl,
                        type: "POST",
                        data: data,
                        success(data) {
                            onComplete(data.access_token);
                        }
                    })
                }
            })
        }
    }

};

export default ModuleAuth;
