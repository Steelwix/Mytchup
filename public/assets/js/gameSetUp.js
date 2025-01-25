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
// dataAboutChampion.addEventListener('change', () => {
//     // When the value changes, use AJAX to send a request to your Symfony controller
//     const selectedOption = dataAboutChampion.options[dataAboutChampion.selectedIndex];
//     const pickedChampion = selectedOption.text;
//     handleChampionChange();
//     const xhr = new XMLHttpRequest();
//     xhr.open('POST', '/ajax/my-pick');
//     xhr.setRequestHeader('Content-Type', 'application/json');
//     xhr.send(JSON.stringify({champion: pickedChampion}));
//     xhr.onload = function () {
//         if (xhr.status === 200) {
//             // Receive the response from the Symfony controller and update the view
//             const responseData = JSON.parse(xhr.response);
//             const champion = JSON.parse(responseData.champion);
//             // Update the view with the responseData
//             const championDetailsDiv = document.getElementById('champion-stats');
//             championDetailsDiv.innerHTML = `
//             <h5>${champion.name} rates : </h5>
//
//             <p>WinRate :
//                 ${responseData.pickWinRate}%</p>
//             <p>Lane Domination :
//                 ${responseData.pickLaneWinRate}%</p>
//
//             <p>overallRate :
//                 ${responseData.pickOverallRate}%</p>
//             `;
//             const championRawStat = document.getElementById('champion-raw-stats');
//             championRawStat.innerHTML = `
//             <h5>${champion.name} raw stats : </h5>
//
//             <p>WonGames :
//                 ${responseData.pickWonGames} / ${responseData.pickTotalGames} </p>
//             <p>Lane Domination :
//                 ${responseData.pickWonLanes} / ${responseData.pickTotalLanes}</p>
//             `;
//             const bestMatchupsList = document.getElementById('bestMatchupsList');
//             var backPoint = '';
//             responseData.bestMatchups.forEach(function (bestMatchup) {
//                 bestMatchupsList.innerHTML = backPoint + `
//                 <div class="col-1">
// <p>${ bestMatchup.champion }</p>
// <p>${ bestMatchup.win_rate } %</p>
// <p> as ${ bestMatchup.playing }</p>
// </div>
//                `;
//                 backPoint = bestMatchupsList.innerHTML
//             });
//         }
//     }
// });
const dataAboutChampion = document.getElementById('push_new_stat_form_firstChampion');
const dataAboutEncounter = document.getElementById('push_new_stat_form_secondChampion');

// Fonction à appeler à chaque changement
const handleChampionChange = () => {

    // Lorsque la valeur change, on utilise AJAX pour envoyer une requête au contrôleur Symfony
    const selectedEncounter = dataAboutEncounter.options[dataAboutEncounter.selectedIndex];
    const EncounterChamp = selectedEncounter.text;
    const selectedPick = dataAboutChampion.options[dataAboutChampion.selectedIndex];
    const PickChamp = selectedPick.text;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/ajax/make-stats');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({ encounter: EncounterChamp, pick: PickChamp }));

    xhr.onload = function () {
        if (xhr.status === 200) {
            // Recevoir la réponse du contrôleur Symfony et mettre à jour la vue
            const responseData = JSON.parse(xhr.response);
            console.log(responseData);
            const bestMatchups = JSON.parse(responseData.bestMatchups);
            console.log(bestMatchups);

            const bestMatchupsList = document.getElementById('bestMatchupsList');
            const template = document.getElementById('matchupTemplate');
            document.getElementById('bestMatchupsList').innerHTML = '';

            bestMatchups.forEach(function (bestMatchup) {
                const clone = template.content.cloneNode(true);
                clone.querySelector('.opponent-name').textContent = bestMatchup.opponent.name;
                clone.querySelector('.win-rate').textContent = `${(bestMatchup.wonGames / bestMatchup.totalGames * 100).toFixed(2)}%`;
                clone.querySelector('.pick-info').textContent = `${bestMatchup.pick.champion.name}`;
                clone.querySelector('.total-played').textContent = `(${bestMatchup.totalGames} games)`;
                bestMatchupsList.appendChild(clone);
            });
        }
    }
};

// Ajouter l'écouteur d'événements pour le changement des deux éléments
dataAboutChampion.addEventListener('change', handleChampionChange);
dataAboutEncounter.addEventListener('change', handleChampionChange);

// function handleChampionChange() {
//
//     const selectedPicked = dataAboutChampion.options[dataAboutChampion.selectedIndex];
//     const pickedChamp = selectedPicked.text;
//     const selectedEncounter = dataAboutEncounter.options[dataAboutEncounter.selectedIndex];
//     const encounterChamp = selectedEncounter.text;
//     console.log(pickedChamp);
//     console.log(encounterChamp);
//     if (pickedChamp != '- Select -' && encounterChamp != '- Select -') {
//         const xhr = new XMLHttpRequest();
//         xhr.open('POST', '/ajax/my-matchup');
//         xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//         xhr.send(JSON.stringify({pick: pickedChamp, encounter: encounterChamp}));
//         xhr.onload = function () {
//             if (xhr.status === 200) {
//                 const responseData = JSON.parse(xhr.response);
//                 console.log(responseData);
//                 const encounter = JSON.parse(responseData.encounter);
//                 const pick = JSON.parse(responseData.pick);
//                 // Update the view with the responseData
//                 const EncounterDetailsDiv = document.getElementById('matchup-stats');
//                 EncounterDetailsDiv.innerHTML = `
//             <h5>${pick.name} Versus ${encounter.name} matchup % rates : </h5>
//
//             <p>WinRate :
//                 ${responseData.winRate}%</p>
//             <p>Lane Domination :
//                 ${responseData.winLaneRate}%</p>
//
//             <p>overallRate :
//                 ${responseData.overallWinrate}%</p>
//             `;
//                 const EncounterRawStat = document.getElementById('matchup-raw-stats');
//                 EncounterRawStat.innerHTML = `
//             <h5>${pick.name} Versus ${encounter.name} matchup rates : </h5>
//
//             <p>WonGames :
//                 ${responseData.wonGames} / ${responseData.totalGames} </p>
//             <p>Lane Domination :
//                 ${responseData.wonLanes} / ${responseData.totalGames}</p>
//             `;
//             }
//
//         };
//
//     }
// }
