<?php

namespace TencentCloudDnsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'records', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?DnsDomain $domain = null;

    #[Assert\NotBlank(message: '前缀不能为空')]
    #[Assert\Length(max: 200, maxMessage: '前缀长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 200, options: ['comment' => '前缀'])]
    private string $name;

    #[Assert\NotBlank(message: '解析值不能为空')]
    #[Assert\Length(max: 500, maxMessage: '解析值长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 500, options: ['comment' => '解析值'])]
    private ?string $value = null;

    #[Assert\Length(max: 20, maxMessage: '记录ID长度不能超过{{ limit }}个字符')]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '远程记录ID'])]
    private ?string $recordId = null;

    #[Assert\Choice(choices: ['A', 'MX', 'TXT', 'CNAME', 'NS', 'URI'], message: '记录类型必须是有效的DNS记录类型')]
    #[ORM\Column(length: 10, enumType: DnsRecordType::class, options: ['comment' => '记录类型'])]
    private DnsRecordType $type;

    #[Assert\Positive(message: 'TTL值必须是正整数')]
    #[ORM\Column(nullable: true, options: ['comment' => 'TTL值'])]
    private ?int $ttl = null;

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

    public function setDomain(?DnsDomain $domain): void
    {
        $this->domain = $domain;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function setRecordId(?string $recordId): void
    {
        $this->recordId = $recordId;
    }

    public function getType(): DnsRecordType
    {
        return $this->type;
    }

    public function setType(DnsRecordType $type): void
    {
        $this->type = $type;
    }

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function setTtl(?int $ttl): void
    {
        $this->ttl = $ttl;
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
