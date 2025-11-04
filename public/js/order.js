document.addEventListener("click", function (e) {
    const modal = document.querySelector(".modal.show");
    if (modal && !modal.querySelector(".modal-content").contains(e.target)) {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
    }
});

function updateOrder(orderId, status) {
    const badge = document.getElementById("statusBadge" + orderId);
    const input = document.getElementById("statusInput" + orderId);
    const form = document.getElementById("statusForm" + orderId);

    badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    badge.className = "badge dropdown-toggle";

    switch (status) {
        case "pending":
            badge.classList.add("text-bg-secondary");
            break;
        case "proses":
            badge.classList.add("text-bg-warning");
            break;
        case "selesai":
            badge.classList.add("text-bg-success");
            break;
        case "batal":
            badge.classList.add("text-bg-danger");
            break;
        default:
            badge.classList.add("text-bg-primary");
    }

    input.value = status;
    form.submit();
}

function updatePayment(orderId, status) {
    const badge = document.getElementById("paymentBadge" + orderId);
    const input = document.getElementById("paymentInput" + orderId);
    const form = document.getElementById("paymentForm" + orderId);

    badge.textContent = status
        .replace(/_/g, " ")
        .replace(/\b\w/g, (c) => c.toUpperCase());
    badge.className = "badge dropdown-toggle";

    switch (status) {
        case "belum_bayar":
            badge.classList.add("text-bg-secondary");
            break;
        case "menunggu_verifikasi":
            badge.classList.add("text-bg-warning");
            break;
        case "lunas":
            badge.classList.add("text-bg-success");
            break;
        case "gagal":
            badge.classList.add("text-bg-danger");
            break;
        default:
            badge.classList.add("text-bg-primary");
    }

    input.value = status;
    form.submit();
}
