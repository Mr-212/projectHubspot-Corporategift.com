<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    
    public function setup_guide_doc(){
        dd('here');
        return view('knowledge_base.setup_guide_doc');
    }

}
