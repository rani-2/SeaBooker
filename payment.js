function showForm(paymentType) {
    // Hide all forms
    document.querySelectorAll('.form-container').forEach(form => {
        form.style.display = 'none';
    });

    // Show the selected form
    document.getElementById(paymentType).style.display = 'block';

    // Remove active class from all buttons
    document.querySelectorAll('.methods button').forEach(button => {
        button.classList.remove('active');
    });

    // Add active class to the clicked button
    event.target.classList.add('active');
}