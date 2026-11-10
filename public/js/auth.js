document.addEventListener("DOMContentLoaded", function () {
    const toggleButtons = document.querySelectorAll(".toggle-password");

    toggleButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            const targetId = button.dataset.target;
            const input = document.getElementById(targetId);

            if (!input) return;

            const isPassword = input.type === "password";

            input.type = isPassword ? "text" : "password";

            button.setAttribute(
                "aria-label",
                isPassword ? "Sembunyikan kata sandi" : "Tampilkan kata sandi",
            );
        });
    });
});
