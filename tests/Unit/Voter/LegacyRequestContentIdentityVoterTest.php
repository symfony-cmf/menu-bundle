<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\Voter;

use Symfony\Cmf\Bundle\MenuBundle\Voter\RequestContentIdentityVoter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @legacy
 */
class LegacyRequestContentIdentityVoterTest extends RequestContentIdentityVoterTestCase
{
    public function testSkipsWhenNoRequestIsAvailable()
    {
        $this->voter->setRequest(null);

        $this->assertNull($this->voter->matchItem($this->createItem()));
    }

    protected function buildVoter(Request $request)
    {
        $voter = new RequestContentIdentityVoter('_content');
        $voter->setRequest($request);

        return $voter;
    }
}
