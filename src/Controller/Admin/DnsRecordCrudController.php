<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @template-extends AbstractCrudController<DnsRecord>
 */
#[AdminCrud(
    routePath: '/tencent-cloud-dns/record',
    routeName: 'tencent_cloud_dns_record'
)]
final class DnsRecordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DnsRecord::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('DNS记录')
            ->setEntityLabelInPlural('DNS记录管理')
            ->setPageTitle(Crud::PAGE_INDEX, 'DNS记录列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建DNS记录')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑DNS记录')
            ->setPageTitle(Crud::PAGE_DETAIL, 'DNS记录详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'value', 'recordId'])
            ->showEntityActionsInlined()
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield AssociationField::new('domain', '所属域名')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setHelp('选择此记录所属的域名')
        ;

        yield TextField::new('name', '记录前缀')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(200)
            ->setHelp('DNS记录的前缀，如 www、@、*')
        ;

        $typeField = EnumField::new('type', '记录类型');
        $typeField->setEnumCases(DnsRecordType::cases());
        yield $typeField
            ->setColumns('col-md-4')
            ->setRequired(true)
            ->setHelp('DNS记录类型')
        ;

        yield TextareaField::new('value', '解析值')
            ->setColumns('col-md-8')
            ->setRequired(true)
            ->setMaxLength(500)
            ->setHelp('DNS记录的解析值，如IP地址或目标主机名')
            ->setNumOfRows(3)
        ;

        yield TextField::new('recordId', '远程记录ID')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->setMaxLength(20)
            ->setHelp('腾讯云DNS服务中的记录ID')
            ->hideOnIndex()
        ;

        yield IntegerField::new('ttl', 'TTL值')
            ->setColumns('col-md-6')
            ->setRequired(false)
            ->setHelp('记录的生存时间（秒）')
            ->hideOnIndex()
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('标识此记录是否有效')
        ;

        yield CodeEditorField::new('context', '上下文信息')
            ->setColumns('col-md-12')
            ->setRequired(false)
            ->setHelp('存储记录相关的额外信息')
            ->hideOnIndex()
            ->setLanguage('javascript')
            ->setNumOfRows(5)
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->hideOnIndex()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('domain', '所属域名'))
            ->add(TextFilter::new('name', '记录前缀'))
            ->add(TextFilter::new('value', '解析值'))
            ->add(TextFilter::new('recordId', '远程记录ID'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
