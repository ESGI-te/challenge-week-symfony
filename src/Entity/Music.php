<?php

namespace App\Entity;

use App\Repository\MusicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: MusicRepository::class)]
class Music
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 500)]
    private ?string $audio_url = null;

    #[ORM\Column]
    private ?float $duration = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $background_img = null;

    #[ORM\ManyToOne(inversedBy: 'music')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToMany(targetEntity: Playlist::class, mappedBy: 'music')]
    private Collection $playlists;

    #[ORM\OneToMany(mappedBy: 'music', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'music', targetEntity: Listening::class, orphanRemoval: true)]
    private Collection $listenings;

    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->listenings = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAudioUrl(): ?string
    {
        return $this->audio_url;
    }

    public function setAudioUrl(string $audio_url): self
    {
        $this->audio_url = $audio_url;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getBackgroundImg(): ?string
    {
        return $this->background_img;
    }

    public function setBackgroundImg(?string $background_img): self
    {
        $this->background_img = $background_img;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists->add($playlist);
            $playlist->addMusic($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->removeElement($playlist)) {
            $playlist->removeMusic($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setMusic($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getMusic() === $this) {
                $comment->setMusic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Listening>
     */
    public function getListenings(): Collection
    {
        return $this->listenings;
    }

    public function addListening(Listening $listening): self
    {
        if (!$this->listenings->contains($listening)) {
            $this->listenings->add($listening);
            $listening->setMusic($this);
        }

        return $this;
    }

    public function removeListening(Listening $listening): self
    {
        if ($this->listenings->removeElement($listening)) {
            // set the owning side to null (unless already changed)
            if ($listening->getMusic() === $this) {
                $listening->setMusic(null);
            }
        }

        return $this;
    }
}
