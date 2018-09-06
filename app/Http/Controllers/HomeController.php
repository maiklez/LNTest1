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

    		$data['source_title'] = $item['title'];
    	  	$data['description'] = $item['description'];
    	  	
    	  	$html = new \Htmldom($item['link']);
    	  	$data['keywords'] = str_replace('Keywords',"", $html->find('div.Keywords', 0)->plaintext);    	  	
    	  	$data['doi_link'] = $html->find('div.DoiLink', 0)->plaintext;
    	  	
    	  	$affiliation_script = $html->find('script');
    	  	$json = [];
    	  	//dump($affiliation->innertext);
    	  	foreach ($affiliation_script as $aff){
    	  		$text = $aff->innertext;
    	  		//dump($text);
    	  		if(stripos($text, 'affiliation')){
    	  			$json = json_decode($text, true);
    	  		}
    	  	}

    	  	
    	  	$authors = $html->find('div.AuthorGroups', 0)->find('a.author');
    	  	$authos_s='';
    	  	foreach($authors as $author){
    	  		$authos_s .= $author->plaintext.', ';
    	  	}
    	  	$data['authors'] = substr($authos_s, 0 ,strlen($authos_s)-2 );
    	  	
    	  	$i=1;
    	  	$affiliations='';
    	  	foreach($authors as $author)
    	  	{
    	  		//quitar espacios
    	  		$affiliations = $author->plaintext;
    	  		
    	  		if(isset($json['authors']['affiliations']['aff000'.$i])){
    	  			foreach ($json['authors']['affiliations']['aff000'.$i]['$$'][2]['$$'] as $affil)
    	  			{
    	  				$affiliations .=  $affil['_'].', ';
    	  			}
    	  			$affiliations .= substr($affiliations, 0 ,strlen($affiliations)-2)."; ";    	  			
    	  		}
    	  		$i++;
    	  	}
    	  	$data['affiliations'] = substr($affiliations, 0 ,strlen($affiliations)-1 );
    	}
    	
       	\Excel::create('TEST1', function($excel) use($data)
       	{
    		$excel->setTitle('test');

    		$excel->sheet('sheet', function($sheet) use($data){
    			$sheet->fromArray($data);
    		});
    	
    	})->download('csv');
    }

    
}
