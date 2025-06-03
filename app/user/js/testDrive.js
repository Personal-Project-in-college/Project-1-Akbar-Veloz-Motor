// Whatsappp
// Saat input diklik atau difokuskan
whatsappInput.addEventListener("focus", () => {
    if (!isPrefixed) {
      whatsappInput.value = prefix;
      isPrefixed = true;
      whatsappInput.classList.add("touched");
  
      // Letakkan kursor setelah prefix
      setTimeout(() => {
        whatsappInput.setSelectionRange(prefix.length, prefix.length);
      }, 0);
    }
  });
  
  // Cegah menghapus/memasukkan karakter sebelum prefix
  whatsappInput.addEventListener("keydown", function (e) {
    if (
      isPrefixed &&
      ((whatsappInput.selectionStart <= prefix.length &&
        (e.key === "Backspace" || e.key === "Delete")) ||
        (whatsappInput.selectionStart < prefix.length && e.key.length === 1))
    ) {
      e.preventDefault();
      whatsappInput.setSelectionRange(prefix.length, prefix.length);
    }
  });
  
  // Jaga agar prefix tetap ada saat mengetik
  whatsappInput.addEventListener("input", () => {
    if (isPrefixed && !whatsappInput.value.startsWith(prefix)) {
      const raw = whatsappInput.value.replace(/[^0-9]/g, "");
      whatsappInput.value = prefix + raw;
    }
  });
  
  // Saat kehilangan fokus (blur)
  whatsappInput.addEventListener("blur", () => {
    const raw = whatsappInput.value.slice(prefix.length).trim();
  
    // Jika tidak ada angka setelah prefix, hapus semuanya
    if (isPrefixed && raw === "") {
      whatsappInput.value = "";
      isPrefixed = false;
      whatsappInput.classList.remove("touched");
    }
  });

  // Choices.js 
  document.addEventListener('DOMContentLoaded', function () {
    const vehicleSelect = document.getElementById('vehicle');
    const choices = new Choices(vehicleSelect, {
      searchEnabled: true,
      itemSelectText: '', // hilangkan teks pilih
      shouldSort: false   // biarkan urutan tetap
    });
  });