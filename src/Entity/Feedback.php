<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{

#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id_feedback = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

public function getIdFeedback(): ?int
{
return $this->id_feedback;
}

public function getMessage(): ?string
{
return $this->message;
}

public function setMessage(string $message): self
{
$this->message = $message;

return $this;
}

public function getCreatedAt(): ?\DateTimeInterface
{
return $this->created_at;
}

public function setCreatedAt(\DateTimeInterface $created_at): self
{
$this->created_at = $created_at;

return $this;
}
}
