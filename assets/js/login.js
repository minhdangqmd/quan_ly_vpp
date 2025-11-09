const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const showpBtn = document.querySelector('.sign-up-btn');
const hideBtn1 = document.querySelector('.close-btnX1');
const hideBtn2 = document.querySelector('.close-btnX2');


// mo sign in

if (showpBtn) {
    showpBtn.addEventListener("click", () =>{
        document.body.classList.toggle("show-popup");
    });
}

if (hideBtn1) {
    hideBtn1.addEventListener("click", () => {
        document.body.classList.remove("show-popup");
    });
}

if (hideBtn2) {
    hideBtn2.addEventListener("click", () => {
        document.body.classList.remove("show-popup");
    });
}


// chuyen sign up voi sign in
if (registerBtn) {
    registerBtn.addEventListener('click', () => {
        container.classList.add("active");
    });
}

if (loginBtn) {
    loginBtn.addEventListener('click', () => {
        container.classList.remove("active");
    });
}

