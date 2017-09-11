<?php

namespace Pants\Test\Task;

use Pants\ContextInterface;
use Pants\Property\PropertiesInterface;
use Pants\Target\TargetInterface;
use PHPUnit\Framework\TestCase;

class TaskTestCase extends TestCase
{
    /**
     * @var ContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockContext;

    /**
     * @var TargetInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCurrentTarget;

    /**
     * @var PropertiesInterface|\PHPUnit_Framework_MockObject_MockObject $mockProperties
     */
    protected $mockProperties;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockCurrentTarget = $this->createMock(TargetInterface::class);
        $this->mockProperties = $this->createMock(PropertiesInterface::class);

        $this->mockContext->expects($this->any())
            ->method('getCurrentTarget')
            ->will($this->returnValue($this->mockCurrentTarget));

        $this->mockCurrentTarget->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->mockContext->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($this->mockProperties));

        $this->mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->mockContext);
        unset($this->mockCurrentTarget);
        unset($this->mockProperties);
    }
}
