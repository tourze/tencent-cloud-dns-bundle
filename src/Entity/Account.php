<?php

namespace TencentCloudDnsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TencentCloudDnsBundle\Repository\AccountRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 密钥可前往官网控制台 https://console.cloud.tencent.com/cam/capi 进行获取
 *
 * @phpstan-property-read int|null $id Doctrine-managed ID, assigned after persistence
 */
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'tencent_cloud_dns_account', options: ['comment' => '腾讯云账号'])]
class Account implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: '名称不能为空')]
    #[Assert\Length(max: 32, maxMessage: '名称长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[Assert\NotBlank(message: 'SecretId不能为空')]
    #[Assert\Length(max: 64, maxMessage: 'SecretId长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => 'SecretId'])]
    private ?string $secretId = null;

    #[Assert\NotBlank(message: 'SecretKey不能为空')]
    #[Assert\Length(max: 120, maxMessage: 'SecretKey长度不能超过{{ limit }}个字符')]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => 'SecretKey'])]
    private ?string $secretKey = null;

    #[Assert\Type(type: 'bool', message: '有效性必须是布尔值')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    public function __toString(): string
    {
        return $this->name ?? 'Account #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecretId(): ?string
    {
        return $this->secretId;
    }

    public function setSecretId(string $secretId): void
    {
        $this->secretId = $secretId;
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    public function setSecretKey(string $secretKey): void
    {
        $this->secretKey = $secretKey;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
