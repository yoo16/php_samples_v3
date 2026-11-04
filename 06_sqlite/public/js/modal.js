function openModal(card) {
    const d = card.dataset;
    document.getElementById("modalId").value = d.id;
    document.getElementById("modalImage").src = d.image;
    document.getElementById("modalTitle").textContent = d.title;
    document.getElementById("modalDate").textContent = d.date;
    document.getElementById("modalBody").textContent = d.body;
    const modal = document.getElementById("imageModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.classList.add("overflow-hidden");
}

function closeModal() {
    const modal = document.getElementById("imageModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    document.body.classList.remove("overflow-hidden");
}

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
});
