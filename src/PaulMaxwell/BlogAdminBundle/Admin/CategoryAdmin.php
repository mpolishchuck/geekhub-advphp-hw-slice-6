<?php

namespace PaulMaxwell\BlogAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('parent', 'entity', array(
                'class' => 'PaulMaxwellBlogBundle:Category',
                'property' => 'title',
                'label' => 'paul_maxwell_blog_admin.category_form.parent',
                'required' => false,
            ))
            ->add('title', 'text', array('label' => 'paul_maxwell_blog_admin.category_form.title'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title', null, array('label' => 'paul_maxwell_blog_admin.category_filter.title'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title', 'text', array('label' => 'paul_maxwell_blog_admin.category_list.title'))
            ->add('parent.title', 'text', array('label' => 'paul_maxwell_blog_admin.category_list.parent'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
                'label' => 'paul_maxwell_blog_admin.list.actions',
            ));
    }
}
