<?php

namespace PaulMaxwell\BlogAdminBundle\Twig\Extension;

use Stfalcon\Bundle\TinymceBundle\Twig\Extension\StfalconTinymceExtension;

class PaulMaxwellTinymceExtension extends StfalconTinymceExtension
{
    public function tinymceInit()
    {
        $config = $this->getParameter('stfalcon_tinymce.config');

        $this->baseUrl = (!isset($config['base_url']) ? null : $config['base_url']);
        /** @var $assets \Symfony\Component\Templating\Helper\CoreAssetsHelper */
        $assets = $this->getService('templating.helper.assets');

        // Get path to tinymce script for the jQuery version of the editor
        $config['jquery_script_url'] = $assets->getUrl(
            $this->baseUrl . 'bundles/stfalcontinymce/vendor/tinymce/tinymce.jquery.min.js'
        );

        // Get local button's image
        foreach ($config['tinymce_buttons'] as &$customButton) {
            $customButton['image'] = $this->getAssetsUrl($customButton['image']);
        }

        // Update URL to external plugins
        foreach ($config['external_plugins'] as &$extPlugin) {
            $extPlugin['url'] = $this->getAssetsUrl($extPlugin['url']);
        }

        // If the language is not set in the config...
        if (empty($config['language'])) {
            // get it from the request
            $config['language'] = $this->getService('request')->getLocale();
        }

        $langDirectory = $this
                ->getService('kernel')
                ->locateResource('@StfalconTinymceBundle/Resources/public/vendor/tinymce/langs') . '/';

        // A language code coming from the locale may not match an existing language file
        $langs = array(
            $config['language'],
            substr($config['language'], 0, 2),
            substr($config['language'], 0, 2) . '_' . strtoupper($config['language']),
            'en',
        );
        foreach ($langs as $lang) {
            if (file_exists($langDirectory . $lang . '.js')) {
                $config['language'] = $lang;
                break;
            }
        }

        // TinyMCE does not allow to set different languages to each instance
        foreach ($config['theme'] as $themeName => $themeOptions) {
            $config['theme'][$themeName]['language'] = $config['language'];
        }

        foreach ($config['theme'] as &$bundleTheme) {
            $bundleTheme['document_base_url'] = $assets->getUrl('');
        }

        return $this->getService('templating')->render('StfalconTinymceBundle:Script:init.html.twig', array(
            'tinymce_config' => preg_replace('/"file_browser_callback":"([^"]+)"\s*/', 'file_browser_callback:$1', json_encode($config)),
            'include_jquery' => $config['include_jquery'],
            'tinymce_jquery' => $config['tinymce_jquery'],
            'base_url'       => $this->baseUrl
        ));
    }
}
