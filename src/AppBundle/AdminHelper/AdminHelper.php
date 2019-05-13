<?php

namespace AppBundle\AdminHelper;

class AdminHelper
{
    public static function displayImage($image, $config)
    {
        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions = array('required' => false);
        if ($image && ($webPath = $image->getImagePath())) {
            // get the container so the full path to the image can be set
            $container = $config->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request_stack')->getCurrentRequest()->getBasePath().$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptions['help'] = '<img style="max-width:100%" src="' . $fullPath . '" class="admin-preview" />';
        }

        return $fileFieldOptions;
    }

    public static function upload_image($url, $saveto)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw = curl_exec($ch);
        curl_close ($ch);
        if(file_exists($saveto)){
            unlink($saveto);
        }
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);
    }

    public static function clearNonAlNum($string, $start = 0, $offset = 35)
    {
        // Get substr from title
        $clear = strtolower(substr($string, $start, $offset));
        // Clean up things like &amp;
        $clear = html_entity_decode($clear);
        // Strip out any url-encoded stuff
        $clear = urldecode($clear);
        // Replace non-AlNum characters with space
        $clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
        // Replace Multiple spaces with single space
        $clear = preg_replace('/ +/', '-', $clear);
        // Trim the string of leading/trailing space
        $clear = trim($clear);

        return $clear;
    }

    public static function getRecLink($object)
    {
        $slugs = array();
        $articles = $object->getRecLink();
        foreach ($articles as $article) {
            $slugs[] = $article->getSlug();
        }

        if (count($slugs) > 0) {
            return $slugs;
        }

        return false;
    }
}