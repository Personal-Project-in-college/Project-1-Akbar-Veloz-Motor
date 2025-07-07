  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <div class="datepicker-wrapper">
      <label for="date">Tentukan Jadwal</label>
      <div class="datepicker-container">
          <input class="modern-datepicker" type="text" id="date" name="order_date" required
              value="<?= htmlspecialchars($form_order_date); ?>">
          <div class="datepicker-icon" id="datepickerIconTrigger">
              <svg viewBox="0 0 24 24" width="20" height="20">
                  <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z" fill="currentColor" />
              </svg>
          </div>
      </div>
  </div>