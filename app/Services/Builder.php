<?php
namespace App\Services;

class Builder
{
    public $cycles;
    public $data;

    public function __construct()
    {
        $this->cycles = new Cycles;
    }

    public function sortByChannel($data)
    {
        $result = [];

        // foreach ($data as $video) {
        //     if ($result['channel'])
        // }
    }

    public function prepareDatabase()
    {
        $this->cycles->importChannel();
        $this->cycles->countChannelRating();
    }

    public function getLastCycles()
    {
        return $this->cycles->getLastCycle();
    }

    public function importChannel()
    {
        $this->cycles->importChannel();
    }

    public function countVideoRating()
    {
        $this->cycles->countChannelRating();
        $this->cycles->countVideoRating();
    }

    public function setNewCycle()
    {
        $this->cycles->countNewCycle();
    }

    public function getData()
    {
        $data = $this->data;
        // uncomment if new fresh object is needed after getting data
        // unset($this->data);
        return $data;
    }
}
