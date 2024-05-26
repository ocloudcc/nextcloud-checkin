document.addEventListener('DOMContentLoaded', function() {
    const checkinForm = document.getElementById('checkin-form');
    const messageDiv = document.getElementById('message');

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
                messageDiv.textContent = 'Check-in successful!';
                messageDiv.style.color = 'green';
            } else {
                messageDiv.textContent = 'An error occurred. Please try again later.';
                messageDiv.style.color = 'red';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.textContent = 'An error occurred. Please try again later.';
            messageDiv.style.color = 'red';
        });
    });
});
