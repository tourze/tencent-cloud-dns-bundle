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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use TencentCloudDnsBundle\Entity\DnsDomain;

#[AdminCrud(
    routePath: '/tencent-cloud-dns/domain',
    routeName: 'tencent_cloud_dns_domain'
)]
final class DnsDomainCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DnsDomain::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('DNS域名')
            ->setEntityLabelInPlural('DNS域名管理')
            ->setPageTitle(Crud::PAGE_INDEX, 'DNS域名列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建DNS域名')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑DNS域名')
            ->setPageTitle(Crud::PAGE_DETAIL, 'DNS域名详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name'])
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

        yield TextField::new('name', '域名')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(32)
            ->setHelp('域名，如 example.com')
        ;

        yield AssociationField::new('account', '所属账号')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setHelp('选择管理此域名的腾讯云账号')
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('标识此域名是否有效')
        ;

        yield CodeEditorField::new('context', '上下文信息')
            ->setColumns('col-md-12')
            ->setRequired(false)
            ->setHelp('存储域名相关的额外信息')
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

        // 在详情页显示关联的DNS记录
        if (Crud::PAGE_DETAIL === $pageName) {
            yield AssociationField::new('records', 'DNS记录')
                ->setHelp('与此域名关联的DNS解析记录')
            ;
        }
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '域名'))
            ->add(EntityFilter::new('account', '所属账号'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
