var submit = document.getElementById('submit');
var form = document.getElementById('form');
var dataForm = form.querySelectorAll('.data-form');

submit.addEventListener('click', function () {
    var contentArray = Array.from(dataForm).map(function(div) {
        return div.innerHTML;
    });
    var inputsData = [];
    dataForm.forEach(function(div) {
        var inputs = div.querySelectorAll('input');

        inputs.forEach(function(input) {
            var inputId = input.id;
            var inputValue = input.value;

            inputsData.push({ id: inputId, value: inputValue });
        });
    });

    console.log(inputsData);
    var jsonData = JSON.stringify(contentArray);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/ajax/data-insert');
    xhr.setRequestHeader('Content-Type', 'application/json');
    // console.log(jsonData);
    xhr.send(JSON.stringify(inputsData));
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Receive the response from the Symfony controller and update the view
            const responseData = JSON.parse(xhr.response);
            // console.log(responseData);
}}});
pickList.addEventListener('change', function () {
    if (this.value !== '- Select -') {
        var champion = document.getElementById(this.value);
        champion.classList.remove('d-none');
    } else {
        gameWon.classList.add('d-none');
    }
});


