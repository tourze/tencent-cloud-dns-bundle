<?php

namespace TencentCloudDnsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use TencentCloudDnsBundle\Repository\DnsRecordRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: DnsRecordRepository::class)]
#[ORM\Table(name: 'tencent_cloud_dns_record', options: ['comment' => '域名解析记录'])]
class DnsRecord implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'records')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DnsDomain $domain = null;

    #[ORM\Column(length: 200, options: ['comment' => '前缀'])]
    private string $name;

    #[ORM\Column(length: 500, options: ['comment' => '解析值'])]
    private ?string $value = null;

    #[ORM\Column(length: 20, options: ['comment' => '远程记录ID'])]
    private ?string $recordId = null;

    #[ORM\Column(length: 10, enumType: DnsRecordType::class, options: ['comment' => '记录类型'])]
    private DnsRecordType $type;

    #[ORM\Column(nullable: true, options: ['comment' => 'TTL值'])]
    private ?int $ttl = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '上下文'])]
    private ?array $context = [];

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    public function __toString(): string
    {
        return sprintf('%s.%s (%s)', $this->name, $this->domain?->getName() ?? '', $this->type->value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?DnsDomain
    {
        return $this->domain;
    }

    public function setDomain(?DnsDomain $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function setRecordId(string $recordId): static
    {
        $this->recordId = $recordId;

        return $this;
    }

    public function getType(): DnsRecordType
    {
        return $this->type;
    }

    public function setType(DnsRecordType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function setTtl(?int $ttl): static
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }
}

