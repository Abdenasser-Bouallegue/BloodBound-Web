<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }
    public function configureFields(string $pageName): iterable
    {
 
        yield TextField::new(propertyName: 'Title');
        yield SlugField::new(propertyName: 'Slug')->SetTargetFieldName(fieldName: 'Title');
        yield TextEditorField::new(propertyName: 'Content');
        yield TextField::new(propertyName: 'FeaturedText');
        yield DateTimeField::new(propertyName: 'CreatedAt')->hideOnForm();
        yield DateTimeField::new(propertyName: 'UpdateAt')->hideOnForm();
        
      }
      
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
