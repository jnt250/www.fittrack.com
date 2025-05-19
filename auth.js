// Toggle password visibility
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.querySelector(`#${fieldId} + .toggle-password`);
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

// Form validation for login
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    if (!username || !password) {
        e.preventDefault();
        alert('Please fill in all fields');
        return false;
    }
    
   
// Form validation for login (no hardcoded credentials)
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (!username || !password) {
        e.preventDefault();
        alert('Please fill in all fields');
    }
});


});

// Form validation for signup
document.getElementById('signupForm')?.addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const terms = document.getElementById('terms').checked;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match');
        return false;
    }
    
    if (!terms) {
        e.preventDefault();
        alert('You must agree to the terms and conditions');
        return false;
    }
    
    // In a real app, this would be handled by server response
    alert('Account created successfully! Redirecting to login...');
    // In real app, would redirect after successful registration
    // window.location.href = 'login.html';
});

// Check if passwords match in real-time
document.getElementById('confirm_password')?.addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    const errorElement = this.nextElementSibling?.nextElementSibling;
    
    if (password && confirmPassword && password !== confirmPassword) {
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            const errorMsg = document.createElement('span');
            errorMsg.className = 'error-message';
            errorMsg.style.color = 'red';
            errorMsg.textContent = 'Passwords do not match';
            this.parentNode.appendChild(errorMsg);
        }
    } else if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.remove();
    }
});