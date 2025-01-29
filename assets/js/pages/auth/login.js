async function validate(event) {
    event.preventDefault();

    // disable submit button
    const loginBtn = document.getElementById("login-btn");
    const loginBtnText = loginBtn.innerHTML;
    loginBtn.disabled = true;

    // change login btn
    loginBtn.innerHTML = `Signing In ...`;

    hideErrors(); // Hide all validation alerts
    const form = event.target;
    const formData = new FormData(form);
    try {
        const response = await fetch("api/v1/auth/login", {
            method: "POST",
            body: formData,
        });

        const result = await response.json();
        if (result.status) {
            if (result?.two_step_enabled) {
                loginContainer.classList.add("d-none");
                otpContainer.classList.remove("d-none");
                form.reset();
                // Set user of 
                document.getElementById("USER_ID").value = result?.user || 0
            } else {
                window.location = result?.url;
                toasterNotification({ message: "User Authenticated Successfully.", type: "success" });
            }
        } else {
            if (result?.type === 'validation') {
                showErrors(result.errors);
            } else {
                toasterNotification({ message: result?.message, type: "error" });
            }
        }
    } catch (error) {
        toasterNotification({ message: error, type: "error" });
    } finally {
        loginBtn.disabled = false;
        loginBtn.innerHTML = `Sign In`;
    }

}