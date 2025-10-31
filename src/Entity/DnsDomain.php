<?php

namespace TencentCloudDnsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TencentCloudDnsBundle\Repository\DnsDomainRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: DnsDomainRepository::class)]
#[ORM\Table(name: 'tencent_cloud_dns_domain', options: ['comment' => '域名'])]
class DnsDomain implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: '域名不能为空')]
    #[Assert\Length(max: 32, maxMessage: '域名长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 32, unique: true, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    /** @var Collection<int, DnsRecord> */
    #[ORM\OneToMany(mappedBy: 'domain', targetEntity: DnsRecord::class)]
    private Collection $records;

    /** @var array<string, mixed> */
    #[Assert\Type(type: 'array', message: '上下文必须是数组')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    #[Assert\Type(type: 'bool', message: '有效性必须是布尔值')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    public function __toString(): string
    {
        return $this->name ?? 'Domain #' . $this->id;
    }

    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    /**
     * @return Collection<int, DnsRecord>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function addRecord(DnsRecord $record): static
    {
        if (!$this->records->contains($record)) {
            $this->records->add($record);
            $record->setDomain($this);
        }

        return $this;
    }

    public function removeRecord(DnsRecord $record): static
    {
        if ($this->records->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getDomain() === $this) {
                $record->setDomain(null);
            }
        }

        return $this;
    }

    /** @return array<string, mixed>|null */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /** @param array<string, mixed>|null $context */
    public function setContext(?array $context): void
    {
        $this->context = $context;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }
}
