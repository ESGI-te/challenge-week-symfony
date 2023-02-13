<?php

namespace App\Service;

use App\Entity\Music;
use App\Utilities\Constants;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MusicService
{
    public function __construct(string $musicDirectory, string $musicCoverDirectory, Security $security, EntityManagerInterface $em) {
        $this->musicDirectory = $musicDirectory;
        $this->musicCoverDirectory = $musicCoverDirectory;
        $this->security = $security;
        $this->em = $em;
    }

    public function upload(Music $music, UploadedFile $audio, UploadedFile $cover) {

        $audioExtension = $audio->guessExtension();
        $coverExtension = $cover->guessExtension();

        if(!$audioExtension || !in_array($audioExtension, Constants::MUSIC_EXTENSIONS)) {
            throw new \Exception('Invalid audio extension');
        }
        if(!$coverExtension || !in_array($coverExtension, Constants::IMG_EXTENSIONS)) {
            throw new \Exception('Invalid image extension');
        }
        $audioOriginalFilename = pathinfo($audio->getClientOriginalName(), PATHINFO_FILENAME);
        $audioHash = bin2hex(random_bytes(10));
        $audioName = $audioOriginalFilename . '-' . $audioHash . '.' . $audioExtension;

        $coverOriginalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
        $coverHash = bin2hex(random_bytes(10));
        $coverName = $coverOriginalFilename . '-' . $coverHash . '.' . $audioExtension;

        $audio->move($this->musicDirectory, $audioName);
        $cover->move($this->musicCoverDirectory, $coverName);

        $date = new DateTimeImmutable('now');
        $duration = $this->getDuration($audioName);
        $music->setDuration($duration);
        $music->setCreatedAt($date);
        $music->setUserId($this->security->getUser());
        $music->setAudioUrl($audioName);
        $music->setBackgroundImg($coverName);

        $this->em->persist($music);
        $this->em->flush();
    }

    public function getDuration($audioName): int | null
    {
        $audio = $this->getAudioFile($audioName);
        $getID3 = new \getID3();
        $audioFile = $getID3->analyze($audio);
        if(!isset($audioFile['playtime_seconds'])) {
            throw new \Exception('Error during audio file analyse');
        }
        return $audioFile['playtime_seconds'];
    }

    public function getAudioFile($filename)
    {
        $finder = new Finder();
        $finder->files()->in($this->musicDirectory)->name($filename);

        foreach ($finder as $file) {
            return $file;
        }

        return null;
    }
}