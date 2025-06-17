document.addEventListener('DOMContentLoaded', function () {
    let purposeChoicesInstance = null;

    const modernSelects = document.querySelectorAll('.modern-select');

    modernSelects.forEach(selectElement => {
        let config;

        if (selectElement.id === 'purpose' || selectElement.id === 'arrival_method') {
            config = {
                searchEnabled: false,
                itemSelectText: '',
                shouldSort: false,
            };
        } else {
            config = {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
            };
        }
        
        const choicesInstance = new Choices(selectElement, config);

        if (selectElement.id === 'purpose') {
            purposeChoicesInstance = choicesInstance;
        }
    });

    const purposeDropdown = document.getElementById('purpose');
    const datepickerWrapper = document.querySelector('.datepicker-wrapper');
    const dateInput = document.getElementById('date');

    if (purposeDropdown && datepickerWrapper && dateInput) {
        const toggleDatePicker = () => {
            if (purposeDropdown.value === 'transaction') {
                datepickerWrapper.style.display = 'none';
                dateInput.required = false;
            } else {
                datepickerWrapper.style.display = 'block';
                dateInput.required = true;
            }
        };
        purposeDropdown.addEventListener('change', toggleDatePicker);
        toggleDatePicker();
    }

    const whatsappInput = document.getElementById('whatsapp');

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
            if (cursorPos < prefix.length) { e.preventDefault(); }
            if (e.key === "Backspace" && cursorPos === prefix.length) { e.preventDefault(); }
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
    const purposeFromUrl = urlParams.get('purpose');

    if (purposeFromUrl === 'test_drive' && purposeChoicesInstance) {
        purposeChoicesInstance.setChoiceByValue('test_drive');
    }
});