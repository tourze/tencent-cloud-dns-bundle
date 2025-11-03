<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use TencentCloudDnsBundle\Entity\Account;

/**
 * @template-extends AbstractCrudController<Account>
 */
#[AdminCrud(
    routePath: '/tencent-cloud-dns/account',
    routeName: 'tencent_cloud_dns_account'
)]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('腾讯云账号')
            ->setEntityLabelInPlural('腾讯云账号管理')
            ->setPageTitle(Crud::PAGE_INDEX, '腾讯云账号列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建腾讯云账号')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑腾讯云账号')
            ->setPageTitle(Crud::PAGE_DETAIL, '腾讯云账号详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'secretId'])
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

        yield TextField::new('name', '名称')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(32)
            ->setHelp('为账号设置一个便于识别的名称')
        ;

        yield TextField::new('secretId', 'SecretId')
            ->setColumns('col-md-6')
            ->setRequired(true)
            ->setMaxLength(64)
            ->setHelp('腾讯云API密钥SecretId，请前往控制台获取')
        ;

        yield TextField::new('secretKey', 'SecretKey')
            ->setColumns('col-md-12')
            ->setRequired(true)
            ->setMaxLength(120)
            ->setHelp('腾讯云API密钥SecretKey，出于安全考虑仅在编辑时显示')
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('标识此账号是否可用于API调用')
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
            ->add(TextFilter::new('name', '名称'))
            ->add(TextFilter::new('secretId', 'SecretId'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
