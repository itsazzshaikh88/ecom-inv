
async function fetchUserToDisplay(userid) {
    const url = `${apiURL}users/${userid}`;

    const authToken = validateUserAuthToken();
    if (!authToken) return;


    try {

        fullPageLoader.classList.toggle("d-none");
        // Fetch product data from the API
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ userid })
        });

        // Parse the JSON response
        const data = await response.json();

        // Check if the API response contains an error
        if (!response.ok || data.status === 'error') {
            const errorMessage = data.message || `Error: ${response.status} ${response.statusText}`;
            throw new Error(errorMessage);
        }

        displayUserInfo(data.data);
    } catch (error) {
        // Show error notification
        toasterNotification({ type: 'error', message: 'Error: ' + error.message });
        console.error(error);

    } finally {
        fullPageLoader.classList.toggle("d-none");
    }
}
function displayUserInfo(data) {
    if (!data) return;

    if (Object.keys(data).length > 0) {
        showFieldContent(data);
    }

}

// Edit Action code goes here
document.addEventListener('DOMContentLoaded', () => {

    const url = new URL(window.location.href);
    const searchParams = new URLSearchParams(url.search);
    const urlSegments = url.pathname.split('/').filter(segment => segment);
    const userid = urlSegments[urlSegments.length - 1];
    fetchUserToDisplay(userid);
});