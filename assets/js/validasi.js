document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        const checkedGejala = document.querySelectorAll('input[name="gejala[]"]:checked');
        if (checkedGejala.length === 0) {
            alert('Silakan pilih minimal satu gejala sebelum melakukan diagnosa.');
            e.preventDefault();
        }
    });
});
