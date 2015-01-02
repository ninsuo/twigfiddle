<?php

namespace Fuz\AppBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Fuz\AppBundle\Entity\UserFavorite;
use Fuz\AppBundle\Entity\UserFavoriteTag;

class UserFavoriteTagTransformer extends AbstractTagTransformer implements DataTransformerInterface
{

    protected $fav;

    public function __construct(UserFavorite $fav = null)
    {
        $this->fav = $fav;
    }

    public function reverseTransform($tags)
    {
        $empty = new UserFavoriteTag();
        $empty->setUserFavorite($this->fav);

        return parent::reverseTransformHelper($this->fav, $empty, $tags);
    }
}
