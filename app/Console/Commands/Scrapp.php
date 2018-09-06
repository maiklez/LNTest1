<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Orchestra\Parser\Xml\Facade as XmlParser;
class Scrapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ln:scrapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $xml = XmlParser::load('https://rss.sciencedirect.com/publication/science/15662535');
    	
        //Year,Source title,Volume,DOI,Authors,Authors with affiliations,Title,Abstract,Keywords
        $data = $xml->parse([
        		'items' => ['uses' => 'channel.item[title,description,link]'],        		
        ]);
        
        $link = $data['items'][0]['link'];

        $html = new \Htmldom($link);
        
        
        $affiliation_script = $html->find('script');$json = [];
        //dump($affiliation->innertext);
        foreach ($affiliation_script as $aff){
        	$text = $aff->innertext;
        	//dump($text);
        	if(stripos($text, 'affiliation')){
        		$json = json_decode($text, true);    		
        	}	
        }        	
        
        //$doi_link = $html->find('div.DoiLink', 0)->plaintext;
        //dump($doi_link);
        
        $authors = $html->find('div.AuthorGroups', 0)->find('a.author');
        $i=1;
        foreach($authors as $author){
        	//quitar espacios
        	dump($author->plaintext);
        	echo PHP_EOL;
        	foreach ($json['authors']['affiliations']['aff000'.$i]['$$'][2]['$$'] as $affil){
        		echo $affil['_'].', ';
        	}
        	echo ";".PHP_EOL;
        	$i++;        	
        	
        }
        	
        
        
        
        
        //Keywords
        //dump(str_replace('Keywords',"", $html->find('div.Keywords', 0)->plaintext));
        
//         foreach ($data['items'] as $item){
//         	$html = new \Htmldom($item['link']);
        	 
        	
//         	dump($item['link']);
//         }
        
        #dump($data['items']);
    }
}
