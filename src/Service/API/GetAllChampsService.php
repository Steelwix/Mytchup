<?php

namespace App\Service\API;

use App\Entity\Champion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetAllChampsService
{
    private $httpClient;
    private $em;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em)
    {
        $this->httpClient = $httpClient;
        $this->em = $em;
    }

    public function getAllChampions(): void
    {
        $version = '13.7.1';
        $language = 'en_US';
        $apiKey = 'RGAPI-188c0b8b-fe79-4505-b039-107e42c931ec';

        $url = sprintf('https://ddragon.leagueoflegends.com/cdn/%s/data/%s/champion.json', $version, $language);

        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'X-Riot-Token' => $apiKey,
            ],
        ]);

        $data = json_decode($response->getContent(), true);

        $championRepository = $this->em->getRepository(Champion::class);
        $storedChamps = $championRepository->findAll();
        $champInDatabase = array();
        foreach ($storedChamps as $stored) {
            $champInDatabase[] = $stored->getName();
        }
        $champNames = array_map(function ($champ) {
            return $champ['name'];
        }, $data['data']);

        $missingChamps = array_diff($champNames, $champInDatabase);

        if (count($missingChamps) !== 0) {
            foreach ($missingChamps as $missing) {
                $newChamp = new Champion;
                $newChamp->setName($missing);
                $this->em->persist($newChamp);
                $this->em->flush();
            }
        }
    }
}
