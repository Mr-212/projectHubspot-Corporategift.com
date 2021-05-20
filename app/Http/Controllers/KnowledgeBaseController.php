<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


// provides the methods to load setup guide, privacy policy and terms & condition pages etc.
class KnowledgeBaseController extends Controller
{
    
    public function setup_guide_doc(){
        return view('knowledge_base.setup_guide_doc');
    }


    public function privacy_policy(){
        
        return view('knowledge_base.privacy_policy');
    }

    public function terms_of_services()
    {

        return view('knowledge_base.terms_of_services');
    }

}
