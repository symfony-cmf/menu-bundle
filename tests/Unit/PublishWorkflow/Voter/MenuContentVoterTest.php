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

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit\PublishWorkflow\Voter;

use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Cmf\Bundle\MenuBundle\PublishWorkflow\Voter\MenuContentVoter;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class MenuContentVoterTest extends \PHPUnit\Framework\Testcase
{
    /**
     * @var MenuContentVoter
     */
    private $voter;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var PublishWorkflowChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pwfc;

    public function setUp()
    {
        $this->pwfc = $this->getMockBuilder('Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker')
            ->disableOriginalConstructor()
            ->getMock();
        $this->container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('cmf_core.publish_workflow.checker')
            ->will($this->returnValue($this->pwfc))
        ;
        $this->voter = new MenuContentVoter($this->container);
        $this->token = new AnonymousToken('', '');
    }

    public function providePublishWorkflowChecker()
    {
        $content = $this->createMock('Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableReadInterface');

        return [
            [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'attributes' => PublishWorkflowChecker::VIEW_ATTRIBUTE,
                $content,
                'isMenuPublishable' => true,
                'isContentPublishable' => true,
            ],
            [
                'expected' => VoterInterface::ACCESS_DENIED,
                'attributes' => PublishWorkflowChecker::VIEW_ATTRIBUTE,
                $content,
                'isMenuPublishable' => false,
                'isContentPublishable' => false,
            ],
            [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'attributes' => [
                    PublishWorkflowChecker::VIEW_ANONYMOUS_ATTRIBUTE,
                    PublishWorkflowChecker::VIEW_ATTRIBUTE,
                ],
                $content,
                'isMenuPublishable' => true,
                'isContentPublishable' => true,
            ],
            [
                'expected' => VoterInterface::ACCESS_DENIED,
                'attributes' => PublishWorkflowChecker::VIEW_ANONYMOUS_ATTRIBUTE,
                $content,
                'isMenuPublishable' => false,
                'isContentPublishable' => false,
            ],
            [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'attributes' => 'other',
                $content,
                'isMenuPublishable' => true,
                'isContentPublishable' => true,
            ],
            [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'attributes' => [PublishWorkflowChecker::VIEW_ATTRIBUTE, 'other'],
                $content,
                'isMenuPublishable' => true,
                'isContentPublishable' => true,
            ],
            [
                'expected' => VoterInterface::ACCESS_GRANTED,
                'attributes' => [PublishWorkflowChecker::VIEW_ATTRIBUTE],
                null,
                'isMenuPublishable' => true,
                'isContentPublishable' => null,
            ],
            [
                'expected' => VoterInterface::ACCESS_ABSTAIN,
                'attributes' => [PublishWorkflowChecker::VIEW_ATTRIBUTE, 'other'],
                null,
                'isMenuPublishable' => true,
                'isContentPublishable' => null,
            ],
            [
                'expected' => VoterInterface::ACCESS_DENIED,
                'attributes' => [PublishWorkflowChecker::VIEW_ATTRIBUTE, 'other'],
                $content,
                'isMenuPublishable' => true,
                'isContentPublishable' => false,
            ],
            [
                'expected' => VoterInterface::ACCESS_DENIED,
                'attributes' => PublishWorkflowChecker::VIEW_ATTRIBUTE,
                $content,
                'isMenuPublishable' => true,
                'isContentPublishable' => false,
            ],
        ];
    }

    /**
     * @dataProvider providePublishWorkflowChecker
     */
    public function testPublishWorkflowChecker($expected, $attributes, $content, $isMenuPusblishable, $isContentPublishable)
    {
        $attributes = (array) $attributes;
        $menuNode = $this->createMock('Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode');
        $menuNode->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($content))
        ;
        $this->pwfc->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue($isContentPublishable))
        ;

        $this->assertEquals($expected, $this->voter->vote($this->token, $menuNode, $attributes));
    }

    public function testUnsupportedClass()
    {
        $result = $this->voter->vote(
            $this->token,
            $this,
            [PublishWorkflowChecker::VIEW_ATTRIBUTE]
        );
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }
}
