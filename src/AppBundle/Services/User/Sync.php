<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-12-06
 * Time: 18:46
 */

namespace AppBundle\Services\User;


use AppBundle\Repository\NoteRepository;
use AppBundle\Repository\TeaFavouriteRepository;
use AppBundle\Repository\TeaRepository;

class Sync
{
    private $noteRepository;
    private $teaFavouriteRepository;
    private $teaRepository;

    public function __construct(NoteRepository $noteRepository, TeaRepository $teaRepository, TeaFavouriteRepository $teaFavouriteRepository)
    {
        $this->noteRepository = $noteRepository;
        $this->teaRepository = $teaRepository;
        $this->teaFavouriteRepository = $teaFavouriteRepository;
    }

    public function execute($user, $params) {
        if (array_key_exists('notes', $params)) {
            foreach ($params['notes'] as $note) {
                $this->noteRepository->writeNote($note, $user);
            }
        }

        if (array_key_exists('favourite_teas', $params)) {
            foreach ($params['favourite_teas'] as $favTea) {
                $tea = $this->teaRepository->find($favTea);
                if (!$this->teaFavouriteRepository->getFavouriteTea($user, $tea))
                    $this->teaFavouriteRepository->putFavouriteTea($user, $tea);
            }
        }
        return ['success' => true];
    }

}