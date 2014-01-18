<?php

namespace PaulMaxwell\BlogAdminBundle\Admin;

use PaulMaxwell\BlogBundle\Entity\CategoryRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ArticleAdmin extends Admin
{
    protected $categoryRepository;

    public function setCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    protected function getCategoryList($key, $label)
    {
        $keyMethod = 'get' . $key;
        $labelMethod = 'get' . $label;

        $categories = $this->categoryRepository->findAll();
        $options = array();
        array_walk(
            $categories,
            function (&$category) use (&$options, $keyMethod, $labelMethod) {
                $options[$category->$keyMethod()] = $category->$labelMethod();
            }
        );

        return $options;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category', 'entity', array(
                'class' => 'PaulMaxwellBlogBundle:Category',
                'property' => 'title',
                'label' => 'paul_maxwell_blog_admin.article_form.category',
            ))
            ->add('title', 'text', array('label' => 'paul_maxwell_blog_admin.article_form.title'))
            ->add('body', 'textarea', array(
                'attr' => array(
                    'class' => 'tinymce',
                    'tinymce' => '{"theme":"advanced"}',
                    'data-theme' => 'advanced',
                ),
                'label' => 'paul_maxwell_blog_admin.article_form.body',
            ))
            ->add('tags', 'entity', array(
                'class' => 'PaulMaxwellBlogBundle:Tag',
                'property' => 'title',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => false,
                'label' => 'paul_maxwell_blog_admin.article_form.tags',
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {


        $datagridMapper
            ->add(
                'category.id',
                null,
                array(
                    'label' => 'paul_maxwell_blog_admin.article_filter.category'
                ),
                'choice',
                array(
                    'choices' => $this->getCategoryList('id', 'title')
                )
            )
            ->add('title', null, array('label' => 'paul_maxwell_blog_admin.article_filter.title'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title', 'text', array('label' => 'paul_maxwell_blog_admin.article_list.title'))
            ->add('category.title', 'text', array('label' => 'paul_maxwell_blog_admin.article_list.category'))
            ->add('postedAt', 'datetime', array('label' => 'paul_maxwell_blog_admin.article_list.created'))
            ->add('modifiedAt', 'datetime', array('label' => 'paul_maxwell_blog_admin.article_list.modified'))
            ->add('hits', 'integer', array('label' => 'paul_maxwell_blog_admin.article_list.hits'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
                'label' => 'paul_maxwell_blog_admin.list.actions',
            ));
    }
}
