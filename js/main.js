document.addEventListener('DOMContentLoaded', function() {
    const checkinForm = document.getElementById('checkin-form');
    const messageDiv = document.getElementById('message');
    document.getElementById('Check-in').innerText = t('checkin', 'Check-in');
    document.getElementById('Daily_Check-in').innerText = t('checkin', 'Daily Check-in');

    checkinForm.addEventListener('submit', function(event) {
        event.preventDefault();

        fetch(OC.generateUrl('/apps/checkin/check'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Requesttoken': OC.requestToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.textContent = data.message+'You have got '+data.rand_MB+'MB today.Your total extra space is '+data.totalExtraSpace+'!';
                messageDiv.style.color = 'green';
            } else {
                messageDiv.textContent = data.message;
                messageDiv.style.color = 'red';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.textContent = data.message;
            messageDiv.style.color = 'red';
        });
    });
});
