var firstChampion = document.getElementById('firstChampion');
var secondChampion = document.getElementById('secondChampion');
var gameWon = document.getElementById('game_won');
var matchupWon = document.getElementById('matchup_won');
var save = document.getElementById('save');
var globalWin = document.getElementById('globalWin');

firstChampion.addEventListener('change', function () {
    console.log(this, secondChampion);
    if (this.value !== '- Select -' && secondChampion.value !== '- Select -') {
        gameWon.classList.remove('d-none');
    } else {
        gameWon.classList.add('d-none');
    }
});
secondChampion.addEventListener('change', function () {
    console.log(this.value, firstChampion.value);

    if (this.value !== '- Select -' && firstChampion.value !== '- Select -') {
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
            const bestMatchupsArray = Array.isArray(bestMatchups) ? bestMatchups : bestMatchups ? [bestMatchups] : [];

            bestMatchupsArray.forEach(function (bestMatchup) {
                const clone = template.content.cloneNode(true);
                console.log(bestMatchup);
                clone.querySelector('.opponent-name').textContent = bestMatchup.pick.champion.name;
                clone.querySelector('.win-rate').textContent = `${(bestMatchup.wonGames / bestMatchup.totalGames * 100).toFixed(2)}%`;
                clone.querySelector('.pick-info').textContent = `${bestMatchup.opponent.name}`;
                clone.querySelector('.total-played').textContent = `${bestMatchup.totalGames} games`;
                if (bestMatchup.wonLanes != null && bestMatchup.totalLanes != null) {
                    clone.querySelector('.lane-domination').textContent = `${bestMatchup.wonLanes} / ${bestMatchup.totalLanes} dominations`;
                } else {
                    clone.querySelector('.lane-domination').textContent = '';
                }
                bestMatchupsList.appendChild(clone);
            });
        }
    }
};

// Ajouter l'écouteur d'événements pour le changement des deux éléments
dataAboutChampion.addEventListener('change', handleChampionChange);
dataAboutEncounter.addEventListener('change', handleChampionChange);

