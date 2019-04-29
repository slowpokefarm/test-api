<?php

namespace App\REST;

class Request
{
    protected $request;
    protected $sorts = [];

    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;

        $this->parseSort();
    }

    /**
     * @param string $name
     * @return Sort|null
     */
    public function sort($name)
    {
        return $this->sorts[$name] ?? null;
    }

    public function query($key, $default = null)
    {
        return $this->request->query($key, $default);
    }

    protected function parseSort()
    {
        $sort = $this->request->input('sort');

        if (!$sort) {
            return;
        }

        $sorts = explode(',', $sort);

        if (!count($sorts)) {
            return;
        }

        foreach ($sorts as $item) {
            [$key, $direction] = explode(':', $item);

            if (!$direction) {
                $direction = 'asc';
            }

            $this->sorts[$key] = new Sort($key, $direction);
        }
    }
}
