 document.addEventListener('DOMContentLoaded', function() {
    const ctx1 = document.getElementById('barChart-1').getContext('2d');
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [{
          label: 'Sales',
          data: [12, 19, 3, 5, 2],
          backgroundColor: [
            'rgba(75, 192, 192,F 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)'
          ],
          borderColor: [
            'rgba(75, 192, 192, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  });



   document.addEventListener('DOMContentLoaded', function() {
    const ctx2 = document.getElementById('barChart-2').getContext('2d');
    new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [{
          label: 'Sales',
          data: [12, 19, 3, 5, 2],
          backgroundColor: [
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)'
          ],
          borderColor: [
            'rgba(75, 192, 192, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  });

  document.querySelectorAll('.change-status').forEach(item => {
  item.addEventListener('click', event => {
    event.preventDefault();
    const newStatus = item.getAttribute('data-status');
    const statusBadge = item.closest('tr').querySelector('.badge');

    if (newStatus === 'Available') {
      statusBadge.classList.remove('bg-warning');
      statusBadge.classList.add('bg-success');
      statusBadge.textContent = 'Available';
    } else if (newStatus === 'Busy') {
      statusBadge.classList.remove('bg-success');
      statusBadge.classList.add('bg-warning');
      statusBadge.textContent = 'Busy';
    }
  });
});

document.addEventListener('DOMContentLoaded', function() {
    const statusElements = document.querySelectorAll('.change-status');
    statusElements.forEach(element => {
      element.addEventListener('click', function(event) {
        event.preventDefault();
        const newStatus = this.getAttribute('data-status');
        const statusLabel = this.closest('tr').querySelector('.status-label');
        statusLabel.textContent = newStatus;
        statusLabel.className = 'badge status-label ' + (newStatus === 'Available' ? 'bg-success' : 'bg-warning');
      });
    });
  });