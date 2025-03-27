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
        icon.src = "/img/icons8-hide-password-50.png"; 
        SoH = 0;
    } else {
        passwordInput.type = "text";
        icon.src = "/img/icons8-eye-50.png";
        SoH = 1;
    }
}
