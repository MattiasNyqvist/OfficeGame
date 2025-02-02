document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("registerForm");
    const usernameInput = document.getElementById("username");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const teamSelect = document.getElementById("team");
    const registerButton = document.getElementById("registerButton");
    const cancelButton = document.getElementById("cancelButton");
    const alertBox = document.getElementById("alertBox");
    const usernameMessage = document.getElementById("usernameMessage");
    const passwordMessage = document.getElementById("passwordMessage");

    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Function to check if all required fields are valid
    function validateForm() {
        const isUsernameValid = usernameInput.value.trim() !== "" && usernameMessage.innerHTML === "";
        const isEmailValid = emailInput.value.trim() !== "";
        const isPasswordValid = passwordInput.value.trim() !== "" && passwordInput.value === confirmPasswordInput.value;
        const isTeamSelected = teamSelect.value !== "";
        
        registerButton.disabled = !(isUsernameValid && isEmailValid && isPasswordValid && isTeamSelected);

        // Update tooltip dynamically
        registerButton.setAttribute("title", registerButton.disabled ? "All fields must be filled in correctly to register" : "");
        bootstrap.Tooltip.getInstance(registerButton)?.dispose();
        bootstrap.Tooltip.getOrCreateInstance(registerButton);
    }

    // Fetch teams from API and populate dropdown
    fetch("../api/get_teams.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Failed to fetch teams");
            }
            return response.json();
        })
        .then(data => {
            if (data.teams.length === 0) {
                console.warn("No teams found in database.");
            } else {
                data.teams.forEach(team => {
                    let option = document.createElement("option");
                    option.value = team.id;
                    option.textContent = team.name;
                    teamSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error("Error loading teams:", error);
        });

    // Check if username is available
    usernameInput.addEventListener("input", function () {
        fetch(`../api/check_username.php?username=${encodeURIComponent(usernameInput.value)}`)
            .then(response => response.json())
            .then(data => {
                usernameMessage.innerHTML = data.available ? "" : "Username is already taken";
                validateForm();
            });
    });

    // Validate password match
    confirmPasswordInput.addEventListener("input", function () {
        if (passwordInput.value !== confirmPasswordInput.value) {
            passwordMessage.innerHTML = "Passwords do not match";
        } else {
            passwordMessage.innerHTML = "";
        }
        validateForm();
    });

    // Enable register button when all fields are valid
    [usernameInput, emailInput, passwordInput, confirmPasswordInput, teamSelect].forEach(input => {
        input.addEventListener("input", validateForm);
    });

    // Handle form submission
    registerForm.addEventListener("submit", function (e) {
        e.preventDefault();

        let data = {
            username: usernameInput.value,
            password: passwordInput.value,
            email: emailInput.value,
            team_id: teamSelect.value
        };

        fetch("../api/register.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            alertBox.classList.remove("d-none", "alert-success", "alert-danger");
            alertBox.classList.add(data.message === "User registered successfully" ? "alert-success" : "alert-danger");
            alertBox.innerHTML = data.message;

            // Reset form if successful
            if (data.message === "User registered successfully") {
                registerForm.reset();
                validateForm(); // Ensure register button gets disabled
            }
        })
        .catch(error => {
            alertBox.classList.remove("d-none");
            alertBox.classList.add("alert-danger");
            alertBox.innerHTML = "An error occurred. Please try again.";
        });
    });

    // Reset form on cancel button click
    cancelButton.addEventListener("click", function () {
        registerForm.reset();
        alertBox.classList.add("d-none");
        usernameMessage.innerHTML = "";
        passwordMessage.innerHTML = "";
        registerButton.disabled = true;
    });
});
