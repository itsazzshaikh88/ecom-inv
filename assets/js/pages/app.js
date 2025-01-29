async function logoutAction() {

    const authToken = getCookie("userTaskAuthToken");
    if (!authToken) return window.location = `${baseURL}login`;

    // Show the SweetAlert confirmation dialog
    const result = await Swal.fire({
        title: "Are you sure?",
        text: "Do you want to log out?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, log me out",
        cancelButtonText: "No, stay logged in",
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: "btn btn-secondary me-2"
        }
    });

    // If user confirms the logout
    if (result.isConfirmed) {
        // Show loading animation
        Swal.fire({
            title: "Signing out...",
            html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            allowOutsideClick: false,
            showConfirmButton: false
        });

        try {
            // Send a logout request to the backend with the auth token
            const response = await fetch("api/v1/auth/logout", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${authToken}` // Include the auth token
                },
                body: JSON.stringify({ action: "logout" })
            });

            const result = await response.json();

            // If the session is successfully destroyed
            if (response.ok && result.success) {
                // Redirect to login page
                window.location.href = `${baseURL}auth/login`;
            } else {
                // Handle failure (optional)
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong! Could not log you out.",
                    confirmButtonText: "OK"
                });
            }
        } catch (error) {
            // Handle error if the request fails
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "There was an issue connecting to the server.",
                confirmButtonText: "OK"
            });
        }
    }
}

