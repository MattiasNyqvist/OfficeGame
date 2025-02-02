document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const loginButton = document.getElementById("loginButton");
    const alertBox = document.getElementById("alertBox");

    // Enable login button when fields are filled
    function validateForm() {
        loginButton.disabled = !(usernameInput.value.trim() !== "" && passwordInput.value.trim() !== "");
    }

    [usernameInput, passwordInput].forEach(input => {
        input.addEventListener("input", validateForm);
    });

    // Handle form submission
    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();

        let data = {
            username: usernameInput.value,
            password: passwordInput.value
        };

        fetch("../api/login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            alertBox.classList.remove("d-none", "alert-success", "alert-danger");
            alertBox.classList.add(data.success ? "alert-success" : "alert-danger");
            alertBox.innerHTML = data.message;

            if (data.success) {
                setTimeout(() => {
                    window.location.href = "../index.php"; // Redirect after success
                }, 1000);
            }
        })
        .catch(error => {
            alertBox.classList.remove("d-none");
            alertBox.classList.add("alert-danger");
            alertBox.innerHTML = "An error occurred. Please try again.";
        });
    });
});
