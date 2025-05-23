document.addEventListener("DOMContentLoaded", function () {
  // Select All Checkbox
  const selectAll = document.getElementById('select-all');
  const checkboxes = document.querySelectorAll('.select-row');

  if (selectAll) {
    selectAll.addEventListener('change', function () {
      checkboxes.forEach(cb => cb.checked = this.checked);
    });
  }

  // Search Filter
//   const searchInput = document.getElementById("search-input");
//   if (searchInput) {
//     searchInput.addEventListener("input", function () {
//       const filter = this.value.toLowerCase();
//       const rows = document.querySelectorAll("#productTable tbody tr");

//       rows.forEach(row => {
//         const vehicleCode = row.cells[1]?.textContent.toLowerCase() || "";
//         const brandModel = row.cells[2]?.textContent.toLowerCase() || "";
//         row.style.display = vehicleCode.includes(filter) || brandModel.includes(filter) ? "" : "none";
//       });
//     });
//   }

  // Pagination
  let currentPage = 1;
  const rowsPerPage = 5;
  const rows = document.querySelectorAll("#productTable tbody tr");
  const totalPages = Math.ceil(rows.length / rowsPerPage);
  const prevButton = document.getElementById("prev");
  const nextButton = document.getElementById("next");

  function updateTable() {
    rows.forEach((row, index) => {
      row.style.display = index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage ? "" : "none";
    });

    document.querySelectorAll("#pagination .page-item").forEach((item, index) => {
      item.classList.remove("active");
      if (index === currentPage) item.classList.add("active");
    });

    if (prevButton) prevButton.classList.toggle("disabled", currentPage === 1);
    if (nextButton) nextButton.classList.toggle("disabled", currentPage === totalPages);
  }

  if (prevButton && nextButton) {
    prevButton.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        updateTable();
      }
    });

    nextButton.addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        updateTable();
      }
    });

    updateTable();
  }
});
