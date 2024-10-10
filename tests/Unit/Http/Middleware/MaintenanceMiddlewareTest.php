<?php

namespace App\Tests\Unit\Http\Middleware;

use Mockery as m;
use Mockery\MockInterface;
use App\Models\Node;
use Illuminate\Http\Response;
use App\Models\Server;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Http\Middleware\MaintenanceMiddleware;

class MaintenanceMiddlewareTest extends MiddlewareTestCase
{
    private MockInterface $response;

    /**
     * Setup tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->response = m::mock(ResponseFactory::class);
    }

    /**
     * Test that a node not in maintenance mode continues through the request cycle.
     */
    public function testHandle(): void
    {
        // maintenance mode is off by default
        $server = Server::factory()->withNode()->make();
        $server->load('node');

        $this->setRequestAttribute('server', $server);

        $this->getMiddleware()->handle($this->request, $this->getClosureAssertions());
    }

    /**
     * Test that a node in maintenance mode returns an error view.
     */
    public function testHandleInMaintenanceMode(): void
    {
        $server = Server::factory()
            ->withNode(Node::factory()->create(['maintenance_mode' => true]))
            ->create()
            ->load('node');

        $this->setRequestAttribute('server', $server);

        $this->response->shouldReceive('view')
            ->once()
            ->with('errors.maintenance')
            ->andReturn(new Response());

        $response = $this->getMiddleware()->handle($this->request, $this->getClosureAssertions());
        $this->assertInstanceOf(Response::class, $response);
    }

    private function getMiddleware(): MaintenanceMiddleware
    {
        return new MaintenanceMiddleware($this->response);
    }
}
