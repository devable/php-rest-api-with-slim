<?php

namespace App\Helper;

class UtilsHelper {
    /**
     * @param array $array
     * @return array
     */
    public static function omitNullValues(array $array): array {
        return array_filter($array, function ($val) { return $val !== null; });
    }

    /**
     * @param string $content
     * @param integer $limit
     * @return string
     */
    public static function excerpt(string $content, int $limit): string
    {
        $excerpt = explode(' ', strip_tags($content), $limit+1);
        if (count($excerpt) >= $limit) {
            array_pop($excerpt);
            $excerpt = implode(' ', $excerpt) . '...';
        } else {
            $excerpt = implode(' ', $excerpt);
        }
        return self::cleanContent($excerpt);
    }

    /**
     * @param string $content
     * @return string
     */
    public static function cleanContent(string $content): string
    {
        $content = strip_tags(
            $content,
            '<b><strong><small><br><em><hr><i><li><ol><p><s><span><table><tbody><thead><tfoot><tr><td><u><ul>'
        );
        $content = preg_replace('`[[^]]*]`', '', $content);
        $content = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i", '<$1$2>', $content);
        $content = str_replace('&nbsp;', ' ', $content);
        $content = trim($content);
        return $content;
    }

    public static function makeUid (int $length = 13): string {
        return bin2hex(random_bytes($length));
    }
}
