<?php

namespace PaulMaxwell\BlogAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TagAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', 'text', array('label' => 'paul_maxwell_blog_admin.tag_form.title'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title', null, array('label' => 'paul_maxwell_blog_admin.tag_filter.title'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title', 'text', array('label' => 'paul_maxwell_blog_admin.tag_list.title'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
                'label' => 'paul_maxwell_blog_admin.list.actions',
            ));
    }
}
