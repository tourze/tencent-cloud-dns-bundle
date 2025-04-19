# 腾讯云DNS Bundle数据库实体设计

本文档详细说明腾讯云DNS Bundle中的数据库实体设计和关系。

## 实体概述

腾讯云DNS Bundle包含三个主要实体：

1. **Account** - 存储腾讯云账号凭证信息
2. **DnsDomain** - 存储DNS域名信息
3. **DnsRecord** - 存储DNS解析记录

这三个实体之间形成了一个层次关系：一个账号可以有多个域名，一个域名可以有多个DNS记录。

## 实体详细设计

### Account 实体

`Account`实体用于存储腾讯云API的访问凭证，对应数据库表`tencent_cloud_dns_account`。

#### 主要字段

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | int | 主键 |
| name | string | 账号名称 |
| secretId | string | 腾讯云API密钥ID |
| secretKey | string | 腾讯云API密钥 |
| createTime | datetime | 创建时间 |
| updateTime | datetime | 更新时间 |

#### 用途

此实体用于存储访问腾讯云DNS API所需的凭证信息。用户可以配置多个腾讯云账号，每个账号可以管理不同的域名。密钥信息可以从腾讯云控制台获取：<https://console.cloud.tencent.com/cam/capi>

### DnsDomain 实体

`DnsDomain`实体用于存储DNS域名信息，对应数据库表`tencent_cloud_dns_domain`。

#### 主要字段

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | int | 主键 |
| name | string | 域名名称 |
| account | Account | 关联的腾讯云账号 |
| createTime | datetime | 创建时间 |
| updateTime | datetime | 更新时间 |

#### 关联关系

- 多对一关系：多个域名可以关联到一个腾讯云账号
- 一对多关系：一个域名可以有多个DNS记录

### DnsRecord 实体

`DnsRecord`实体用于存储DNS解析记录，对应数据库表`tencent_cloud_dns_record`。

#### 主要字段

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | int | 主键 |
| domain | DnsDomain | 关联的域名 |
| name | string | 子域名前缀 |
| type | DnsRecordType | 记录类型 |
| value | string | 记录值 |
| ttl | int | 生存时间（秒） |
| recordId | string | 腾讯云DNS记录ID |
| createTime | datetime | 创建时间 |
| updateTime | datetime | 更新时间 |

#### 关联关系

- 多对一关系：多个DNS记录可以关联到一个域名

#### 记录类型

`DnsRecordType`枚举定义了支持的DNS记录类型：

- A - 将域名指向IPv4地址
- MX - 邮件交换记录
- TXT - 文本记录
- CNAME - 别名记录
- NS - 域名服务器记录
- URI - URI记录

## 实体关系图

```er
Account 1 --- * DnsDomain 1 --- * DnsRecord
```

## 设计说明

### 数据完整性

- 使用外键约束确保数据完整性
- 删除账号或域名时，相关联的记录也会被删除

### 同步机制

当本地创建或修改DNS记录时，系统会自动与腾讯云DNS服务同步。同步过程如下：

1. 创建/修改本地DNS记录
2. 调用腾讯云DNS API创建/修改远程记录
3. 获取远程记录ID并更新本地记录

同样，可以通过命令行工具从腾讯云DNS服务同步记录到本地数据库。

### 安全考虑

- 敏感信息（如secretKey）应妥善保管
- 建议使用环境变量或密钥管理系统存储密钥，而不是硬编码

## 使用示例

以下是一个完整的使用示例，展示了如何创建账号、域名和DNS记录：

```php
// 创建账号
$account = new Account();
$account->setName('我的腾讯云账号');
$account->setSecretId('your-secret-id');
$account->setSecretKey('your-secret-key');
$entityManager->persist($account);

// 创建域名
$domain = new DnsDomain();
$domain->setName('example.com');
$domain->setAccount($account);
$entityManager->persist($domain);

// 创建A记录
$record = new DnsRecord();
$record->setDomain($domain);
$record->setName('www');
$record->setType(DnsRecordType::A);
$record->setValue('192.168.1.1');
$record->setTtl(600);
$entityManager->persist($record);

// 保存所有更改
$entityManager->flush();
```

这个设计允许用户灵活管理多个腾讯云账号和域名，同时保持与腾讯云DNS服务的同步。
