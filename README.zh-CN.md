# 腾讯云DNS Bundle

[![Latest Version](https://img.shields.io/packagist/v/tourze/tencent-cloud-dns-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-dns-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/tencent-cloud-dns-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-dns-bundle)

[English](README.md) | [中文](README.zh-CN.md)

一个提供腾讯云DNS（DNSPod）服务集成的Symfony Bundle，用于管理域名记录。

## 功能特性

- 管理腾讯云DNS账号
- 创建和管理DNS域名
- 创建、更新和删除DNS记录
- 在腾讯云和本地数据库之间同步DNS记录
- 支持多种DNS记录类型（A、MX、TXT、CNAME、NS、URI）
- 提供DNS管理的命令行工具

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 2.20/3.0 或更高版本
- 已启用DNS服务的腾讯云账号

## 安装

```bash
composer require tourze/tencent-cloud-dns-bundle
```

该Bundle使用Symfony的自动配置功能，安装后将自动启用。

## 配置

此Bundle不需要在Symfony应用程序中进行特定配置。它使用Doctrine实体来存储配置和数据。

## 使用方法

### 设置腾讯云账号

在使用DNS功能之前，您需要添加一个腾讯云账号：

```php
use TencentCloudDnsBundle\Entity\Account;

// 创建新账号
$account = new Account();
$account->setName('我的腾讯云账号');
$account->setSecretId('your-secret-id'); // 从腾讯云控制台获取
$account->setSecretKey('your-secret-key'); // 从腾讯云控制台获取

// 保存账号
$entityManager->persist($account);
$entityManager->flush();
```

### 管理域名

```php
use TencentCloudDnsBundle\Entity\DnsDomain;

// 创建新域名
$domain = new DnsDomain();
$domain->setName('example.com');
$domain->setAccount($account); // 关联到您的腾讯云账号

// 保存域名
$entityManager->persist($domain);
$entityManager->flush();
```

### 管理DNS记录

```php
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;

// 创建新DNS记录
$record = new DnsRecord();
$record->setDomain($domain); // 关联到您的域名
$record->setName('www'); // 子域名
$record->setType(DnsRecordType::A);
$record->setValue('192.168.1.1'); // A记录的IP地址
$record->setTtl(600); // 生存时间（秒）

// 保存记录
$entityManager->persist($record);
$entityManager->flush();

// 记录将与腾讯云DNS同步
```

### 从腾讯云同步记录

您可以使用提供的命令从腾讯云同步DNS记录到本地数据库：

```bash
bin/console tencent-cloud-dns:sync-domain-record-to-local
```

## 工作原理

该Bundle提供了一组Doctrine实体来存储DNS配置和记录。它使用腾讯云SDK与DNSPod API通信，用于创建、更新和删除DNS记录。

工作流程如下：

1. 使用您的腾讯云凭证创建Account实体
2. 创建与账号关联的DnsDomain实体
3. 创建与域名关联的DnsRecord实体
4. Bundle将自动将记录与腾讯云DNS同步

## 数据库实体设计

### Account（账号）

存储腾讯云API访问凭证：
- `id`: 主键
- `name`: 账号名称
- `secretId`: 腾讯云API密钥ID
- `secretKey`: 腾讯云API密钥

### DnsDomain（域名）

存储DNS域名信息：
- `id`: 主键
- `name`: 域名名称（如example.com）
- `account`: 关联的腾讯云账号

### DnsRecord（DNS记录）

存储DNS解析记录：
- `id`: 主键
- `domain`: 关联的域名
- `name`: 子域名前缀
- `type`: 记录类型（A, MX, TXT, CNAME, NS, URI）
- `value`: 记录值
- `ttl`: 生存时间
- `recordId`: 腾讯云DNS记录ID

## 许可证

此Bundle基于MIT许可证提供。有关更多信息，请参阅LICENSE文件。
