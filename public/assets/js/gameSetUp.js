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
    const pickedChampion = selectedOption.text;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/ajax/my-pick');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({ champion: pickedChampion }));
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Receive the response from the Symfony controller and update the view
            const responseData = JSON.parse(xhr.response);
            const champion = JSON.parse(responseData.champion);
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
    const EncounterChamp = selectedOption.text;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/ajax/my-encounter');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({ encounter: EncounterChamp }));
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Receive the response from the Symfony controller and update the view
            const responseData = JSON.parse(xhr.response);
            const encounter = JSON.parse(responseData.encounter);
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
function handleChampionChange() {

    console.log(dataAboutChampion);
    console.log(dataAboutEncounter);
    const selectedPicked = dataAboutChampion.options[dataAboutChampion.selectedIndex];
    const pickedChamp = selectedPicked.text;
    const selectedEncounter = dataAboutEncounter.options[dataAboutEncounter.selectedIndex];
    const encounterChamp = selectedEncounter.text;
    console.log(pickedChamp);
    console.log(encounterChamp);
    // Create XMLHttpRequest object
    const xhr = new XMLHttpRequest();

    // Configure and send AJAX request
    xhr.open('POST', '/ajax/my-matchup');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(`firstChampion=${pickedChamp}&secondChampion=${encounterChamp}`);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const responseData = JSON.parse(xhr.response);
            console.log(responseData);
        } else {
            // Handle error from controller
        }
    };

}

// Add event listener to both elements
document.querySelectorAll('#push_new_stat_form_firstChampion, #push_new_stat_form_secondChampion').forEach(element => {
    element.addEventListener('change', handleChampionChange);
});