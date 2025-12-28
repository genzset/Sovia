function validateLogin() {
  let username = document.getElementById("username").value;
  let password = document.getElementById("password").value;
  if (username.trim() === "" || password.trim() === "") {
    alert("Username and password cannot be empty!");
    return false;
  }
  return true;
}

function validateRegister() {
  let username = document.getElementById("username").value;
  let password = document.getElementById("password").value;
  if (username.trim() === "" || password.trim() === "") {
    alert("Username and password cannot be empty!");
    return false;
  }
  if (username.length < 3) {
    alert("Username must be at least 3 characters!");
    return false;
  }
  return true;
}

function validateStatus() {
  const content = document.getElementById("content");
  if (!content) return true;

  if (content.value.trim() === "") {
    alert("Please enter some content!");
    return false;
  }
  return true;
}

function togglePostForm() {
  const form = document.getElementById("post-form");
  const overlay = document.getElementById("post-form-overlay");

  if (!form) return;

  const isVisible = form.style.display === "block";

  form.style.display = isVisible ? "none" : "block";

  if (overlay) {
    overlay.style.display = isVisible ? "none" : "block";
  }
}

function toggleComment(statusId) {
  let commentSection = document.getElementById("comment-" + statusId);
  commentSection.style.display = commentSection.style.display === "none" ? "block" : "none";
}
