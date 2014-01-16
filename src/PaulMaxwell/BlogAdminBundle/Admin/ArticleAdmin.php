<?php

namespace PaulMaxwell\BlogAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ArticleAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category', 'entity', array(
                'class' => 'PaulMaxwellBlogBundle:Category',
                'property' => 'title',
            ))
            ->add('title', 'text')
            ->add('body', 'textarea', array(
                'attr' => array(
                    'class' => 'tinymce',
                    'tinymce' => '{"theme":"advanced"}',
                    'data-theme' => 'advanced',
                ),
            ))
            ->add('tags', 'entity', array(
                'class' => 'PaulMaxwellBlogBundle:Tag',
                'property' => 'title',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => false,
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'category.title'
            )
            ->add('title');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title', 'text')
            ->add('category.title', 'text', array('label' => 'Category'))
            ->add('postedAt', 'datetime', array('label' => 'Created'))
            ->add('modifiedAt', 'datetime', array('label' => 'Last Changed'))
            ->add('hits', 'integer')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
            ));
    }
}
