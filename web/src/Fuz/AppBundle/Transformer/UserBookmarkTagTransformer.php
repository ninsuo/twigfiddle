<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Transformer;

use Fuz\AppBundle\Entity\UserBookmark;
use Fuz\AppBundle\Entity\UserBookmarkTag;

class UserBookmarkTagTransformer extends AbstractTagTransformer
{

    protected $bookmark;

    public function __construct(UserBookmark $bookmark = null)
    {
        $this->bookmark = $bookmark;
    }

    public function reverseTransform($tags)
    {
        $empty = new UserBookmarkTag();
        $empty->setUserBookmark($this->bookmark);

        return parent::reverseTransformHelper($this->bookmark, $empty, $tags);
    }
}
