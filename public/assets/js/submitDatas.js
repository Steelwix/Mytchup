document.addEventListener('DOMContentLoaded', function () {
    var submitButtons = document.querySelectorAll('.submit'); // Select all elements with class 'submit'

    submitButtons.forEach(function (submit) {
        submit.addEventListener('click', function () {
            var form = submit.closest('form'); // Get the parent form of the clicked submit button
            var col8Divs = form.querySelectorAll('.col-8');

            var contentArray = Array.from(col8Divs).map(function (div) {
                return div.innerHTML;
            });

            var inputsData = [];
            col8Divs.forEach(function (div) {
                var inputs = div.querySelectorAll('input');

                inputs.forEach(function (input) {
                    var inputId = input.id;
                    var inputMatchCase = input.name;
                    var inputValue = input.value;
                    var inputClass = div.classList.contains("matchup-data") ? div.classList[2] : "";
                    inputsData.push({ id: inputId, value: inputValue, matchCase: inputMatchCase, class: inputClass });
                });
            });

            var jsonData = JSON.stringify(contentArray);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/ajax/data-insert');
            xhr.setRequestHeader('Content-Type', 'application/json');
            console.log(inputsData);
            xhr.send(JSON.stringify(inputsData));
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const responseData = JSON.parse(xhr.response);
                    console.log(responseData);
                }
            };
        });
    });
});
