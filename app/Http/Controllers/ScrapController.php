<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Builder;

class ScrapController extends Controller
{
    public $builder;

    public function __construct()
    {
        $this->builder = new Builder;
    }

    public function index()
    {
        $data = $this->builder->getLastCycles();

        return view("content", compact( "data" )); // "channels", "videos", "video" )); //
    }

    //
    public function importChannel()
    {
        $this->builder->importChannel();

        $message = 'Insert of the Channel and videos data to database was successfull.';

        return view("content", compact( "message" )); //"data" ));
    }

    public function countVideoRating()
    {
        $this->builder->countVideoRating();

        $message = 'Video ratings were updated.';

        return view("content", compact( "message" ));
    }
}
