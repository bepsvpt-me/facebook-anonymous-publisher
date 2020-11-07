<?php

namespace FacebookAnonymousPublisher\Firewall;

class Firewall
{
    /**
     * @var Models\Firewall
     */
    protected $model;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->model = new Models\Firewall;
    }

    /**
     * Get the valid IP address or false if no valid IP address was found.
     *
     * @return false|string
     */
    public function ip()
    {
        return Utility::ip();
    }

    /**
     * Determine the request ip address is banned or not.
     *
     * @return string|bool
     */
    public function isBanned()
    {
        if (false !== ($banned = session('isBan', false))) {
            $this->ban();

            return $banned;
        }

        $banned = $this->model
            ->where('ip', $this->ip())
            ->whereIn('type', ['regular', 'permanent'])
            ->first(['ip', 'type']);

        if (! is_null($banned)) {
            return $banned->getAttribute('type');
        }

        return $this->isSegmentBanned();
    }

    /**
     * Determine the request ip address is banned or not.
     *
     * @return bool|string
     */
    protected function isSegmentBanned()
    {
        $banned = $this->model
            ->where('type', 'segment')
            ->get()
            ->contains(function ($cidr) {
                return Utility::contains($cidr->getAttribute('ip'), $this->ip());
            });

        return $banned ? 'segment' : false;
    }

    /**
     * Determine the request ip address is in allow countries.
     *
     * @param array $codes
     *
     * @return bool
     */
    public function isAllowCountry(array $codes = ['*'])
    {
        try {
            return in_array('*', $codes, true)
                || in_array(Utility::isoCode($this->ip()), $codes, true);
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Ban ip address.
     *
     * @param string|null $ip
     * @param string $type
     *
     * @return Models\Firewall
     */
    public function ban($ip = null, $type = 'regular')
    {
        $this->validateType($type);

        if (! is_null($ip)) {
            if ('segment' === $type) {
                $ip = Utility::cidr($ip);
            }
        } else {
            $ip = $this->ip();

            session(['isBan' => $type]);
        }

        return $this->model->firstOrCreate(compact('ip'), compact('type'));
    }

    /**
     * Validate the type is valid or not.
     *
     * @param string $type
     */
    protected function validateType($type)
    {
        if (! in_array($type, ['regular', 'permanent', 'segment'], true)) {
            throw new \InvalidArgumentException;
        }
    }

    /**
     * Unban ip address.
     *
     * @param string|null $ip
     *
     * @return int
     */
    public function unban($ip = null)
    {
        if (is_null($ip)) {
            $ip = $this->ip();

            session()->forget('isBan');
        }

        return $this->model->destroy($ip);
    }

    /**
     * Unban all ip addresses.
     *
     * @return void
     */
    public function unbanAll()
    {
        $this->model->truncate();
    }
}
