// Dinamic login functionality for Chessistem
const container = document.getElementById("container");
const registerbtn = document.getElementById("register");
const loginbtn = document.getElementById("login");

registerbtn?.addEventListener("click", () => {
    container?.classList.add("active");
});

loginbtn?.addEventListener("click", () => {
    container?.classList.remove("active");
});

var SoH = 0; 
function passSoH() {
    let passwordInput = document.getElementById("password");
    let icon = document.getElementById("pass-icon");

    if (!passwordInput || !icon) return;

    if (SoH === 1) {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
        SoH = 0;
    } else {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
        SoH = 1;
    }
}

// Export for Vite compatibility
export { passSoH };
