<?php

namespace App\Services;

use DateTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LocationService
{
    public function handleRecouvrements()
    {
        // Utiliser une clé de cache pour s'assurer que le traitement ne se fait qu'une fois par jour
        $cacheKey = 'recouvrements_processed';

        // Vérifier si le cache existe déjà
        if (Cache::has($cacheKey)) {
            return; // Ne rien faire si l'opération a déjà été effectuée
        }

        $locations = DB::table('location')
            ->join('locataire', 'locataire.id_locataire', '=', 'location.id_locataire')
            ->join('appartements', 'location.id_appartement', '=', 'appartements.id_appartement')
            ->join('residences', 'appartements.id_residence', '=', 'residences.id_residence')
            ->where('location.status_location', '=', 0)
            ->where('location.status','=','active')
            ->get();

        foreach ($locations as $location) {
            $existRecouvrement = DB::table('recouvrements')
                ->where('id_location', '=', $location->id_location)
                ->orderByDesc('date_recouvrements')
                ->limit(1)
                ->first();

            $dateDernierRecouvrement = $existRecouvrement
                ? $existRecouvrement->date_recouvrements
                : $location->date_emmenagement;

            $dateAmenagement = DateTime::createFromFormat('Y-m-d', $dateDernierRecouvrement);
            $endOfMonth = (clone $dateAmenagement)->modify('last day of this month');

            if (new DateTime() >= $endOfMonth) {

                $nextMonthDate = (clone $dateAmenagement)
                    ->modify('first day of next month')
                    ->setDate(
                        (int) $dateAmenagement->format('Y'),
                        (int) $dateAmenagement->format('m') + 1,
                        5
                    );

                $codeRecouvrement = genererCode('recouvrement');

                DB::table('recouvrements')->insert([
                    'id_location' => $location->id_location,
                    'code_recouvrement' => $codeRecouvrement,
                    'date_recouvrements' => $nextMonthDate->format('Y-m-d'),
                ]);
            }
        }

        // Mettre en cache l'état de traitement pour une durée de 24 heures
        Cache::put($cacheKey, true, now()->addDay());
    }
}
