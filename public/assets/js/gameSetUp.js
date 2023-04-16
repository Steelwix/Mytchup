var firstChampion = document.getElementById('firstChampion');
var secondChampion = document.getElementById('secondChampion');
var gameWon = document.getElementById('game_won');
var matchupWon = document.getElementById('matchup_won');
var save = document.getElementById('save');
var globalWin = document.getElementById('globalWin');




secondChampion.addEventListener('change', function () {
    if (this.value !== '- Select -') {
        gameWon.classList.remove('d-none');
    } else {
        gameWon.classList.add('d-none');
    }
});
gameWon.addEventListener('change', function () {
    if (this.value !== '- Select -') {
        matchupWon.classList.remove('d-none');
    } else {
        matchupWon.classList.add('d-none');
    }
});
matchupWon.addEventListener('change', function () {
    if (this.value !== '- Select -') {
        save.classList.remove('d-none');
    } else {
        save.classList.add('d-none');
    }
});

// Attach an event listener to the form field "newGameStat.firstChampion"
const dataAboutChampion = document.getElementById('push_new_stat_form_firstChampion');
dataAboutChampion.addEventListener('change', () => {
    // When the value changes, use AJAX to send a request to your Symfony controller
    const selectedOption = dataAboutChampion.options[dataAboutChampion.selectedIndex];
    const displayedValue = selectedOption.text;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/ajax/my-pick');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({ champion: displayedValue }));
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Receive the response from the Symfony controller and update the view
            const responseData = JSON.parse(xhr.response);
            console.log(responseData);
            const champion = JSON.parse(responseData.champion);
            console.log(champion);
            // Update the view with the responseData
            const championDetailsDiv = document.getElementById('champion-stats');
            championDetailsDiv.innerHTML = `
            <h5>${champion.name} rates : </h5>

            <p>WinRate :
                ${responseData.pickWinRate}%</p>
            <p>Lane Domination :
                ${responseData.pickLaneWinRate}%</p>

            <p>overallRate :
                ${responseData.pickOverallRate}%</p>
            `;
            const championRawStat = document.getElementById('champion-raw-stats');
            championRawStat.innerHTML = `
            <h5>${champion.name} raw stats : </h5>

            <p>WonGames :
                ${responseData.pickWonGames} / ${responseData.pickTotalGames} </p>
            <p>Lane Domination :
                ${responseData.pickWonLanes} / ${responseData.pickTotalLanes}</p>
            `;
        }
    }
});
const dataAboutEncounter = document.getElementById('push_new_stat_form_secondChampion');
dataAboutEncounter.addEventListener('change', () => {
    // When the value changes, use AJAX to send a request to your Symfony controller
    const selectedOption = dataAboutEncounter.options[dataAboutEncounter.selectedIndex];
    const displayedValue = selectedOption.text;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/ajax/my-encounter');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({ encounter: displayedValue }));
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Receive the response from the Symfony controller and update the view
            const responseData = JSON.parse(xhr.response);
            console.log(responseData);
            const encounter = JSON.parse(responseData.encounter);
            console.log(encounter);
            // Update the view with the responseData
            const EncounterDetailsDiv = document.getElementById('encounter-stats');
            EncounterDetailsDiv.innerHTML = `
            <h5>Versus ${encounter.name} rates : </h5>

            <p>WinRate :
                ${responseData.encounterWinRate}%</p>
            <p>Lane Domination :
                ${responseData.encounterLaneWinRate}%</p>

            <p>overallRate :
                ${responseData.encounterOverallRate}%</p>
            `;
            const EncounterRawStat = document.getElementById('encounter-raw-stats');
            EncounterRawStat.innerHTML = `
            <h5>Versus ${encounter.name} raw stats : </h5>

            <p>WonGames :
                ${responseData.encounterWonGames} / ${responseData.encounterTotalGames} </p>
            <p>Lane Domination :
                ${responseData.encounterWonLanes} / ${responseData.encounterTotalLanes}</p>
            `;
        }
    }
});