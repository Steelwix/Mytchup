<?php

namespace App\Service;

use App\Entity\Champion;
use App\Entity\Matchup;
use App\Entity\Pick;
use App\Entity\User;

class MatchupManager{

    public function __construct(){}

    public function getMatchupsFromUser(User $user){
        $picks = $user->getPicks();
        $matchups = [];
        foreach ($picks as $pick){
            $matchups[] = $pick->getMatchups();
        }
        return $matchups;
    }

    public function filterMatchupsByChampions(array $matchups, Champion $pick, Champion $opponent){
        foreach ($matchups as $matchupByPick){
            foreach ($matchupByPick as $matchup){
            $matchup_pick = $matchup->getPick();
            $matchup_pick_champion = $matchup_pick->getChampion();
            $matchup_opponent = $matchup->getOpponent();
            if($matchup_pick_champion === $pick && $matchup_opponent === $opponent){
                return $matchup;
            }
        }}
        return false;
    }

    public function filterNewMatchupsByChampions(array $matchups, string $pick, string $opponent){
        foreach ($matchups as $matchup){

                if($matchup['pick'] == $pick && $matchup['opponent'] == $opponent){
                    return $matchup;
                }
            }
        return false;
    }
}