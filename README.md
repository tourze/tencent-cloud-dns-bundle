# Tencent Cloud DNS Bundle

[![Latest Version](https://img.shields.io/packagist/v/tourze/tencent-cloud-dns-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-dns-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/tencent-cloud-dns-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/tencent-cloud-dns-bundle)

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle that provides integration with Tencent Cloud DNS (DNSPod) service for managing domain records.

## Features

- Manage Tencent Cloud DNS accounts
- Create and manage DNS domains
- Create, update, and delete DNS records
- Synchronize DNS records between Tencent Cloud and local database
- Support for various DNS record types (A, MX, TXT, CNAME, NS, URI)
- Command line tools for DNS management

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 2.20/3.0 or higher
- Tencent Cloud account with DNS service enabled

## Installation

```bash
composer require tourze/tencent-cloud-dns-bundle
```

The bundle uses Symfony's auto-configuration, so it will be automatically enabled once installed.

## Configuration

This bundle doesn't require any specific configuration in your Symfony application. It uses Doctrine entities to store configuration and data.

## Usage

### Setting up a Tencent Cloud Account

Before using the DNS features, you need to add a Tencent Cloud account:

```php
use TencentCloudDnsBundle\Entity\Account;

// Create a new account
$account = new Account();
$account->setName('My Tencent Cloud Account');
$account->setSecretId('your-secret-id'); // From Tencent Cloud Console
$account->setSecretKey('your-secret-key'); // From Tencent Cloud Console

// Save the account
$entityManager->persist($account);
$entityManager->flush();
```

### Managing Domains

```php
use TencentCloudDnsBundle\Entity\DnsDomain;

// Create a new domain
$domain = new DnsDomain();
$domain->setName('example.com');
$domain->setAccount($account); // Link to your Tencent Cloud account

// Save the domain
$entityManager->persist($domain);
$entityManager->flush();
```

### Managing DNS Records

```php
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;

// Create a new DNS record
$record = new DnsRecord();
$record->setDomain($domain); // Link to your domain
$record->setName('www'); // Subdomain
$record->setType(DnsRecordType::A);
$record->setValue('192.168.1.1'); // IP address for A record
$record->setTtl(600); // Time to live in seconds

// Save the record
$entityManager->persist($record);
$entityManager->flush();

// The record will be synchronized with Tencent Cloud DNS
```

### Synchronizing Records from Tencent Cloud

You can use the provided command to synchronize DNS records from Tencent Cloud to your local database:

```bash
bin/console tencent-cloud-dns:sync-domain-record-to-local
```

## How It Works

The bundle provides a set of Doctrine entities to store DNS configuration and records. It uses the Tencent Cloud SDK to communicate with the DNSPod API for creating, updating, and deleting DNS records.

The workflow is as follows:

1. Create an Account entity with your Tencent Cloud credentials
2. Create a DnsDomain entity linked to the account
3. Create DnsRecord entities linked to the domain
4. The bundle will automatically synchronize the records with Tencent Cloud DNS

## License

This bundle is available under the MIT license. See the LICENSE file for more information.
