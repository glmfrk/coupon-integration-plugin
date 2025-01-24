document.addEventListener("DOMContentLoaded", function () {
    const subscriptionForm = document.getElementById("subscription-form");

    if (subscriptionForm) {
        subscriptionForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const subscriptionIdInput = document.getElementById("subscription_id");
            const errorMessageDiv = document.getElementById("error_message");
            const loadingSpinner = document.getElementById("loading_spinner");

            if (!subscriptionIdInput.value) {
                errorMessageDiv.innerHTML = "<p style='color: red;'>Subscription ID is required.</p>";
                return;
            }

            // Show loading spinner
            loadingSpinner.style.display = "block";
            errorMessageDiv.innerHTML = "";

            // Prepare data for AJAX
            const formData = new FormData();
            formData.append("action", "integration_submit_subscription");
            formData.append("subscription_id", subscriptionIdInput.value);

            // Send the AJAX request
            fetch(couponIntegration.ajax_url, {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    loadingSpinner.style.display = "none";
                    console.log(data);
                    
                    if (data.success) {
                        errorMessageDiv.innerHTML = `<p style='color: green;'>${data.data.message}</p>`;
                    } else {
                        errorMessageDiv.innerHTML = `<p style='color: red;'>${data.data.message}</p>`;
                    }
                })
                .catch((error) => {
                    loadingSpinner.style.display = "none";
                    errorMessageDiv.innerHTML = "<p style='color: red;'>An error occurred. Please try again.</p>";
                });
        });
    }
});
