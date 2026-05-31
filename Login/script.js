function toggleTheme() {
  document.body.classList.toggle("dark-mode");
}

function togglePassword() {
  const pwdInput = document.getElementById("password");
  const icon = document.getElementById("togglePwdIcon");

  if (pwdInput.type === "password") {
    pwdInput.type = "text";
    icon.src = "icons/eye-off.png";
  } else {
    pwdInput.type = "password";
    icon.src = "icons/eye.png";
  }
}

function validateForm() {
  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();

  if (!username || !password) {
    alert("Please fill in all fields.");
    return false;
  }
  return true;
}
