var submit = document.getElementById('submit');
var form = document.getElementById('form');
var col8Divs = form.querySelectorAll('.col-8');


submit.addEventListener('click', function () {
    var contentArray = Array.from(col8Divs).map(function(div) {
        return div.innerHTML;
    });
    var inputsData = [];
    col8Divs.forEach(function(div) {
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
}}})