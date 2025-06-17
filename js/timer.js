document.addEventListener('DOMContentLoaded', function() {
    const timerDisplay = document.getElementById('cancel-timer');
    const cancelButton = document.getElementById('cancel-button');
    
    if (timerDisplay && cancelButton) {
        let timeLeft = 300; 

        const countdown = setInterval(function() {
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerDisplay.textContent = "Waktu Habis";
                cancelButton.disabled = true;
                cancelButton.style.opacity = '0.5';
            } else {
                const minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                
                seconds = seconds < 10 ? '0' + seconds : seconds;
                
                timerDisplay.textContent = `0${minutes}:${seconds}`;
                timeLeft--;
            }
        }, 1000);

        cancelButton.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')) {
                alert('Janji temu Anda telah dibatalkan.');
                clearInterval(countdown);
                timerDisplay.textContent = "Dibatalkan";
                cancelButton.disabled = true;
                cancelButton.style.display = 'none'; 
            }
        });
    }
});