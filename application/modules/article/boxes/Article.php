<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Article\Boxes;
defined('ACCESS') or die('no direct access');

class Article extends \Ilch\Box
{
    public function render()
    {
        $articleMapper = new \Article\Mappers\Article();
        $this->getView()->set('articles', $articleMapper->getArticleList('', 5));
    }
}