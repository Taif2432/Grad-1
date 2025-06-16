<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ContentType;

class ContentTypeAPIController extends APIController
{
    public function index()
    {
        $types = ContentType::all();
        return response()->json(['content_types' => $types]);
    }
}
