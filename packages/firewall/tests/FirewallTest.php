<?php

class FirewallTest extends Base
{
    /**
     * @var FacebookAnonymousPublisher\Firewall\Firewall
     */
    protected $firewall;

    /**
     * @var FacebookAnonymousPublisher\Firewall\Models\Firewall
     */
    protected $model;

    public function setUp()
    {
        parent::setUp();

        $this->firewall = new FacebookAnonymousPublisher\Firewall\Firewall;

        $this->model = new FacebookAnonymousPublisher\Firewall\Models\Firewall;
    }

    public function test_ip()
    {
        $_SERVER['HTTP_CF_Connecting_IP'] = '140.109.1.1';

        $_SERVER['REMOTE_ADDR'] = '103.21.244.115';

        $this->assertSame('140.109.1.1', $this->firewall->ip());

        $_SERVER['REMOTE_ADDR'] = '140.109.1.2';

        $this->assertSame('140.109.1.2', $this->firewall->ip());
    }

    public function test_is_banned()
    {
        $this->firewall->ban('140.109.1.1');
        $this->firewall->ban('140.109.1.2');
        $this->firewall->ban('140.109.1.3');

        $_SERVER['REMOTE_ADDR'] = '140.109.1.2';

        $this->assertSame('regular', $this->firewall->isBanned());

        $_SERVER['REMOTE_ADDR'] = '140.109.1.4';

        $this->assertFalse($this->firewall->isBanned());
    }

    public function test_is_banned_when_same_session()
    {
        $this->firewall->ban('140.109.1.1');

        $_SERVER['REMOTE_ADDR'] = '140.109.1.2';

        $this->assertFalse($this->firewall->isBanned());

        $this->firewall->ban();

        $this->assertSame('regular', $this->firewall->isBanned());

        $_SERVER['REMOTE_ADDR'] = '140.109.1.3';

        $this->assertSame('regular', $this->firewall->isBanned());
    }

    public function test_is_banned_use_different_type()
    {
        $this->firewall->ban('140.109.1.1', 'permanent');

        $_SERVER['REMOTE_ADDR'] = '140.109.1.1';

        $this->assertSame('permanent', $this->firewall->isBanned());

        $this->expectException(InvalidArgumentException::class);

        $this->firewall->ban('140.109.1.2', 'apple');
    }

    public function test_is_banned_use_segment()
    {
        $this->firewall->ban('2001:db8::/127', 'segment');
        $this->firewall->ban('140.109.0.0/16', 'segment');

        $_SERVER['REMOTE_ADDR'] = '140.109.50.22';

        $this->assertSame('segment', $this->firewall->isBanned());

        $_SERVER['REMOTE_ADDR'] = '140.109.1.1';

        $this->assertSame('segment', $this->firewall->isBanned());

        $_SERVER['REMOTE_ADDR'] = '140.110.1.1';

        $this->assertFalse($this->firewall->isBanned());

        $_SERVER['REMOTE_ADDR'] = '2001:db8:0000:0000:0000:0000:0000:0001';

        $this->assertSame('segment', $this->firewall->isBanned());

        $_SERVER['REMOTE_ADDR'] = '2001:db8:0000:0000:0000:0000:0000:0005';

        $this->assertFalse($this->firewall->isBanned());
    }

    public function test_is_allow_country()
    {
        $this->assertTrue($this->firewall->isAllowCountry());

        $_SERVER['REMOTE_ADDR'] = '140.109.1.1';

        $this->assertTrue($this->firewall->isAllowCountry(['TW']));

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->assertTrue($this->firewall->isAllowCountry(['TW']));

        $_SERVER['REMOTE_ADDR'] = '202.232.86.11';

        $this->assertFalse($this->firewall->isAllowCountry(['TW']));
    }

    public function test_ban_and_unban()
    {
        $_SERVER['REMOTE_ADDR'] = '140.109.1.1';

        $this->firewall->ban();

        $this->assertTrue($this->model->where('ip', '140.109.1.1')->exists());

        $this->firewall->ban('140.109.1.2');

        $this->assertTrue($this->model->where('ip', '140.109.1.2')->exists());

        $this->assertCount(2, $this->model->all());

        $this->firewall->unban();

        $this->assertFalse($this->model->where('ip', '140.109.1.1')->exists());

        $this->firewall->unban('140.109.1.2');

        $this->assertFalse($this->model->where('ip', '140.109.1.2')->exists());

        $this->assertCount(0, $this->model->all());
    }

    public function test_unban_all()
    {
        $_SERVER['REMOTE_ADDR'] = '140.109.1.1';

        $this->firewall->ban();
        $this->firewall->ban('140.109.1.2');
        $this->firewall->ban('140.109.1.3');
        $this->firewall->ban('140.109.1.4');
        $this->firewall->ban('140.109.1.5');

        $this->assertCount(5, $this->model->all());

        $this->firewall->unbanAll();

        $this->assertCount(0, $this->model->all());
    }
}
