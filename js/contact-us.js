document.addEventListener("DOMContentLoaded", function () {
    let purposeChoicesInstance = null;
    const modernSelects = document.querySelectorAll(".modern-select");

    modernSelects.forEach((selectElement) => {
        let config;

        if (
            selectElement.id === "purpose" ||
            selectElement.id === "arrival_method"
        ) {
            config = {
                searchEnabled: false,
                itemSelectText: "",
                shouldSort: false,
            };
        } else {
            config = {
                searchEnabled: true,
                itemSelectText: "",
                shouldSort: false,
            };
        }

        const choicesInstance = new Choices(selectElement, config);

        if (selectElement.id === "purpose") {
            purposeChoicesInstance = choicesInstance;
        }
    });

    const dateInput = document.getElementById("date");
    const datepickerIconTrigger = document.getElementById(
        "datepickerIconTrigger"
    );
    const isMobile = window.innerWidth <= 768;
    const flatpickrContainer = document.getElementById("flatpickr-modal");

    if (dateInput) {
        const fp = flatpickr(dateInput, {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            maxDate: new Date().fp_incr(6),
            altInput: true,
            altFormat: "d F Y H:i",
            altInputClass: "modern-datepicker",
            disableMobile: true,
            defaultDate: dateInput.value || null,
            placeholder: "Pilih Jadwal",
            appendTo: isMobile && flatpickrContainer ? flatpickrContainer : undefined,

            onReady: function (selectedDates, dateStr, instance) {
                if (instance.altInput) {
                    instance.altInput.placeholder = "Pilih Jadwal";
                }

                if (datepickerIconTrigger) {
                    datepickerIconTrigger.addEventListener("click", function () {
                        instance.open();
                    });
                }
            },
        });
    }

    const whatsappInput = document.getElementById("whatsapp");

    if (whatsappInput) {
        const prefix = "+62 ";

        whatsappInput.addEventListener("focus", () => {
            if (!whatsappInput.value) {
                whatsappInput.value = prefix;
                setTimeout(() => {
                    whatsappInput.setSelectionRange(prefix.length, prefix.length);
                }, 0);
            }
        });

        whatsappInput.addEventListener("keydown", (e) => {
            const cursorPos = whatsappInput.selectionStart;
            if (cursorPos < prefix.length) e.preventDefault();
            if (e.key === "Backspace" && cursorPos === prefix.length)
                e.preventDefault();
        });

        whatsappInput.addEventListener("input", () => {
            if (!whatsappInput.value.startsWith(prefix)) {
                whatsappInput.value = prefix;
            }
        });

        whatsappInput.addEventListener("blur", () => {
            if (whatsappInput.value.trim() === prefix.trim()) {
                whatsappInput.value = "";
            }
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const purposeFromUrl = urlParams.get("purpose");

    if (purposeFromUrl === "test_driver" && purposeChoicesInstance) {
        purposeChoicesInstance.setChoiceByValue("test_driver");
    }
});


let map, marker;
const modalMapElementId = 'modal-map';
const latitudeInput = document.getElementById("latitude");
const longitudeInput = document.getElementById("longitude");
const modalLatitudeDisplay = document.getElementById("modal-latitude-display");
const modalLongitudeDisplay = document.getElementById("modal-longitude-display");
const imageModal = document.getElementById("imageModal");


function openLocationModal() {
    imageModal.style.display = "flex";
    
    let currentLat = parseFloat(latitudeInput.value);
    let currentLon = parseFloat(longitudeInput.value);

    const defaultLat = -6.5684;
    const defaultLon = 107.7562;

    if (isNaN(currentLat) || isNaN(currentLon)) {
        currentLat = defaultLat;
        currentLon = defaultLon;
    }

    // Leaflet map needs its container to be visible before it can calculate its size correctly.
    // Use a small timeout to ensure the modal is fully displayed before initializing/resizing the map.
    setTimeout(() => {
        initMap(currentLat, currentLon);
        if (map) {
            map.invalidateSize(); 
        }
    }, 100);
}

function closeLocationModal() {
    imageModal.style.display = "none";
}

function saveLocationAndCloseModal() {
    if (marker) {
        const pos = marker.getLatLng();
        latitudeInput.value = pos.lat;
        longitudeInput.value = pos.lng;
    }
    closeLocationModal();
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    } else {
        alert("Geolocation tidak didukung oleh browser Anda.");
        openLocationModal(); 
    }
}

function success(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;

    latitudeInput.value = lat;
    longitudeInput.value = lon;

    openLocationModal();
    setTimeout(() => {
        initMap(lat, lon);
        if (map) {
            map.invalidateSize(); 
        }
    }, 100);
}

function initMap(lat, lon) {
    if (!document.getElementById(modalMapElementId)) {
        console.error("Container peta dengan ID '" + modalMapElementId + "' tidak ditemukan.");
        return;
    }

    if (!map) {
        map = L.map(modalMapElementId).setView([lat, lon], 16);
        L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([lat, lon], { draggable: true }).addTo(map);

        marker.on("dragend", function () {
            const pos = marker.getLatLng();
            modalLatitudeDisplay.textContent = pos.lat.toFixed(8);
            modalLongitudeDisplay.textContent = pos.lng.toFixed(8);
        });
    } else {
        map.setView([lat, lon], 16);
        marker.setLatLng([lat, lon]);
    }
    
    modalLatitudeDisplay.textContent = lat.toFixed(8);
    modalLongitudeDisplay.textContent = lon.toFixed(8);
}

function error() {
    alert("Tidak bisa mendapatkan lokasi Anda saat ini. Pastikan Anda telah mengizinkan akses lokasi. Anda akan diarahkan ke peta untuk memilih lokasi secara manual.");
    openLocationModal();
}