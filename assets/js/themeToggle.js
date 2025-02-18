document.addEventListener("DOMContentLoaded", function () {
  const themeToggle = document.getElementById("themeToggle");
  const themeIcon = document.getElementById("themeIcon");
  const currentTheme = localStorage.getItem("theme");

  if (currentTheme === "dark") {
    document.body.classList.add("dark-mode");
    themeIcon.classList.replace("fa-moon", "fa-sun");
  }

  themeToggle.addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");
    if (document.body.classList.contains("dark-mode")) {
      localStorage.setItem("theme", "dark");
      themeIcon.classList.replace("fa-moon", "fa-sun");
    } else {
      localStorage.setItem("theme", "light");
      themeIcon.classList.replace("fa-sun", "fa-moon");
    }
  });
});
