<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 06.06.18
 * Time: 18:47
 */

namespace Jinya\Entity\Video;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\HistoryEnabledEntity;
use Jinya\Entity\SlugEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="video")
 */
class Video extends HistoryEnabledEntity
{
    use SlugEntity;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $video;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $poster;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Video\VideoPosition", mappedBy="video")
     */
    private $positions;

    /**
     * Video constructor.
     */
    public function __construct()
    {
        $this->positions = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    /**
     * @param Collection $positions
     */
    public function setPositions(Collection $positions): void
    {
        $this->positions = $positions;
    }

    /**
     * @return null|string
     */
    public function getPoster(): ?string
    {
        return $this->poster;
    }

    /**
     * @param null|string $poster
     */
    public function setPoster(?string $poster): void
    {
        $this->poster = $poster;
    }

    /**
     * @return null|string
     */
    public function getVideo(): ?string
    {
        return $this->video;
    }

    /**
     * @param null|string $video
     */
    public function setVideo(?string $video): void
    {
        $this->video = $video;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Specify data which should be serialized to JSON
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'id' => $this->id,
            'video' => $this->video,
            'createdAt' => $this->getCreatedAt(),
            'createdBy' => $this->getCreator(),
            'lastUpdatedAt' => $this->getLastUpdatedAt(),
            'updatedBy' => $this->getUpdatedBy(),
        ];
    }
}
