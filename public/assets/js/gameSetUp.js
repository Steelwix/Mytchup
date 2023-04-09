var firstChampion = document.getElementById('firstChampion');
var secondChampion = document.getElementById('secondChampion');
var gameWon = document.getElementById('game_won');
var matchupWon = document.getElementById('matchup_won');
var save = document.getElementById('save');



firstChampion.addEventListener('change', function () {
    console.log(this.value);
    if (this.value !== '- Select -') {
        secondChampion.classList.remove('d-none');
    } else {
        secondChampion.classList.add('d-none');
    }
});
secondChampion.addEventListener('change', function () {
    console.log(this.value);
    if (this.value !== '- Select -') {
        gameWon.classList.remove('d-none');
    } else {
        gameWon.classList.add('d-none');
    }
});
gameWon.addEventListener('change', function () {
    console.log(this.value);
    if (this.value !== '- Select -') {
        matchupWon.classList.remove('d-none');
    } else {
        matchupWon.classList.add('d-none');
    }
});
matchupWon.addEventListener('change', function () {
    console.log(this.value);
    if (this.value !== '- Select -') {
        save.classList.remove('d-none');
    } else {
        save.classList.add('d-none');
    }
});
