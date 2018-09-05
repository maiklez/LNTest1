<?php

namespace App\Http\Controllers;


use Orchestra\Parser\Xml\Facade as XmlParser;

class HomeController extends Controller
{
	
	
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    	
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	return view('home');
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function test1()
    {	
    	
    	$xml = XmlParser::load('https://rss.sciencedirect.com/publication/science/15662535');
    	 
    	//Year,Source title,Volume,DOI,Authors,Authors with affiliations,Title,Abstract,Keywords
    	$parser = $xml->parse([
    			'items' => ['uses' => 'channel.item[title,description,link]'],
    	]);
    	
    	$data=[];
    	foreach ($parser['items'] as $item){
    	  	$html = new \Htmldom($item['link']);
    	  	//dump($item['link']);
    	  	$data['source_title'] = $item['title'];
    	  	$data['description'] = $item['description'];
    	  	
    	  	$html = new \Htmldom($item['link']);
    	  	$data['keywords'] = str_replace('Keywords',"", $html->find('div.Keywords', 0)->plaintext);    	  	
    	  	$data['doi_link'] = $html->find('div.DoiLink', 0);
    	}
    	
       	\Excel::create('TEST1', function($excel) use($data){
    		$excel->setTitle('test');

    		$excel->sheet('sheet', function($sheet) use($data){
    			$sheet->fromArray($data['items']);
    		});
    	
    	})->download('csv');
    }

    
}
