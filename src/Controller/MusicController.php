<?php

namespace App\Controller;

use App\Entity\Music;
use App\Form\UploadMusicType;
use App\Service\MusicService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/music', name: 'music_')]
class MusicController extends AbstractController
{
    #[Route('/upload', name: 'upload')]
    public function upload(Request $request, MusicService $musicService): Response
    {
        $music = new Music();
        $form = $this->createForm(UploadMusicType::class, $music);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $audio = $form->get('audio')->getData();
            $cover = $form->get('background_img')->getData();
            $musicService->upload($music, $audio, $cover);
        }

        return $this->render('music/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
